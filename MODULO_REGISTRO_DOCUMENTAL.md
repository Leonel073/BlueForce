# 📄 Módulo de Registro Documental - BlueForce

## Descripción General
Módulo completo para el registro de documentos (correspondencia) en la Escuela de Posgrado de la Armada Boliviana. Permite registrar documentos con remitentes y múltiples destinatarios de forma intuitiva y segura.

## 🎯 Ubicación en la Aplicación
- **Ruta Web:** `http://localhost:8000/documentos`
- **Enlace en Sidebar:** "Documentos" → icono `📄`

## 📋 Funcionalidades Implementadas

### 1️⃣ Formulario de Registro Documental
El formulario se divide en **3 secciones principales:**

#### **SECCIÓN 1: DATOS DEL DOCUMENTO**
- **CITE** (Requerido): Código de identificación única del documento
- **Asunto** (Requerido): Descripción detallada del contenido
- **Tipo de Documento** (Requerido): Selección de catálogo predefinido
  - Memorándum, Oficio, Resolución, etc.
- **Nivel de Urgencia** (Requerido): Clasificación de prioridad
  - Normal, Urgente, Muy Urgente, etc.
- **Estado**: Automáticamente se asigna "Recibido" (no editable)

#### **SECCIÓN 2: REMITENTE (PERSONA)**
- **Nombre** (Requerido): Nombre completo del remitente
- **Correo** (Opcional): Email del remitente
- **Cargo** (Opcional): Puesto u ocupación
- **Institución** (Opcional): Entidad de procedencia
- **Tipo** (Requerido): INTERNO o EXTERNO
  - INTERNO: Dentro de la institución
  - EXTERNO: Fuera de la institución

**Nota importante:** El remitente se crea automáticamente en la BD, no se selecciona de un listado existente.

#### **SECCIÓN 3: DESTINATARIOS**
- **Selección Múltiple de Departamentos** (Requerido): Checkboxes con todos los departamentos
  - Ventanilla de Recepción
  - Dirección General
  - Secretaría
  - Departamento Administrativo
  - Departamento Financiero
  - Recursos Humanos
- Contador en tiempo real de departamentos seleccionados

---

## ⚙️ Arquitectura Técnica

### Modelos Eloquent Creados
```
app/Models/
├── Persona.php                      # Remitentes y destinatarios
├── Correspondencia.php              # Documento principal
├── CorrespondenciaDestinatario.php  # Relación documento-departamento
├── TipoDocumento.php                # Catálogo
├── EstadoDocumento.php              # Catálogo
├── NivelUrgencia.php                # Catálogo
└── Departamento.php                 # Catálogo
```

### Controlador
```
app/Http/Controllers/
└── DocumentoController.php
    ├── show()     → GET /documentos (muestra formulario)
    └── store()    → POST /documentos (guarda documento)
```

### Rutas
```php
// GET: Mostrar formulario
Route::get('/documentos', [DocumentoController::class, 'show'])->name('documentos.show');

// POST: Guardar documento
Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
```

### Vista Blade
```
resources/views/user/
└── documento-registro.blade.php
```

---

## 🔄 Flujo de Guardado de Datos

```
1. Usuario completa el formulario
        ↓
2. Validación en Cliente (Bootstrap 5)
        ↓
3. Envío POST a /documentos
        ↓
4. Validación en Servidor (Laravel Validation)
        ↓
5. TRANSACCIÓN DB::transaction() {
        ├─ 1️⃣ CREATE PERSONA (remitente automático)
        ├─ 2️⃣ CREATE CORRESPONDENCIA (documento)
        └─ 3️⃣ CREATE CORRESPONDENCIA_DESTINATARIO (múltiples registros)
   }
        ↓
6. Mensaje de Éxito → Redirect a /documentos
```

---

## ✅ Validaciones Implementadas

### Cliente (JavaScript Bootstrap 5)
- Validación nativa HTML5 con feedback visual

### Servidor (Laravel Validation)

```php
'cite' => 'required|string|max:100',
'asunto' => 'required|string|max:500',
'tipo_documento' => 'required|exists:TIPO_DOCUMENTO,idTipoDocumento',
'nivel_urgencia' => 'required|exists:NIVEL_URGENCIA,idUrgencia',
'nombre_remitente' => 'required|string|max:200',
'correo_remitente' => 'nullable|email|max:150',
'cargo_remitente' => 'nullable|string|max:150',
'institucion_remitente' => 'nullable|string|max:200',
'tipo_remitente' => 'required|in:INTERNO,EXTERNO',
'departamentos' => 'required|array|min:1',
'departamentos.*' => 'exists:DEPARTAMENTO,idDepartamento',
```

---

## 🎨 Diseño y Paleta de Colores

El módulo respeta la paleta de colores corporativa:

- **Gradiente Sidebar/Headers:** `#0d1b2a` → `#1b263b` (Azul Oscuro)
- **Accento:** `#ffc107` (Amarillo Dorado)
- **Fondos:** `#f5f7fb` (Gris Claro)
- **Bordes:** 4px `#ffc107` en cards

### Componentes Visuales
- ✅ Tarjetas con sombra profesional
- ✅ Iconos Bootstrap Icons integrados
- ✅ Responsive (Desktop, Tablet, Mobile)
- ✅ Formulario de dos columnas en desktop (1 columna en móvil)
- ✅ Alertas contextuales (éxito, error, validación)

---

## 📡 Tablas de Base de Datos

### PERSONA
```sql
CREATE TABLE PERSONA (
    idPersona BIGINT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200),
    correo VARCHAR(150) NULLABLE,
    cargo VARCHAR(150) NULLABLE,
    institucion VARCHAR(200) NULLABLE,
    tipo ENUM('INTERNO', 'EXTERNO'),
    activo BOOLEAN DEFAULT TRUE
);
```

### CORRESPONDENCIA
```sql
CREATE TABLE CORRESPONDENCIA (
    idDocumento BIGINT PRIMARY KEY AUTO_INCREMENT,
    cite VARCHAR(100),
    asunto TEXT,
    fecha DATETIME CURRENT_TIMESTAMP,
    idTipoDocumento BIGINT FK → TIPO_DOCUMENTO.idTipoDocumento,
    idEstado BIGINT FK → ESTADO_DOCUMENTO.idEstado,
    idUrgencia BIGINT FK → NIVEL_URGENCIA.idUrgencia,
    idRemitente BIGINT FK → PERSONA.idPersona,
    activo BOOLEAN DEFAULT TRUE
);
```

### CORRESPONDENCIA_DESTINATARIO
```sql
CREATE TABLE CORRESPONDENCIA_DESTINATARIO (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    idDocumento BIGINT FK → CORRESPONDENCIA.idDocumento,
    idPersona BIGINT FK → PERSONA.idPersona,
    activo BOOLEAN DEFAULT TRUE
);
```

---

## 🚀 Cómo Usar

### 1. Acceder al Módulo
```
http://localhost:8000/documentos
```

### 2. Completar el Formulario

**Ejemplo práctico:**

| Campo | Valor |
|-------|-------|
| CITE | SGPA-2026-05-001 |
| Asunto | Solicitud de revisión de proyecto de investigación |
| Tipo Documento | Memorándum |
| Nivel Urgencia | Urgente |
| Nombre Remitente | Dr. Juan Pérez |
| Correo | juan@ejemplo.com |
| Cargo | Director de Investigación |
| Institución | Universidad Nacional |
| Tipo Remitente | EXTERNO |
| Departamentos | ✓ Dirección General, ✓ Secretaría |

### 3. Enviar Formulario
- Click en "Guardar Documento" (azul oscuro)
- El sistema crea automáticamente:
  - 1 Registro PERSONA (remitente)
  - 1 Registro CORRESPONDENCIA (documento)
  - 2 Registros CORRESPONDENCIA_DESTINATARIO (uno por departamento)

### 4. Confirmación
- Mensaje de éxito: ✅ "Documento registrado correctamente"
- Redirección a formulario limpio para nuevo registro

---

## 🔒 Transacciones y Seguridad

El sistema usa **DB::transaction()** para garantizar:
- ✅ Atomicidad: Todo se guarda o nada se guarda
- ✅ Consistencia: Validación en servidor
- ✅ Aislamiento: Ningún dato parcial en BD
- ✅ Durabilidad: Una vez guardado, persiste

**Manejo de Errores:**
```php
try {
    DB::transaction(function () { ... });
    return redirect()->with('success', 'Documento registrado');
} catch (\Exception $e) {
    return redirect()->back()
        ->withInput()
        ->with('error', 'Error: ' . $e->getMessage());
}
```

---

## 📝 Notas Importantes

1. **Creación Automática de Remitente:** No es necesario tener la persona registrada previamente
2. **Estado Predeterminado:** Siempre se asigna "Recibido"
3. **Múltiples Destinatarios:** Se pueden seleccionar varios departamentos
4. **Modularidad:** Este módulo NO afecta otros módulos (Enviadas, Recibidas)
5. **Validaciones Dobles:** Cliente y servidor para máxima seguridad

---

## 🧪 Testing Manual

### Caso 1: Registro Exitoso
1. Llenar todos los campos obligatorios
2. Seleccionar al menos 1 departamento
3. Click "Guardar Documento"
4. ✅ Debe mostrar mensaje de éxito y limpiar formulario

### Caso 2: Validación - Campos Vacíos
1. Dejar campos obligatorios en blanco
2. Click "Guardar Documento"
3. ✅ Deben aparecer mensajes de error individuales

### Caso 3: Validación - Sin Departamentos
1. Llenar datos del documento y remitente
2. NO seleccionar ningún departamento
3. Click "Guardar Documento"
4. ✅ Debe mostrar: "Debe seleccionar al menos un departamento"

### Caso 4: Email Inválido
1. Completar formulario
2. En correo_remitente: escribir "algo@"
3. Click "Guardar Documento"
4. ✅ Debe mostrar: "El correo debe ser válido"

---

## 📚 Archivos Creados/Modificados

```
CREADOS:
✅ app/Models/Persona.php
✅ app/Models/Correspondencia.php
✅ app/Models/CorrespondenciaDestinatario.php
✅ app/Models/TipoDocumento.php
✅ app/Models/EstadoDocumento.php
✅ app/Models/NivelUrgencia.php
✅ app/Models/Departamento.php
✅ app/Http/Controllers/DocumentoController.php
✅ resources/views/user/documento-registro.blade.php

MODIFICADOS:
✅ routes/web.php (añadidas rutas documentos.show y documentos.store)
✅ app/View/Components/Sidebar.php (actualizado nombre ruta)
```

---

## 🎓 Nivel de Complejidad
- **Principiante:** ✅ Código claro y bien comentado
- **Modular:** ✅ No afecta otros módulos
- **Escalable:** ✅ Fácil de extender con más funcionalidades

---

## 📞 Contacto / Soporte
Módulo desarrollado como parte del sistema BlueForce - Escuela de Posgrado Armada Boliviana.

**Versión:** 1.0  
**Fecha:** 2026-05-05  
**Estado:** ✅ Completo y Funcional
