# 🚀 API REST PROFESIONAL - GESTIÓN DE CORRESPONDENCIA

## 📋 Descripción

Una API REST completa y profesional desarrollada en **Laravel 12** para el sistema de gestión de correspondencia. Implementa buenas prácticas de arquitectura, seguridad y escalabilidad.

---

## ✨ CARACTERÍSTICAS IMPLEMENTADAS

### ✅ Autenticación y Seguridad
- Autenticación con **Laravel Sanctum** (tokens)
- Validación de tokens en cada solicitud
- Middleware de autenticación
- Protección de rutas por rol

### ✅ Arquitectura RESTful
- Endpoints siguiendo estándar REST
- Versionamiento de API (`/api/v1/`)
- Controllers separados por módulo
- Respuestas JSON estandarizadas
- Códigos HTTP correctos (200, 201, 400, 401, 403, 404, 422, 500)

### ✅ Validación y Manejo de Errores
- Form Requests para validación
- Manejo global de excepciones
- Mensajes de error descriptivos
- Validación de campos requeridos

### ✅ Base de Datos
- Relaciones Eloquent configuradas
- Eager loading optimizado
- Soft deletes (eliminación lógica)
- Auditoría de cambios

### ✅ Recursos y Serialización
- API Resources para respuestas
- Relationships cargadas con `whenLoaded()`
- Datos sensibles ocultos
- Formato JSON consistente

### ✅ Búsqueda, Filtrado y Ordenamiento
- Búsqueda de texto en múltiples columnas
- Filtrado por campos específicos
- Ordenamiento ascendente/descendente
- Combinación de opciones

### ✅ Paginación
- Paginación automática
- Personalización de cantidad por página
- Información de paginación en respuestas
- Límite máximo de registros por página

### ✅ Reportes y Estadísticas
- Reportes por estado, urgencia, tipo
- Derivaciones pendientes
- Análisis de performance
- Dashboard general

---

## 📁 ESTRUCTURA DE CARPETAS

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/                    # Controladores API v1
│   │           ├── AuthController.php
│   │           ├── CorrespondenciaController.php
│   │           ├── DerivacionController.php
│   │           ├── PersonaController.php
│   │           ├── DepartamentoController.php
│   │           ├── UsuarioController.php
│   │           ├── SeguimientoController.php
│   │           ├── AuditoriaController.php
│   │           ├── ReporteController.php
│   │           ├── EstadisticasController.php
│   │           └── BaseController.php
│   ├── Requests/
│   │   └── Api/                       # Form Requests
│   │       └── CorrespondenciaRequest.php
│   ├── Resources/                     # API Resources
│   │   └── ApiResources.php
│   └── Middleware/                    # Middleware personalizado
├── Traits/
│   ├── ApiResponse.php                # Respuestas estandarizadas
│   └── QueryBuilder.php               # Filtrado y búsqueda
├── Exceptions/
│   └── ApiException.php               # Excepciones personalizadas
└── Models/                            # Modelos existentes

routes/
├── api.php                            # Rutas API principales
└── api/
    └── v1.php                         # Rutas v1 específicas

# Documentación
├── API_ARQUITECTURA.md                # Arquitectura general
├── GUIA_POSTMAN.md                    # Cómo probar con Postman
└── CONSUMIR_API_FRONTEND.md           # Ejemplos de consumo desde frontend
```

---

## 🔧 INSTALACIÓN Y CONFIGURACIÓN

### Paso 1: Verificar requisitos
```bash
php -v                    # PHP 8.2+
composer --version        # Composer
node --version            # Node.js
```

### Paso 2: Instalar dependencias
```bash
composer install
npm install
```

### Paso 3: Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

### Paso 4: Configurar base de datos en `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestioncorrespondencia
DB_USERNAME=root
DB_PASSWORD=
```

### Paso 5: Ejecutar migraciones
```bash
php artisan migrate
php artisan db:seed    # Opcional: cargar datos de prueba
```

### Paso 6: Iniciar servidor
```bash
php artisan serve
```

El servidor estará disponible en: **http://localhost:8000**

---

## 📚 DOCUMENTACIÓN DETALLADA

### Leer primero:
1. **[API_ARQUITECTURA.md](./API_ARQUITECTURA.md)** - Arquitectura, endpoints, respuestas
2. **[GUIA_POSTMAN.md](./GUIA_POSTMAN.md)** - Cómo probar con Postman
3. **[CONSUMIR_API_FRONTEND.md](./CONSUMIR_API_FRONTEND.md)** - Ejemplos de consumo

---

## 🌐 ENDPOINTS DISPONIBLES

### Autenticación
```
POST   /api/v1/auth/login                    # Login
POST   /api/v1/auth/register                 # Registrar usuario
POST   /api/v1/auth/logout                   # Cerrar sesión
POST   /api/v1/auth/refresh                  # Renovar token
GET    /api/v1/auth/me                       # Perfil del usuario
POST   /api/v1/auth/change-password          # Cambiar contraseña
```

### Documentos (Correspondencia)
```
GET    /api/v1/documentos                    # Listar (paginado)
POST   /api/v1/documentos                    # Crear
GET    /api/v1/documentos/{id}               # Obtener detalle
PUT    /api/v1/documentos/{id}               # Actualizar
DELETE /api/v1/documentos/{id}               # Eliminar
GET    /api/v1/documentos/{id}/derivaciones  # Derivaciones del documento
GET    /api/v1/documentos/{id}/seguimiento   # Seguimiento del documento
POST   /api/v1/documentos/{id}/cambiar-estado # Cambiar estado
```

### Derivaciones
```
GET    /api/v1/derivaciones                  # Listar
POST   /api/v1/derivaciones                  # Crear
GET    /api/v1/derivaciones/{id}             # Obtener
PUT    /api/v1/derivaciones/{id}             # Actualizar
DELETE /api/v1/derivaciones/{id}             # Eliminar
POST   /api/v1/derivaciones/{id}/recibir     # Marcar como recibida
POST   /api/v1/derivaciones/{id}/rechazar    # Rechazar derivación
POST   /api/v1/derivaciones/{id}/re-derivar  # Re-derivar
```

### Personas, Departamentos, Usuarios
```
GET    /api/v1/personas                      # Listar personas
POST   /api/v1/personas                      # Crear persona
GET    /api/v1/personas/{id}                 # Obtener persona
GET    /api/v1/departamentos                 # Listar departamentos
POST   /api/v1/departamentos                 # Crear departamento
GET    /api/v1/usuarios                      # Listar usuarios
POST   /api/v1/usuarios                      # Crear usuario
```

### Auditoría y Reportes
```
GET    /api/v1/auditorias                    # Listar auditorías
GET    /api/v1/auditorias/usuario/{userId}   # Auditoría por usuario
GET    /api/v1/auditorias/documento/{docId}  # Auditoría por documento
GET    /api/v1/reportes/documentos-por-estado
GET    /api/v1/reportes/documentos-por-urgencia
GET    /api/v1/reportes/derivaciones-pendientes
GET    /api/v1/estadisticas/dashboard        # Dashboard
```

---

## 📝 EJEMPLO DE USO

### Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@system.com",
    "password": "password"
  }'
```

### Listar documentos
```bash
curl -X GET "http://localhost:8000/api/v1/documentos?page=1&per_page=10" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### Crear documento
```bash
curl -X POST http://localhost:8000/api/v1/documentos \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "cite": "CITE-2024-001",
    "asunto": "Nueva solicitud",
    "fecha": "2024-05-20",
    "idTipoDocumento": 1,
    "idEstado": 1,
    "idUrgencia": 1,
    "idRemitente": 1
  }'
```

---

## 🔑 AUTENTICACIÓN

### Obtener Token

1. Haz login:
```bash
curl -X POST http://localhost:8000/api/v1/auth/login
```

2. El servidor retorna un token en la respuesta

3. Usa el token en todas las solicitudes:
```bash
Authorization: Bearer {TOKEN_AQUI}
```

### Token expira después de:
- **1440 minutos (24 horas)** por defecto
- Configurable en `.env` con `SANCTUM_EXPIRATION`

### Renovar token:
```bash
curl -X POST http://localhost:8000/api/v1/auth/refresh \
  -H "Authorization: Bearer {TOKEN_ANTIGUO}"
```

---

## 📊 RESPUESTAS JSON

### Éxito (200 OK)
```json
{
  "success": true,
  "status": 200,
  "message": "Documentos obtenidos correctamente",
  "data": [
    {
      "idDocumento": 1,
      "cite": "CITE-2024-001",
      "asunto": "Solicitud de información"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total": 150,
    "per_page": 10,
    "last_page": 15
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### Éxito (201 Created)
```json
{
  "success": true,
  "status": 201,
  "message": "Documento creado correctamente",
  "data": { "idDocumento": 1, ... },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### Error (422 Validation Error)
```json
{
  "success": false,
  "status": 422,
  "message": "Validation failed",
  "errors": {
    "asunto": ["El campo asunto es requerido"],
    "fecha": ["La fecha debe ser una fecha válida"]
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### Error (401 Unauthorized)
```json
{
  "success": false,
  "status": 401,
  "message": "Token inválido o expirado",
  "errors": [],
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

---

## 🔍 BÚSQUEDA Y FILTRADO

### Búsqueda simple
```
GET /api/v1/documentos?search=solicitud
```

### Ordenamiento
```
GET /api/v1/documentos?sort=fecha              # Ascendente
GET /api/v1/documentos?sort=-fecha             # Descendente
GET /api/v1/documentos?sort=asunto,-fecha      # Múltiple
```

### Filtrado
```
GET /api/v1/documentos?idEstado=1&idUrgencia=2
GET /api/v1/documentos?activo=1
```

### Combinado
```
GET /api/v1/documentos?page=1&per_page=20&sort=-fecha&search=CITE&idEstado=1
```

---

## 🛡️ SEGURIDAD

### Implementado
- ✅ Tokens Sanctum seguros
- ✅ Validación de entrada
- ✅ Protección contra SQL injection (Eloquent)
- ✅ CSRF protection (en web routes)
- ✅ Rate limiting (configurable)
- ✅ Soft deletes (no borrado físico)

### Recomendaciones
- Cambia contraseña por defecto en producción
- Usa HTTPS en producción (no HTTP)
- Configura CORS apropiadamente
- Implementa rate limiting más restrictivo
- Monitorea logs en `storage/logs/`

---

## 📈 PRÓXIMOS PASOS

### Fase 1: Completar (Ya hecho)
- [x] Autenticación con Sanctum
- [x] CRUD completo para todos los módulos
- [x] Búsqueda, filtrado y ordenamiento
- [x] Paginación
- [x] Reportes y estadísticas
- [x] Auditoría

### Fase 2: Optimización
- [ ] Agregar caching (Redis)
- [ ] Rate limiting avanzado
- [ ] Documentación Swagger/OpenAPI
- [ ] Tests unitarios y funcionales
- [ ] GraphQL (opcional)

### Fase 3: Producción
- [ ] CI/CD con GitHub Actions
- [ ] Deploy automático
- [ ] Monitoreo con Sentry
- [ ] Backup automático
- [ ] CDN para assets

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Error: "SQLSTATE Connection refused"
**Solución:** Asegúrate de que MySQL esté corriendo en XAMPP

### Error: "TokenMismatchException"
**Solución:** Limpia las cookies o inicia sesión nuevamente

### Error: "Class not found"
**Solución:** Ejecuta `composer dump-autoload`

### Error: "No application encryption key"
**Solución:** Ejecuta `php artisan key:generate`

---

## 📞 SOPORTE

Para preguntas o problemas:
1. Revisa la documentación en `API_ARQUITECTURA.md`
2. Consulta `GUIA_POSTMAN.md` para testing
3. Verifica los logs en `storage/logs/laravel.log`
4. Revisa las migraciones en `database/migrations/`

---

## 📄 LICENCIA

Este proyecto está bajo licencia MIT.

---

## ✨ Autor

Desarrollado como arquitectura profesional para gestión de correspondencia en Laravel 12.

**Última actualización:** Mayo 2024

