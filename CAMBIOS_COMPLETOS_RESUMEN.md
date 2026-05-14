# 📋 Resumen Completo de Cambios - Sistema de Gestión de Correspondencia

## 🎯 Objetivos Logrados

1. ✅ **Normalizar estructura de CARGO** - De string en PERSONA a tabla catálogo
2. ✅ **Diferenciar INTERNO vs EXTERNO** - Personas internas muestran cargo, externas no
3. ✅ **Búsqueda sin duplicidad** - Cargar personas desde BD por CI
4. ✅ **Selección dinámica de destinatarios** - Basado en departamento seleccionado

---

## 📊 Cambios en Base de Datos

### Tabla: CARGO (NUEVA)

```sql
CREATE TABLE CARGO (
    idCargo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) UNIQUE NOT NULL,
    descripcion TEXT,
    nivel VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Cargos iniciales (22):**
- **Directivos (3):** Director General, Subdirector Académico, Subdirector Administrativo
- **Académicos (5):** Docente, Coordinador Académico, Jefe de Programa, Especialista Educativo, Tutor
- **Administrativos (3):** Recepcionista, Especialista Administrativo, Asistente Administrativo
- **Operativos (11):** Técnico Informática, Técnico Mantenimiento, Chofer, Portero, Conserje, etc.

### Tabla: PERSONA (MODIFICADA)

**Cambio Principal:** Reemplazar campo `cargo` (string) por `idCargo` (FK)

```sql
-- ANTES:
ALTER TABLE PERSONA ADD COLUMN cargo VARCHAR(150) NULL;

-- DESPUÉS:
ALTER TABLE PERSONA ADD COLUMN idCargo INT NULL;
ALTER TABLE PERSONA ADD CONSTRAINT fk_persona_cargo 
FOREIGN KEY (idCargo) REFERENCES CARGO(idCargo) ON DELETE SET NULL;
```

---

## 💻 Cambios en Código

### A. Modelos

#### 📝 `app/Models/Cargo.php` (NUEVO)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'CARGO';
    protected $primaryKey = 'idCargo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel',
        'activo'
    ];

    // Relación: Un cargo puede tener muchas personas
    public function personas()
    {
        return $this->hasMany(Persona::class, 'idCargo', 'idCargo');
    }

    // Scope: Solo cargos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope: Filtrar por nivel
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }
}
```

#### 📝 `app/Models/Persona.php` (MODIFICADA)

**Cambio clave:** Reemplazar relación 'cargo' (string) con relación al modelo Cargo

```php
// Fillable - ANTES:
protected $fillable = ['nombre', 'cargo', ...];

// Fillable - DESPUÉS:
protected $fillable = ['nombre', 'idCargo', ...];

// Relación - ANTES:
// No existía relación, cargo era string

// Relación - DESPUÉS:
public function cargo()
{
    return $this->belongsTo(Cargo::class, 'idCargo', 'idCargo');
}
```

---

### B. Controladores

#### 📝 `app/Http/Controllers/DocumentoController.php` (MODIFICADA)

**Método 1: `buscarPersona($ci)` - ACTUALIZADO**

```php
public function buscarPersona($ci)
{
    $persona = Persona::where('ci', $ci)
        ->where('activo', true)
        ->with('cargo') // Cargar relación
        ->first();

    if (!$persona) {
        return response()->json(['success' => false]);
    }

    // Retornar con cargo como relación
    return response()->json([
        'success' => true,
        'persona' => [
            'nombre' => $persona->nombre,
            'correo' => $persona->correo,
            'cargo' => $persona->cargo?->nombre, // null si EXTERNO o sin cargo
            'institucion' => $persona->institucion,
            'telefono_celular' => $persona->telefono_celular,
            'telefono_fijo' => $persona->telefono_fijo,
            'tipo' => $persona->tipo,
            'idDepartamento' => $persona->idDepartamento,
            'es_interno' => $persona->tipo === 'INTERNO'
        ]
    ]);
}
```

**Método 2: `obtenerPersonasPorDepartamento($idDepartamento)` - NUEVO**

```php
public function obtenerPersonasPorDepartamento($idDepartamento)
{
    $personas = Persona::where('idDepartamento', $idDepartamento)
        ->where('tipo', 'INTERNO')
        ->where('activo', true)
        ->with('cargo')
        ->get(['idPersona', 'nombre', 'idCargo'])
        ->map(function ($persona) {
            return [
                'idPersona' => $persona->idPersona,
                'nombre' => $persona->nombre,
                'cargo' => $persona->cargo ? $persona->cargo->nombre : 'Sin cargo'
            ];
        });

    return response()->json($personas);
}
```

#### 📝 `app/Http/Controllers/Admin/PersonaController.php` (MODIFICADA)

**Métodos afectados:**

1. **`edit($id)` - Cargar dropdown de cargos**
```php
public function edit(Persona $persona)
{
    $departamentos = Departamento::all();
    $cargos = Cargo::where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view('admin.personas.edit', compact('persona', 'departamentos', 'cargos'));
}
```

2. **`update()` - Validar y asignar cargo**
```php
public function update(Request $request, Persona $persona)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:200',
        'tipo' => 'required|in:INTERNO,EXTERNO',
        'idCargo' => 'nullable|exists:CARGO,idCargo',
        // ... otras validaciones
    ]);

    // Lógica especial: Si es EXTERNO, no tener cargo
    if ($validated['tipo'] === 'EXTERNO') {
        $validated['idCargo'] = null;
    }

    $persona->update($validated);
    return redirect()->route('personas.index')->with('success', 'Persona actualizada');
}
```

3. **`buscar()` - Mostrar cargo en búsqueda**
```php
public function buscar(Request $request)
{
    $query = $request->input('q');

    $personas = Persona::where('nombre', 'like', "%$query%")
        ->orWhere('ci', 'like', "%$query%")
        ->with('cargo', 'departamento')
        ->get()
        ->map(function ($p) {
            return [
                'id' => $p->idPersona,
                'nombre' => $p->nombre,
                'tipo' => $p->tipo,
                'cargo' => $p->cargo?->nombre,
                'departamento' => $p->departamento?->nombre
            ];
        });

    return response()->json($personas);
}
```

---

### C. Rutas

#### 📝 `routes/web.php` (MODIFICADA)

**Nueva ruta agregada:**

```php
Route::get(
    '/documentos/departamento/{idDepartamento}/personas',
    [DocumentoController::class, 'obtenerPersonasPorDepartamento']
)->name('documentos.departamento.personas');
```

---

### D. Vistas

#### 📝 `resources/views/correspondencia/documento-registro.blade.php` (MODIFICADA)

**Cambios principales:**

1. **Campo Cargo - AHORA READ-ONLY**

```blade
<div class="mb-3" id="cargo_remitente_section" style="display: none;">
    <label class="form-label fw-semibold">
        <i class="bi bi-briefcase"></i>
        Cargo
    </label>
    <input type="text" 
           id="cargo_remitente" 
           name="cargo_remitente"
           class="form-control" 
           readonly>
    <small class="text-muted d-block mt-2">
        <i class="bi bi-info-circle me-1"></i>
        Solo disponible para personas INTERNAS. Este campo se carga automáticamente.
    </small>
</div>
```

2. **Nueva sección: Persona Destinataria**

```blade
{{-- PERSONA DESTINATARIA (Cargada dinámicamente) --}}
<div class="mb-3" id="destinatario_section" style="display: none;">
    <label class="form-label fw-semibold">
        <i class="bi bi-person-check"></i>
        Persona Destinataria
    </label>
    <select id="persona_destinataria"
            name="persona_destinataria"
            class="form-select">
        <option value="">
            -- Seleccione una persona --
        </option>
    </select>
    <small class="text-muted d-block mt-2">
        <i class="bi bi-info-circle me-1"></i>
        Personas activas del departamento seleccionado.
    </small>
</div>
```

3. **JavaScript - Event Listeners**

```javascript
// 1. Buscar persona por CI
document.getElementById('ci_remitente').addEventListener('blur', function() {
    // Cargar persona y mostrar/ocultar cargo
    // Populate cargo_remitente field si es interno
});

// 2. Toggle cargo basado en tipo_remitente
function toggleCargoField() {
    const tipoValue = document.getElementById('tipo_remitente').value;
    const cargoSection = document.getElementById('cargo_remitente_section');
    
    if (tipoValue === 'INTERNO') {
        cargoSection.style.display = 'block';
    } else {
        cargoSection.style.display = 'none';
    }
}

// 3. Cargar personas al seleccionar departamento
document.getElementById('departamento').addEventListener('change', function() {
    const idDepartamento = this.value;
    
    if (!idDepartamento) {
        document.getElementById('destinatario_section').style.display = 'none';
        return;
    }
    
    fetch(`/documentos/departamento/${idDepartamento}/personas`)
        .then(response => response.json())
        .then(personas => {
            // Poblar dropdown persona_destinataria
            let html = '<option value="">-- Seleccione una persona --</option>';
            personas.forEach(p => {
                html += `<option value="${p.idPersona}">${p.nombre} (${p.cargo})</option>`;
            });
            document.getElementById('persona_destinataria').innerHTML = html;
            document.getElementById('destinatario_section').style.display = 'block';
        });
});
```

#### 📝 `resources/views/admin/personas/edit.blade.php` (MODIFICADA)

**Campo Cargo - NUEVO DROPDOWN (antes era text input)**

```blade
<div class="mb-3" id="cargo_section">
    <label class="form-label fw-semibold">
        <i class="bi bi-briefcase"></i>
        Cargo
        <span class="badge bg-info">Solo para INTERNOS</span>
    </label>
    <select name="idCargo" 
            id="cargo_select"
            class="form-select @error('idCargo') is-invalid @enderror">
        <option value="">-- Sin cargo --</option>
        @foreach($cargos as $cargo)
            <option value="{{ $cargo->idCargo }}" 
                    @selected(old('idCargo', $persona->idCargo) == $cargo->idCargo)>
                {{ $cargo->nombre }}
            </option>
        @endforeach
    </select>
    @error('idCargo')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="text-muted d-block mt-2">
        Solo disponible para personas INTERNAS.
    </small>
</div>

<script>
document.getElementById('tipo_select').addEventListener('change', function() {
    const cargoSection = document.getElementById('cargo_section');
    const cargoSelect = document.getElementById('cargo_select');
    
    if (this.value === 'EXTERNO') {
        cargoSection.style.display = 'none';
        cargoSelect.value = '';
    } else {
        cargoSection.style.display = 'block';
    }
});
</script>
```

#### 📝 `resources/views/admin/personas/index.blade.php` (MODIFICADA)

**Columna Cargo - ACCEDER VÍA RELACIÓN**

```blade
<!-- ANTES -->
<td>{{ $persona->cargo ?? 'N/A' }}</td>

<!-- DESPUÉS -->
<td>{{ $persona->cargo?->nombre ?? 'N/A' }}</td>
```

---

### E. Validación (Form Request)

#### 📝 `app/Http/Requests/StoreDocumentoRequest.php` (MODIFICADA)

```php
// Removed:
'cargo_remitente' => [
    'nullable',
    'string',
    'max:150',
    'regex:/^[\pL\s]+$/u'
],

// Added:
'persona_destinataria' => 'nullable|exists:PERSONA,idPersona',
```

---

### F. Migraciones

#### 📝 `database/migrations/2026_05_08_092000_create_cargo_table.php` (NUEVA)

```php
Schema::create('CARGO', function (Blueprint $table) {
    $table->id('idCargo');
    $table->string('nombre', 150)->unique();
    $table->text('descripcion')->nullable();
    $table->string('nivel', 50)->nullable(); // Directivo, Académico, Administrativo, Operativo
    $table->boolean('activo')->default(true);
    $table->timestamps();
});
```

#### 📝 `database/migrations/2026_05_05_152243_creacion_tablas.php` (MODIFICADA)

- Removido: `$table->string('cargo', 150)->nullable();` de PERSONA
- Agregado: `$table->unsignedBigInteger('idCargo')->nullable();` a PERSONA

#### 📝 `database/migrations/2026_05_08_091500_modificaciones_base_datos.php` (MODIFICADA)

- Agregado FK: PERSONA.idCargo → CARGO.idCargo

---

### G. Seeders

#### 📝 `database/seeders/CargoSeeder.php` (NUEVO)

Crea 22 cargos iniciales en 4 categorías (Directivos, Académicos, Administrativos, Operativos)

#### 📝 `database/seeders/PersonaSeeder.php` (MODIFICADA)

- Cambió `'cargo' => 'Recepcionista'` por `'idCargo' => $cargos['Recepcionista']`
- Usa mapeo dinámico de cargos por nombre

#### 📝 `database/seeders/DatabaseSeeder.php` (MODIFICADA)

**Orden de ejecución correcto:**

```php
RolSeeder::class
DepartamentoSeeder::class
TipoDocumentoSeeder::class
EstadoDocumentoSeeder::class
NivelUrgenciaSeeder::class
CargoSeeder::class      // ← ANTES de PersonaSeeder
PersonaSeeder::class    // ← DESPUÉS de CargoSeeder
UserSeeder::class
```

---

## 🔄 Flujos de Trabajo

### Flujo 1: Crear Documento - Persona INTERNA

```
1. Usuario ingresa CI en "CI Remitente"
2. Sistema busca persona por CI
3. API retorna: nombre, cargo, tipo=INTERNO
4. JavaScript muestra campo "Cargo" (read-only) con valor
5. Usuario selecciona "Departamento Destino"
6. API carga personas internas del departamento
7. Dropdown "Persona Destinataria" se puebla con formato: "Nombre (Cargo)"
8. Usuario puede seleccionar persona específica
9. Formulario se valida y guarda
```

### Flujo 2: Crear Documento - Persona EXTERNA

```
1. Usuario ingresa CI en "CI Remitente"
2. Sistema busca persona por CI
3. API retorna: nombre, tipo=EXTERNO, cargo=null
4. JavaScript OCULTA campo "Cargo"
5. Usuario selecciona "Departamento Destino"
6. API carga personas internas del departamento
7. Dropdown "Persona Destinataria" se puebla
8. Usuario puede seleccionar persona específica
9. Formulario se valida sin cargo_remitente y guarda
```

### Flujo 3: Admin - Crear Persona INTERNA

```
1. Admin navega a Personas → Crear
2. Llena nombre, CI, correo, etc.
3. Selecciona "Tipo = INTERNO"
4. Campo "Cargo" aparece como dropdown
5. Admin selecciona cargo de lista (ej: Recepcionista)
6. Guardar - se asigna idCargo correcto
```

### Flujo 4: Admin - Crear Persona EXTERNA

```
1. Admin navega a Personas → Crear
2. Llena campos básicos
3. Selecciona "Tipo = EXTERNO"
4. Campo "Cargo" desaparece (JavaScript)
5. Guardar - se guarda con idCargo = null
```

---

## ✅ Testing Checklist

Ejecutar en orden:

```bash
# 1. Resetear base de datos
php artisan migrate:fresh --seed

# 2. Verificar tabla CARGO
php artisan tinker
DB::table('CARGO')->count(); // Debe ser 22

# 3. Verificar relaciones
$persona = App\Models\Persona::with('cargo')->first();
$persona->cargo; // Debe retornar objeto Cargo o null

# 4. Verificar rutas
php artisan route:list | grep persona
```

---

## 📚 Archivos Modificados

| Archivo | Tipo | Cambio |
|---------|------|--------|
| app/Models/Cargo.php | NUEVO | Modelo para tabla CARGO |
| app/Models/Persona.php | MOD | Agregar relación belongsTo(Cargo) |
| app/Http/Controllers/DocumentoController.php | MOD | buscarPersona() + obtenerPersonasPorDepartamento() |
| app/Http/Controllers/Admin/PersonaController.php | MOD | edit(), update(), buscar() con cargo |
| routes/web.php | MOD | Agregar ruta departamentos/personas |
| resources/views/correspondencia/documento-registro.blade.php | MOD | Cargo read-only + destinatario section + JS |
| resources/views/admin/personas/edit.blade.php | MOD | Cargo dropdown + toggle |
| resources/views/admin/personas/index.blade.php | MOD | Mostrar cargo->nombre |
| app/Http/Requests/StoreDocumentoRequest.php | MOD | Remover cargo_remitente, agregar persona_destinataria |
| database/migrations/2026_05_08_092000_create_cargo_table.php | NUEVO | Tabla CARGO |
| database/seeders/CargoSeeder.php | NUEVO | 22 cargos iniciales |
| database/seeders/PersonaSeeder.php | MOD | Usar idCargo en lugar de cargo string |
| database/seeders/DatabaseSeeder.php | MOD | Agregar CargoSeeder antes de PersonaSeeder |

---

## 🎓 Conclusión

El sistema ahora:
- ✅ Normaliza cargos en tabla catálogo
- ✅ Diferencia personas internas (con cargo) vs externas (sin cargo)
- ✅ Permite seleccionar destinatarios específicos por departamento
- ✅ Valida y guarda información sin duplicidad
- ✅ Proporciona interfaz clara para admin y usuarios finales

