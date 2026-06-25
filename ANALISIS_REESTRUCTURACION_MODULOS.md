# 📊 ANÁLISIS PRE-REESTRUCTURACIÓN
## Módulos: Correspondencia, Mi Bandeja, Envíos
**Fecha:** 24/06/2026  
**Objetivo:** Separar vistas admin/user manteniendo lógica de negocio

---

## 🔍 ESTADO ACTUAL DEL SISTEMA

### RUTAS DEFINIDAS

#### Sección USER (líneas 74-110)
```
✓ GET  /mi-bandeja                       → EnvioController@bandeja      (envios.bandeja)
✓ GET  /bandeja/pendientes               → BandejaController@pendientes (bandeja.pendientes)
✓ GET  /bandeja/recibidos                → BandejaController@recibidos  (bandeja.recibidos)
✓ GET  /bandeja/atendidos                → BandejaController@atendidos  (bandeja.atendidos)
✓ GET  /bandeja/archivados               → BandejaController@archivados (bandeja.archivados)
✓ GET  /envios                           → EnvioController@index        (envios.index)
✓ GET  /envios/{id}/derivar              → EnvioController@derivarForm  (envios.derivar.form)
✓ POST /envios/{id}/derivar              → EnvioController@derivar      (envios.derivar)
✓ PUT  /envios/{id}/finalizar            → EnvioController@finalizar    (envios.finalizar)
✓ GET  /correspondencia                  → CorrespondenciaController@index (correspondencia.index)
✓ GET  /correspondencia/{id}             → CorrespondenciaController@show  (correspondencia.show)
```

#### Sección ADMIN (líneas 239-241)
```
✓ GET  /admin/correspondencia            → CorrespondenciaController@index (correspondencia)
✓ GET  /admin/correspondencia/{id}       → CorrespondenciaController@show  (correspondencia.show)
✓ POST /admin/correspondencia/{id}/derivar → CorrespondenciaController@derivar (correspondencia.derivar)
```

**PROBLEMA:** Ambas rutas (user y admin) usan el MISMO controlador y comparten lógica de filtrado

---

### CONTROLADORES EXISTENTES

#### 1. CorrespondenciaController (General)
**Ubicación:** `app/Http/Controllers/CorrespondenciaController.php`
**Métodos:**
- `index(Request $request)` - Filtra por usuario (idRol verificado)
- `show($id)` - Muestra detalle documento
- `derivar(Request $request, $id)` - Derivación
- `finalizar($id)` - Finalización

**Lógica:**
```php
// Línea ~30: Filtra documentos según idRol
if (Auth::user()->idRol == 1) {
    // Admin: ve todos
    $documentos = Correspondencia::all();
} else {
    // User: solo sus documentos
    $documentos = Correspondencia::where('idUsuario', Auth::id());
}
```

#### 2. BandejaController (Solo User)
**Ubicación:** `app/Http/Controllers/BandejaController.php`
**Métodos:**
- `pendientes()` - Documentos pendientes del usuario
- `recibidos()` - Documentos recibidos del usuario
- `atendidos()` - Documentos atendidos del usuario
- `archivados()` - Documentos archivados del usuario

**Lógica:** Filtra siempre por `Auth::id()` (usuario actual)

#### 3. EnvioController (Genera Envíos)
**Ubicación:** `app/Http/Controllers/EnvioController.php`
**Métodos:**
- `index(Request $request)` - Envíos realizados por usuario
- `bandeja(Request $request)` - Mi bandeja (recibidos pendientes)
- `derivarForm($id)` - Formulario derivación
- `derivar(Request $request, $id)` - Procesar derivación
- `finalizar($id)` - Finalizar envío

**Lógica:** Filtra envíos según usuario actual

---

### VISTAS EXISTENTES

#### USER (Ruta normal)
```
✓ resources/views/correspondencia/
  ├── documento-registro.blade.php (crear documento)
  ├── index.blade.php (correspondencia listado - SHARED)
  └── show.blade.php (detalle - SHARED)

✓ resources/views/envio/
  ├── bandeja.blade.php (mi bandeja - SHARED)
  ├── derivar.blade.php (derivación - SHARED)
  └── index.blade.php (enviados - SHARED)

✓ resources/views/user/bandeja/
  ├── pendientes.blade.php (específico user)
  ├── recibidos.blade.php (específico user)
  ├── atendidos.blade.php (específico user)
  └── archivados.blade.php (específico user)

✗ FALTA: resources/views/user/correspondencia/
✗ FALTA: resources/views/user/envios/
```

#### ADMIN (Ruta /admin)
```
✗ FALTA COMPLETAMENTE: resources/views/admin/correspondencia/
✗ FALTA COMPLETAMENTE: resources/views/admin/envios/
✗ FALTA COMPLETAMENTE: resources/views/admin/bandeja/

✓ EXISTE: resources/views/admin/documentos/
✓ EXISTE: resources/views/admin/usuarios/
✓ EXISTE: resources/views/admin/departamentos/
✓ EXISTE: resources/views/admin/personas/
✓ EXISTE: resources/views/admin/reportes/
```

---

## 🎯 PROBLEMA IDENTIFICADO

### Problema 1: Rutas ADMIN sin vistas dedicadas
Las rutas admin usan CorrespondenciaController pero NO tienen vistas en `admin/correspondencia/`
- Ruta: `GET /admin/correspondencia` → llama a vista que NO existe
- Resultado: Posible error o redireccionamiento implícito

### Problema 2: Vistas COMPARTIDAS entre Admin y User
Las vistas en `resources/views/correspondencia/` y `resources/views/envio/` se usan TANTO para:
- Rutas user normales (`/correspondencia`)
- Rutas admin (`/admin/correspondencia`)

Esto causa:
- Lógica confusa en vistas
- Difícil de mantener
- Imposible aplicar estilos/permisos diferentes

### Problema 3: BandejaController solo para USER
No existe equivalente ADMIN para bandeja
- Admin no puede ver "bandeja de entrada" como admin
- Solo ve documentos en "Gestión Documental"

### Problema 4: Sin vistas separadas en /user/
Falta estructura clara de vistas user
- Solo existen subdirectorios específicos en `user/bandeja/`
- No hay `user/correspondencia/`, `user/envios/`

---

## 📋 ESTRATEGIA DE REESTRUCTURACIÓN

### FASE 1: Creación de estructura de carpetas

```
Crear:
├── resources/views/admin/correspondencia/
│   ├── index.blade.php          (listado admin)
│   └── show.blade.php           (detalle admin)
├── resources/views/admin/envios/
│   ├── index.blade.php          (listado admin)
│   ├── bandeja.blade.php        (bandeja admin)
│   └── derivar.blade.php        (derivación admin)
├── resources/views/admin/bandeja/
│   ├── pendientes.blade.php     (pendientes admin)
│   ├── recibidos.blade.php      (recibidos admin)
│   ├── atendidos.blade.php      (atendidos admin)
│   └── archivados.blade.php     (archivados admin)
├── resources/views/user/correspondencia/
│   ├── index.blade.php          (listado user)
│   ├── show.blade.php           (detalle user)
│   └── documento-registro.blade.php (crear)
├── resources/views/user/envios/
│   ├── index.blade.php          (listado user)
│   ├── bandeja.blade.php        (bandeja user)
│   └── derivar.blade.php        (derivación user)
```

### FASE 2: Crear controladores separados para ADMIN

#### Opción A (Recomendada): Usar mismos controladores con lógica mejorada
- Mantener `CorrespondenciaController`, `EnvioController`
- Agregar lógica para detectar si es admin o user
- Retornar vista correspondiente según rol

#### Opción B: Crear controladores admin separados
- `Admin/CorrespondenciaController`
- `Admin/EnviosController`
- `Admin/BandejaController`
- Duplica lógica pero es más ordenado

**ELEGIR OPCIÓN A** - Menos cambios, mantiene lógica centralizada

### FASE 3: Modificar retorno de vistas

**Cambio en CorrespondenciaController@index:**
```php
public function index(Request $request)
{
    if (Auth::user()->idRol == 1) {
        // Admin
        return view('admin.correspondencia.index', compact('documentos'));
    } else {
        // User
        return view('user.correspondencia.index', compact('documentos'));
    }
}
```

**Cambio en EnvioController@index:**
```php
public function index(Request $request)
{
    if (Auth::user()->idRol == 1) {
        // Admin
        return view('admin.envios.index', compact('envios'));
    } else {
        // User
        return view('user.envios.index', compact('envios'));
    }
}
```

### FASE 4: Copiar y adaptar vistas

1. Copiar vistas existentes a nuevas ubicaciones admin
2. Adaptar estilos/funcionalidades para admin
3. Limpiar vistas user (mover a `user/`)

### FASE 5: Verificar rutas en sidebars

Asegurar que:
- Admin ve links a `/admin/correspondencia`, `/admin/envios`
- User ve links a `/correspondencia`, `/envios`

### FASE 6: Notificaciones de nueva correspondencia

Agregar lógica en:
- `CorrespondenciaController@show()`
- `EnvioController@bandeja()`

Para actualizar estado de "Pendiente" a "Recibido"

---

## 🚨 RIESGOS IDENTIFICADOS

### Riesgo 1: Rutas duplicadas
- Ruta user: `GET /correspondencia`
- Ruta admin: `GET /admin/correspondencia`
- Mismo controlador, mismo nombre
- **Mitiga:** Asegurar que middleware 'admin' protege ruta admin

### Riesgo 2: Estado compartido en vistas
- Si una vista es usada por admin y user, cambios afectan ambos
- **Mitiga:** Crear vistas SEPARADAS completamente

### Riesgo 3: Lógica en controlador muy compleja
- Detectar rol + retornar vista correcta
- **Mitiga:** Usar método helper `view()` con condicional claro

### Riesgo 4: Datos filtrados incorrectamente
- Admin podría ver solo sus datos en lugar de todos
- **Mitiga:** Verificar lógica de filtrado en controlador

### Riesgo 5: Romper navegación
- Sidebars apuntando a rutas equivocadas
- **Mitiga:** Revisar todos los `route()` en vistas

---

## ✅ PLAN DE ACCIÓN

### ANTES DE MODIFICAR:
1. ✓ Mapear todas las rutas ← YA HECHO
2. ✓ Mapear todos los controladores ← YA HECHO
3. ✓ Mapear todas las vistas ← YA HECHO
4. ✓ Identificar riesgos ← YA HECHO
5. Documentar cambios

### DURANTE MODIFICACIÓN:
1. Crear carpetas de vistas admin
2. Crear carpetas de vistas user
3. Copiar vistas a nuevas ubicaciones
4. Adaptar retorno de vistas en controladores
5. Limpiar vistas antiguas (sin eliminar)
6. Revisar sidebars

### DESPUÉS DE MODIFICACIÓN:
1. Verificar que no hay errores de ruta
2. Probar como admin
3. Probar como user
4. Verificar notificaciones
5. Comprobar Gestión Documental sigue funcionando
6. Comprobar Dashboard sigue funcionando

---

## 📊 RESUMEN DE CAMBIOS

| Componente | Tipo | Cantidad | Acción |
|---|---|---|---|
| Carpetas | Crear | 8 | Nuevas carpetas vistas |
| Vistas | Copiar | ~10 | Copiar a ubicaciones separadas |
| Controladores | Modificar | 3 | Agregar lógica de retorno |
| Rutas | Modificar | 0 | NO se cambian (Ya existen) |
| Sidebars | Revisar | 1 | Verificar enlaces |
| Métodos | Agregar | 2 | Notificación correspondencia nueva |

---

**Estado:** 🟡 ANÁLISIS COMPLETO - LISTO PARA APROBACIÓN
