# SISTEMA DE GESTIÓN DE CORRESPONDENCIA - ARCHITECTURAL OVERVIEW

## 📋 Table of Contents
1. [System Purpose](#system-purpose)
2. [Route Definitions](#route-definitions)
3. [Controllers & Methods](#controllers--methods)
4. [Database Models & Relationships](#database-models--relationships)
5. [Database Migrations & Schema](#database-migrations--schema)
6. [Middleware Implementations](#middleware-implementations)
7. [Observers & Audit System](#observers--audit-system)
8. [Helpers & Services](#helpers--services)
9. [View Structure](#view-structure)
10. [Architecture Patterns](#architecture-patterns)

---

## System Purpose

**Gestion de Correspondencia** is a Laravel 12-based document management and correspondence tracking system designed for institutional/governmental settings. It enables organizations to:

- **Create & Register** incoming and outgoing correspondence documents
- **Route & Distribute** documents across departments and users
- **Track Document Flow** through a derivation (routing) system
- **Audit Changes** with comprehensive logging of all system modifications
- **Manage Users & Departments** with role-based access control
- **Report & Analyze** correspondence patterns and system activity

### Key Business Capabilities:
- Document lifecycle management (Draft → Pending → Finalized/Archived)
- Multi-level department-based routing
- Real-time document status tracking
- Complete audit trail of all operations
- Role-based dashboard views (Admin, User, Dashboard)

---

## Route Definitions

### 1. **Public Routes** (`routes/web.php`)

```
GET  /                    → Welcome page
GET  /page                → Auth page (name: 'page')
```

### 2. **Authentication Routes** (`routes/auth.php` - Breeze)

```
// Guest Middleware
GET  /register                      → RegisteredUserController@create
POST /register                      → RegisteredUserController@store
GET  /login                         → AuthenticatedSessionController@create
POST /login                         → AuthenticatedSessionController@store
GET  /forgot-password               → PasswordResetLinkController@create
POST /forgot-password               → PasswordResetLinkController@store
GET  /reset-password/{token}        → NewPasswordController@create
POST /reset-password                → NewPasswordController@store

// Auth Middleware
GET  /verify-email                  → EmailVerificationPromptController
GET  /verify-email/{id}/{hash}      → VerifyEmailController
POST /email/verification-notification → EmailVerificationNotificationController@store
GET  /confirm-password              → ConfirmablePasswordController@show
```

### 3. **User Profile Routes** (`routes/web.php`)

```
Middleware: auth

GET    /profile             → ProfileController@edit      (name: 'profile.edit')
PATCH  /profile             → ProfileController@update    (name: 'profile.update')
DELETE /profile             → ProfileController@destroy   (name: 'profile.destroy')
```

### 4. **Admin Routes** (Prefix: `/admin`, Middleware: `auth`)

```
GET /admin/dashboard                           → DashboardController@index
                                                 (name: 'admin.index')
GET /admin/api/dashboard-estadisticas          → DashboardController@estadisticasDashboard
                                                 (name: 'admin.api.estadisticas.dashboard')
GET /admin/api/dashboard-departamentos         → DashboardController@estadisticasDepartamentos
                                                 (name: 'admin.api.estadisticas.departamentos')
```

### 5. **User Dashboard Routes** (Middleware: `auth, verified`)

```
GET /user/dashboard                            → UserDashboardController@index
                                                 (name: 'user.dashboard')
```

### 6. **Document Management Routes** (Prefix: none, Middleware: `auth, verified`)

#### Documents (Documentos)
```
GET  /documentos                               → DocumentoController@index
                                                 (name: 'documentos.index')
GET  /documentos/crear                         → DocumentoController@show
                                                 (name: 'documentos.crear')
POST /documentos                               → DocumentoController@store
                                                 (name: 'documentos.store')
GET  /documentos/{id}                          → DocumentoController@detalle
                                                 (name: 'documentos.detalle')
```

#### Person Search (Búsqueda de Personas)
```
GET /persona/buscar/{ci}                       → DocumentoController@buscarPersona
                                                 (name: 'persona.buscar')
GET /documentos/departamento/{idDepartamento}/personas
                                                 → DocumentoController@obtenerPersonasPorDepartamento
                                                 (name: 'documentos.departamento.personas')
```

#### Correspondence (Correspondencia General)
```
GET /correspondencia                           → CorrespondenciaController@index
                                                 (name: 'correspondencia.index')
GET /correspondencia/{id}                      → CorrespondenciaController@show
                                                 (name: 'correspondencia.show')
```

#### Sent/Routing (Envíos)
```
GET  /envios                                   → EnvioController@index
                                                 (name: 'envios.index')
GET  /mi-bandeja                               → EnvioController@bandeja
                                                 (name: 'envios.bandeja')
GET  /envios/{id}/derivar                      → EnvioController@derivarForm
                                                 (name: 'envios.derivar.form')
POST /envios/{id}/derivar                      → EnvioController@derivar
                                                 (name: 'envios.derivar')
PUT  /envios/{id}/finalizar                    → EnvioController@finalizar
                                                 (name: 'envios.finalizar')
```

#### Received (Recibidas)
```
GET  /recibidas                                → RecibidasController@index
                                                 (name: 'recibidas.index')
POST /recibidas/{id}/recibir                   → RecibidasController@recibir
                                                 (name: 'recibidas.recibir')
POST /recibidas/{id}/finalizar                 → RecibidasController@finalizar
                                                 (name: 'recibidas.finalizar')
```

#### Configuration (Configuración)
```
GET /user/configuracion                        → Returns view('user.configuracion')
                                                 (name: 'user.configuracion')
```

---

## Controllers & Methods

### 1. **DocumentoController** (`app/Http/Controllers/DocumentoController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `index()` | List all documents with statistics (pending, finalized, urgent) | View with pagination |
| `show()` | Display document creation form | Form view |
| `store()` | Save new document to database | Redirect/Response |
| `detalle($id)` | Show document details | Document detail view |
| `buscarPersona($ci)` | Search person by ID card (CI) | JSON response |
| `obtenerPersonasPorDepartamento($idDepartamento)` | Fetch persons in a department | JSON array |

**Key Logic:**
- Eager loads relationships: tipoDocumento, estado, urgencia, remitente, derivaciones
- Counts documents by status (Pending, Finalized, Urgent)
- Uses request validation through `StoreDocumentoRequest`

---

### 2. **EnvioController** (`app/Http/Controllers/EnvioController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `index()` | List sent documents with filtering | Paginated derivations |
| `bandeja()` | Show user's inbox (received items) | Inbox view |
| `derivarForm($id)` | Show routing/derivation form | Form view |
| `derivar($id)` | Process document routing | Redirect/Response |
| `finalizar($id)` | Mark document as finalized | Response |

**Key Logic:**
- Filters by: buscar (search), departamento destino/origen, transito status, estado, urgencia
- Tracks derivation dates: fechaEnvio, fechaRecepcion
- Relationships: documento, departamentos (origen/destino), usuarios

---

### 3. **CorrespondenciaController** (`app/Http/Controllers/CorrespondenciaController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `index()` | List all correspondence with filters | Paginated list |
| `show($id)` | View correspondence details | Detail view |

**Key Logic:**
- Filters: buscar (cite/asunto), estado, urgencia, departamento
- Includes related data: tipoDocumento, estado, urgencia, remitente, derivaciones
- User-aware queries

---

### 4. **RecibidasController** (`app/Http/Controllers/RecibidasController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `index()` | List received correspondence | List view |
| `recibir($id)` | Mark document as received (fechaRecepcion) | Response |
| `finalizar($id)` | Complete received document | Response |

---

### 5. **UserDashboardController** (`app/Http/Controllers/UserDashboardController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `index()` | User dashboard with statistics | Dashboard view |

---

### 6. **ProfileController** (`app/Http/Controllers/ProfileController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `edit()` | Show profile edit form | Form view |
| `update()` | Update user profile | Redirect |
| `destroy()` | Delete user account | Redirect |

---

### 7. **AuditoriaController** (`app/Http/Controllers/AuditoriaController.php`)

| Method | Purpose | Returns |
|--------|---------|---------|
| `index()` | List audit logs with advanced filtering | Paginated audit trail |
| `show($idAuditoria)` | View audit record details | Audit detail view |

**Filters:** idUsuario, accion, modelo, fechaInicio, fechaFin, busqueda (IP/navegador/ruta)

---

### 8. **Admin Controllers** (`app/Http/Controllers/Admin/`)

#### DashboardController
```
index()                         → Admin dashboard with statistics
estadisticasDashboard()         → API endpoint for dashboard stats (cached 60s)
estadisticasDepartamentos()     → API endpoint for department stats (cached 60s)
```

**Cache Keys:**
- `dashboard.contadores` - Total docs, users, derivations, departments
- `dashboard.estados` - Documents by state
- `dashboard.departamentos` - Stats by department

**State IDs:**
- `ESTADO_FINALIZADO = 3`
- `ESTADO_ARCHIVADO = 4`

#### UsuarioController
- User management (CRUD)

#### DepartamentoController
- Department management (CRUD)

#### PersonaController
- Person/contact management (CRUD)

#### ReporteController
- Report generation

---

## Database Models & Relationships

### 1. **User Model**

```php
protected $fillable = ['name', 'email', 'password', 'idPersona', 'idRol', 'activo']
```

**Relationships:**
- `correspondencias()` - hasMany(Correspondencia, 'idUsuario', 'id')
- `persona()` - belongsTo(Persona, 'idPersona', 'idPersona')
- `rol()` - belongsTo(Rol, 'idRol', 'idRol')

---

### 2. **Correspondencia Model** (Main Document Entity)

```php
protected $table = 'CORRESPONDENCIA'
protected $primaryKey = 'idDocumento'
protected $fillable = [
    'cite',              // Reference code
    'asunto',            // Subject
    'fecha',             // Creation date
    'idTipoDocumento',   // Document type
    'idEstado',          // Document state
    'idUrgencia',        // Urgency level
    'idUsuario',         // Creator/Owner
    'idRemitente',       // Sender (Person)
    'activo'             // Active flag
]
```

**Relationships:**
- `tipoDocumento()` - belongsTo(TipoDocumento)
- `estado()` - belongsTo(EstadoDocumento)
- `urgencia()` - belongsTo(NivelUrgencia)
- `remitente()` - belongsTo(Persona)
- `derivaciones()` - hasMany(Derivacion)
- `destinatarios()` - hasMany(CorrespondenciaDestinatario)
- `seguimiento()` - hasMany(Seguimiento)

---

### 3. **Derivacion Model** (Routing/Transfer Records)

```php
protected $table = 'DERIVACION'
protected $primaryKey = 'idDerivacion'
protected $fillable = [
    'idDocumento',              // Reference to document
    'orden',                    // Routing order
    'idDepartamentoOrigen',     // Sending department
    'idDepartamentoDestino',    // Receiving department
    'idUsuarioAsignado',        // Assigned user
    'idUsuarioEnvio',           // Sender user
    'instruccion',              // Instructions
    'fechaEnvio',               // Send date
    'fechaRecepcion',           // Receipt date (nullable)
    'activo'
]
```

**Relationships:**
- `documento()` - belongsTo(Correspondencia)
- `departamentoOrigen()` - belongsTo(Departamento)
- `departamentoDestino()` - belongsTo(Departamento)
- `usuarioAsignado()` - belongsTo(User)
- `usuarioEnvio()` - belongsTo(User)

---

### 4. **Departamento Model**

```php
protected $table = 'DEPARTAMENTO'
protected $primaryKey = 'idDepartamento'
protected $fillable = ['nombre', 'idPersonaEncargada', 'activo']
```

**Relationships:**
- `encargado()` - belongsTo(Persona, 'idPersonaEncargada')
- `personas()` - hasMany(Persona)
- `derivacionesDestino()` - hasMany(Derivacion, 'idDepartamentoDestino')
- `correspondenciasDestinatario()` - hasMany(CorrespondenciaDestinatario)

**Scopes:**
- `scopeActivos()` - Filter active departments

---

### 5. **Persona Model** (Contact Records)

```php
protected $table = 'PERSONA'
protected $primaryKey = 'idPersona'
protected $fillable = [
    'nombre',                // Name
    'correo',               // Email
    'telefono_celular',     // Mobile phone
    'telefono_fijo',        // Landline
    'ci',                   // ID card (unique)
    'institucion',          // Institution
    'tipo',                 // INTERNAL|EXTERNAL
    'idDepartamento',       // Department (FK)
    'idCargo',             // Position/Role (FK)
    'activo',
    'fecha_creacion',
    'fecha_deshabilitacion'
]
```

**Relationships:**
- `cargo()` - belongsTo(Cargo)
- `departamento()` - belongsTo(Departamento)
- `responsabilidades()` - hasMany(DepartamentoResponsable)

---

### 6. **Cargo Model** (Position/Role)

```php
protected $table = 'CARGO'
protected $primaryKey = 'idCargo'
protected $fillable = [
    'nombre',       // Position name
    'descripcion',  // Description
    'nivel',        // Hierarchical level
    'activo'
]
```

**Relationships:**
- `personas()` - hasMany(Persona)

**Scopes:**
- `scopeActivos()` - Filter active positions
- `scopeNivel($nivel)` - Filter by level

---

### 7. **Seguimiento Model** (Document Tracking)

```php
protected $table = 'SEGUIMIENTO'
protected $primaryKey = 'idSeguimiento'
protected $fillable = [
    'idDocumento',   // Document reference
    'fecha',         // Tracking date
    'ubicacion',     // Location/Status
    'idEstado',      // State reference
    'activo'
]
```

**Relationships:**
- `correspondencia()` - belongsTo(Correspondencia)
- `estado()` - belongsTo(EstadoDocumento)
- `usuario()` - belongsTo(User)

---

### 8. **Auditoria Model** (Audit Log)

```php
protected $table = 'AUDITORIA'
protected $primaryKey = 'idAuditoria'
protected $fillable = [
    'idUsuario',        // User who made change
    'modelo',           // Model class name
    'idRegistro',       // Record ID changed
    'accion',           // CREATE|UPDATE|DELETE
    'datosAnteriores',  // Previous data (JSON)
    'datosNuevos',      // New data (JSON)
    'ip',              // Client IP
    'navegador',       // Browser info
    'ruta',            // Request route
    'fecha'            // Timestamp
]
```

**Casts:**
- `datosAnteriores` → array
- `datosNuevos` → array
- `fecha` → datetime

**Relationships:**
- `usuario()` - belongsTo(User)

**Appends:**
- `accion_badge` - HTML badge with color (CREATE=green, UPDATE=yellow, DELETE=red)
- `cambios_resumo` - Summary of changes

---

### 9. **Catalog Models** (Simple Lookup Tables)

#### EstadoDocumento
```
idEstado, nombre
Common states: Pendiente (1), En Tránsito (2), Finalizado (3), Archivado (4)
```

#### NivelUrgencia
```
idUrgencia, nombre
Examples: Normal, Importante, Urgente, MuyUrgente
```

#### TipoDocumento
```
idTipoDocumento, nombre
Examples: Memorandum, Informe, Solicitud, Respuesta
```

#### CorrespondenciaDestinatario
```
idDocumento, idPersona
Junction table for multiple recipients
```

#### DepartamentoResponsable
```
idPersona, idDepartamento
Audit trail of department responsibilities
```

#### Rol (Role)
```
idRol, nombre
User roles for access control
```

---

## Database Migrations & Schema

### Migration Timeline

| Migration | Purpose |
|-----------|---------|
| `0001_01_01_000000_create_users_table` | Laravel users table |
| `0001_01_01_000001_create_cache_table` | Laravel cache table |
| `0001_01_01_000002_create_jobs_table` | Laravel jobs queue table |
| `2026_05_05_152243_creacion_tablas` | **INITIAL SCHEMA** - All main tables |
| `2026_05_08_091500_modificaciones_base_datos` | Add phone, CI, departments to PERSONA |
| `2026_05_08_091700_eliminar_estado_usuario` | Remove ESTADO_USUARIO catalog |
| `2026_05_08_092000_create_cargo_table` | Create CARGO (positions) table |
| `2026_05_14_100000_persona_audit_fields` | Add audit fields to PERSONA |
| `2026_05_14_120000_add_id_usuario_envio_to_derivacion_table` | Add user tracking to DERIVACION |
| `2026_05_17_000000_mejorar_auditoria_table` | Enhance AUDITORIA table |

### Initial Schema (2026_05_05_152243)

**Catalogs:**
```sql
ROL(idRol, nombre)
ESTADO_USUARIO(idEstadoUsuario, nombre)
DEPARTAMENTO(idDepartamento, nombre)
ESTADO_DOCUMENTO(idEstado, nombre)
NIVEL_URGENCIA(idUrgencia, nombre)
TIPO_DOCUMENTO(idTipoDocumento, nombre)
```

**Core Tables:**
```sql
PERSONA(
    idPersona PRIMARY KEY,
    nombre VARCHAR(200),
    correo VARCHAR(150),
    institucion VARCHAR(200),
    tipo ENUM('INTERNO','EXTERNO'),
    activo BOOLEAN DEFAULT true
)

CORRESPONDENCIA(
    idDocumento PRIMARY KEY,
    cite VARCHAR(100),
    asunto TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    idTipoDocumento FK,
    idEstado FK,
    idUrgencia FK,
    idUsuario FK,
    idRemitente FK → PERSONA,
    activo BOOLEAN DEFAULT true
)

CORRESPONDENCIA_DESTINATARIO(
    id PRIMARY KEY,
    idDocumento FK → CORRESPONDENCIA,
    idPersona FK → PERSONA,
    activo BOOLEAN DEFAULT true
)

AUDITORIA(
    idLog PRIMARY KEY,
    idUsuario FK,
    accion TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

### Schema Modifications

**PERSONA enhancements (2026_05_08_091500):**
```sql
ALTER TABLE PERSONA ADD COLUMN
    telefono_celular VARCHAR(20),
    telefono_fijo VARCHAR(20),
    ci VARCHAR(20) UNIQUE,
    idDepartamento FK → DEPARTAMENTO
```

**DEPARTAMENTO enhancements:**
```sql
ALTER TABLE DEPARTAMENTO ADD COLUMN
    idPersonaEncargada FK → PERSONA,
    activo BOOLEAN DEFAULT true
```

**CARGO creation (2026_05_08_092000):**
```sql
CREATE TABLE CARGO(
    idCargo PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150),
    descripcion TEXT,
    nivel VARCHAR(50),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

**AUDITORIA enhancements (2026_05_17_000000):**
```sql
ALTER TABLE AUDITORIA ADD COLUMN
    modelo VARCHAR(100),
    idRegistro BIGINT,
    datosAnteriores JSON,
    datosNuevos JSON,
    ip VARCHAR(45),
    navegador TEXT,
    ruta VARCHAR(255)
```

**DERIVACION enhancements (2026_05_14_120000):**
```sql
ALTER TABLE DERIVACION ADD COLUMN
    idUsuarioEnvio FK → users
```

---

## Middleware Implementations

### 1. **Admin Middleware** (`app/Http/Middleware/IsAdmin.php` and `AdminMiddleware.php`)

**Purpose:** Restrict routes to admin users only

**Implementation Pattern:**
```php
// Routes protected with:
Route::middleware(['auth', 'admin'])
```

---

### 2. **Authentication Middleware** (Built-in Laravel)

**Applied to:**
- Profile routes
- Admin panel
- Dashboard routes
- Verified routes (email verification required)

**Middleware Stack:**
```php
Route::middleware(['auth'])              // Basic authentication
Route::middleware(['auth', 'verified'])  // Email verified requirement
```

---

### 3. **Guest Middleware** (Built-in Laravel)

**Applied to:**
- Login page
- Registration page
- Password reset flows

---

### 4. **Email Verification Middleware** (Built-in Laravel)

**Applied to:**
- User dashboard
- Document management routes

**Routes requiring verification:**
- `/user/dashboard`
- `/documentos/*`
- `/correspondencia/*`
- `/envios/*`
- `/recibidas/*`

---

## Observers & Audit System

### Overview

The system uses **GenericAuditObserver** pattern to automatically track all changes to critical entities.

### GenericAuditObserver Implementation

**Location:** `app/Observers/GenericAuditObserver.php`

**Tracked Events:**
```
created()  → Fires after model INSERT
updating() → Fires before model UPDATE (stores original values)
updated()  → Fires after model UPDATE (records changes)
deleted()  → Fires after model DELETE
```

**Key Features:**

1. **Change Detection:**
   - Captures original data during `updating()` event
   - Compares before/after during `updated()` event
   - Stores only changed fields

2. **Data Capture:**
   - `datosAnteriores` - Previous values (before change)
   - `datosNuevos` - New values (after change)
   - Only includes fields that changed

3. **Context Capture:**
   - `idUsuario` - Authenticated user ID
   - `modelo` - Class name of changed model
   - `idRegistro` - PK of the record
   - `accion` - CREATE/UPDATE/DELETE

### Registered Models

**In AppServiceProvider (boot method):**

```php
$modelos = [
    Correspondencia::class,
    Derivacion::class,
    User::class,
    Departamento::class,
    Persona::class,
    EstadoDocumento::class,
    NivelUrgencia::class,
    TipoDocumento::class,
    Seguimiento::class,
];

foreach ($modelos as $modelo) {
    $modelo::observe(GenericAuditObserver::class);
}
```

**All 9 key entities are monitored** for complete audit trail.

### Audit Record Structure

```php
Auditoria::create([
    'idUsuario'      => Auth::id(),
    'modelo'         => 'Correspondencia',
    'idRegistro'     => $document->idDocumento,
    'accion'         => 'CREATE|UPDATE|DELETE',
    'datosAnteriores'=> array, // JSON
    'datosNuevos'    => array, // JSON
    'ip'             => request()->ip(),
    'navegador'      => request()->userAgent(),
    'ruta'           => request()->path(),
    'fecha'          => now()
]);
```

### Audit Access Methods

**Model Attribute:**
- `accion_badge` - Returns HTML badge: `<span class="badge bg-{color}">ICON ACTION</span>`

**Colors:**
- CREATE → `bg-success` (green)
- UPDATE → `bg-warning` (yellow)
- DELETE → `bg-danger` (red)

---

## Helpers & Services

### AuditoriaHelper Class

**Location:** `app/Helpers/AuditoriaHelper.php`

**Purpose:** Centralized audit data retrieval and analysis

#### Public Methods:

##### 1. `obtenerAuditorias($idUsuario, $accion, $modelo, $fechaInicio, $fechaFin, $perPage)`

```php
Returns: Paginated collection of audit records
Filters:
  - By user
  - By action (CREATE/UPDATE/DELETE)
  - By model
  - By date range
Relationships loaded: usuario (with name, email)
Order: DESC by fecha
Pagination: Default 15 per page
```

##### 2. `obtenerResumen($dias)`

```php
Returns: Array with activity summary
Structure: [
    'total'             => Total audit records in period,
    'creaciones'        => CREATE count,
    'actualizaciones'   => UPDATE count,
    'eliminaciones'     => DELETE count,
    'usuarios_activos'  => Distinct user count
]
Default Period: Last 7 days
```

##### 3. `obtenerActividadPorUsuario($dias)`

```php
Returns: Collection of user activity grouped by idUsuario
Fields: idUsuario, total (count), usuario (loaded relation)
Order: DESC by total
Default Period: Last 7 days
```

##### 4. `obtenerActividadPorModelo($dias)`

```php
Returns: Collection of model activity grouped by modelo and accion
Fields: modelo, accion, total (count)
Default Period: Last 7 days
```

---

## View Structure

### Directory Organization

```
resources/views/
├── admin/
│   ├── dashboard.blade.php      // Admin panel
│   ├── usuarios.blade.php       // User management
│   ├── departamentos.blade.php  // Department management
│   ├── personas.blade.php       // Contact management
│   └── reportes.blade.php       // Reports
├── auditoria/
│   ├── index.blade.php          // Audit log list
│   └── show.blade.php           // Audit detail
├── auth/
│   ├── login.blade.php          // Login form
│   ├── register.blade.php       // Registration form
│   └── page.blade.php           // Auth page
├── correspondencia/
│   ├── index.blade.php          // Document list
│   ├── show.blade.php           // Document detail
│   └── form.blade.php           // Document form
├── dashboard.blade.php          // Main dashboard
├── envio/
│   ├── index.blade.php          // Sent documents
│   ├── bandeja.blade.php        // User inbox
│   ├── derivar.blade.php        // Routing form
│   └── finalizar.blade.php      // Finalize view
├── recibidas/
│   ├── index.blade.php          // Received documents
│   └── recibir.blade.php        // Receive form
├── layouts/
│   ├── app.blade.php            // Main layout
│   ├── guest.blade.php          // Login layout
│   └── navigation.blade.php     // Navigation component
├── profile/
│   ├── edit.blade.php           // Profile edit
│   └── delete.blade.php         // Delete account
├── user/
│   ├── dashboard.blade.php      // User dashboard
│   ├── configuracion.blade.php  // Settings
│   └── correspondencia.blade.php
├── components/
│   └── [Reusable components]
└── welcome.blade.php            // Home page
```

### Key Views:

**Admin Dashboard:**
- Statistics/metrics
- Document counts by state
- Department overview
- User activity

**User Dashboard:**
- Pending documents
- Recent activity
- Quick stats

**Document Management:**
- Create/list correspondence
- Route documents between departments
- Track delivery status
- Receive and finalize documents

**Audit Views:**
- Paginated audit logs
- Filter by user, action, model, date range
- Detailed change history

---

## Architecture Patterns

### 1. **Repository Pattern (Implicit)**

**Implementation:**
- Controllers use Model queries directly
- Queries encapsulated in controller methods
- AuditoriaHelper provides centralized data retrieval

```php
// Example from DocumentoController
$documentos = Correspondencia::with([
    'tipoDocumento',
    'estado',
    'urgencia',
    'remitente',
    'derivaciones.departamentoDestino'
])->orderByDesc('fecha')->paginate(10);
```

---

### 2. **Observer Pattern (Active)**

**Implementation:**
- Automatic audit trail via GenericAuditObserver
- Decouples audit logic from business logic
- Registered globally in AppServiceProvider

```php
// Automatic execution on model events
Correspondencia::observe(GenericAuditObserver::class);
```

---

### 3. **Service Layer (Partial)**

**Implementation:**
- AuditoriaHelper as centralized service
- Provides reusable audit queries
- Supports dashboard statistics

```php
$resumen = AuditoriaHelper::obtenerResumen(7);  // 7-day summary
$usuarios = AuditoriaHelper::obtenerActividadPorUsuario(30);  // 30-day active users
```

---

### 4. **Middleware Pattern**

**Implementation:**
- Authentication/Authorization via middleware stack
- Admin routes protected with IsAdmin middleware
- Email verification gates

```php
Route::middleware(['auth', 'verified'])->group(...)
Route::middleware(['auth', 'admin'])->group(...)
```

---

### 5. **Relationship Eager Loading**

**Pattern:**
- Controllers use `with()` to eager load relationships
- Prevents N+1 query problems
- Improves performance

```php
Correspondencia::with([
    'tipoDocumento',
    'estado',
    'urgencia',
    'remitente',
    'derivaciones.departamentoDestino'
])->get()
```

---

### 6. **Query Builder with Scopes**

**Implemented Scopes:**
- `Cargo::scopeActivos()` - Filter active positions
- `Cargo::scopeNivel()` - Filter by hierarchy level
- `Departamento::scopeActivos()` - Filter active departments

```php
Cargo::activos()->nivel('GERENTE')->get();
Departamento::activos()->get();
```

---

### 7. **Caching Strategy**

**Implemented in DashboardController:**

```php
Cache::remember('dashboard.contadores', 60, function() {
    return [
        Correspondencia::count(),
        User::count(),
        Derivacion::count(),
        Departamento::count(),
    ];
});
```

**Cache Keys:**
- `dashboard.contadores` - 60 seconds TTL
- `dashboard.estados` - 60 seconds TTL
- `dashboard.departamentos` - 60 seconds TTL

---

### 8. **State Machine (Implicit)**

**Document States:**
```
Draft/Created → Pendiente (1)
             → En Tránsito (2)
             → Finalizado (3)
             → Archivado (4)
```

**State Transitions:**
- Route via Derivacion (transitions to En Tránsito)
- Receive (reduces En Tránsito items)
- Finalize (transitions to Finalizado)

---

### 9. **Multi-Level Filtering**

**Pattern in Controllers:**

```php
$query = Model::query();

if ($request->filled('filter1')) {
    $query->where('field1', $request->filter1);
}

if ($request->filled('filter2')) {
    $query->whereHas('relation', function($q) use ($request) {
        $q->where('field2', $request->filter2);
    });
}

return $query->paginate();
```

**Applied in:**
- DocumentoController - filters by cite, asunto, estado, urgencia
- EnvioController - filters by departamento, estado, urgencia, búsqueda
- AuditoriaController - filters by usuario, acción, modelo, fecha

---

### 10. **Request Validation**

**Implementation:**
- `StoreDocumentoRequest` for document creation
- Form Request classes for data validation
- Automatic validation error handling

```php
Route::post('/documentos', [DocumentoController::class, 'store'])
     ->validate(StoreDocumentoRequest::class);
```

---

## Data Flow Summary

### Document Creation Flow

```
User → DocumentoController@show (form)
    ↓
    → DocumentoController@store (validation)
    ↓
    → Correspondencia model created
    ↓
    → GenericAuditObserver@created (audit logged)
    ↓
    → Database (CORRESPONDENCIA table)
```

### Document Routing Flow

```
User → EnvioController@derivarForm (routing form)
    ↓
    → EnvioController@derivar (store derivation)
    ↓
    → Derivacion model created
    ↓
    → GenericAuditObserver@created (audit logged)
    ↓
    → Correspondencia state updated (En Tránsito)
    ↓
    → Recipient receives notification (implicit)
```

### Document Receipt Flow

```
Recipient → RecibidasController@index (inbox)
         ↓
         → RecibidasController@recibir (mark received)
         ↓
         → Derivacion.fechaRecepcion updated
         ↓
         → GenericAuditObserver@updated (audit logged)
         ↓
         → RecibidasController@finalizar (complete)
         ↓
         → Correspondencia.idEstado = Finalizado (3)
```

### Audit Trail Flow

```
Any Model Event → GenericAuditObserver detects change
              ↓
              → Captures user, model, action, data
              ↓
              → Auditoria record created
              ↓
              → AuditoriaController displays logs
              ↓
              → Admin views audit history
```

---

## Key Implementation Details

### 1. **Foreign Key Strategy**

- Snake_case IDs: `idDocumento`, `idDepartamento`, `idUsuario`
- Explicitly defined in model relationships
- Migrations define FK constraints

### 2. **Timestamps**

- Most catalog tables: `public $timestamps = false`
- Auditoria table: Uses `fecha` instead of standard timestamps
- Some tables (Cargo): Use standard `created_at`, `updated_at`

### 3. **Boolean Fields**

- `activo` flag on all major tables
- Soft delete alternative
- Allows historical records retention

### 4. **JSON Storage**

- `Auditoria.datosAnteriores` and `datosNuevos` are JSON
- Cast to array in model
- Enables flexible change tracking

### 5. **Authentication**

- Uses Laravel Breeze
- Email verification required for core features
- Role-based access via `User.idRol`

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| Frontend | Blade templating, Bootstrap 5, Vite |
| Database | MySQL/MariaDB |
| Auth | Laravel Breeze (Email + Password) |
| PDF Export | barryvdh/laravel-dompdf |
| Testing | PHPUnit 11.5 |
| Dev Tools | Laravel Pint, Laravel Sail, Laravel Tinker |

---

## Summary

**Gestion de Correspondencia** is a well-structured document management system built on Laravel with:

✅ **Clear separation of concerns** - Controllers, Models, Observers, Helpers
✅ **Comprehensive audit trail** - Automatic tracking of all changes
✅ **Flexible routing system** - Multi-level department-based document flow
✅ **Role-based access** - Admin and user tiers
✅ **Performance optimization** - Query eager loading, caching strategy
✅ **Scalable architecture** - Observer pattern, service layer, request validation

**Core Business Logic:** Documents move through departments via derivations, with complete audit history maintained for compliance and traceability.

