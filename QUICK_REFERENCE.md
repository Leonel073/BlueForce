# QUICK REFERENCE - GestiónCorrespondencia
## Guía Rápida de Rutas, Componentes y Validaciones

---

## 🚀 RUTAS PRINCIPALES

### ADMIN ROUTES (Todos requieren idRol = 1)
```
GET  /admin/dashboard                        → DashboardController@index
GET  /admin/documentos                       → DocumentoController@adminIndex (GESTIÓN DOCUMENTAL)
GET  /admin/documentos/{id}                  → DocumentoController@detalle
GET  /admin/documentos/{id}/edit             → DocumentoController@edit
PUT  /admin/documentos/{id}                  → DocumentoController@update

ADMIN EXCLUSIVE OPERACIONES:
GET  /admin/bandeja                          → EnvioController@bandeja
GET  /admin/bandeja/{id}                     → EnvioController@derivarForm
POST /admin/bandeja/{id}/derivar             → EnvioController@derivar

GET  /admin/envios                           → EnvioController@index
PUT  /admin/envios/{id}/finalizar            → EnvioController@finalizar

POST /admin/correspondencia/{id}/derivar     → CorrespondenciaController@derivar
```

### USER ROUTES (Todos requieren idRol ≠ 1)
```
GET  /user/dashboard                         → UserDashboardController@index
GET  /correspondencia                        → CorrespondenciaController@index
GET  /correspondencia/{id}                   → CorrespondenciaController@show

GET  /mi-bandeja                             → EnvioController@bandeja
GET  /envios                                 → EnvioController@index
GET  /envios/{id}/derivar                    → EnvioController@derivarForm
POST /envios/{id}/derivar                    → EnvioController@derivar
PUT  /envios/{id}/finalizar                  → EnvioController@finalizar

GET  /recibidas                              → RecibidasController@index
POST /recibidas/{id}/recibir                 → RecibidasController@recibir
POST /recibidas/{id}/atender                 → RecibidasController@atender
POST /recibidas/{id}/archivar                → RecibidasController@archivar
```

---

## 🎨 COMPONENTES GLOBALES

### Modales
```php
{{-- Incluido automáticamente en app.blade.php --}}
@include('components.modals')
```

**Funciones Disponibles:**
```javascript
// Confirmación genérica
showConfirmModal('¿Está seguro?', function() {
    // Callback cuando confirma
    console.log('Confirmado');
});

// Logout específico
showLogoutConfirm();

// Notificación
showNotificationModal('Título', 'Mensaje de notificación');
```

### Alertas
```php
{{-- Incluido automáticamente en app.blade.php --}}
@include('components.alerts')
```

**Flash Messages (desde controlador):**
```php
// Éxito
return back()->with('success', 'Operación completada');

// Error
return back()->with('error', 'Ocurrió un error');

// Advertencia
return back()->with('warning', 'Advertencia importante');

// Información
return back()->with('info', 'Información relevante');
```

### Sidebar Actualizado
```php
{{-- Incluido en app.blade.php --}}
@include('components.sidebar_updated')
```

**Lógica:**
```php
@if(Auth::user()->idRol == 1)
    // ADMIN: Ve módulos administrativos
@else
    // USER: Ve módulos operacionales
@endif
```

---

## 🔐 VALIDACIONES DE SEGURIDAD

### Patrón: Validar Responsable Actual

**Ubicaciones Implementadas:**
1. `CorrespondenciaController::derivar()` - Línea 310
2. `CorrespondenciaController::finalizar()` - Línea 455
3. `RecibidasController::recibir()` - Línea 108
4. `RecibidasController::atender()` - Línea 177
5. `RecibidasController::archivar()` - Línea 240
6. `EnvioController::derivar()` - Línea 285 (especial, valida departamento)
7. `EnvioController::finalizar()` - Línea 500

**Código Standard:**
```php
$user = Auth::user();

// Solo validar si NO es admin
if ($user->idRol != 1) {
    // Obtener última derivación
    $ultimaDerivacion = Derivacion::where('idDocumento', $id)
        ->orderByDesc('orden')
        ->first();

    // Verificar que tiene responsable
    if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
        throw new Exception('No tiene permisos para esta acción');
    }

    // Verificar que ES el responsable actual
    if ($ultimaDerivacion->idUsuarioAsignado != $user->id) {
        throw new Exception('No es el responsable actual');
    }
}

// Proceder con la operación
```

### Qué Está Protegido

| Acción | Protected | Validación |
|--------|-----------|-----------|
| Derivar documento | ✅ | Responsable actual O Admin |
| Finalizar correspondencia | ✅ | Solo Admin |
| Recibir documento | ✅ | Responsable actual O Admin |
| Atender documento | ✅ | Responsable actual O Admin |
| Archivar documento | ✅ | Responsable actual O Admin |
| Finalizar envío | ✅ | Responsable actual O Admin |

---

## 🛠️ MIDDLEWARE ALIASES

```php
// Registrados en bootstrap/app.php

'admin' => \App\Http\Middleware\IsAdmin::class,
'user'  => \App\Http\Middleware\IsUser::class,
'nocache' => \App\Http\Middleware\NoCache::class,
'role' => \App\Http\Middleware\CheckRole::class,
```

**Uso en Rutas:**
```php
// Solo admin
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () { ... });

// Solo usuario normal
Route::middleware(['auth', 'verified', 'user'])
    ->group(function () { ... });
```

---

## 📊 TABLA DE PERMISOS

| Operación | Admin | User (Responsable) | User (Otros) |
|-----------|-------|-------------------|-------------|
| Ver Gestión Documental | ✅ | ❌ | ❌ |
| Ver Mi Correspondencia | ❌ | ✅ | ✅ |
| Derivar Documento | ✅ | ✅* | ❌ |
| Recibir Documento | ✅ | ✅* | ❌ |
| Atender Documento | ✅ | ✅* | ❌ |
| Archivar Documento | ✅ | ✅* | ❌ |
| Finalizar Documento | ✅** | ❌ | ❌ |
| Acceder Auditoría | ✅ | ❌ | ❌ |

**Notas:**
- `*` = Solo si es responsable actual
- `**` = En módulo correspondencia admin

---

## 🎯 FLUJOS TÍPICOS

### Flujo: Crear y Derivar Documento (Usuario)
```
1. Usuario A: GET /correspondencia → Listado
2. Usuario A: POST /documentos → Crear documento
3. Usuario A: GET /documentos/{id} → Ver detalles
4. Usuario A: GET /envios/{id}/derivar → Formulario
5. Usuario A: POST /envios/{id}/derivar → Enviar a Depto B
6. Usuario B recibe notificación (en desarrollo)
7. Usuario B: GET /mi-bandeja → Ve documento pendiente
8. Usuario B: POST /recibidas/{id}/recibir → Marca como recibido
9. Usuario B: POST /recibidas/{id}/atender → Marca como atendido
10. Usuario B: POST /recibidas/{id}/archivar → Archiva
```

### Flujo: Supervisar Documentos (Admin)
```
1. Admin: GET /admin/dashboard → Dashboard con stats
2. Admin: GET /admin/documentos → GESTIÓN DOCUMENTAL (CONTROL CENTER)
3. Admin: GET /admin/documentos/{id} → Ver detalles
4. Admin: GET /admin/documentos/{id}/edit → Editar datos
5. Admin: PUT /admin/documentos/{id} → Guardar (con modal de confirmación)
6. Admin: POST /admin/correspondencia/{id}/derivar → Derivar si necesario
```

---

## 🔍 DEBUGGING

### Log de Intentos No Autorizados
```
Ubicación: storage/logs/laravel.log
Búsqueda: "Acceso denegado a sección admin"
Incluye: user_id, user_name, user_role, path, ip
```

### Verificar Responsable de Documento
```php
$doc = Correspondencia::find($id);
$ultimaDer = $doc->derivaciones()->orderByDesc('orden')->first();
$responsable = $ultimaDer->usuarioAsignado; // Usuario responsable actual
```

### Debug en Blade
```blade
<!-- Ver usuario actual -->
{{ Auth::user()->name }} ({{ Auth::user()->idRol }})

<!-- Ver si es admin -->
{{ Auth::user()->idRol == 1 ? 'Admin' : 'Usuario' }}

<!-- Ver ruta actual -->
{{ request()->route()->getName() }}
```

---

## 📱 RESPONSIVE BREAKPOINTS

```css
/* Desktop: 992px+ */
- Sidebar: Visible y colapsable
- Tablas: Scroll horizontal controlado
- Modales: Centrados, ancho completo

/* Tablet: 768px - 991px */
- Sidebar: Overlay drawer
- Tablas: Scroll horizontal con controles
- Modales: Adaptados a pantalla

/* Mobile: < 768px */
- Sidebar: Hamburguesa y overlay
- Tablas: Scroll horizontal con thumb
- Modales: Margen reducido
- Alertas: Ancho adaptado
```

---

## 🚨 ERRORES COMUNES

### ❌ "No tiene permisos para esta acción"
**Causa:** No es responsable actual del documento
**Solución:** Verificar que la última derivación apunta a este usuario

### ❌ "No es el responsable actual"
**Causa:** El documento fue derivado a otro usuario
**Solución:** El responsable actual debe realizar la acción

### ❌ "Debe usar el panel de administración"
**Causa:** Admin intentó acceder a ruta de usuario
**Solución:** Usar rutas admin (`/admin/...`) en lugar de rutas de usuario

### ❌ "Acceso denegado"
**Causa:** Usuario normal intentó acceder a sección admin
**Solución:** Verificar rol del usuario (idRol = 1 para admin)

---

## 📋 CHECKLISTS

### Crear Nueva Validación
- [ ] Obtener usuario actual: `$user = Auth::user()`
- [ ] Verificar si NO es admin: `if ($user->idRol != 1)`
- [ ] Obtener última derivación: `Derivacion::orderByDesc('orden')->first()`
- [ ] Validar responsable: `if (...->idUsuarioAsignado != $user->id)`
- [ ] Lanzar excepción o retornar error
- [ ] Usar try-catch para manejar excepción
- [ ] Retornar mensaje claro al usuario

### Crear Nueva Ruta Admin
- [ ] Agregar ruta en `routes/web.php` (dentro de grupo admin)
- [ ] Aplicar middleware: `middleware(['auth', 'verified', 'admin'])`
- [ ] Verificar nombre: `.name('admin.xxxx')`
- [ ] Crear método en controlador
- [ ] Crear vista o retornar JSON
- [ ] Probar acceso como admin
- [ ] Verificar denegación para usuario normal

### Crear Nuevo Modal
- [ ] Agregar HTML en `components/modals.blade.php`
- [ ] Crear función JavaScript: `showXxxModal(...)`
- [ ] Documental función en comentario
- [ ] Incluir en `app.blade.php` (ya incluye)
- [ ] Probar función en consola
- [ ] Integrar en vista necesaria

---

## 📞 CONTACTO/SOPORTE

**Documentación completa:**
- `AUDIT_FINAL_VERIFICATION.md` - Auditoría completa
- `IMPLEMENTACION_RESUMIDA.md` - Resumen ejecutivo
- `QUICK_REFERENCE.md` - Este archivo

**Archivos clave:**
- Rutas: `routes/web.php`
- Componentes: `resources/views/components/`
- Controllers: `app/Http/Controllers/`
- Middleware: `app/Http/Middleware/`

---

**Última actualización:** 24 de Junio, 2026  
**Versión:** 1.0.0

