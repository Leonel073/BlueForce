# ✓ GUÍA DE VERIFICACIÓN MANUAL DE RUTAS

## Objetivo
Validar que las rutas auditadas funcionan correctamente en tu ambiente local.

---

## 🔧 PASO 1: Limpiar Cache del Sistema

```bash
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

**Qué hace cada comando:**
- `optimize:clear` - Limpia archivos compilados
- `route:clear` - Limpia cache de rutas
- `config:clear` - Limpia cache de configuración
- `cache:clear` - Limpia todo caché

---

## 📋 PASO 2: Verificar Rutas Definidas

### Opción A: Ver todas las rutas

```bash
php artisan route:list
```

**Buscar específicamente:**
```bash
php artisan route:list | grep -E "(documentos|bandeja|envios)"
```

**Esperado:** Deberías ver:
```
GET|HEAD  admin/documentos ..................... admin.documentos.index
GET|HEAD  mi-bandeja .......................... envios.bandeja
GET|HEAD  envios .............................. envios.index
```

### Opción B: Ver solo rutas admin

```bash
php artisan route:list | grep "^  GET.*admin/"
```

### Opción C: Ver solo rutas de envios

```bash
php artisan route:list | grep "envios"
```

---

## 🗄️ PASO 3: Verificar Base de Datos

### 3.1 Verificar que existen usuarios con roles correctos

```bash
php artisan tinker
```

Luego ejecutar:

```php
// Buscar usuario admin
$admin = App\Models\User::where('idRol', 1)->first();
echo "Admin: " . $admin->name . " (idRol: " . $admin->idRol . ")\n";

// Buscar usuario regular
$user = App\Models\User::where('idRol', '!=', 1)->first();
echo "User: " . $user->name . " (idRol: " . $user->idRol . ")\n";

// Listar todos los usuarios con sus roles
App\Models\User::all(['id', 'name', 'email', 'idRol'])->each(function($u) {
    echo $u->id . " | " . $u->name . " | " . $u->email . " | idRol: " . $u->idRol . "\n";
});

exit;
```

**Esperado:**
```
User: Admin (idRol: 1)
User: Usuario Regular (idRol: 2 o similar)
```

---

## 🌐 PASO 4: Probar Rutas en Navegador

### 4.1 Sin autenticación (deberían redirigir a login)

```
http://localhost/admin/documentos
http://localhost/mi-bandeja
http://localhost/envios
```

**Esperado:** Redirección a `/login`

### 4.2 Como usuario ADMIN

1. Ingresar al sistema con usuario admin
2. Verificar que en el sidebar aparece "Gestión Documental"
3. Clic en "Gestión Documental"
4. URL debe cambiar a `/admin/documentos`
5. Debe cargar la vista correctamente

**Si no funciona:**
```php
// En tinker
auth()->login(App\Models\User::where('idRol', 1)->first());
echo route('admin.documentos.index');
// Debe mostrar: http://localhost/admin/documentos
```

### 4.3 Como usuario REGULAR

1. Ingresar al sistema con usuario regular
2. Verificar que NO aparece "Gestión Documental" en el sidebar
3. Verificar que aparece "Mi Bandeja"
4. Clic en "Mi Bandeja"
5. URL debe cambiar a `/mi-bandeja`
6. Debe cargar la vista correctamente

**Si no funciona:**
```php
// En tinker
auth()->login(App\Models\User::where('idRol', '!=', 1)->first());
echo route('envios.bandeja');
// Debe mostrar: http://localhost/mi-bandeja

echo route('envios.index');
// Debe mostrar: http://localhost/envios
```

---

## 🔍 PASO 5: Verificar Middleware

### 5.1 Confirmar que los middleware existen

```bash
ls -la app/Http/Middleware/
```

**Debe mostrar:**
```
CheckRole.php
IsAdmin.php
IsUser.php
NoCache.php
```

### 5.2 Revisar contenido del middleware admin

```bash
cat app/Http/Middleware/IsAdmin.php
```

**Debe contener algo como:**
```php
public function handle(Request $request, Closure $next)
{
    if (auth()->check() && auth()->user()->idRol == 1) {
        return $next($request);
    }
    return redirect('/');
}
```

### 5.3 Revisar contenido del middleware user

```bash
cat app/Http/Middleware/IsUser.php
```

**Debe contener algo como:**
```php
public function handle(Request $request, Closure $next)
{
    if (auth()->check() && auth()->user()->idRol != 1) {
        return $next($request);
    }
    return redirect('/');
}
```

---

## 📝 PASO 6: Verificar Controladores

### 6.1 Verificar que DocumentoController tiene método adminIndex

```bash
grep -n "public function adminIndex" app/Http/Controllers/DocumentoController.php
```

**Esperado:**
```
Línea: public function adminIndex()
```

### 6.2 Verificar que EnvioController tiene método bandeja e index

```bash
grep -n "public function bandeja\|public function index" app/Http/Controllers/EnvioController.php
```

**Esperado:**
```
Línea: public function bandeja()
Línea: public function index()
```

---

## 🧪 PASO 7: Test Unitario de Rutas

### Opción A: Crear test rápido

```bash
php artisan make:test RoutesTest
```

Editar `tests/Feature/RoutesTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class RoutesTest extends TestCase
{
    public function test_admin_can_access_documentos()
    {
        $admin = User::factory()->create(['idRol' => 1]);
        
        $response = $this->actingAs($admin)
            ->get(route('admin.documentos.index'));
        
        $response->assertStatus(200);
    }

    public function test_user_can_access_bandeja()
    {
        $user = User::factory()->create(['idRol' => 2]);
        
        $response = $this->actingAs($user)
            ->get(route('envios.bandeja'));
        
        $response->assertStatus(200);
    }

    public function test_user_can_access_enviados()
    {
        $user = User::factory()->create(['idRol' => 2]);
        
        $response = $this->actingAs($user)
            ->get(route('envios.index'));
        
        $response->assertStatus(200);
    }

    public function test_unauthenticated_redirects_to_login()
    {
        $response = $this->get(route('admin.documentos.index'));
        
        $response->assertRedirect('/login');
    }
}
```

Ejecutar tests:

```bash
php artisan test tests/Feature/RoutesTest.php
```

---

## 🐛 PASO 8: Verificar Logs de Error

```bash
tail -f storage/logs/laravel.log
```

Luego intenta acceder a las rutas. Si hay errores, aparecerán aquí.

**Buscar específicamente:**
```bash
grep -i "error\|exception" storage/logs/laravel.log | tail -20
```

---

## ✅ CHECKLIST DE VERIFICACIÓN

- [ ] Limpié el cache (`php artisan optimize:clear`)
- [ ] Las rutas aparecen en `php artisan route:list`
- [ ] Los usuarios admin (idRol=1) existen en BD
- [ ] Los usuarios regulares existen en BD
- [ ] Los middleware están en `app/Http/Middleware/`
- [ ] El controlador DocumentoController tiene método `adminIndex`
- [ ] El controlador EnvioController tiene métodos `bandeja` e `index`
- [ ] Como admin, puedo ver "Gestión Documental" en el sidebar
- [ ] Como admin, al clic voy a `/admin/documentos`
- [ ] Como usuario regular, NO veo "Gestión Documental"
- [ ] Como usuario regular, veo "Mi Bandeja" y "Enviadas"
- [ ] Al clic en "Mi Bandeja" voy a `/mi-bandeja`
- [ ] Al clic en "Enviadas" voy a `/envios`
- [ ] Los logs no muestran errores

---

## 🆘 TROUBLESHOOTING

### Problema: Ruta devuelve 404

**Soluciones:**
1. `php artisan route:clear`
2. `php artisan route:cache`
3. Revisar que la ruta existe en `web.php`
4. Revisar que el controlador y método existen

### Problema: Acceso denegado (403)

**Soluciones:**
1. Verificar que el usuario tiene el rol correcto (idRol)
2. Verificar que el middleware está permitiendo el acceso
3. Revisar logs: `tail -f storage/logs/laravel.log`

### Problema: Usuario sin ver el menú

**Soluciones:**
1. Verificar que está autenticado
2. Verificar que su email está verificado
3. Revisar que `Auth::user()->idRol` devuelve valor correcto

### Problema: Ruta funciona en consola pero no en navegador

**Soluciones:**
1. Limpiar caché del navegador
2. Usar incógnito/privado
3. Limpiar cookies: DevTools → Application → Cookies

---

## 📞 COMANDOS ÚTILES

```bash
# Ver todas las rutas
php artisan route:list

# Filtrar rutas específicas
php artisan route:list | grep "documentos"

# Buscar en web.php
grep -n "route(" routes/web.php | grep "documentos"

# Ver estructura de tablas
php artisan migrate:status

# Entrar a PHP interactivo
php artisan tinker

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Ver último error
tail -1 storage/logs/laravel.log

# Limpiar todo
php artisan optimize:clear && php artisan cache:clear
```

---

## 📊 Tabla de Referencia Rápida

| Ruta | URL | Middleware | Rol | Verificación |
|------|-----|-----------|-----|---|
| admin.documentos.index | /admin/documentos | auth, admin | Admin | ✓ |
| envios.bandeja | /mi-bandeja | auth, user | All | ✓ |
| envios.index | /envios | auth, user | All | ✓ |
| correspondencia.index | /correspondencia | auth, user | All | ✓ |
| admin.correspondencia | /admin/correspondencia | auth, admin | Admin | ✓ |

---

**Última actualización:** 24/06/2026  
**Versión:** 1.0  
**Status:** ✅ Verificación completada
