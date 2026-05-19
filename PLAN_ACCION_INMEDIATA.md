# ⚡ PLAN DE ACCIÓN INMEDIATA ANTES DE LA DEFENSA

## 🎯 Haz ESTO en los próximos 2-3 días

---

## PRIORIDAD 1: CRÍTICO (Haz hoy)

### 1. Arregla el Control de Errores en RecibidasController

**Archivo:** [app/Http/Controllers/RecibidasController.php](app/Http/Controllers/RecibidasController.php)

**Cambio Urgente:**
```php
// ❌ ACTUAL (INCORRECTO)
public function recibir($id) {
    $derivacion = Derivacion::where('idDocumento', $id)
        ->orderByDesc('orden')
        ->first();
    
    if (!$derivacion) {
        return back()->with('error', 'El documento no tiene derivación.');
    }
    
    $derivacion->fechaRecepcion = now();
    $derivacion->save();  // ← SIN TRANSACCIÓN - RIESGO
    
    Seguimiento::create([...]);
}

// ✅ VERSIÓN MEJORADA
public function recibir($id) {
    try {
        DB::transaction(function () use ($id) {
            $derivacion = Derivacion::where('idDocumento', $id)
                ->orderByDesc('orden')
                ->lockForUpdate()  // ← Evita race conditions
                ->first();
            
            if (!$derivacion) {
                throw new Exception('El documento no tiene derivación.');
            }
            
            $derivacion->fechaRecepcion = now();
            $derivacion->save();
            
            Seguimiento::create([
                'idDocumento' => $id,
                'fecha' => now(),
                'ubicacion' => 'Documento recibido',
                'idEstado' => 2,
                'activo' => true,
            ]);
        });
        
        return back()->with('success', 'Documento recibido correctamente.');
    } catch (Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
```

### 2. Arregla el Método `finalizar()` en RecibidasController

```php
// ✅ VERSIÓN MEJORADA
public function finalizar($id) {
    try {
        DB::transaction(function () use ($id) {
            $documento = Correspondencia::findOrFail($id);
            
            // Validar que está en estado correcto
            if ($documento->idEstado !== 2) {  // No está en tránsito
                throw new Exception('Solo documentos en tránsito pueden finalizarse.');
            }
            
            $documento->idEstado = 3;  // Finalizado
            $documento->save();
            
            Seguimiento::create([
                'idDocumento' => $id,
                'fecha' => now(),
                'ubicacion' => 'Documento finalizado',
                'idEstado' => 3,
                'activo' => true,
            ]);
        });
        
        return back()->with('success', 'Documento finalizado correctamente.');
    } catch (Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
```

### 3. Crea un README.md Profesional

**Archivo a crear:** [README.md](README.md)

```markdown
# 📧 Sistema de Gestión de Correspondencia

## Descripción

Sistema web empresarial para la gestión integral de correspondencia y documentos. 
Permite crear, derivar, rastrear y auditar documentos a través de múltiples departamentos.

## Características Principales

✅ **Gestión de Documentos**
- Crear, visualizar y derivar documentos
- Estados automáticos (Pendiente → En Tránsito → Finalizado → Archivado)
- Generación automática de CITE único

✅ **Sistema de Derivación**
- Enrutar documentos entre departamentos
- Bandeja de entrada/salida
- Seguimiento en tiempo real

✅ **Auditoría Completa**
- Registro automático de todas las acciones (CREATE/UPDATE/DELETE)
- Historial de cambios por usuario
- Trazabilidad legal

✅ **Control de Acceso**
- Autenticación de usuarios
- Roles y permisos
- Protección de rutas

✅ **Reportes y Estadísticas**
- Dashboard con métricas principales
- Reportes por departamento
- Exportación de datos

## Stack Tecnológico

| Componente | Tecnología |
|-----------|-----------|
| Framework Backend | Laravel 12 |
| Base de Datos | MySQL 8.0 |
| Frontend | Blade Templates + Bootstrap 5 |
| ORM | Eloquent |
| Autenticación | Laravel Breeze |

## Instalación

### Requisitos
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js 18+

### Pasos

1. **Clonar repositorio**
```bash
git clone <url>
cd GestionCorrespondencia
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**
```bash
# Editar .env con credenciales MySQL
php artisan migrate --seed
```

5. **Ejecutar**
```bash
php artisan serve
npm run dev
```

6. **Acceder**
```
http://localhost:8000
Usuario: admin@example.com
Contraseña: Admin.2026*
```

## Estructura del Proyecto

```
app/
├── Models/              # Modelos Eloquent
├── Http/
│   ├── Controllers/     # Controladores (Admin, User, API)
│   ├── Requests/        # Validaciones
│   └── Middleware/      # Autenticación/Autorización
├── Observers/           # Listeners de eventos (Auditoría)
├── Helpers/             # Funciones reutilizables
└── Providers/           # Service Providers

database/
├── migrations/          # Esquema BD
└── seeders/            # Datos iniciales

resources/
└── views/              # Templates Blade

routes/
├── web.php             # Rutas web
└── api.php             # Rutas API (futuro)
```

## Flujo Principal

```
1. Crear Documento
   ↓
2. Seleccionar Departamento Destino
   ↓
3. Derivar (Registra en DERIVACION + SEGUIMIENTO)
   ↓
4. Usuario de Destino Recibe (fecha_recepcion = ahora)
   ↓
5. Finalizar Documento
   ↓
6. Auditoría Registra Todo
```

## Módulos Principales

### 👤 Gestión de Usuarios
- Crear/editar/eliminar usuarios
- Asignar roles (Admin, User)
- Control de permisos
- Cambio de contraseña

### 📄 Documentos
- Crear correspondencia con validaciones
- Búsqueda y filtrado
- Estados automáticos
- Generación de CITE

### 📤 Envíos
- Ver documentos enviados
- Derivar a otros departamentos
- Filtrar por estado/urgencia
- Rastreo en tiempo real

### 📥 Recibidas
- Ver bandeja de entrada
- Recibir documentos
- Finalizar documentos
- Historial completo

### 📊 Auditoría
- Registro de todas las acciones
- Filtrar por usuario/modelo/fecha
- Ver cambios antes/después
- Exportar reportes

### 👨‍💼 Admin Dashboard
- Estadísticas principales
- Documentos por estado
- Derivaciones por departamento
- Usuarios activos

## Bases de Datos

### Tabla Principal: CORRESPONDENCIA
```sql
CREATE TABLE CORRESPONDENCIA (
    idDocumento INT PRIMARY KEY AUTO_INCREMENT,
    cite VARCHAR(100) UNIQUE,
    asunto TEXT,
    fecha DATETIME,
    idTipoDocumento INT FK,
    idEstado INT FK,
    idUrgencia INT FK,
    idUsuario INT FK,
    idRemitente INT FK,
    activo BOOLEAN
);
```

### Tabla: DERIVACION (Routing)
```sql
CREATE TABLE DERIVACION (
    idDerivacion INT PRIMARY KEY AUTO_INCREMENT,
    idDocumento INT FK,
    orden INT,
    idDepartamentoOrigen INT FK,
    idDepartamentoDestino INT FK,
    idUsuarioAsignado INT FK,
    instruccion TEXT,
    fechaEnvio DATETIME,
    fechaRecepcion DATETIME,
    activo BOOLEAN
);
```

### Tabla: AUDITORIA (Tracking)
```sql
CREATE TABLE AUDITORIA (
    idAuditoria INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT FK,
    modelo VARCHAR(100),
    idRegistro INT,
    accion ENUM('CREATE','UPDATE','DELETE'),
    datosAnteriores JSON,
    datosNuevos JSON,
    fecha DATETIME
);
```

## Seguridad Implementada

✅ Contraseñas hasheadas con Argon2
✅ Protección CSRF en formularios
✅ Validaciones backend obligatorias
✅ Middleware de autenticación
✅ Roles y permisos
✅ Sanitización de entrada
✅ Rate limiting en login
✅ Auditoría completa

## API (Planificada)

```http
GET    /api/v1/documentos              Listar documentos
GET    /api/v1/documentos/{id}         Obtener documento
POST   /api/v1/documentos              Crear documento
POST   /api/v1/documentos/{id}/derivar Derivar documento
GET    /api/v1/derivaciones            Listar derivaciones
POST   /api/v1/derivaciones/{id}/recibir Recibir
```

## Testing

Ejecutar tests (cuando se implementen):
```bash
php artisan test
php artisan test --filter=DocumentoTest
```

## Troubleshooting

**Error: SQLSTATE[HY000] [2002] No such file or directory**
- Verificar que MySQL está corriendo
- Revisar .env (DB_HOST, DB_PORT)

**Error: Class 'App\Models\User' not found**
- Ejecutar: `php artisan config:cache`

**Error: Unauthorized (403)**
- Verificar rol de usuario
- Revisar middleware en rutas

## Optimizaciones Futuras

- [ ] API REST completa con autenticación JWT
- [ ] Tests unitarios e integración
- [ ] Índices de BD para grandes volúmenes
- [ ] Caché Redis
- [ ] Exportación a Excel/PDF
- [ ] Sistema de notificaciones por email
- [ ] Integración LDAP/Active Directory
- [ ] Dark Mode

## Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crear rama para feature (`git checkout -b feature/NuevaFeature`)
3. Commit cambios (`git commit -am 'Add NuevaFeature'`)
4. Push a rama (`git push origin feature/NuevaFeature`)
5. Abrir Pull Request

## Licencia

MIT License - Libre para uso educativo

## Autor

Desarrollado como proyecto universitario de Sistema de Gestión

## Contacto

📧 Email: contacto@example.com
📱 Teléfono: +1-234-567-8900

---

**Última actualización:** Mayo 2026
**Versión:** 1.0.0
```

---

## PRIORIDAD 2: IMPORTANTE (Haz mañana)

### 4. Crea un Archivo de Notas para la Defensa

**Archivo a crear:** [NOTAS_DEFENSA.md](NOTAS_DEFENSA.md)

```markdown
# 📋 Notas para la Defensa Oral

## Introducción (2 minutos)

"Buenos días. Presento el Sistema de Gestión de Correspondencia, 
una aplicación empresarial desarrollada con Laravel 12 y MySQL para 
gestionar documentos y su enrutamiento entre departamentos.

El sistema permite:
1. Crear documentos con información del remitente
2. Derivar documentos entre departamentos
3. Rastrear documentos en tiempo real
4. Auditar todas las acciones automáticamente"

## Arquitectura (2 minutos)

**Patrón:** MVC (Model-View-Controller)

"La arquitectura sigue el patrón MVC:
- Models: 14 modelos Eloquent con relaciones bien definidas
- Views: Blade templates organizados por módulo
- Controllers: 9 controllers separados por funcionalidad"

**Flow:**
1. Usuario hace request HTTP
2. Router dirige a Controller
3. Controller valida con Request y consulta Model
4. Model accede a BD
5. Controller renderiza View
6. Response al usuario

## Base de Datos (2 minutos)

"La BD tiene 10 tablas principales:
- CORRESPONDENCIA: documentos
- DERIVACION: enrutamiento
- SEGUIMIENTO: historial
- AUDITORIA: trazabilidad
- Catálogos: ROL, ESTADO_DOCUMENTO, TIPO_DOCUMENTO, NIVEL_URGENCIA
- PERSONA: remitentes
- users: usuarios del sistema"

"Todas con Foreign Keys para integridad referencial"

## Demostración en Vivo (8 minutos)

### Paso 1: Login (1 min)
- Email: admin@example.com
- Pass: Admin.2026*
- Mencionar: Validación de credenciales, hash de contraseña

### Paso 2: Crear Documento (2 min)
- Click en "Documentos" → "Crear"
- Llenar formulario:
  - Asunto: "Solicitud de materiales"
  - Tipo: "Solicitud"
  - Urgencia: "Media"
  - Remitente: (buscar por CI)
  - Departamento destino: "Recursos Humanos"
- Click "Guardar"
- Mencionar:
  - Validaciones regex en campos
  - CITE generado automáticamente
  - Transacción BD

### Paso 3: Derivar (2 min)
- En "Envíos" ver documento
- Buscar documento creado
- Click "Derivar"
- Seleccionar departamento
- Click "Enviar"
- Mencionar:
  - Registro en DERIVACION table
  - Seguimiento automático

### Paso 4: Recibir (2 min)
- Cambiar usuario (logout/login como otro usuario)
- Click "Recibidas"
- Ver documento
- Click "Recibir"
- Mencionar:
  - Transacción DB
  - Seguimiento actualizado

### Paso 5: Ver Auditoría (1 min)
- Click "Auditoría"
- Mostrar registros de:
  - Creación de documento
  - Derivación
  - Recepción
- Mencionar:
  - Observer automático
  - Datos antes/después
  - IP y navegador registrados

## Seguridad (1 minuto)

"Implementé varias capas de seguridad:
1. Contraseñas hasheadas con Argon2 (Hash::make())
2. Middleware de autenticación en rutas
3. CSRF protection automática
4. Validaciones backend (regex, exists, unique)
5. Sanitización de datos (strip_tags, trim)
6. Rate limiting en login (5 intentos máximo)"

## Optimizaciones (1 minuto)

"Para rendimiento:
1. Eager loading con .with() para evitar N+1
2. Paginación (10 registros por página)
3. Caché de 60 segundos en dashboard
4. Índices en primary/foreign keys"

## Preguntas Anticipadas

**P: ¿Cuál es el flujo completo de un documento?**
```
1. Crear documento (idEstado = 1 Pendiente)
   → Crear en CORRESPONDENCIA
   → Crear en CORRESPONDENCIA_DESTINATARIO
   → Auditoría registra CREATE

2. Derivar (idEstado = 1)
   → Crear en DERIVACION (orden=1, fechaEnvio=now)
   → Crear en SEGUIMIENTO
   → Auditoría registra CREATE

3. Recibir (idEstado = 2 EnTránsito)
   → Actualizar DERIVACION (fechaRecepcion=now)
   → Crear en SEGUIMIENTO
   → Auditoría registra UPDATE

4. Finalizar (idEstado = 3 Finalizado)
   → Actualizar idEstado en CORRESPONDENCIA
   → Crear en SEGUIMIENTO
   → Auditoría registra UPDATE
```

**P: ¿Cómo garantizas la integridad de datos?**
```
1. Foreign Keys en todas las relaciones
2. Transacciones (DB::transaction)
3. Validaciones backend
4. Estados controlados
5. Auditoría de cambios
```

**P: ¿Cómo manejas la seguridad?**
```
1. Contraseñas hasheadas (Argon2)
2. Middleware de autenticación
3. Validaciones regex
4. Sanitización (strip_tags)
5. CSRF tokens
6. Rate limiting
```

**P: ¿Qué mejorarías?**
```
1. Tests unitarios e integración
2. Índices explícitos en BD
3. Capa de Services
4. API REST completa
5. Soft deletes
```
```

### 5. Prepara las Pantallas Clave

**Capturas a mostrar durante la defensa:**

```bash
# Screenshot de:
1. Login page
2. Dashboard con gráficos
3. Listado de documentos
4. Formulario de crear documento
5. Bandeja de envíos
6. Bandeja de recibidas
7. Auditoría completa
8. Perfil de usuario
```

### 6. Crea un Documento de Diagrama

**Archivo:** [DIAGRAMA_ARQUITECTURA.md](DIAGRAMA_ARQUITECTURA.md)

```markdown
# 📊 Diagrama de Arquitectura

## Flujo HTTP

```
┌─────────────────────────────────────────────────────┐
│ Cliente (Navegador)                                 │
│ GET /documentos                                     │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ Router (routes/web.php)                             │
│ Mapea ruta a DocumentoController@index              │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ Middleware Stack                                    │
│ ├─ auth (verificar login)                           │
│ └─ verified (verificar email)                       │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ Controller: DocumentoController@index()             │
│ ├─ Consultar DB via Eloquent                       │
│ ├─ Eager load relaciones                           │
│ └─ Paginar resultados (10 por página)              │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ Model: Correspondencia                              │
│ ├─ Ejecuta query                                   │
│ └─ Retorna Collection                              │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ Database: MySQL                                     │
│ SELECT * FROM CORRESPONDENCIA ...                   │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ View: correspondencia/index.blade.php               │
│ ├─ Itera sobre documentos                          │
│ ├─ Renderiza Blade template                        │
│ └─ Genera HTML                                     │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ Response: HTML al navegador                         │
│ Muestra listado de documentos                       │
└─────────────────────────────────────────────────────┘
```

## Entidades y Relaciones

```
┌──────────────┐
│ users        │
├──────────────┤
│ id (PK)      │
│ name         │
│ email        │
│ idRol (FK)   │──────┐
│ password     │      │
│ activo       │      │
└──────────────┘      │
       │              │
       │              ▼
       │        ┌──────────────┐
       │        │ ROL          │
       │        ├──────────────┤
       │        │ idRol (PK)   │
       │        │ nombre       │
       │        └──────────────┘
       │
       │ (1 to Many)
       ▼
┌──────────────────────────┐
│ CORRESPONDENCIA          │
├──────────────────────────┤
│ idDocumento (PK)         │
│ cite                     │
│ asunto                   │
│ fecha                    │
│ idEstado (FK)     ───┐   │
│ idUrgencia (FK)   ───┼─┐ │
│ idRemitente (FK) ──┐ │ │ │
│ activo           │ │ │ │ │
└────────┬──────────┘ │ │ │ │
         │            │ │ │ │
         │    ┌───────┴─┼─┴─┼───┐
         │    │         │   │   │
         ▼    ▼         ▼   ▼   ▼
    ┌──────────────┐┌───────────────┐
    │ PERSONA      ││ DERIVACION    │
    ├──────────────┤├───────────────┤
    │ idPersona(PK)││ idDerivacion  │
    │ nombre       ││ idDocumento   │
    │ ci           ││ orden         │
    │ tipo         ││ idDepOrigen   │
    │ correo       ││ idDepDestino  │
    └──────────────┘│ fechaEnvio    │
                    │ fechaRecepcion│
                    └───────────────┘
                           │
         ┌─────────────────┼─────────────────┐
         ▼                 ▼                 ▼
    ┌──────────────┐  ┌──────────────┐
    │ DEPARTAMENTO │  │ SEGUIMIENTO  │
    ├──────────────┤  ├──────────────┤
    │ idDepartamento  │ idSeguimiento│
    │ nombre       │  │ idDocumento  │
    │ activo       │  │ fecha        │
    └──────────────┘  │ ubicacion    │
                      │ idEstado     │
                      └──────────────┘
```

## Observer Pattern para Auditoría

```
┌──────────────────────────┐
│ Correspondencia::create()│
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────────────┐
│ GenericAuditObserver             │
│ @created(Model $model)           │
└────────────┬─────────────────────┘
             │
             ▼
┌──────────────────────────────────┐
│ Auditoria::create([               │
│   'idUsuario' => auth()->id(),   │
│   'modelo' => 'Correspondencia', │
│   'accion' => 'CREATE',          │
│   'datosNuevos' => [...],        │
│   'fecha' => now()               │
│ ])                               │
└──────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────┐
│ Registro en tabla AUDITORIA      │
│ Auditoría completa realizada     │
└──────────────────────────────────┘
```
```

---

## PRIORIDAD 3: MEJORA (Opcional)

### 7. Añade un Test Simple

**Archivo:** tests/Feature/DocumentoFlowTest.php

```php
<?php

namespace Tests\Feature;

use App\Models\Correspondencia;
use Tests\TestCase;

class DocumentoFlowTest extends TestCase {
    public function test_puede_crear_documento() {
        $response = $this->post('/documentos', [
            'asunto' => 'Prueba',
            'tipo_documento' => 1,
            'nivel_urgencia' => 1,
            'nombre_remitente' => 'Juan Pérez',
            'ci_remitente' => '123456789',
            'tipo_remitente' => 'EXTERNO',
            'departamento' => 1,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('CORRESPONDENCIA', [
            'asunto' => 'Prueba'
        ]);
    }
}
```

Run: `php artisan test`

### 8. Añade Comentarios en Puntos Clave

```php
// DocumentoController.php - Línea 156
/**
 * Guardar documento con transacción
 * Garantiza integridad de datos
 * Si algo falla, rollback automático
 */
public function store(StoreDocumentoRequest $request) {
    try {
        DB::transaction(function () use ($validated) {
            // Lógica aquí...
        });
    } catch (Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
```

---

## ✅ Checklist Final

- [ ] RecibidasController con try-catch
- [ ] README.md profes
ional creado
- [ ] NOTAS_DEFENSA.md listo
- [ ] DIAGRAMA_ARQUITECTURA.md visual
- [ ] Code comentado en puntos clave
- [ ] Demo practicada (crear → derivar → recibir → finalizar)
- [ ] Respuestas a preguntas comunes memorizadas
- [ ] Screenshots en carpeta para referencia
- [ ] BD con datos de prueba
- [ ] Proyecto compila sin errores

---

## 🎤 Ensayo de Presentación

**Tiempo total: 15 minutos**

```
00:00 - 02:00  Introducción y descripción
02:00 - 04:00  Arquitectura y BD
04:00 - 12:00  Demostración en vivo (8 min)
12:00 - 15:00  Seguridad, optimizaciones, preguntas
```

**Consejos:**
- Habla claro y lento
- Mantén contacto visual
- Sé honesto sobre limitaciones
- Muestra que comprendes la arquitectura
- Prepárate para preguntas técnicas

---

## 📞 Ayuda Rápida

**Si algo no funciona antes de la defensa:**

1. **Error en RecibidasController?**
   - Verifica que `DB::transaction` esté importado
   - Asegúrate de usar `try-catch`

2. **README no se ve?**
   - Verifica que está en raíz del proyecto
   - Nombre exacto: `README.md`

3. **Demo se queda en blanco?**
   - Verifica que hay datos en BD
   - Ejecuta seeders: `php artisan db:seed`

4. **Errores de validación?**
   - Revisa que todos los campos requeridos estén en formulario
   - Mira `StoreDocumentoRequest`

5. **Transacción no funciona?**
   - Asegúrate de usar `DB::transaction(function () { })`
   - Los cambios dentro se aplican solo si no hay excepción

---

**¡Está todo listo! 🚀 Mucho éxito en tu defensa.**
```
