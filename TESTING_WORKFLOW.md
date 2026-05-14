# 🧪 Testing Workflow - Gestión de Correspondencia

## Pre-requisitos
- PHP 8.2+ con Laravel 12
- MySQL/MariaDB ejecutándose
- XAMPP activo

---

## 1️⃣ Resetear Base de Datos

```bash
php artisan migrate:fresh --seed
```

**Esto ejecutará:**
- ✅ Todos los seeders en orden correcto
- ✅ 22 cargos iniciales (Directivos, Académicos, Administrativos, Operativos)
- ✅ 5 departamentos
- ✅ 18 personas (3 por departamento, todas con cargo asignado)
- ✅ Usuarios de prueba

---

## 2️⃣ Pruebas del Formulario de Registro (documento-registro.blade.php)

### 🔍 Test 1: Buscar Persona INTERNA

**Objetivo:** Verificar que al buscar una persona interna, se muestre su cargo

**Pasos:**
1. Navegar a `/documentos/crear`
2. En campo **CI Remitente**, ingresar CI de persona interna (ej: `11111111`)
3. Presionar Tab o hacer blur del campo

**Resultado Esperado:**
- ✅ Campos se rellenan: Nombre, Correo, Institución, Teléfonos
- ✅ Campo **Tipo** = `INTERNO`
- ✅ Campo **Cargo** = Se muestra el nombre del cargo (ej: "Recepcionista")
- ✅ Sección cargo es visible con valor poblado

**Respuesta API esperada:**
```json
{
  "success": true,
  "persona": {
    "nombre": "Juan Pérez",
    "correo": "juan@escuela.bo",
    "cargo": "Recepcionista",
    "institucion": "Escuela de Postgrado",
    "telefono_celular": "+591 76543210",
    "telefono_fijo": "+591 3 1234567",
    "tipo": "INTERNO",
    "idDepartamento": 1,
    "es_interno": true
  }
}
```

---

### 🔍 Test 2: Buscar Persona EXTERNA

**Objetivo:** Verificar que personas externas NO muestren cargo

**Pasos:**
1. En campo **CI Remitente**, cambiar CI a una persona externa (ej: `99999999`)
2. Presionar Tab o hacer blur del campo

**Resultado Esperado:**
- ✅ Campos se rellenan (nombre, correo, etc.)
- ✅ Campo **Tipo** = `EXTERNO`
- ✅ Sección cargo DESAPARECE (display: none)
- ✅ Campo cargo está limpio y oculto

**Respuesta API esperada:**
```json
{
  "success": true,
  "persona": {
    "nombre": "Persona Externa",
    "correo": "externa@empresa.com",
    "cargo": null,
    "institucion": "Otra Institución",
    "tipo": "EXTERNO",
    "idDepartamento": null,
    "es_interno": false
  }
}
```

---

### 🔍 Test 3: Cambiar Tipo a EXTERNO

**Objetivo:** Verificar que al cambiar tipo a EXTERNO, se oculte el cargo

**Pasos:**
1. Cargar una persona INTERNA (Test 1)
2. Cambiar dropdown **Tipo Remitente** de "INTERNO" a "EXTERNO"

**Resultado Esperado:**
- ✅ Sección cargo desaparece inmediatamente
- ✅ Campo cargo se limpia

---

### 🔍 Test 4: Seleccionar Departamento Destino

**Objetivo:** Verificar que al seleccionar un departamento, se carguen sus personas internas

**Pasos:**
1. Completar campos remitente (Tests 1-3)
2. Scroll a sección **Departamento Destino**
3. Seleccionar un departamento del dropdown (ej: "Dirección General")

**Resultado Esperado:**
- ✅ Aparece nueva sección **Persona Destinataria**
- ✅ Dropdown se puebla con personas internas del departamento
- ✅ Cada persona muestra formato: "Nombre (Cargo)"
  - Ejemplo: "María García (Directora General)"
  - Ejemplo: "Carlos López (Subdirector Académico)"

**Respuesta API esperada:**
```json
[
  {
    "idPersona": 1,
    "nombre": "María García",
    "cargo": "Directora General"
  },
  {
    "idPersona": 2,
    "nombre": "Carlos López",
    "cargo": "Subdirector Académico"
  },
  {
    "idPersona": 3,
    "nombre": "Roberto Morales",
    "cargo": "Docente"
  }
]
```

---

### 🔍 Test 5: Seleccionar Persona Destinataria

**Objetivo:** Verificar que se pueda seleccionar una persona del dropdown

**Pasos:**
1. Completar Test 4
2. En dropdown **Persona Destinataria**, seleccionar una persona

**Resultado Esperado:**
- ✅ Persona se selecciona correctamente
- ✅ Valor se mantiene en el dropdown
- ✅ Campo `persona_destinataria` contiene el `idPersona`

---

## 3️⃣ Pruebas del Panel Admin

### 🔍 Test 6: Crear Persona INTERNA

**Objetivo:** Verificar que se puede crear una persona con cargo

**Pasos:**
1. Navegar a Admin → Personas
2. Hacer clic en **Crear Persona**
3. Llenar formulario con `Tipo = INTERNO`

**Resultado Esperado:**
- ✅ Campo **Cargo** es visible y es dropdown
- ✅ Dropdown contiene 22 cargos disponibles
- ✅ Al guardar, se asigna correctamente el `idCargo`

---

### 🔍 Test 7: Crear Persona EXTERNA

**Objetivo:** Verificar que personas externas no puedan tener cargo

**Pasos:**
1. En formulario de persona, cambiar `Tipo = EXTERNO`

**Resultado Esperado:**
- ✅ Campo **Cargo** desaparece (JavaScript toggle)
- ✅ Si intenta guardar, se guarda con `idCargo = null`

---

### 🔍 Test 8: Editar Persona

**Objetivo:** Verificar que se puede cambiar cargo en formulario edit

**Pasos:**
1. Admin → Personas → Editar una persona INTERNA
2. Cambiar cargo en dropdown
3. Guardar cambios

**Resultado Esperado:**
- ✅ Campo cargo muestra el cargo actual
- ✅ Al cambiar, se guarda el nuevo `idCargo`
- ✅ El cambio se refleja en el listado

---

### 🔍 Test 9: Listado de Personas

**Objetivo:** Verificar que en listado se muestre cargo correctamente

**Pasos:**
1. Admin → Personas (listado)
2. Observar columna **Cargo**

**Resultado Esperado:**
- ✅ Personas internas muestran su cargo (ej: "Recepcionista")
- ✅ Personas externas muestran "N/A" o vacío
- ✅ Nombres de cargo coinciden con tabla CARGO

---

## 4️⃣ Pruebas de Validación

### 🔍 Test 10: Validación de Persona Destinataria

**Objetivo:** Verificar que la validación acepta persona_destinataria

**Pasos:**
1. Completar formulario documento-registro
2. Seleccionar un departamento y una persona destinataria
3. Hacer submit del formulario

**Resultado Esperado:**
- ✅ Formulario se valida correctamente
- ✅ Documento se crea exitosamente
- ✅ Campo `persona_destinataria` se guarda en base de datos

---

## 5️⃣ Troubleshooting

### ❌ Problema: El dropdown de personas está vacío

**Diagnóstico:**
```bash
# Verificar si hay personas en ese departamento
php artisan tinker
>>> DB::table('PERSONA')->where('idDepartamento', 1)->where('tipo', 'INTERNO')->where('activo', true)->get()
```

**Solución:**
- Ejecutar `php artisan migrate:fresh --seed` nuevamente
- Crear personas de prueba en admin

---

### ❌ Problema: Cargo no se muestra al buscar persona

**Diagnóstico:**
```bash
# Verificar relación en base de datos
php artisan tinker
>>> $persona = App\Models\Persona::find(1);
>>> $persona->cargo()->first(); // Debe retornar el cargo o null
```

**Solución:**
- Verificar que PERSONA.idCargo tiene valor correcto
- Verificar que existe el CARGO con ese idCargo

---

### ❌ Problema: Error 404 en ruta departamentos

**Diagnóstico:**
- Verificar que ruta está registrada: `php artisan route:list | grep persona`

**Solución:**
```bash
php artisan route:list
# Buscar: GET /documentos/departamento/{idDepartamento}/personas
# Debe estar presente
```

---

## 6️⃣ Queries SQL de Validación

```sql
-- Verificar tabla CARGO
SELECT * FROM CARGO LIMIT 5;

-- Verificar personas con cargo
SELECT p.nombre, c.nombre as cargo, p.tipo 
FROM PERSONA p
LEFT JOIN CARGO c ON p.idCargo = c.idCargo
WHERE p.activo = 1;

-- Verificar personas por departamento
SELECT p.nombre, d.nombre as departamento, c.nombre as cargo
FROM PERSONA p
JOIN DEPARTAMENTO d ON p.idDepartamento = d.idDepartamento
LEFT JOIN CARGO c ON p.idCargo = c.idCargo
WHERE p.tipo = 'INTERNO' AND p.activo = 1
ORDER BY d.nombre;

-- Contar cargos por tipo
SELECT nivel, COUNT(*) as cantidad FROM CARGO GROUP BY nivel;
```

---

## 7️⃣ Checklist Final

- [ ] Base de datos fue reseteada con `migrate:fresh --seed`
- [ ] Test 1: Búsqueda persona INTERNA muestra cargo
- [ ] Test 2: Búsqueda persona EXTERNA no muestra cargo
- [ ] Test 3: Cambiar tipo a EXTERNO oculta cargo
- [ ] Test 4: Seleccionar departamento carga personas
- [ ] Test 5: Se puede seleccionar persona destinataria
- [ ] Test 6: Admin - crear persona INTERNA con cargo
- [ ] Test 7: Admin - crear persona EXTERNA sin cargo
- [ ] Test 8: Admin - editar persona y cambiar cargo
- [ ] Test 9: Listado Admin muestra cargo correctamente
- [ ] Test 10: Formulario valida y guarda persona_destinataria

---

## 📝 Notas Importantes

1. **Cargo es read-only en formulario:** El campo `cargo_remitente` solo se completa automáticamente al buscar la persona por CI, no permite edición manual.

2. **Persona destinataria es opcional:** Si el usuario quiere enviar a todo el departamento sin persona específica, puede dejar vacío.

3. **Solo personas INTERNAS en destinatarios:** El endpoint `/documentos/departamento/{id}/personas` filtra automáticamente solo personas INTERNAS y ACTIVAS.

4. **CI debe tener mínimo 3 caracteres:** La búsqueda de persona se activa con blur del campo CI solo si tiene 3+ caracteres.

---

## 🚀 Próximos Pasos (Futuro)

- [ ] Agregar validación de destinatario según estado documento
- [ ] Historial de cambios de cargo
- [ ] Auditoría de documentos por cargo
- [ ] Reporte: personas activas por departamento y cargo

