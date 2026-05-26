# API REST - ARQUITECTURA PROFESIONAL
## Sistema de Gestión de Correspondencia - Laravel 12

---

## 📋 ÍNDICE DE CONTENIDOS

1. **Estructura de la Aplicación**
2. **Autenticación y Autorización**
3. **Endpoints Disponibles**
4. **Códigos HTTP**
5. **Respuestas JSON Estándar**
6. **Validaciones**
7. **Paginación y Filtrado**
8. **Manejo de Errores**
9. **Seguridad**
10. **Cómo Probar con Postman**

---

## 1️⃣ ESTRUCTURA DE LA APLICACIÓN

```
app/Http/
├── Controllers/
│   ├── Api/
│   │   ├── V1/
│   │   │   ├── AuthController.php          # Autenticación
│   │   │   ├── CorrespondenciaController.php
│   │   │   ├── DerivacionController.php
│   │   │   ├── PersonaController.php
│   │   │   ├── DepartamentoController.php
│   │   │   ├── UsuarioController.php
│   │   │   ├── SeguimientoController.php
│   │   │   ├── AuditoriaController.php
│   │   │   ├── ReporteController.php
│   │   │   └── EstadisticasController.php
│   │   └── BaseController.php              # Controlador base
│   ├── Requests/
│   │   ├── Api/
│   │   │   ├── LoginRequest.php
│   │   │   ├── RegisterRequest.php
│   │   │   ├── StoreCorrespondenciaRequest.php
│   │   │   ├── UpdateCorrespondenciaRequest.php
│   │   │   └── ... (más)
│   └── Middleware/
│       ├── ApiKey.php
│       ├── CheckRole.php
│       └── ThrottleApi.php
├── Resources/
│   ├── UserResource.php
│   ├── CorrespondenciaResource.php
│   ├── DerivacionResource.php
│   └── ... (más)
└── Traits/
    ├── ApiResponse.php                    # Respuestas estandarizadas
    └── QueryBuilder.php                   # Filtrado y búsqueda

routes/
├── api.php                                # Rutas API principales
└── api/
    └── v1.php                             # Rutas API v1

database/
├── migrations/
│   └── ... (migrations para API)
└── seeders/
    └── ApiDataSeeder.php

app/Exceptions/
├── ApiException.php                       # Exception base para API
├── ValidationException.php
└── UnauthorizedException.php
```

---

## 2️⃣ AUTENTICACIÓN Y AUTORIZACIÓN

### Flujo de Autenticación

```
┌─────────────────────────────────────────────────────────────┐
│                   CLIENTE (Frontend)                         │
└──────────────────────────┬──────────────────────────────────┘
                           │
                    POST /api/v1/auth/login
                    { email, password }
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                   API (Backend)                              │
│           AuthController@login                              │
│     ↓                                                       │
│     1. Validar credenciales                                │
│     2. Generar token con Sanctum                           │
│     3. Retornar token + usuario                            │
└──────────────────────────┬──────────────────────────────────┘
                           │
                    Response: Token + User
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│             CLIENTE guarda Token en localStorage             │
└─────────────────────────────────────────────────────────────┘

Solicitudes posteriores con Token:
┌─────────────────────────────────────────────────────────────┐
│ Header: Authorization: Bearer {token}                       │
│ GET /api/v1/documentos                                      │
│        ↓                                                    │
│ Middleware: CheckToken (Sanctum)                           │
│        ↓                                                    │
│ Si válido → Ejecutar controlador                           │
│ Si inválido → Retornar 401 Unauthorized                    │
└─────────────────────────────────────────────────────────────┘
```

### Tipos de Roles y Permisos

```
Admin (id=1)
├── Acceso a todo
├── Gestionar usuarios
└── Ver auditoría

Gerente Departamento (id=2)
├── Ver documentos del departamento
├── Crear derivaciones
└── Ver seguimiento

Usuario Regular (id=3)
├── Ver propios documentos
├── Ver derivaciones asignadas
└── Actualizar seguimiento

Consultor Externo (id=4)
├── Solo lectura
└── Documentos públicos
```

---

## 3️⃣ ENDPOINTS DISPONIBLES

### AUTENTICACIÓN

```http
POST /api/v1/auth/login
POST /api/v1/auth/register
POST /api/v1/auth/logout
POST /api/v1/auth/refresh
GET  /api/v1/auth/me
```

### DOCUMENTOS (CORRESPONDENCIA)

```http
GET    /api/v1/documentos                    # Lista paginada
POST   /api/v1/documentos                    # Crear
GET    /api/v1/documentos/{id}               # Obtener detalle
PUT    /api/v1/documentos/{id}               # Actualizar
DELETE /api/v1/documentos/{id}               # Eliminar
GET    /api/v1/documentos/{id}/derivaciones  # Derivaciones del documento
GET    /api/v1/documentos/{id}/seguimiento   # Seguimiento del documento
GET    /api/v1/documentos/search?q=...       # Búsqueda
```

### DERIVACIONES

```http
GET    /api/v1/derivaciones                  # Lista paginada
POST   /api/v1/derivaciones                  # Crear
GET    /api/v1/derivaciones/{id}             # Obtener detalle
PUT    /api/v1/derivaciones/{id}             # Actualizar
DELETE /api/v1/derivaciones/{id}             # Eliminar
POST   /api/v1/derivaciones/{id}/recibir     # Marcar como recibida
```

### PERSONAS

```http
GET    /api/v1/personas                      # Lista paginada
POST   /api/v1/personas                      # Crear
GET    /api/v1/personas/{id}                 # Obtener detalle
PUT    /api/v1/personas/{id}                 # Actualizar
DELETE /api/v1/personas/{id}                 # Eliminar
```

### DEPARTAMENTOS

```http
GET    /api/v1/departamentos                 # Lista paginada
POST   /api/v1/departamentos                 # Crear
GET    /api/v1/departamentos/{id}            # Obtener detalle
PUT    /api/v1/departamentos/{id}            # Actualizar
DELETE /api/v1/departamentos/{id}            # Eliminar
GET    /api/v1/departamentos/{id}/usuarios   # Usuarios del departamento
```

### USUARIOS

```http
GET    /api/v1/usuarios                      # Lista paginada
POST   /api/v1/usuarios                      # Crear
GET    /api/v1/usuarios/{id}                 # Obtener detalle
PUT    /api/v1/usuarios/{id}                 # Actualizar
DELETE /api/v1/usuarios/{id}                 # Eliminar
POST   /api/v1/usuarios/{id}/cambiar-rol     # Cambiar rol de usuario
```

### SEGUIMIENTOS

```http
GET    /api/v1/seguimientos                  # Lista paginada
GET    /api/v1/seguimientos/{id}             # Obtener detalle
```

### AUDITORÍAS

```http
GET    /api/v1/auditorias                    # Lista paginada
GET    /api/v1/auditorias/{id}               # Obtener detalle
GET    /api/v1/auditorias/usuario/{userId}   # Auditoría por usuario
GET    /api/v1/auditorias/documento/{docId}  # Auditoría por documento
```

### REPORTES Y ESTADÍSTICAS

```http
GET    /api/v1/reportes/documentos-por-estado       # Reporte por estado
GET    /api/v1/reportes/documentos-por-urgencia     # Reporte por urgencia
GET    /api/v1/reportes/documentos-por-tipo         # Reporte por tipo
GET    /api/v1/reportes/derivaciones-pendientes     # Derivaciones pendientes
GET    /api/v1/estadisticas/dashboard               # Dashboard
GET    /api/v1/estadisticas/documentos/mes          # Documentos por mes
```

---

## 4️⃣ CÓDIGOS HTTP

```
200 OK                   - Solicitud exitosa, retorna datos
201 Created              - Recurso creado exitosamente
204 No Content           - Eliminación exitosa, sin contenido
400 Bad Request          - Solicitud malformada o parámetros inválidos
401 Unauthorized         - Token inválido o ausente
403 Forbidden            - Usuario no tiene permiso
404 Not Found            - Recurso no encontrado
422 Unprocessable Entity - Validación fallida
429 Too Many Requests    - Límite de solicitudes excedido
500 Internal Server Error- Error del servidor
503 Service Unavailable  - Servicio no disponible
```

---

## 5️⃣ RESPUESTAS JSON ESTÁNDAR

### ✅ Solicitud Exitosa (200 OK)

```json
{
  "success": true,
  "status": 200,
  "message": "Documento obtenido correctamente",
  "data": {
    "idDocumento": 1,
    "cite": "CITE-2024-001",
    "asunto": "Solicitud de información",
    "fecha": "2024-05-20",
    "estado": "En proceso",
    "urgencia": "Normal",
    "creador": "Juan Pérez"
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### ✅ Recurso Creado (201 Created)

```json
{
  "success": true,
  "status": 201,
  "message": "Documento creado correctamente",
  "data": {
    "idDocumento": 1,
    "cite": "CITE-2024-001",
    "asunto": "Nueva solicitud"
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### ✅ Lista Paginada (200 OK)

```json
{
  "success": true,
  "status": 200,
  "message": "Documentos obtenidos correctamente",
  "data": [
    { "idDocumento": 1, "cite": "CITE-2024-001", ... },
    { "idDocumento": 2, "cite": "CITE-2024-002", ... }
  ],
  "pagination": {
    "current_page": 1,
    "total": 150,
    "per_page": 10,
    "last_page": 15,
    "from": 1,
    "to": 10
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### ❌ Error de Validación (422 Unprocessable Entity)

```json
{
  "success": false,
  "status": 422,
  "message": "Validación fallida",
  "errors": {
    "asunto": ["El campo asunto es requerido"],
    "fecha": ["La fecha debe ser una fecha válida"]
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### ❌ No Autorizado (401 Unauthorized)

```json
{
  "success": false,
  "status": 401,
  "message": "Token inválido o expirado",
  "errors": [],
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### ❌ Recurso No Encontrado (404 Not Found)

```json
{
  "success": false,
  "status": 404,
  "message": "Documento no encontrado",
  "errors": [],
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### ❌ Error del Servidor (500 Internal Server Error)

```json
{
  "success": false,
  "status": 500,
  "message": "Error interno del servidor",
  "errors": [],
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

---

## 6️⃣ VALIDACIONES

### Login
- Email: requerido, email válido
- Password: requerido, mínimo 6 caracteres

### Crear Correspondencia
- cite: requerido, único
- asunto: requerido, máximo 255 caracteres
- fecha: requerida, formato YYYY-MM-DD
- idTipoDocumento: requerido, existe en tabla
- idEstado: requerido, existe en tabla
- idUrgencia: requerido, existe en tabla
- idRemitente: requerido, existe en tabla

### Crear Derivación
- idDocumento: requerido, existe
- idDepartamentoDestino: requerido, existe
- idUsuarioAsignado: requerido, existe
- instruccion: requerido, mínimo 10 caracteres

---

## 7️⃣ PAGINACIÓN Y FILTRADO

### Paginación

```http
GET /api/v1/documentos?page=1&per_page=10
GET /api/v1/documentos?page=2&per_page=25
```

### Ordenamiento

```http
GET /api/v1/documentos?sort=fecha      # Ascendente
GET /api/v1/documentos?sort=-fecha     # Descendente
GET /api/v1/documentos?sort=asunto,-fecha
```

### Búsqueda

```http
GET /api/v1/documentos?search=solicitud
GET /api/v1/documentos?search=CITE-2024
```

### Filtrado Avanzado

```http
GET /api/v1/documentos?estado=1&urgencia=2&tipo=3
GET /api/v1/documentos?fecha_desde=2024-01-01&fecha_hasta=2024-12-31
GET /api/v1/documentos?activo=1
```

### Combinados

```http
GET /api/v1/documentos?page=1&per_page=20&sort=-fecha&estado=1&search=solicitud
```

---

## 8️⃣ MANEJO DE ERRORES

### Ejemplo: Crear documento con datos inválidos

```bash
curl -X POST http://localhost/api/v1/documentos \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "asunto": "",
    "fecha": "fecha-invalida"
  }'
```

**Respuesta 422:**

```json
{
  "success": false,
  "status": 422,
  "message": "Validación fallida",
  "errors": {
    "asunto": [
      "El campo asunto es requerido"
    ],
    "fecha": [
      "La fecha debe ser una fecha válida",
      "La fecha debe ser una fecha anterior a hoy"
    ]
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### Errores Comunes

| Situación | Código | Mensaje |
|-----------|--------|---------|
| Token ausente | 401 | Token no proporcionado |
| Token expirado | 401 | Token expirado |
| Recurso no existe | 404 | Recurso no encontrado |
| Sin permisos | 403 | No tiene permiso para esta acción |
| Datos inválidos | 422 | Validación fallida |
| Limite de solicitudes | 429 | Demasiadas solicitudes |

---

## 9️⃣ SEGURIDAD

### Headers de Seguridad

```
Authorization: Bearer {token}           # Token Sanctum
Content-Type: application/json
Accept: application/json
```

### Middleware Aplicado

- **Sanctum**: Autenticación con tokens
- **CheckRole**: Validación de roles
- **ThrottleApi**: Rate limiting (60 solicitudes/minuto)
- **CORS**: Configurado para frontend

### Variables de Entorno (.env)

```env
APP_ENV=production
APP_DEBUG=false
SANCTUM_STATEFUL_DOMAINS=localhost,app.local
SANCTUM_EXPIRATION=1440
SESSION_LIFETIME=120
```

---

## 🔟 CÓMO PROBAR CON POSTMAN

### 1. Importar Colección

- Descargar: `GestionCorrespondencia-API-Postman.json`
- En Postman: Import → Upload Files
- Seleccionar el archivo

### 2. Variables de Entorno

Crear nuevo Environment:

```json
{
  "base_url": "http://localhost:8000",
  "api_version": "v1",
  "token": "{{ token recibido del login }}"
}
```

### 3. Flujo de Pruebas

**Paso 1: Login**

```
POST {{base_url}}/api/{{api_version}}/auth/login
Body (JSON):
{
  "email": "admin@system.com",
  "password": "password"
}
```

Copiar token de respuesta y asignar a variable `token`

**Paso 2: Listar Documentos**

```
GET {{base_url}}/api/{{api_version}}/documentos
Headers:
Authorization: Bearer {{token}}
```

**Paso 3: Crear Documento**

```
POST {{base_url}}/api/{{api_version}}/documentos
Headers:
Authorization: Bearer {{token}}
Body (JSON):
{
  "cite": "CITE-2024-001",
  "asunto": "Nueva solicitud",
  "fecha": "2024-05-20",
  "idTipoDocumento": 1,
  "idEstado": 1,
  "idUrgencia": 1,
  "idRemitente": 1
}
```

---

## SIGUIENTES PASOS

Este documento es un **plan maestro** que guiará la implementación de los 30 pasos descritos.

Cada paso incluirá:
- Archivos a crear
- Código con explicaciones
- Dónde colocar el código
- Cómo probarlo

**¡Comenzamos en el Paso 1!**

