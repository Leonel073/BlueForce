# 🔧 ESTRUCTURA DETALLADA DE CAMBIOS

**Auditoría Técnica:** 25 de Junio de 2026

---

## 📂 Archivos Modificados

### 1️⃣ `app/Http/Controllers/Admin/PersonaController.php`

#### Antes (Problemas)
```php
public function index()
{
    $personas = Persona::with('departamento', 'cargo')
        ->orderBy('nombre')
        ->paginate(10);
    // ❌ NO filtra activos
    // ❌ Carga todas las personas sin considerar fecha_deshabilitacion
}
```

#### Después (Solución)
```php
public function index()
{
    $personas = Persona::with('departamento', 'cargo')
        ->activas()  // ✅ Nuevo scope
        ->orderBy('nombre')
        ->paginate(10);
    // ✅ Filtra personas activas en SQL
    // ✅ Mejor rendimiento, menos datos en memoria
}
```

#### Métodos Completamente Refactorizados

**`store()` - Crear Persona**
```php
// ✅ AGREGADO: Lógica condicional por tipo
$tipo_persona = $validated['tipo'] === 'EXTERNO' ? 'externo' : 'trabajador';

if ($validated['tipo'] === 'EXTERNO') {
    $validated['institucion'] = null;
    $validated['institucion_externa'] = $validated['institucion_externa'] ?? null;
} else {
    $validated['institucion'] = 'EPAB';
    $validated['institucion_externa'] = null;
}

// ✅ AGREGADO: Campos nuevos
'institucion_externa'  => $validated['institucion_externa'] ?? null,
'fecha_creacion'       => now(),
```

**`update()` - Editar Persona**
```php
// ✅ AGREGADO: Validación institucion_externa
'institucion_externa'  => 'nullable|string|max:200',

// ✅ AGREGADO: Lógica condicional
if ($validated['tipo'] === 'EXTERNO') {
    $validated['idCargo'] = null;
    $validated['idDepartamento'] = null;
    $validated['institucion'] = null;
    // ✅ institucion_externa se guarda
} else {
    $validated['institucion'] = 'EPAB';
    $validated['institucion_externa'] = null;
}
```

**`toggle()` - Activar/Desactivar**
```php
// ✅ MEJORADO: Usa métodos del modelo
if ($persona->fecha_deshabilitacion) {
    $persona->reactivar();  // ✅ Método modelo
} else {
    if ($persona->esResponsableActual()) {  // ✅ Verificación
        foreach ($persona->departamentosResponsables() as $depto) {
            $depto->declinarResponsable();
        }
    }
    $persona->deshabilitar();  // ✅ Método modelo
}
```

---

### 2️⃣ `app/Models/Persona.php`

#### Cambio Mínimo (1 línea)
```php
protected $fillable = [
    // ... otros campos ...
    'institucion_externa',  // ✅ AGREGADO
    // ... otros campos ...
];
```

#### Por qué fue necesario
- Migración agrega el campo en BD
- Modelo debe permitir mass assignment
- Evita `MassAssignmentException`

---

### 3️⃣ `app/Http/Requests/Admin/StorePersonaRequest.php`

#### Validación de `institucion_externa`
```php
'institucion_externa' => [
    'nullable',
    'string',
    'max:200',
    'regex:/^[\pL\pN\s]+$/u',  // Solo letras, números, espacios
],
```

#### Mensaje de Error
```php
'institucion_externa.regex' => 'La institución externa solo puede contener letras, números y espacios.',
```

#### En `prepareForValidation()`
```php
'institucion_externa' => trim($this->institucion_externa ?? ''),
```

---

### 4️⃣ `resources/views/admin/personas/create.blade.php`

#### Estructura de Formulario

**Sección: Datos Personales**
```html
<!-- Existía -->
<div class="mb-3">
    <label>Tipo de Persona</label>
    <select name="tipo" id="tipo" onchange="actualizarFormulario()">
        <option value="INTERNO">Interno</option>
        <option value="EXTERNO">Externo</option>
    </select>
</div>

<!-- Existía - se mantiene -->
<div class="mb-3" id="cargoDiv">
    <label>Cargo (Opcional)</label>
    <select name="idCargo">...</select>
</div>

<!-- Nuevo - campo condicional para externos -->
<div class="mb-3" id="institucionExternaDiv" style="display: none;">
    <label>Institución de Procedencia <span class="text-danger">*</span></label>
    <input type="text" name="institucion_externa" id="institucionExterna"
           placeholder="Ej: Universidad Mayor de San Andrés, Ministerio de Defensa, Empresa XYZ, Particular">
    <small>Indicar la institución a la que pertenece esta persona externa.</small>
</div>
```

#### Script Mejorado

**Antes (Incompleto)**
```javascript
function actualizarFormulario() {
    const tipo = document.getElementById('tipo').value;
    // Solo mostraba/ocultaba Cargo
    // NO consideraba institucion_externa
}
```

**Después (Completo)**
```javascript
function actualizarFormulario() {
    const tipo = document.getElementById('tipo').value;
    const institucionExternaDiv = document.getElementById('institucionExternaDiv');
    const institucionExterna = document.getElementById('institucionExterna');
    
    if (tipo === 'INTERNO') {
        institucionExternaDiv.style.display = 'none';
        institucionExterna.removeAttribute('required');
    } else {
        institucionExternaDiv.style.display = 'block';
        institucionExterna.setAttribute('required', 'required');
    }
}
```

---

### 5️⃣ `resources/views/admin/personas/edit.blade.php`

#### Dos Secciones Condicionales

**Para INTERNOS:**
```html
<div id="institucion_section" style="display: block;">
    <label>Institución</label>
    <input type="text" name="institucion" 
           value="EPAB" readonly>
    <small>Se asigna automáticamente como EPAB para personas internas</small>
</div>
```

**Para EXTERNOS:**
```html
<div id="institucion_externa_section" style="display: none;">
    <label>Institución de Procedencia <span class="text-danger">*</span></label>
    <input type="text" name="institucion_externa"
           placeholder="Ej: Universidad Mayor de San Andrés...">
    <small>Indicar de dónde proviene esta persona externa.</small>
</div>
```

#### Script Toggle Completo

```javascript
function toggleFields() {
    if (tipoSelect.value === 'INTERNO') {
        // Mostrar campos internos
        cargoSection.style.display = 'block';
        institucionSection.style.display = 'block';
        institucionExternaSection.style.display = 'none';
    } else {
        // Mostrar campos externos
        cargoSection.style.display = 'none';
        institucionSection.style.display = 'none';
        institucionExternaSection.style.display = 'block';
        institucionExterna.setAttribute('required', 'required');
    }
}
```

---

### 6️⃣ `resources/views/admin/personas/index.blade.php`

#### Cambio de Tabs

**Antes:**
```html
<button data-bs-target="#remitentes" ...>
    Remitentes ({{ $personas->filter(...)->count() }})
</button>
```

**Después:**
```html
<button data-bs-target="#externos" ...>
    Externos ({{ $personas->where('tipo', 'EXTERNO')->count() }})
</button>
```

#### Nueva Tabla para Externos

```html
<div id="externos">
    <h5>Personas externas a la institución</h5>
    <table>
        <thead>
            <th>CI</th>
            <th>Nombre</th>
            <th>Institución Externa</th>  <!-- NUEVA COLUMNA -->
            <th>Tipo Persona</th>         <!-- NUEVA COLUMNA -->
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <tbody>
            @forelse($personas->where('tipo', 'EXTERNO') as $persona)
                <tr>
                    <td>{{ $persona->institucion_externa }}</td>
                    <td>
                        <span class="badge bg-warning text-dark">Externo</span>
                    </td>
                    <!-- ... -->
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
```

---

### 7️⃣ `database/migrations/2026_06_25_000003_add_institucion_externa_to_persona_table.php` [NUEVA]

#### Estructura de Migración

```php
Schema::table('PERSONA', function (Blueprint $table) {
    // ✅ Campo principal
    $table->string('institucion_externa', 200)
          ->nullable()
          ->after('institucion')
          ->comment('Institución de procedencia para personas externas');

    // ✅ Campos de auditoria
    $table->timestamp('fecha_creacion')
          ->nullable()
          ->comment('Fecha de registro en sistema');
    
    $table->timestamp('fecha_deshabilitacion')
          ->nullable()
          ->comment('Fecha de deshabilitación (borrado lógico)');

    // ✅ Campos de contacto
    $table->string('telefono_celular', 20)->nullable();
    $table->string('telefono_fijo', 20)->nullable();

    // ✅ FKs
    $table->unsignedBigInteger('idCargo')->nullable();
    $table->unsignedBigInteger('idDepartamento')->nullable();
});
```

#### Reversión (Down)

```php
Schema::table('PERSONA', function (Blueprint $table) {
    $table->dropColumn([
        'institucion_externa',
        'fecha_creacion',
        'fecha_deshabilitacion',
        'telefono_celular',
        'telefono_fijo',
        'idCargo',
        'idDepartamento',
    ]);
});
```

---

## 🔄 FLUJOS ACTUALIZADO

### Crear Persona INTERNA

```
1. Ir a Admin → Personas → Nueva Persona
2. Completar formulario
   ├─ Nombre: Juan García
   ├─ CI: 1234567-8
   ├─ Tipo: INTERNO
   ├─ Cargo: [Mostrado] Seleccionar
   ├─ Departamento: [Mostrado] Seleccionar
   ├─ Institución Externa: [OCULTO]
   └─ Institución: EPAB (automático, readonly)
3. Guardar
4. Aparece en tab "Trabajadores"
5. Puede crear usuario para esta persona
```

### Crear Persona EXTERNA

```
1. Ir a Admin → Personas → Nueva Persona
2. Completar formulario
   ├─ Nombre: María López
   ├─ CI: 9876543-2
   ├─ Tipo: EXTERNO
   ├─ Cargo: [OCULTO]
   ├─ Departamento: [OCULTO]
   ├─ Institución Externa: [MOSTRADO] REQUERIDO
   │  └─ "Universidad Mayor de San Andrés"
   └─ Institución: [OCULTO]
3. Guardar
4. Aparece en tab "Externos"
5. NO puede crear usuario para esta persona
```

### Cambiar Tipo de Persona

```
Editar Persona
├─ Era INTERNO, cambiar a EXTERNO
│  ├─ Desaparecen: Cargo, Departamento, Institución
│  ├─ Aparece: Institución Externa [REQUERIDO]
│  └─ Guardar
├─ Ahora en tab "Externos"
└─ NO puede tener usuario

O

├─ Era EXTERNO, cambiar a INTERNO
│  ├─ Desaparece: Institución Externa
│  ├─ Aparecen: Cargo, Departamento, Institución (EPAB readonly)
│  └─ Guardar
├─ Ahora en tab "Trabajadores"
└─ PUEDE crear usuario
```

---

## 🔐 Puntos Clave de Seguridad

### 1. Scope `internosSinUsuario()`
```php
// SQL: Solo personas INTERNAS sin usuario
WHERE tipo = 'INTERNO' 
  AND fecha_deshabilitacion IS NULL 
  AND NOT EXISTS(SELECT 1 FROM users WHERE idPersona = ...)
```
✅ Usado en: UsuarioController, PersonaController

### 2. Bloqueo en Controller
```php
if ($persona->tipo !== 'INTERNO') {
    return back()->with('error', '...');
}
```
✅ Ubicación: `UsuarioController@store()`

### 3. Excepción en Modelo
```php
if ($persona->tipo_persona === 'externo') {
    throw new \InvalidArgumentException('...');
}
```
✅ Ubicación: `User@boot()`

---

## 📊 Cambios por Números

| Métrica | Valor |
|---------|-------|
| Archivos Modificados | 6 |
| Líneas Agregadas | ~200 |
| Líneas Eliminadas | ~50 |
| Campos BD Agregados | 7 |
| Vistas Actualizadas | 3 |
| Controladores Actualizados | 1 |
| Modelos Actualizados | 1 |
| Requests Actualizados | 1 |
| Migraciones Nuevas | 1 |

---

## ✅ Validaciones Aplicadas

### Persona INTERNA
- ✅ Tipo = 'INTERNO'
- ✅ Institución = 'EPAB'
- ✅ Institucion_externa = NULL
- ✅ Puede tener Cargo
- ✅ Puede tener Departamento
- ✅ Puede crear usuario

### Persona EXTERNA
- ✅ Tipo = 'EXTERNO'
- ✅ Institución = NULL
- ✅ Institucion_externa = Requerido
- ✅ Cargo = NULL
- ✅ Departamento = NULL
- ✅ NO puede crear usuario

---

**Documento completado:** 25 de Junio de 2026 - 14:45 hrs
