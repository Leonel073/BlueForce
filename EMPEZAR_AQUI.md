# ✅ CHECKLIST FINAL - CÓMO EMPEZAR A USAR LA API

## 🚀 PASOS FINALES ANTES DE USAR

### 1️⃣ VERIFICAR CONFIGURACIÓN BÁSICA

```bash
# Verifica que PHP esté corriendo
php -v

# Verifica que composer esté instalado
composer --version

# Verifica Node.js
node --version
```

### 2️⃣ INICIAR XAMPP

1. Abre XAMPP Control Panel
2. Haz click en "Start" para Apache y MySQL
3. Espera a que ambos estén corriendo (verdes)

**Verificar conexión:**
```bash
# Prueba que MySQL esté en puerto 3306
telnet localhost 3306
```

### 3️⃣ CONFIGURAR BASE DE DATOS

1. Abre phpMyAdmin: http://localhost/phpmyadmin
2. Crea una base de datos llamada `gestioncorrespondencia`
3. En el archivo `.env` verifica:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestioncorrespondencia
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ EJECUTAR MIGRACIONES

```bash
# En la carpeta del proyecto
cd c:\xampp\htdocs\GestionCorrespondencia

# Ejecuta las migraciones
php artisan migrate

# Opcional: Carga datos de prueba
php artisan db:seed
```

Si la tabla `PERSONA` no existe, crea una migración manual o actualiza desde web interface.

### 5️⃣ CREAR USUARIO DE PRUEBA

**Opción 1: Mediante Tinker**
```bash
php artisan tinker
```

```php
// Dentro de tinker
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@system.com';
$user->password = Hash::make('password123');
$user->idPersona = 1;
$user->idRol = 1;
$user->activo = 1;
$user->save();
exit;
```

**Opción 2: SQL directo**
```sql
INSERT INTO users 
(name, email, password, idPersona, idRol, activo, created_at) 
VALUES 
('Admin', 'admin@system.com', '$2y$12$...', 1, 1, 1, NOW());
```

### 6️⃣ INICIAR EL SERVIDOR LARAVEL

```bash
# En la carpeta del proyecto
php artisan serve

# El servidor estará disponible en http://localhost:8000
```

**Verifica que esté corriendo:**
- Abre en navegador: http://localhost:8000
- Deberías ver la página de bienvenida de Laravel

### 7️⃣ PROBAR PRIMER ENDPOINT

**En terminal (PowerShell):**
```bash
$headers = @{
    "Content-Type" = "application/json"
}
$body = @{
    email = "admin@system.com"
    password = "password123"
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri "http://localhost:8000/api/v1/auth/login" `
    -Method Post `
    -Headers $headers `
    -Body $body

$response.Content | ConvertFrom-Json | ConvertTo-Json
```

**Esperado (200 OK):**
```json
{
  "success": true,
  "status": 200,
  "message": "Autenticación exitosa",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "type": "Bearer",
    "user": {...}
  }
}
```

---

## 📱 USAR POSTMAN

### Instalación
1. Descarga: https://www.postman.com/downloads/
2. Instala la versión para tu SO
3. Abre Postman

### Configuración Rápida
1. Click "New" → "Request"
2. Selecciona "POST"
3. URL: `http://localhost:8000/api/v1/auth/login`
4. Headers:
   ```
   Content-Type: application/json
   Accept: application/json
   ```
5. Body (raw JSON):
   ```json
   {
     "email": "admin@system.com",
     "password": "password123"
   }
   ```
6. Click "Send"
7. Guarda el token de la respuesta

### Probar Otros Endpoints
Con el token, prueba:
```
GET http://localhost:8000/api/v1/auth/me
Headers: Authorization: Bearer {TOKEN}
```

---

## 🐛 ERRORES Y SOLUCIONES

### Error: "Connection refused"
**Solución:**
- Verifica que MySQL esté corriendo en XAMPP
- Verifica el puerto 3306 esté disponible

### Error: "SQLSTATE syntax error"
**Solución:**
- Ejecuta `php artisan migrate` nuevamente
- Verifica que la tabla PERSONA exista

### Error: "500 Internal Server Error"
**Solución:**
- Revisa los logs: `storage/logs/laravel.log`
- Ejecuta `php artisan cache:clear`
- Ejecuta `php artisan config:cache`

### Error: "Class not found"
**Solución:**
```bash
composer dump-autoload
php artisan cache:clear
```

### Error: "No application encryption key"
**Solución:**
```bash
php artisan key:generate
```

---

## 📚 DOCUMENTACIÓN DISPONIBLE

Dentro del proyecto encontrarás:

1. **[API_ARQUITECTURA.md](./API_ARQUITECTURA.md)**
   - Arquitectura general
   - Todos los endpoints
   - Respuestas JSON
   - Códigos HTTP

2. **[GUIA_POSTMAN.md](./GUIA_POSTMAN.md)**
   - Cómo usar Postman
   - Flujo de pruebas paso a paso
   - Ejemplos de todos los endpoints

3. **[CONSUMIR_API_FRONTEND.md](./CONSUMIR_API_FRONTEND.md)**
   - Ejemplos con Vanilla JS
   - Ejemplos con Axios
   - Ejemplos con Vue 3
   - Ejemplos con React

4. **[README_API.md](./README_API.md)**
   - Resumen general
   - Instalación
   - Estructura de carpetas

---

## 🎯 PRÓXIMOS PASOS

### Paso 1: Familiarizarse con los endpoints
- Lee [API_ARQUITECTURA.md](./API_ARQUITECTURA.md)
- Prueba los 5 primeros endpoints en Postman

### Paso 2: Crear un documento
```bash
POST /api/v1/documentos
Body:
{
  "cite": "CITE-2024-001",
  "asunto": "Solicitud de prueba",
  "fecha": "2024-05-20",
  "idTipoDocumento": 1,
  "idEstado": 1,
  "idUrgencia": 1,
  "idRemitente": 1
}
```

### Paso 3: Crear una derivación
```bash
POST /api/v1/derivaciones
Body:
{
  "idDocumento": 1,
  "idDepartamentoOrigen": 1,
  "idDepartamentoDestino": 2,
  "idUsuarioAsignado": 2,
  "instruccion": "Revisar y aprobar"
}
```

### Paso 4: Consultar reportes
```bash
GET /api/v1/reportes/documentos-por-estado
GET /api/v1/estadisticas/dashboard
```

### Paso 5: Crear frontend
- Lee [CONSUMIR_API_FRONTEND.md](./CONSUMIR_API_FRONTEND.md)
- Elige tu framework preferido
- Implementa los servicios

---

## 💡 TIPS PROFESIONALES

### 1. Usa variables de entorno en Postman
- Ahorras tiempo copiando URLs
- Facilita cambiar entre dev/prod

### 2. Guarda tus colecciones
- Exporta desde Postman
- Comparte con el equipo
- Control de versiones en Git

### 3. Usa Thunder Client (alternativa)
- Alternativa ligera a Postman
- Integrada en VS Code
- Menos recursos

### 4. Monitorea los logs
```bash
# Terminal en otra ventana
tail -f storage/logs/laravel.log
```

### 5. Usa eventos observadores (Observers)
- La auditoría funciona automáticamente
- Los cambios se registran solos

---

## 📊 ESTRUCTURA DE RESPUESTAS

### Éxito
```json
{
  "success": true,
  "status": 200,
  "message": "Mensaje descriptivo",
  "data": {...},
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### Lista Paginada
```json
{
  "success": true,
  "status": 200,
  "message": "Documentos obtenidos correctamente",
  "data": [...],
  "pagination": {
    "current_page": 1,
    "total": 150,
    "per_page": 10,
    "last_page": 15
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

### Error
```json
{
  "success": false,
  "status": 422,
  "message": "Validation failed",
  "errors": {
    "campo": ["Error message"]
  },
  "timestamp": "2024-05-20T10:30:45.123456Z"
}
```

---

## 🔐 SEGURIDAD

### Antes de producción:
- [ ] Cambiar contraseña por defecto
- [ ] Configurar HTTPS
- [ ] Activar CORS restrictivo
- [ ] Implementar rate limiting
- [ ] Validar todas las entradas
- [ ] Usar variables de entorno
- [ ] Revisar logs regularmente
- [ ] Backups automáticos

### Configuración de seguridad:
```bash
# Ocultar versión de Laravel
APP_DEBUG=false

# CORS en .env o config/cors.php
CORS_ALLOWED_ORIGINS=https://app.example.com

# Rate limiting
RATE_LIMIT=60  # Solicitudes por minuto
```

---

## ✨ ¡LISTO!

Ya tienes una **API REST profesional** con:
- ✅ 60+ endpoints
- ✅ Autenticación con Sanctum
- ✅ Validaciones completas
- ✅ Búsqueda y filtrado
- ✅ Paginación
- ✅ Reportes y estadísticas
- ✅ Documentación completa
- ✅ Ejemplos de uso

**Consulta la documentación y comienza a integrar con tu frontend.**

---

## 📞 PREGUNTAS FRECUENTES

**P: ¿Dónde está la colección Postman?**
R: Crea una nueva en Postman usando los ejemplos en GUIA_POSTMAN.md

**P: ¿Puedo usar la API sin frontend?**
R: Sí, prueba directamente con curl o Postman

**P: ¿Cómo agrego más filtros?**
R: Actualiza los arrays `$filterableColumns` en los controladores

**P: ¿Se puede cambiar la versión de API?**
R: Copia routes/api/v1.php a routes/api/v2.php y personaliza

**P: ¿Cómo agregar más endpoints?**
R: Sigue el mismo patrón en BaseController, agrega ruta, crea método

---

**¡Éxito con tu API!** 🚀

