# 🚀 Guía Rápida - Trabajo con Cargos

## 📦 Instalación / Rollback

```bash
# Ejecutar migraciones
php artisan migrate:fresh --seed

# Ver historial
php artisan migrate:status

# Rollback (si es necesario)
php artisan migrate:rollback
```

---

## 💻 Uso en Código

### En Controladores

```php
use App\Models\Persona;
use App\Models\Cargo;

// Obtener persona con su cargo
$persona = Persona::with('cargo')->find(1);
echo $persona->cargo->nombre;  // "Director General"

// Crear persona con cargo
$persona = Persona::create([
    'nombre' => 'Juan Pérez',
    'ci' => '1234567',
    'telefono_celular' => '+591 71234567',
    'tipo' => 'INTERNO',
    'idDepartamento' => 1,
    'idCargo' => 3,  // FK a tabla CARGO
]);

// Obtener todas las personas de un cargo
$cargId = Cargo::where('nombre', 'Recepcionista')->first()->idCargo;
$recepcionistas = Persona::where('idCargo', $cargoId)->get();

// O más elegante con Eloquent
$recepcionistas = Persona::whereHas('cargo', function($query) {
    $query->where('nombre', 'Recepcionista');
})->get();

// Contar por cargo
$estadisticas = Cargo::activos()
    ->with('personas')
    ->get()
    ->map(function($cargo) {
        return [
            'cargo' => $cargo->nombre,
            'nivel' => $cargo->nivel,
            'cantidad' => $cargo->personas->count(),
        ];
    });
```

### En Vistas Blade

```blade
<!-- Mostrar cargo de persona -->
<p>Cargo: {{ $persona->cargo?->nombre ?? 'Sin asignar' }}</p>

<!-- Listar personas por cargo -->
@forelse($cargo->personas as $persona)
    <li>{{ $persona->nombre }} - {{ $persona->departamento->nombre }}</li>
@empty
    <li>No hay personal asignado</li>
@endforelse

<!-- Seleccionar en formulario -->
<select name="idCargo">
    <option value="">-- Sin cargo --</option>
    @foreach(Cargo::activos()->get() as $cargo)
        <option value="{{ $cargo->idCargo }}">
            {{ $cargo->nombre }} ({{ $cargo->nivel }})
        </option>
    @endforeach
</select>
```

---

## 🔍 Consultas Útiles

### SQL Directo

```sql
-- Personas con sus cargos
SELECT p.nombre, c.nombre AS cargo, d.nombre AS departamento
FROM PERSONA p
LEFT JOIN CARGO c ON p.idCargo = c.idCargo
LEFT JOIN DEPARTAMENTO d ON p.idDepartamento = d.idDepartamento
WHERE p.activo = 1;

-- Cargos sin personal
SELECT c.* 
FROM CARGO c
LEFT JOIN PERSONA p ON c.idCargo = p.idCargo
WHERE p.idPersona IS NULL AND c.activo = 1;

-- Distribución por nivel
SELECT nivel, COUNT(*) as cantidad
FROM CARGO
WHERE activo = 1
GROUP BY nivel;

-- Personal por departamento y cargo
SELECT 
    d.nombre AS departamento,
    c.nombre AS cargo,
    COUNT(p.idPersona) AS cantidad
FROM DEPARTAMENTO d
LEFT JOIN PERSONA p ON d.idDepartamento = p.idDepartamento
LEFT JOIN CARGO c ON p.idCargo = c.idCargo
WHERE d.activo = 1 AND p.activo = 1
GROUP BY d.idDepartamento, c.idCargo
ORDER BY d.nombre, c.nombre;
```

---

## 📋 Request Validation

```php
// app/Http/Requests/StorePersonaRequest.php

public function rules(): array {
    return [
        'nombre' => 'required|string|max:200',
        'ci' => 'required|string|unique:PERSONA',
        'telefono_celular' => 'required|string|max:20',
        'idDepartamento' => 'required|exists:DEPARTAMENTO,idDepartamento',
        'idCargo' => 'nullable|exists:CARGO,idCargo',  // ← FK Cargo
    ];
}

public function messages(): array {
    return [
        'idCargo.exists' => 'El cargo seleccionado no existe.',
    ];
}
```

---

## 🎯 Casos de Uso Comunes

### 1. Cambiar cargo a persona

```php
$persona = Persona::find(1);
$nuevoCargoId = 5;  // Nuevo cargo

$persona->update(['idCargo' => $nuevoCargoId]);

// Con validación
if (Cargo::where('idCargo', $nuevoCargoId)->where('activo', 1)->exists()) {
    $persona->update(['idCargo' => $nuevoCargoId]);
}
```

### 2. Reportar personas sin cargo

```php
$sinCargo = Persona::where('idCargo', null)
    ->where('activo', 1)
    ->with('departamento')
    ->get();

// Mostrar en Excel
return $sinCargo->map(function($p) {
    return [
        'nombre' => $p->nombre,
        'departamento' => $p->departamento->nombre,
        'ci' => $p->ci,
    ];
});
```

### 3. Agregar nuevo cargo

```php
$nuevoCargo = Cargo::create([
    'nombre' => 'Coordinador Académico',
    'descripcion' => 'Responsable de programas académicos',
    'nivel' => 'Académico',
    'activo' => true,
]);

// Verificar
echo $nuevoCargo->idCargo;  // ID asignado automáticamente
```

### 4. Desactivar cargo (sin eliminar datos)

```php
// Usar onDelete('set null') - persona queda sin cargo
$cargo = Cargo::find(5);
$cargo->update(['activo' => false]);

// Las personas del cargo no se eliminan
// Quedan con idCargo = 5 pero cargo->activo = false
```

---

## ⚠️ Errores Comunes

### ❌ Error: FK Constraint Failed
```
SQLSTATE[23000]: Integrity constraint violation: 
1452 Cannot add or update a child row
```
**Solución**: Asegurar que idCargo existe en tabla CARGO

```php
// Verificar antes de insertar
$cargoExists = Cargo::find($request->idCargo);
if (!$cargoExists) {
    throw new \Exception("Cargo no existe");
}
```

### ❌ Error: Unique Constraint
```
SQLSTATE[23000]: Integrity constraint violation: 
1062 Duplicate entry
```
**Solución**: El nombre del cargo debe ser único

```php
// Validar
$request->validate([
    'nombre' => 'unique:CARGO,nombre',
]);
```

---

## 📊 Relación Completa

```
1 CARGO ←→ Muchas PERSONAS
1 PERSONA ←→ 1 CARGO

Ejemplo:
- CARGO #1: "Recepcionista"
  - PERSONA #1: María López
  - PERSONA #2: Carlos Mendez
  - PERSONA #3: Verónica Torrez

- CARGO #2: "Director General"
  - PERSONA #4: Dr. Juan Pérez
```

---

## 🔑 Claves Foráneas (FK)

| Tabla | Campo | Referencia | Acción |
|-------|-------|-----------|--------|
| PERSONA | idCargo | CARGO.idCargo | set null |
| PERSONA | idDepartamento | DEPARTAMENTO.idDepartamento | set null |
| DEPARTAMENTO | idPersonaEncargada | PERSONA.idPersona | set null |

---

## 📅 Versionado

**Versión**: 1.0  
**Última actualización**: 2026-05-13  
**Compatible con**: Laravel 12+

---

## 🆘 Soporte Rápido

**¿Cómo ver todos los cargos?**
```php
$cargos = Cargo::activos()->get();
```

**¿Cómo asignar cargo a persona?**
```php
$persona->update(['idCargo' => $cargoId]);
```

**¿Cómo obtener personas de un cargo?**
```php
$cargo->personas()->get();
```

**¿Cómo remover cargo de persona?**
```php
$persona->update(['idCargo' => null]);
```

---

Más información: Ver `CAMBIOS_BASE_DATOS.md`
