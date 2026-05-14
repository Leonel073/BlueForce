# ⚡ Comandos Rápidos - Gestión de Correspondencia

## 🚀 Setup Inicial

```bash
# 1. Resetear base de datos con todos los seeders
php artisan migrate:fresh --seed

# 2. Limpiar cache de Laravel
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 3. Generar nuevas claves (si es necesario)
php artisan key:generate
```

---

## 🔍 Verificación Rápida

```bash
# Verificar tabla CARGO existe y tiene datos
php artisan tinker
>>> DB::table('CARGO')->count()
22

# Verificar personas con cargo
>>> DB::table('PERSONA')->with('cargo')->get()

# Contar personas INTERNAS vs EXTERNAS
>>> DB::table('PERSONA')->where('tipo', 'INTERNO')->count()
>>> DB::table('PERSONA')->where('tipo', 'EXTERNO')->count()

# Verificar relación en modelo
>>> $p = App\Models\Persona::find(1);
>>> $p->cargo

# Salir de Tinker
>>> exit
```

---

## 🧪 Pruebas desde Terminal

```bash
# Test 1: API - Buscar persona por CI
curl -X GET "http://localhost:8000/persona/buscar/11111111"

# Test 2: API - Obtener personas de un departamento
curl -X GET "http://localhost:8000/documentos/departamento/1/personas"

# Test 3: Ver todas las rutas registradas
php artisan route:list | grep persona
php artisan route:list | grep departamento
```

---

## 📊 Queries SQL Útiles

```sql
-- Ver tabla CARGO completa
SELECT * FROM CARGO;

-- Ver personas con sus cargos
SELECT 
    p.nombre as persona,
    p.tipo,
    COALESCE(c.nombre, 'N/A') as cargo,
    d.nombre as departamento
FROM PERSONA p
LEFT JOIN CARGO c ON p.idCargo = c.idCargo
LEFT JOIN DEPARTAMENTO d ON p.idDepartamento = d.idDepartamento
ORDER BY d.nombre, p.nombre;

-- Personas INTERNAS sin cargo (error de datos)
SELECT nombre FROM PERSONA 
WHERE tipo = 'INTERNO' AND idCargo IS NULL AND activo = 1;

-- Personas EXTERNAS con cargo (error de datos)
SELECT nombre FROM PERSONA 
WHERE tipo = 'EXTERNO' AND idCargo IS NOT NULL AND activo = 1;

-- Contar personas por departamento y tipo
SELECT 
    d.nombre as departamento,
    p.tipo,
    COUNT(*) as cantidad
FROM PERSONA p
JOIN DEPARTAMENTO d ON p.idDepartamento = d.idDepartamento
WHERE p.activo = 1
GROUP BY d.nombre, p.tipo;

-- Cargos más usados
SELECT 
    c.nombre as cargo,
    COUNT(p.idPersona) as cantidad_personas
FROM CARGO c
LEFT JOIN PERSONA p ON c.idCargo = p.idCargo
GROUP BY c.nombre
ORDER BY cantidad_personas DESC;
```

---

## 🛠️ Solución Rápida de Problemas

```bash
# Si falta CargoSeeder en DatabaseSeeder
php artisan tinker
>>> // Ejecutar CargoSeeder manualmente
>>> DB::seed('CargoSeeder')
>>> exit

# Si las relaciones no cargan
php artisan migrate:rollback --step=3
php artisan migrate
php artisan db:seed --class=CargoSeeder

# Regenerar cache de composer
composer dump-autoload

# Si hay conflictos de FK
php artisan migrate:refresh
```

---

## 📝 Logs y Debugging

```bash
# Ver últimos 100 líneas del log
tail -100 storage/logs/laravel.log

# Limpiar logs
rm storage/logs/laravel.log

# Ver queries ejecutadas (agregar en AppServiceProvider)
DB::listen(function ($query) {
    dump($query->sql);
    dump($query->bindings);
});
```

---

## 🎯 Flujo Completo de Testing

```bash
# 1. Setup
php artisan migrate:fresh --seed

# 2. Verificar
php artisan tinker
>>> DB::table('CARGO')->count()  # 22
>>> DB::table('PERSONA')->count() # 18 (3 por depto * 5 deptos + 3 extras)
>>> exit

# 3. Visitar en navegador
# http://localhost:8000/documentos/crear

# 4. Probar búsqueda persona por CI
# CI: 11111111 (persona INTERNA)
# CI: 99999999 (persona EXTERNA)

# 5. Probar selección de departamento
# Seleccionar cualquier departamento del dropdown
# Verificar que aparece "Persona Destinataria" con lista poblada
```

---

## 📦 Archivos Importantes a Revisar

```bash
# Ver cambios en DocumentoController
cat app/Http/Controllers/DocumentoController.php | grep -A 30 "obtenerPersonasPorDepartamento"

# Ver formulario documento
nano resources/views/correspondencia/documento-registro.blade.php
# Buscar: id="cargo_remitente_section"
# Buscar: id="destinatario_section"

# Ver JavaScript en formulario
nano resources/views/correspondencia/documento-registro.blade.php
# Scroll hasta el final para ver los event listeners

# Ver validación
cat app/Http/Requests/StoreDocumentoRequest.php | grep -A 2 "persona_destinataria"
```

---

## 🔐 Permisos y Ownership (si es necesario)

```bash
# En XAMPP/Windows (usualmente no es necesario)
# Si hay problemas de permisos:

chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

## 📈 Monitoreo en Producción (futuro)

```bash
# Ver cantidad de documentos registrados
SELECT COUNT(*) FROM CORRESPONDENCIA;

# Ver personas más mencionadas como destinatarias
SELECT 
    p.nombre,
    COUNT(cd.idPersona) as documentos
FROM PERSONA p
LEFT JOIN CORRESPONDENCIA_DESTINATARIO cd ON p.idPersona = cd.idPersona
GROUP BY p.nombre
ORDER BY documentos DESC
LIMIT 10;
```

---

## 🎓 Cheat Sheet Blade Template

```blade
<!-- Mostrar cargo de persona (safe) -->
{{ $persona->cargo?->nombre ?? 'Sin cargo' }}

<!-- Loop de cargos -->
@foreach($cargos as $cargo)
    <option value="{{ $cargo->idCargo }}">
        {{ $cargo->nombre }}
    </option>
@endforeach

<!-- Condicional si es interno -->
@if($persona->tipo === 'INTERNO' && $persona->cargo)
    Cargo: {{ $persona->cargo->nombre }}
@endif
```

---

## 🎓 Cheat Sheet JavaScript

```javascript
// Fetch con JSON response
fetch(`/documentos/departamento/${id}/personas`)
    .then(r => r.json())
    .then(personas => {
        // Array de {idPersona, nombre, cargo}
    });

// Toggle elemento
document.getElementById('section').style.display = 
    condition ? 'block' : 'none';

// Listener a select
document.getElementById('dropdown').addEventListener('change', function() {
    console.log(this.value); // Valor seleccionado
});
```

---

## ✨ Tips Importantes

1. **Siempre resetear DB después de cambios en migrations:**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Si "Cargo" no aparece en dropdown:** Verificar que CARGO.activo = 1

3. **Si búsqueda de persona no funciona:** Verificar que persona.activo = 1

4. **Si destinatarios no cargan:** Verificar que personas.tipo = 'INTERNO' Y personas.activo = 1

5. **Borrar personas de prueba que no sirven:**
   ```sql
   DELETE FROM PERSONA WHERE nombre LIKE 'Test%';
   ```

---

## 🆘 Support - Donde Buscar

| Problema | Ubicación | Solución |
|----------|-----------|----------|
| Error al cargar cargo | app/Models/Persona.php | Verificar relación `cargo()` |
| API retorna 404 | routes/web.php | Verificar ruta existe |
| Dropdown vacío | DocumentoController.php | Verificar filtro de personas |
| Campo cargo no se muestra | documento-registro.blade.php | Verificar JavaScript toggleCargoField() |
| Validación falla | StoreDocumentoRequest.php | Verificar reglas de validación |

