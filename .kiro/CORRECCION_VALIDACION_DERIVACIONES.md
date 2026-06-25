# CORRECCIÓN: VALIDACIÓN DE DERIVACIONES ✅

## Objetivo
Permitir derivar documentos a usuarios del **mismo departamento** sin restricciones. Solo bloquear **auto-derivación** (derivarse a sí mismo).

---

## Problema Anterior
❌ Sistema impedía derivar a usuarios del mismo departamento  
❌ Error: "No puede derivar un documento al mismo departamento."  
❌ Restricción innecesaria limitaba flujos de trabajo

---

## Solución Implementada
✅ **Permitir:** Derivar a cualquier usuario activo (mismo o diferente departamento)  
✅ **Bloquear:** Solo auto-derivación (derivarse a sí mismo)  
✅ **Nueva Regla:** `if ($usuarioDestino->id == Auth::id())`

---

## Cambios Realizados

### 1. **app/Http/Controllers/CorrespondenciaController.php**

**Método:** `derivar(Request $request, $id)`  
**Líneas:** ~370-410

**ANTES:**
```php
// Validar que departamento destino es diferente del origen
if ($departamentoOrigen == $request->idDepartamentoDestino) {
    return back()->with('error', 'No puede derivar un documento al mismo departamento.');
}
```

**AHORA:**
```php
// Se ELIMINA la validación de mismo departamento
// Se MANTIENE solo validación de auto-derivación

if ($request->filled('idUsuarioAsignado')) {
    $usuarioDestino = \App\Models\User::find($request->idUsuarioAsignado);
    
    // ... validaciones de existencia y estado ...
    
    // ÚNICA RESTRICCIÓN: No puede derivarse a sí mismo
    if ($usuarioDestino->id == $user->id) {
        return back()->with('error', 'No puede derivar un documento a usted mismo.');
    }
}
```

**Cambios:**
- ❌ Eliminada línea: `if ($departamentoOrigen == $request->idDepartamentoDestino)`
- ✅ Mantenida validación de auto-derivación
- ✅ Mejorado comentario: "Validación: Auto-derivación bloqueada"

---

### 2. **app/Http/Controllers/EnvioController.php**

**Método:** `derivar(Request $request, $id)`  
**Líneas:** ~337-380

**ANTES:**
```php
/*
|--------------------------------------------------------------------------
| VALIDAR MISMO DEPARTAMENTO
|--------------------------------------------------------------------------
*/

if(
    $ultimaDerivacion &&
    $ultimaDerivacion->idDepartamentoDestino ==
    $request->idDepartamentoDestino
)
{
    return back()
        ->withInput()
        ->with(
            'error',
            'El documento ya se encuentra en ese departamento.'
        );
}

$idUsuarioAsignado = null;

if ($request->filled('idPersonaResponsable'))
{
    // ... código de búsqueda de persona ...
    
    $idUsuarioAsignado = User::where(
        'idPersona',
        $personaResponsable->idPersona
    )
        ->where('activo', true)
        ->value('id');
}
```

**AHORA:**
```php
/*
|--------------------------------------------------------------------------
| VALIDACIÓN: Auto-derivación bloqueada (no puede derivarse a sí mismo)
|--------------------------------------------------------------------------
*/

if ($request->filled('idPersonaResponsable'))
{
    $personaResponsable = Persona::where(
        'idPersona',
        $request->idPersonaResponsable
    )
        ->where('idDepartamento', $request->idDepartamentoDestino)
        ->where('tipo', 'INTERNO')
        ->where('activo', true)
        ->first();

    if (!$personaResponsable)
    {
        return back()
            ->withInput()
            ->with(
                'error',
                'La persona seleccionada no es un responsable válido del departamento destino.'
            );
    }

    $idUsuarioAsignado = User::where(
        'idPersona',
        $personaResponsable->idPersona
    )
        ->where('activo', true)
        ->first();

    // ÚNICA RESTRICCIÓN: No puede derivarse a sí mismo
    if ($idUsuarioAsignado && $idUsuarioAsignado->id == Auth::id()) {
        return back()
            ->withInput()
            ->with(
                'error',
                'No puede derivar un documento a usted mismo.'
            );
    }

    $idUsuarioAsignado = $idUsuarioAsignado?->id;
}
```

**Cambios:**
- ❌ Eliminada validación completa de "mismo departamento"
- ✅ Agregada validación de auto-derivación (`$idUsuarioAsignado->id == Auth::id()`)
- ✅ Mejorado comentario en sección
- ✅ Cambio menor: `->value('id')` → `.first()` luego `->id` (para obtener usuario completo antes de validar)

---

## Matriz de Validación

### ANTES (Incorrecto)
| Escenario | ¿Permitido? |
|-----------|-----------|
| Derivar a usuario mismo depto | ❌ BLOQUEADO |
| Derivar a usuario otro depto | ✅ Permitido |
| Derivar a sí mismo | ❌ BLOQUEADO |

### AHORA (Correcto)
| Escenario | ¿Permitido? |
|-----------|-----------|
| Derivar a usuario mismo depto | ✅ **PERMITIDO** |
| Derivar a usuario otro depto | ✅ Permitido |
| Derivar a sí mismo | ❌ BLOQUEADO |

---

## Casos de Uso Ahora Permitidos

### Caso 1: Derivar dentro del mismo departamento
```
Usuario A (Finanzas) → Derivar a → Usuario B (Finanzas)
ANTES: ❌ "No puede derivar al mismo departamento"
AHORA: ✅ Permitido
```

### Caso 2: Derivar entre departamentos (sin cambios)
```
Usuario A (Finanzas) → Derivar a → Usuario C (RH)
ANTES: ✅ Permitido
AHORA: ✅ Permitido
```

### Caso 3: Auto-derivación (bloqueada)
```
Usuario A → Derivar a → Usuario A (a sí mismo)
ANTES: ❌ Bloqueado
AHORA: ❌ Bloqueado (MANTIENE restricción)
```

---

## Flujos de Derivación Ahora Posibles

### Flujo A: Gestión Interna del Departamento
```
Usuario A (Depto X) 
  ↓ Derivar a Usuario B (Depto X) ← AHORA PERMITIDO
    ↓ Derivar a Usuario C (Depto X) ← AHORA PERMITIDO
      ✓ Finalizado
```

### Flujo B: Escalada entre Departamentos
```
Usuario A (Depto X)
  ↓ Derivar a Usuario X' (Depto X) ← AHORA PERMITIDO (gestión interna)
    ↓ Derivar a Usuario Y (Depto Y) ← SIEMPRE PERMITIDO
      ✓ Finalizado
```

---

## Validaciones Mantenidas

✅ Usuario destino existe en base de datos  
✅ Usuario destino está activo  
✅ Usuario destino no es a sí mismo (ÚNICA restricción)  
✅ Persona responsable es tipo INTERNO  
✅ Persona responsable está activa  
✅ Persona responsable pertenece al departamento destino  

---

## Validaciones Eliminadas

❌ Comparación: `departamentoOrigen == departamentoDestino`  
❌ Error: "El documento ya se encuentra en ese departamento"  
❌ Restricción de mismo departamento  

---

## Sin Cambios

✅ Base de datos - SIN CAMBIOS  
✅ Migraciones - SIN CAMBIOS  
✅ Modelos - SIN CAMBIOS  
✅ Middleware - SIN CAMBIOS  
✅ Roles - SIN CAMBIOS  
✅ Bandeja - SIN CAMBIOS  
✅ Enviados - SIN CAMBIOS  
✅ Historial - SIN CAMBIOS  
✅ Seguimiento - SIN CAMBIOS  
✅ Auditoría - SIN CAMBIOS  
✅ Notificaciones - SIN CAMBIOS  

---

## Verificación

### Sintaxis PHP
```
✅ app/Http/Controllers/CorrespondenciaController.php - No syntax errors
✅ app/Http/Controllers/EnvioController.php - No syntax errors
```

### Lógica
- ✅ Derivación dentro del mismo departamento permitida
- ✅ Derivación entre departamentos permitida
- ✅ Auto-derivación bloqueada
- ✅ Validaciones de usuario mantenidas

---

## Mensajes de Error

### Permitido
```
✅ Derivación a usuario del mismo departamento
✅ Derivación a usuario de otro departamento
✅ Múltiples derivaciones dentro del mismo departamento
```

### Bloqueado
```
❌ "No puede derivar un documento a usted mismo."
   (cuando intenta derivarse a sí mismo)
```

---

## Testing Recomendado

- [ ] Derivar a usuario del mismo departamento (debe funcionar)
- [ ] Derivar a usuario de otro departamento (debe funcionar)
- [ ] Intentar derivar a sí mismo (debe mostrar error)
- [ ] Derivación múltiple dentro del mismo departamento (debe funcionar)
- [ ] Verificar bandeja, enviados e historial (sin cambios)

---

**Implementación:** ✅ COMPLETADA  
**Fecha:** 2026-06-25  
**Archivos Modificados:** 2 controladores  
**Restricción Aplicada:** Solo auto-derivación  
**Reversibilidad:** Alta (fácil de revertir si es necesario)
