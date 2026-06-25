# 📋 PLAN DETALLADO DE MODIFICACIONES

## FASE 1: CREAR ESTRUCTURA DE CARPETAS

### Carpetas a crear:
```
1. resources/views/admin/correspondencia/
2. resources/views/admin/envios/
3. resources/views/admin/bandeja/
4. resources/views/user/correspondencia/
5. resources/views/user/envios/
```

---

## FASE 2: VISTAS A CREAR

### 2.1 ADMIN - Correspondencia
**Origen → Destino**
- `correspondencia/index.blade.php` → `admin/correspondencia/index.blade.php`
  - Mostrar TODAS las correspondencias
  - Agregar filtros admin
  - Columna "Usuario" adicional
  
- `correspondencia/show.blade.php` → `admin/correspondencia/show.blade.php`
  - Detalle completo
  - Opciones derivación admin

### 2.2 ADMIN - Envíos  
**Origen → Destino**
- `envio/index.blade.php` → `admin/envios/index.blade.php`
  - Mostrar TODOS los envíos
  - Agregar filtros admin
  
- `envio/bandeja.blade.php` → `admin/envios/bandeja.blade.php`
  - Bandeja entrada del admin
  - Documentos recibidos para admin
  
- `envio/derivar.blade.php` → `admin/envios/derivar.blade.php`
  - Formulario derivación admin

### 2.3 ADMIN - Bandeja
**Origen: crear nuevos basado en `user/bandeja/`**
- `admin/bandeja/pendientes.blade.php`
  - Documentos en estado Pendiente (TODOS)
  
- `admin/bandeja/recibidos.blade.php`
  - Documentos en estado Recibido (TODOS)
  
- `admin/bandeja/atendidos.blade.php`
  - Documentos en estado Atendido (TODOS)
  
- `admin/bandeja/archivados.blade.php`
  - Documentos en estado Archivado (TODOS)

### 2.4 USER - Correspondencia
**Origen → Destino**
- `correspondencia/index.blade.php` → `user/correspondencia/index.blade.php`
  - Mostrar solo correspondencias del usuario
  
- `correspondencia/show.blade.php` → `user/correspondencia/show.blade.php`
  - Detalle documento del usuario
  
- `correspondencia/documento-registro.blade.php` → `user/correspondencia/documento-registro.blade.php`
  - Crear nuevo documento

### 2.5 USER - Envíos
**Origen → Destino**
- `envio/index.blade.php` → `user/envios/index.blade.php`
  - Envíos realizados por usuario
  
- `envio/bandeja.blade.php` → `user/envios/bandeja.blade.php`
  - Mi bandeja (documentos recibidos)
  
- `envio/derivar.blade.php` → `user/envios/derivar.blade.php`
  - Derivación documento del usuario

---

## FASE 3: CONTROLADORES A MODIFICAR

### 3.1 CorrespondenciaController
**Archivo:** `app/Http/Controllers/CorrespondenciaController.php`

**Método: index()**
```php
// ANTES (línea ~25):
return view('correspondencia.index', compact('documentos'));

// DESPUÉS:
if (Auth::user()->idRol == 1) {
    // Admin ve todos los documentos
    return view('admin.correspondencia.index', compact('documentos'));
} else {
    // User ve solo sus documentos
    return view('user.correspondencia.index', compact('documentos'));
}
```

**Método: show()**
```php
// ANTES (línea ~199):
return view('correspondencia.show', compact('documento'));

// DESPUÉS:
// *** AGREGAR AQUÍ: Notificación de correspondencia recibida ***
if ($documento->idEstado == $estadoPendiente && $documento->idUsuario == Auth::id()) {
    // Actualizar de Pendiente a Recibido
    $documento->idEstado = $estadoRecibido;
    $documento->save();
}

if (Auth::user()->idRol == 1) {
    return view('admin.correspondencia.show', compact('documento'));
} else {
    return view('user.correspondencia.show', compact('documento'));
}
```

**Líneas a modificar:** ~25, ~199

### 3.2 EnvioController
**Archivo:** `app/Http/Controllers/EnvioController.php`

**Método: index()**
```php
// ANTES (línea ~25):
return view('envio.index', compact('envios'));

// DESPUÉS:
if (Auth::user()->idRol == 1) {
    // Admin ve todos los envíos
    return view('admin.envios.index', compact('envios'));
} else {
    // User ve sus envíos
    return view('user.envios.index', compact('envios'));
}
```

**Método: bandeja()**
```php
// ANTES (línea ~152):
return view('envio.bandeja', compact(...));

// DESPUÉS:
if (Auth::user()->idRol == 1) {
    return view('admin.envios.bandeja', compact(...));
} else {
    return view('user.envios.bandeja', compact(...));
}
```

**Método: derivarForm()**
```php
// ANTES (línea ~236):
return view('envio.derivar', compact(...));

// DESPUÉS:
if (Auth::user()->idRol == 1) {
    return view('admin.envios.derivar', compact(...));
} else {
    return view('user.envios.derivar', compact(...));
}
```

**Líneas a modificar:** ~25, ~152, ~236

### 3.3 BandejaController (Solo user)
**Archivo:** `app/Http/Controllers/BandejaController.php`

**OPCIÓN 1: Mantener igual (solo user)**
```php
// Sin cambios - solo user accede a estos métodos
return view('user.bandeja.pendientes', compact('documentos'));
```

**OPCIÓN 2: Agregar lógica admin (mejor)**
```php
// En cada método:
if (Auth::user()->idRol == 1) {
    // Admin ve TODOS los documentos con ese estado
    $documentos = Correspondencia::whereHas('estado', ...);
} else {
    // User ve solo SUS documentos con ese estado
    $documentos = Correspondencia::where('idUsuario', Auth::id())->whereHas('estado', ...);
}

if (Auth::user()->idRol == 1) {
    return view('admin.bandeja.pendientes', compact('documentos'));
} else {
    return view('user.bandeja.pendientes', compact('documentos'));
}
```

**Recomendación:** OPCIÓN 2 para consistencia

**Líneas a modificar:** ~15, ~30, ~45, ~60 (todos los métodos)

---

## FASE 4: ROUTES A CREAR

**NOTA:** No se agregan nuevas rutas, se reusan las existentes.

Sin embargo, se PODRÍA crear rutas admin explícitas:

```php
// ACTUAL:
Route::get('/correspondencia', [CorrespondenciaController::class, 'index'])
    ->name('correspondencia.index');

// NUEVA RUTA ADMIN (opcional):
Route::get('/admin/correspondencia', [CorrespondenciaController::class, 'index'])
    ->name('admin.correspondencia.index');
```

**DECISIÓN:** Mantener rutas actuales sin cambios. El controlador detecta rol.

---

## FASE 5: SIDEBAR A REVISAR

**Archivo:** `resources/views/components/sidebar_updated.blade.php`

### Enlaces para ADMIN:
```blade
<!-- ANTES: -->
<a href="{{ route('correspondencia.index') }}">Correspondencia</a>

<!-- DESPUÉS: Asegurar que es admin -->
@if(Auth::user()->idRol == 1)
    <a href="{{ route('correspondencia') }}">Correspondencia</a>
    <a href="{{ route('envios') }}">Envíos</a>
@endif
```

### Enlaces para USER:
```blade
<!-- ANTES: -->
<a href="{{ route('correspondencia.index') }}">Correspondencia</a>

<!-- DESPUÉS: Asegurar que es user -->
@if(Auth::user()->idRol != 1)
    <a href="{{ route('correspondencia.index') }}">Correspondencia</a>
    <a href="{{ route('envios.index') }}">Enviados</a>
    <a href="{{ route('envios.bandeja') }}">Mi Bandeja</a>
@endif
```

**Líneas a revisar:** Toda sección de navegación de correspondencia/envíos

---

## 📝 CHECKLIST DE IMPLEMENTACIÓN

### PASO 1: Preparación
- [ ] Crear carpetas admin/correspondencia, admin/envios, admin/bandeja
- [ ] Crear carpetas user/correspondencia, user/envios
- [ ] Respaldar vistas actuales

### PASO 2: Copiar vistas
- [ ] Copiar correspondencia/*.blade.php a admin/correspondencia/
- [ ] Copiar correspondencia/*.blade.php a user/correspondencia/
- [ ] Copiar envio/*.blade.php a admin/envios/
- [ ] Copiar envio/*.blade.php a user/envios/
- [ ] Copiar user/bandeja/*.blade.php a admin/bandeja/

### PASO 3: Modificar controladores
- [ ] Modificar CorrespondenciaController@index
- [ ] Modificar CorrespondenciaController@show + agregar notificación
- [ ] Modificar EnvioController@index
- [ ] Modificar EnvioController@bandeja
- [ ] Modificar EnvioController@derivarForm
- [ ] Modificar BandejaController (todos métodos)

### PASO 4: Verificar
- [ ] Probar acceso admin a /admin/correspondencia
- [ ] Probar acceso user a /correspondencia
- [ ] Probar notificación de correspondencia nueva
- [ ] Verificar Gestión Documental funciona
- [ ] Verificar Dashboard funciona

### PASO 5: Sidebar
- [ ] Revisar sidebar_updated.blade.php
- [ ] Asegurar links correctos para admin
- [ ] Asegurar links correctos para user

---

## ⚠️ CAMBIOS A NO HACER

- ❌ NO modificar modelos Correspondencia, Envio
- ❌ NO modificar middleware
- ❌ NO cambiar lógica de negocio en controladores
- ❌ NO eliminar rutas existentes
- ❌ NO modificar ReporteController o DashboardController
- ❌ NO modificar Gestión Documental

---

## 🎯 RESULTADO ESPERADO

```
Admin accede a /admin/correspondencia
    ↓
CorrespondenciaController@index detecta idRol == 1
    ↓
Ve TODAS las correspondencias
    ↓
Retorna view('admin.correspondencia.index')
    ↓
Vista admin muestra interfaz admin con opciones admin

---

User accede a /correspondencia
    ↓
CorrespondenciaController@index detecta idRol != 1
    ↓
Ve SOLO sus correspondencias
    ↓
Retorna view('user.correspondencia.index')
    ↓
Vista user muestra interfaz user con opciones user
```

---

## 📊 IMPACTO DE CAMBIOS

| Módulo | Cambios | Riesgo | Impacto |
|--------|---------|--------|--------|
| Correspondencia | Vistas separadas | Bajo | Alto |
| Envíos | Vistas separadas | Bajo | Alto |
| Bandeja | Vistas separadas | Bajo | Medio |
| Gestión Documental | Ninguno | Ninguno | Ninguno |
| Dashboard | Ninguno | Ninguno | Ninguno |
| Reportes | Ninguno | Ninguno | Ninguno |
| Usuarios | Ninguno | Ninguno | Ninguno |

---

**Estado:** 🟢 PLAN LISTO PARA EJECUCIÓN
