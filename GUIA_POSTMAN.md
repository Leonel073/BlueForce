# GUÍA COMPLETA: CÓMO PROBAR LA API CON POSTMAN

## 📌 PASOS PARA CONFIGURAR POSTMAN

### Paso 1: Descargar e Instalar Postman

- Descarga desde: https://www.postman.com/downloads/
- Instala la versión para tu sistema operativo

### Paso 2: Crear un Nuevo Workspace

1. Abre Postman
2. Click en "Create a new workspace"
3. Dale un nombre: "GestionCorrespondencia-API"
4. Asegúrate de que API esté corriendo: `php artisan serve`

### Paso 3: Crear Variable de Entorno

1. Click en "Environments" (abajo a la izquierda)
2. Click en "+" para crear nuevo ambiente
3. Dale nombre: "Development"
4. Agrega estas variables:

```
base_url         | localhost:8000
api_version      | v1
token            | [vacío, se llena al hacer login]
documento_id     | [vacío]
```

### Paso 4: Usar el Ambiente

1. En la esquina superior derecha, selecciona "Development"
2. Ahora puedes usar `{{base_url}}` en tus requests

---

## 🧪 FLUJO DE PRUEBAS

### NIVEL 1: AUTENTICACIÓN

#### 1.1 LOGIN

```http
POST {{base_url}}/api/{{api_version}}/auth/login
```

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**
```json
{
  "email": "admin@system.com",
  "password": "password123"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "status": 200,
  "message": "Autenticación exitosa",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "type": "Bearer",
    "user": {
      "id": 1,
      "name": "Administrador",
      "email": "admin@system.com",
      "activo": true
    },
    "expires_in": 86400
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

**IMPORTANTE:** 
- Copia el valor de `data.token`
- En Postman, ve a Environment → Development
- En la variable `token`, pega el valor
- Presiona Save

#### 1.2 OBTENER PERFIL (Verificar token)

```http
GET {{base_url}}/api/{{api_version}}/auth/me
```

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

**Expected Response (200):** Los datos del usuario autenticado

---

### NIVEL 2: DOCUMENTOS (CORRESPONDENCIA) - CRUD

#### 2.1 LISTAR DOCUMENTOS

```http
GET {{base_url}}/api/{{api_version}}/documentos?page=1&per_page=10
```

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

**Query Parameters:**
- `page=1` - Página
- `per_page=10` - Documentos por página
- `sort=fecha` - Ordenar por fecha
- `sort=-fecha` - Orden descendente
- `search=solicitud` - Buscar por CITE o asunto
- `idEstado=1` - Filtrar por estado
- `with=tipoDocumento,estado` - Incluir relaciones

**Expected Response (200):** Lista paginada de documentos

#### 2.2 CREAR DOCUMENTO

```http
POST {{base_url}}/api/{{api_version}}/documentos
```

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body:**
```json
{
  "cite": "CITE-2024-001",
  "asunto": "Solicitud de información sobre proyecto",
  "fecha": "2024-05-20",
  "idTipoDocumento": 1,
  "idEstado": 1,
  "idUrgencia": 1,
  "idRemitente": 1
}
```

**Expected Response (201):** 
```json
{
  "success": true,
  "status": 201,
  "message": "Documento creado correctamente",
  "data": {
    "idDocumento": 1,
    "cite": "CITE-2024-001",
    ...
  }
}
```

**IMPORTANTE:** Guarda el `idDocumento` en la variable `documento_id`

#### 2.3 OBTENER DOCUMENTO ESPECÍFICO

```http
GET {{base_url}}/api/{{api_version}}/documentos/{{documento_id}}
```

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

**Expected Response (200):** Detalles completos del documento

#### 2.4 ACTUALIZAR DOCUMENTO

```http
PUT {{base_url}}/api/{{api_version}}/documentos/{{documento_id}}
```

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body:**
```json
{
  "cite": "CITE-2024-001",
  "asunto": "Solicitud actualizada",
  "fecha": "2024-05-20",
  "idTipoDocumento": 1,
  "idEstado": 2,
  "idUrgencia": 2,
  "idRemitente": 1
}
```

**Expected Response (200):** Documento actualizado

#### 2.5 ELIMINAR DOCUMENTO

```http
DELETE {{base_url}}/api/{{api_version}}/documentos/{{documento_id}}
```

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response (204):** Sin contenido (eliminado exitosamente)

---

### NIVEL 3: DERIVACIONES

#### 3.1 CREAR DERIVACIÓN

```http
POST {{base_url}}/api/{{api_version}}/derivaciones
```

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body:**
```json
{
  "idDocumento": 1,
  "idDepartamentoOrigen": 1,
  "idDepartamentoDestino": 2,
  "idUsuarioAsignado": 2,
  "instruccion": "Revisar y aprobar el documento según protocolo establecido"
}
```

**Expected Response (201):** Derivación creada

#### 3.2 RECIBIR DERIVACIÓN

```http
POST {{base_url}}/api/{{api_version}}/derivaciones/1/recibir
```

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response (200):** Derivación marcada como recibida

#### 3.3 RE-DERIVAR DOCUMENTO

```http
POST {{base_url}}/api/{{api_version}}/derivaciones/1/re-derivar
```

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body:**
```json
{
  "idDepartamentoDestino": 3,
  "idUsuarioAsignado": 3,
  "instruccion": "Re-derivar a otro departamento para análisis"
}
```

**Expected Response (201):** Nueva derivación creada

---

### NIVEL 4: PERSONAS

#### 4.1 LISTAR PERSONAS

```http
GET {{base_url}}/api/{{api_version}}/personas?page=1&per_page=10
```

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

**Expected Response (200):** Lista de personas

#### 4.2 CREAR PERSONA

```http
POST {{base_url}}/api/{{api_version}}/personas
```

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body:**
```json
{
  "nombre": "Juan Pérez García",
  "ci": "1234567890",
  "cargo": "Gerente de Proyectos",
  "email": "juan.perez@empresa.com",
  "telefono": "+34 912345678"
}
```

**Expected Response (201):** Persona creada

---

### NIVEL 5: DEPARTAMENTOS

#### 5.1 LISTAR DEPARTAMENTOS

```http
GET {{base_url}}/api/{{api_version}}/departamentos
```

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

#### 5.2 OBTENER DERIVACIONES PENDIENTES DEL DEPARTAMENTO

```http
GET {{base_url}}/api/{{api_version}}/departamentos/1/derivaciones-pendientes
```

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

---

### NIVEL 6: USUARIOS

#### 6.1 LISTAR USUARIOS

```http
GET {{base_url}}/api/{{api_version}}/usuarios
```

#### 6.2 CAMBIAR ROL DE USUARIO

```http
POST {{base_url}}/api/{{api_version}}/usuarios/5/cambiar-rol
```

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body:**
```json
{
  "idRol": 2
}
```

#### 6.3 OBTENER DERIVACIONES PENDIENTES DEL USUARIO

```http
GET {{base_url}}/api/{{api_version}}/usuarios/2/derivaciones-pendientes
```

---

### NIVEL 7: AUDITORÍAS Y REPORTES

#### 7.1 LISTAR AUDITORÍAS

```http
GET {{base_url}}/api/{{api_version}}/auditorias?page=1&per_page=20
```

#### 7.2 AUDITORÍA POR USUARIO

```http
GET {{base_url}}/api/{{api_version}}/auditorias/usuario/1
```

#### 7.3 AUDITORÍA POR DOCUMENTO

```http
GET {{base_url}}/api/{{api_version}}/auditorias/documento/1
```

#### 7.4 REPORTE: DOCUMENTOS POR ESTADO

```http
GET {{base_url}}/api/{{api_version}}/reportes/documentos-por-estado
```

#### 7.5 REPORTE: DOCUMENTOS POR URGENCIA

```http
GET {{base_url}}/api/{{api_version}}/reportes/documentos-por-urgencia
```

#### 7.6 REPORTE: DERIVACIONES PENDIENTES

```http
GET {{base_url}}/api/{{api_version}}/reportes/derivaciones-pendientes
```

---

### NIVEL 8: ESTADÍSTICAS

#### 8.1 DASHBOARD PRINCIPAL

```http
GET {{base_url}}/api/{{api_version}}/estadisticas/dashboard
```

**Expected Response (200):**
```json
{
  "success": true,
  "data": {
    "documentos": {
      "total": 150,
      "hoy": 5,
      "mes": 45
    },
    "derivaciones": {
      "total": 200,
      "pendientes": 15,
      "procesadas": 185
    },
    "usuarios": {
      "total": 20,
      "admin": 2
    },
    "estados": [...],
    "urgencias": [...]
  }
}
```

#### 8.2 DOCUMENTOS POR MES

```http
GET {{base_url}}/api/{{api_version}}/estadisticas/documentos-mes
```

#### 8.3 DOCUMENTOS POR HORA DEL DÍA

```http
GET {{base_url}}/api/{{api_version}}/estadisticas/documentos-dia
```

#### 8.4 TIEMPO PROMEDIO DE DERIVACIONES

```http
GET {{base_url}}/api/{{api_version}}/estadisticas/derivaciones-tiempo-promedio
```

---

## ⚠️ ERRORES COMUNES Y SOLUCIONES

### Error 401: Unauthorized

**Problema:** El token es inválido o expiró

**Solución:**
1. Haz login nuevamente
2. Copia el nuevo token
3. Actualiza la variable `token` en el ambiente

### Error 422: Validation failed

**Problema:** Los datos enviados no son válidos

**Solución:**
1. Verifica que todos los campos requeridos estén presentes
2. Verifica que los IDs de relaciones existan
3. Verifica el formato de fechas (YYYY-MM-DD)

### Error 404: Not found

**Problema:** El recurso no existe

**Solución:**
1. Verifica que el ID sea correcto
2. Verifica que el recurso no haya sido eliminado

### Error 500: Internal server error

**Problema:** Error en el servidor

**Solución:**
1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica que la base de datos esté conectada
3. Ejecuta `php artisan migrate` si hay migraciones pendientes

---

## 📝 CREAR COLECCIÓN REUTILIZABLE

Para no escribir las URLs cada vez:

1. En Postman, Click en "New" → "Collection"
2. Dale nombre: "GestionCorrespondencia API v1"
3. Para cada endpoint, crear una carpeta:
   - Auth
   - Documentos
   - Derivaciones
   - Personas
   - Departamentos
   - etc.

4. Dentro de cada carpeta, crear los requests
5. Usar {{base_url}} y {{token}} en todas las URLs
6. Cuando termines, haz Export para compartir con el equipo

---

## 🔒 NOTAS DE SEGURIDAD

1. **NUNCA** compartas tokens por email
2. **NUNCA** comites tokens en Git
3. Los tokens expiran (revisa `expires_in` en la respuesta)
4. Usa HTTPS en producción (no HTTP)
5. Cambia las contraseñas por defecto

---

## 🚀 PRÓXIMOS PASOS

Después de dominar Postman:

1. **Crear Frontend** que consume la API
2. **Documentar con Swagger/OpenAPI**
3. **Agregar más validaciones**
4. **Implementar Rate Limiting**
5. **Agregar Caching**
6. **Usar JWT en lugar de Sanctum** (opcional, para mayor escalabilidad)

