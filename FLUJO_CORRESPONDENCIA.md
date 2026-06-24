# FLUJO COMPLETO DE CORRESPONDENCIA - Documentación Técnica

**Fecha de Implementación:** 24 de Junio, 2026  
**Versión:** 1.0  
**Estado:** Implementado y Listo para Testing

---

## 📋 ÍNDICE

1. [Descripción General](#descripción-general)
2. [Estados Oficiales](#estados-oficiales)
3. [Flujo de Transiciones](#flujo-de-transiciones)
4. [Arquitectura Técnica](#arquitectura-técnica)
5. [Base de Datos](#base-de-datos)
6. [API Endpoints](#api-endpoints)
7. [Modelos Eloquent](#modelos-eloquent)
8. [Controladores](#controladores)
9. [Vistas Blade](#vistas-blade)
10. [Auditoría y Trazabilidad](#auditoría-y-trazabilidad)
11. [Ejemplos de Uso](#ejemplos-de-uso)
12. [Testing](#testing)

---

## 📌 DESCRIPCIÓN GENERAL

El flujo de correspondencia es un sistema completo de gestión de documentos que permite:

- ✅ **Crear documentos** en estado inicial PENDIENTE
- ✅ **Derivar documentos** manteniendo estado PENDIENTE
- ✅ **Recibir documentos** (destinatario confirma → RECIBIDO)
- ✅ **Atender documentos** (usuario completa → ATENDIDO)
- ✅ **Archivar documentos** (usuario finaliza → ARCHIVADO)

Cada transición de estado registra:
- **Usuario** que realizó la acción
- **Fecha y Hora** exacta de la transición
- **Estado Anterior** y **Estado Nuevo**
- **Tipo de Acción** (CREAR, DERIVAR, RECIBIR, ATENDER, ARCHIVAR)
- **Observación** opcional del usuario

---

## 🎯 ESTADOS OFICIALES

```
┌──────────────────────────────────────────────────────────────┐
│                     ESTADOS DE DOCUMENTO                      │
├──────────────────────────────────────────────────────────────┤
│ 1. PENDIENTE   → Estado inicial, esperando acción             │
│ 2. RECIBIDO    → Destinatario confirmó recepción              │
│ 3. ATENDIDO    → Usuario completó la atención                 │
│ 4. ARCHIVADO   → Documento finalizado y archivado             │
└──────────────────────────────────────────────────────────────┘
```

---

## 🔄 FLUJO DE TRANSICIONES

### Diagrama de Flujo

```
                    ┌─────────────────┐
                    │  CREAR DOCUMENTO │
                    └────────┬─────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │    PENDIENTE     │  ◄──── Estado Inicial
                    └────────┬─────────┘
                             │
                    ┌────────┴─────────┐
                    │                  │
                    ▼                  ▼
            ┌─────────────────┐  ┌──────────────┐
            │   DERIVAR       │  │  RECIBIR     │
            │ (Permanece en   │  │ (por         │
            │  PENDIENTE)     │  │  destinatario)
            └─────────────────┘  └──────┬───────┘
                    │                   │
                    └──────────┬────────┘
                               │
                               ▼
                    ┌──────────────────┐
                    │    RECIBIDO      │
                    └────────┬─────────┘
                             │
                             ▼
                    ┌──────────────────┐
                    │    ATENDER       │
                    │  (por usuario)   │
                    └────────┬─────────┘
                             │
                             ▼
                    ┌──────────────────┐
                    │    ATENDIDO      │
                    └────────┬─────────┘
                             │
                             ▼
                    ┌──────────────────┐
                    │    ARCHIVAR      │
                    │  (por usuario)   │
                    └────────┬─────────┘
                             │
                             ▼
                    ┌──────────────────┐
                    │    ARCHIVADO     │  ◄──── Estado Final
                    └──────────────────┘
```

### Reglas de Transición

| De Estado | A Estado | Acción | Usuario | Condición |
|-----------|----------|--------|---------|-----------|
| — | PENDIENTE | CREAR | Creador | Inicial |
| PENDIENTE | PENDIENTE | DERIVAR | Sistema | Interna |
| PENDIENTE | RECIBIDO | RECIBIR | Destinatario | Recepción |
| RECIBIDO | ATENDIDO | ATENDER | Usuario | Completado |
| ATENDIDO | ARCHIVADO | ARCHIVAR | Usuario | Final |

---

## 🏗️ ARQUITECTURA TÉCNICA

### Componentes Principales

```
┌─────────────────────────────────────────────────────────────┐
│                       APPLICATION LAYER                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Controllers:                                                │
│  ├── EstadoTransicionController    (Transiciones de estado)  │
│  ├── CorrespondenciaController API (Crear documentos)       │
│  └── DerivacionController          (Derivaciones)            │
│                                                               │
│  Models:                                                      │
│  ├── EstadoTransicion              (Historial de cambios)   │
│  ├── Correspondencia               (Documentos)              │
│  ├── EstadoDocumento               (Catálogo de estados)    │
│  └── Derivacion                    (Envíos)                  │
│                                                               │
│  Views:                                                       │
│  ├── components/historial-transiciones.blade.php             │
│  └── components/acciones-transicion.blade.php                │
│                                                               │
├─────────────────────────────────────────────────────────────┤
│                       PERSISTENCE LAYER                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Tables:                                                      │
│  ├── ESTADO_DOCUMENTO              (Estados: 4 filas)       │
│  ├── ESTADO_TRANSICION             (Historial completo)      │
│  ├── CORRESPONDENCIA               (Documentos)              │
│  └── AUDITORIA                     (Auditoría general)       │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 💾 BASE DE DATOS

### Tabla: ESTADO_DOCUMENTO

```sql
CREATE TABLE ESTADO_DOCUMENTO (
    idEstado BIGINT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

-- Datos iniciales:
-- id=1, nombre='Pendiente'
-- id=2, nombre='Recibido'
-- id=3, nombre='Atendido'
-- id=4, nombre='Archivado'
```

### Tabla: ESTADO_TRANSICION (Nueva)

```sql
CREATE TABLE ESTADO_TRANSICION (
    idTransicion BIGINT PRIMARY KEY AUTO_INCREMENT,
    idDocumento BIGINT NOT NULL,
    idUsuario BIGINT NULL,
    idEstadoAnterior BIGINT NULL,
    idEstadoNuevo BIGINT NOT NULL,
    accion ENUM('CREAR', 'DERIVAR', 'RECIBIR', 'ATENDER', 'ARCHIVAR'),
    observacion TEXT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    
    -- Foreign Keys
    FOREIGN KEY (idDocumento) REFERENCES CORRESPONDENCIA(idDocumento) ON DELETE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (idEstadoAnterior) REFERENCES ESTADO_DOCUMENTO(idEstado) ON DELETE SET NULL,
    FOREIGN KEY (idEstadoNuevo) REFERENCES ESTADO_DOCUMENTO(idEstado) ON DELETE RESTRICT,
    
    -- Índices
    INDEX idx_documento (idDocumento),
    INDEX idx_usuario (idUsuario),
    INDEX idx_fecha (fecha),
    INDEX idx_accion (accion)
);
```

### Cambios en Tabla: CORRESPONDENCIA

La tabla `CORRESPONDENCIA` ya tiene el campo `idEstado` que se actualiza en cada transición.

---

## 🔌 API ENDPOINTS

### Base URL
```
POST/GET /api/v1/documentos/{id}/[accion]
```

### Crear Documento
```
POST /api/v1/documentos
Content-Type: application/json
Authorization: Bearer [token]

{
    "cite": "SEG-2026-001",
    "asunto": "Asunto del documento",
    "idTipoDocumento": 1,
    "idEstado": 1,
    "idUrgencia": 1,
    "idRemitente": 1
}

Response 201:
{
    "success": true,
    "message": "Documento creado correctamente",
    "data": {
        "idDocumento": 1,
        "cite": "SEG-2026-001",
        "estado": "Pendiente",
        "fecha": "24/06/2026 14:30:00"
    }
}
```

### Obtener Historial de Transiciones
```
GET /api/v1/documentos/{id}/historial-transiciones
Authorization: Bearer [token]

Response 200:
{
    "success": true,
    "data": [
        {
            "idTransicion": 1,
            "accion": "CREAR",
            "accionLegible": "Documento Creado",
            "estadoAnterior": null,
            "estadoNuevo": "Pendiente",
            "usuario": "Juan Pérez",
            "fecha": "24/06/2026 14:30:00",
            "observacion": "Documento creado en el sistema",
            "resumen": "N/A → Pendiente"
        }
    ]
}
```

### Recibir Documento
```
POST /api/v1/documentos/{id}/recibir
Content-Type: application/json
Authorization: Bearer [token]

{
    "observacion": "Documento recibido correctamente" (opcional)
}

Response 200:
{
    "success": true,
    "message": "Documento marcado como RECIBIDO",
    "data": {
        "idDocumento": 1,
        "estado": "Recibido",
        "fecha": "24/06/2026 15:00:00"
    }
}
```

### Atender Documento
```
POST /api/v1/documentos/{id}/atender
Content-Type: application/json
Authorization: Bearer [token]

{
    "observacion": "Documento atendido" (opcional)
}

Response 200:
{
    "success": true,
    "message": "Documento marcado como ATENDIDO",
    "data": {
        "idDocumento": 1,
        "estado": "Atendido",
        "fecha": "24/06/2026 16:00:00"
    }
}
```

### Archivar Documento
```
POST /api/v1/documentos/{id}/archivar
Content-Type: application/json
Authorization: Bearer [token]

{
    "observacion": "Documento archivado" (opcional)
}

Response 200:
{
    "success": true,
    "message": "Documento marcado como ARCHIVADO",
    "data": {
        "idDocumento": 1,
        "estado": "Archivado",
        "fecha": "24/06/2026 17:00:00"
    }
}
```

### Obtener Transiciones Permitidas
```
GET /api/v1/documentos/{id}/transiciones-permitidas
Authorization: Bearer [token]

Response 200:
{
    "success": true,
    "estadoActual": "Pendiente",
    "transiciones": [
        {
            "nombre": "Recibir",
            "accion": "cambiarARecibido",
            "metodo": "RECIBIR",
            "clase": "btn-success"
        }
    ]
}
```

---

## 📦 MODELOS ELOQUENT

### EstadoTransicion Model

```php
namespace App\Models;

class EstadoTransicion extends Model
{
    protected $table = 'ESTADO_TRANSICION';
    protected $primaryKey = 'idTransicion';
    public $timestamps = false;

    protected $fillable = [
        'idDocumento',
        'idUsuario',
        'idEstadoAnterior',
        'idEstadoNuevo',
        'accion',
        'observacion',
        'fecha',
        'activo',
    ];

    // Relaciones
    public function documento(): BelongsTo
    public function usuario(): BelongsTo
    public function estadoAnterior(): BelongsTo
    public function estadoNuevo(): BelongsTo

    // Accesorios
    public function getAccionLegibleAttribute(): string
    public function getAccionBadgeAttribute(): string
    public function getFechaFormateadaAttribute(): string
    public function getResumenAttribute(): string
}
```

### Correspondencia Model - Nuevos Métodos

```php
class Correspondencia extends Model
{
    // Relación
    public function transiciones()
    public function obtenerHistorialTransiciones()
    public function obtenerUltimaTransicion()

    // Cambios de Estado
    public function cambiarEstado($nuevoEstado, $accion, $observacion = null, $idUsuario = null)
    public function cambiarARecibido($observacion = null, $idUsuario = null): bool
    public function cambiarAAtendido($observacion = null, $idUsuario = null): bool
    public function cambiarAArchivado($observacion = null, $idUsuario = null): bool

    // Validaciones
    public function puedeRecibirse(): bool
    public function puedeAtenderse(): bool
    public function puedeArchivarse(): bool
    public function obtenerTransicionesPermitidas(): array
}
```

---

## 🎮 CONTROLADORES

### EstadoTransicionController

```php
namespace App\Http\Controllers;

class EstadoTransicionController extends Controller
{
    // GET /api/v1/documentos/{id}/historial-transiciones
    public function obtenerHistorial($idDocumento)

    // POST /api/v1/documentos/{id}/recibir
    public function recibir(Request $request, $idDocumento)

    // POST /api/v1/documentos/{id}/atender
    public function atender(Request $request, $idDocumento)

    // POST /api/v1/documentos/{id}/archivar
    public function archivar(Request $request, $idDocumento)

    // GET /api/v1/documentos/{id}/transiciones-permitidas
    public function obtenerTransicionesPermitidas($idDocumento)
}
```

---

## 🎨 VISTAS BLADE

### Componente: historial-transiciones.blade.php

Muestra un timeline visual de todas las transiciones de estado con:
- Icono de acción (crear, derivar, recibir, atender, archivar)
- Transición de estados (anterior → nuevo)
- Usuario que realizó la acción
- Fecha y hora formateada
- Observación si existe

```blade
@include('components.historial-transiciones', ['documento' => $documento])
```

### Componente: acciones-transicion.blade.php

Muestra botones de acciones disponibles con:
- Botones dinámicos según estado actual
- Modales para confirmar acción
- Campo de observación
- Llamadas AJAX a API

```blade
@include('components.acciones-transicion', ['documento' => $documento])
```

---

## 📊 AUDITORÍA Y TRAZABILIDAD

### Dos Niveles de Auditoría

#### 1. Tabla ESTADO_TRANSICION (Específica)
- Registra SOLO cambios de estado
- Captura transiciones válidas
- Incluye usuario, fecha, hora, observación

#### 2. Tabla AUDITORIA (General)
- Registra TODOS los cambios en documentos
- Captura cambios en cualquier campo
- Incluye datos anteriores y nuevos
- Registra automaticamente vía GenericAuditObserver

### Ejemplo de Auditoría Integrada

```
ESTADO_TRANSICION (Específica):
┌──────────────────────────────────────────────┐
│ idTransicion | accion | estadoAnterior | ... │
├──────────────────────────────────────────────┤
│ 1            | CREAR  | NULL           | ... │
│ 2            | RECIBIR| Pendiente      | ... │
│ 3            | ATENDER| Recibido       | ... │
│ 4            | ARCHIVAR|Atendido       | ... │
└──────────────────────────────────────────────┘

AUDITORIA (General):
┌─────────────────────────────────────────────┐
│ idLog | modelo | accion | cambios         │
├─────────────────────────────────────────────┤
│ ...   | CORRESPONDENCIA | UPDATE | {"idEstado": [1, 2]} │
│ ...   | CORRESPONDENCIA | UPDATE | {"idEstado": [2, 3]} │
│ ...   | CORRESPONDENCIA | UPDATE | {"idEstado": [3, 4]} │
└─────────────────────────────────────────────┘
```

---

## 💡 EJEMPLOS DE USO

### JavaScript - Cambiar a Recibido

```javascript
// Recibir documento
const response = await fetch('/api/v1/documentos/1/recibir', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
        observacion: 'Recibido en oficina central'
    })
});

const data = await response.json();
if (data.success) {
    console.log('Documento recibido:', data.data);
}
```

### PHP - Cambiar de Estado Programáticamente

```php
$documento = Correspondencia::find(1);

// Validar si puede recibirse
if ($documento->puedeRecibirse()) {
    $documento->cambiarARecibido('Recepción confirmada');
}

// Obtener historial
$historial = $documento->obtenerHistorialTransiciones();
foreach ($historial as $transicion) {
    echo $transicion->accion_legible;
}

// Obtener transiciones permitidas
$transiciones = $documento->obtenerTransicionesPermitidas();
```

### Blade - Mostrar Componentes

```blade
<!-- En la vista del documento -->

<div class="container mt-4">
    <!-- Acciones disponibles -->
    @include('components.acciones-transicion', ['documento' => $documento])
    
    <!-- Historial de transiciones -->
    @include('components.historial-transiciones', ['documento' => $documento])
</div>
```

---

## 🧪 TESTING

### Tests a Realizar

```php
// Tests de Transición
test('Documento creado en estado Pendiente');
test('Solo puede recibirse desde Pendiente');
test('Solo puede atenderse desde Recibido');
test('Solo puede archivarse desde Atendido');
test('Historial registra todas las transiciones');
test('Usuario se registra en cada transición');
test('Fecha y hora se registran correctamente');

// Tests de Validación
test('puedeRecibirse() retorna true solo si es Pendiente');
test('puedeAtenderse() retorna true solo si es Recibido');
test('puedeArchivarse() retorna true solo si es Atendido');

// Tests de API
test('GET historial-transiciones retorna array de transiciones');
test('POST recibir cambia estado a Recibido');
test('POST atender cambia estado a Atendido');
test('POST archivar cambia estado a Archivado');

// Tests de Auditoría
test('AUDITORIA registra cambios de estado');
test('ESTADO_TRANSICION registra cada transición');
```

---

## 📝 NOTAS FINALES

### Archivos Creados/Modificados

**Creados:**
- `database/migrations/2026_06_24_143000_create_estado_transicion_table.php`
- `app/Models/EstadoTransicion.php`
- `app/Http/Controllers/EstadoTransicionController.php`
- `resources/views/components/historial-transiciones.blade.php`
- `resources/views/components/acciones-transicion.blade.php`

**Modificados:**
- `app/Models/Correspondencia.php` - Agregados métodos de transición
- `routes/api/v1.php` - Agregadas rutas de transiciones
- `app/Http/Controllers/Api/V1/CorrespondenciaController.php` - Registro de transición inicial

### Pendientes (Opcional)

- [ ] Crear seeder para generar datos de prueba
- [ ] Crear tests unitarios e integración
- [ ] Crear migraciones reversibles para rollback
- [ ] Implementar notificaciones por email en transiciones
- [ ] Crear reportes de flujo de documentos
- [ ] Agregar validaciones adicionales con Requests

---

**Arquitecto Funcional:** Sistema de Flujo Completo de Correspondencia  
**Fecha:** 24 de Junio, 2026  
**Versión:** 1.0 - Producción Listo
