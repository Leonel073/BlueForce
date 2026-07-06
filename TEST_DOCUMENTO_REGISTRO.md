# TEST PLAN - FORMULARIO "REGISTRO DE DOCUMENTOS" (REMITENTE)

**Versión:** 1.0  
**Fecha:** 5 de Julio de 2026  
**Estado:** ✅ LISTO PARA TESTING

---

## RESUMEN DE CAMBIOS

| Archivo | Tipo de Cambio | Líneas |
|---------|---|---|
| `StoreDocumentoRequest.php` | Validación condicional, Regex mejorado | +168, -168 |
| `DocumentoController.php` | Lógica "Yo Mismo" vs "Otra Persona" | +163, -163 |
| `documento-registro.blade.php` | Removidos `required` de campos condicionales | +9, -9 |

---

## MANUAL DE TESTING

### TEST 1: Seleccionar "Yo Mismo" y registrar documento ✅

**Precondición:**
- Usuario debe estar autenticado
- Usuario debe tener una Persona asociada

**Pasos:**
1. Navegar a: `/correspondencia/documento-registro`
2. El formulario carga con "Yo Mismo" seleccionado (predeterminado)
3. Llenar campos visibles:
   - **Asunto:** "Prueba de documento registrado por Yo Mismo"
   - **Tipo Documento:** Seleccionar cualquiera (ej: "Nota")
   - **Nivel Urgencia:** Seleccionar cualquiera (ej: "Normal")
   - **Departamento Destino:** Seleccionar uno
   - **Responsable Destino:** Seleccionar una persona interna
4. Presionar botón: **"Registrar Documento"**

**Resultado Esperado:**
- ✅ Documento registrado exitosamente
- ✅ NO aparecen errores de validación sobre:
  - "El nombre completo del remitente es obligatorio"
  - "El carnet de identidad es obligatorio"
  - "Debe seleccionar el tipo de remitente"
  - "El teléfono celular es obligatorio"
- ✅ Remitente es el usuario autenticado
- ✅ Redirección a dashboard o lista de documentos

**Evidencia de Éxito:**
```
Documento registrado correctamente. ✅
Remitente: [Nombre del usuario autenticado]
```

---

### TEST 2: Nombre con acentos en "Otra Persona" ✅

**Precondición:**
- Usuario autenticado
- Acceso a formulario de registro

**Pasos:**
1. Navegar a: `/correspondencia/documento-registro`
2. Seleccionar opción: **"Otra Persona"**
3. En búsqueda de personas, escribir nombre con acentos:
   - Ejemplo: "María Pérez"
4. Si no existe, presionar: **"Registrar Nueva Persona"**
5. Llenar formulario de nueva persona:
   - **Nombre Completo:** "María Fernanda López-García"
   - **CI:** "1234567-8"
   - **Teléfono Celular:** "+591 71234567"
   - **Tipo Remitente:** "EXTERNO"
   - **Institución:** "Ministerio de Educación"
6. Presionar: **"Registrar Nueva Persona"**
7. Presionar: **"Usar esta persona"**
8. Llenar datos del documento:
   - **Asunto:** "Prueba de nombre con acentos"
   - **Tipo Documento:** Seleccionar
   - **Nivel Urgencia:** Seleccionar
   - **Departamento Destino:** Seleccionar
   - **Responsable Destino:** Seleccionar
9. Presionar: **"Registrar Documento"**

**Resultado Esperado:**
- ✅ Nombre "María Fernanda López-García" se acepta
- ✅ NO aparece error: "El nombre solo puede contener letras y espacios"
- ✅ Documento se registra con remitente correcto
- ✅ Datos persisten en la base de datos

**Ejemplos de Nombres Válidos Adicionales:**
- Juan Pérez ✅
- José Luis García ✅
- Ana María Choque ✅
- Luis Fernando Pérez Rojas ✅
- María Elena López-González ✅

**Ejemplos de Nombres Inválidos (Deben Rechazarse):**
- Juan123 ❌ (número no permitido)
- María@López ❌ (@ no permitido)
- Juan_García ❌ (_ no permitido)

---

### TEST 3: Validación condicional - Campos requeridos ✅

**Precondición:**
- Usuario autenticado

**Pasos - Parte A: "Yo Mismo"**
1. Seleccionar: **"Yo Mismo"**
2. NO llenar:
   - Nombre del remitente
   - CI del remitente
   - Teléfono celular
3. Presionar: **"Registrar Documento"** (sin llenar estos campos ni otros)

**Resultado Esperado - Parte A:**
- ❌ Error por Asunto (no requerido)
- ❌ Error por Tipo Documento (no requerido)
- ❌ Error por Nivel Urgencia (no requerido)
- ❌ Error por Departamento Destino (requerido)
- ❌ NO error sobre Nombre del remitente ✅
- ❌ NO error sobre CI del remitente ✅
- ❌ NO error sobre Teléfono del remitente ✅

**Pasos - Parte B: "Otra Persona"**
1. Seleccionar: **"Otra Persona"**
2. Presionar: **"Registrar Nueva Persona"**
3. NO llenar ningún campo
4. Presionar: **"Registrar Nueva Persona"**

**Resultado Esperado - Parte B:**
- ❌ Error: "El nombre completo del remitente es obligatorio" ✅
- ❌ Error: "El carnet de identidad es obligatorio" ✅
- ❌ Error: "El teléfono celular es obligatorio" ✅
- ❌ Error: "Debe seleccionar el tipo de remitente" ✅

---

### TEST 4: Cambio entre opciones de remitente ✅

**Precondición:**
- Usuario autenticado

**Pasos:**
1. Seleccionar: **"Otra Persona"**
2. Llenar campos:
   - **Nombre:** "Juan García"
   - **CI:** "1234567-8"
   - **Teléfono:** "+591 71234567"
3. Seleccionar: **"Yo Mismo"**

**Resultado Esperado:**
- ✅ Bloque "Otra Persona" se oculta
- ✅ Bloque "Yo Mismo" se muestra
- ✅ Los datos ingresados en "Otra Persona" se limpian
- ✅ Campos de "Otra Persona" están vacíos si se vuelve a cambiar

---

### TEST 5: Búsqueda de persona existente ✅

**Precondición:**
- Existen personas en la base de datos

**Pasos:**
1. Seleccionar: **"Otra Persona"**
2. En campo "Buscar Persona", escribir: "María"
3. Esperar 1-2 segundos (debounce)
4. Presionar: **"Seleccionar"** en una persona de los resultados

**Resultado Esperado:**
- ✅ Se muestran resultados de búsqueda
- ✅ Se puede seleccionar una persona
- ✅ Los datos se cargan en el formulario
- ✅ Se muestra tarjeta de confirmación
- ✅ Al presionar "Usar esta persona", se prepara para registro

---

### TEST 6: Verificación de duplicados ✅

**Precondición:**
- Usuario autenticado

**Pasos:**
1. Seleccionar: **"Otra Persona"**
2. Presionar: **"Registrar Nueva Persona"**
3. Llenar datos de una persona que YA existe:
   - **CI:** (el CI de una persona ya registrada)
4. Presionar: **"Registrar Nueva Persona"**

**Resultado Esperado:**
- ⚠️ Se muestra alerta: "Se encontraron personas similares"
- ✅ Se ofrece usar una persona existente O crear de todos modos
- ✅ El sistema previene duplicados innecesarios

---

### TEST 7: Flujo completo "Otra Persona" con acentos ✅

**Precondición:**
- Usuario autenticado
- Acceso a formulario

**Pasos Completos:**
1. Ir a: `/correspondencia/documento-registro`
2. Seleccionar: **"Otra Persona"**
3. Buscar o registrar: **"José María Oporto-García"**
   - Nombre: "José María Oporto-García"
   - CI: "9876543-1"
   - Teléfono: "+591 75555555"
   - Tipo: "INTERNO"
   - Departamento: "Dirección General"
4. Seleccionar: **"Usar esta persona"**
5. Llenar documento:
   - Asunto: "Documento con remitente de nombre acentuado"
   - Tipo: "Memorando"
   - Urgencia: "Alta"
   - Departamento Destino: "Recursos Humanos"
   - Responsable: Seleccionar persona
6. Presionar: **"Registrar Documento"**

**Resultado Esperado:**
- ✅ Persona se registra correctamente
- ✅ Nombre "José María Oporto-García" se almacena sin problemas
- ✅ Documento se registra correctamente
- ✅ Remitente aparece con nombre completo en el documento

---

## CASOS EDGE CASOS A PROBAR

### Edge Case 1: Usuario sin Persona asociada

**Escenario:**
- Usuario está autenticado pero no tiene Persona vinculada

**Acción:**
- Seleccionar "Yo Mismo"
- Presionar "Registrar Documento"

**Resultado Esperado:**
- ❌ Error: "El usuario autenticado no tiene una persona asociada"
- ✅ Mensaje amigable al usuario

---

### Edge Case 2: Caracteres límite en nombre

**Escenarios a probar:**
- Nombre con 200 caracteres (máximo permitido) ✅
- Nombre con 201 caracteres ❌ (debe rechazarse)
- Solo espacios "           " ❌ (debe rechazarse)
- Número de caracteres especiales acentuados: "ÁÉÍÓÚÁÉÍÓÚÁÉÍÓÚ" ✅

---

### Edge Case 3: Combinaciones de caracteres

**Válidas:**
- "M. Cristina Oporto-García" ✅ (con punto y guión)
- "José-María López" ✅ (guión en nombre)
- "ANA MARÍA" ✅ (todo mayúsculas)
- "ana maría" ✅ (todo minúsculas)

**Inválidas:**
- "Ana María Lopez." ❌ (punto final)
- "Ana María Lopez/" ❌ (barra)
- "Ana María Lopez123" ❌ (número)

---

## AUTOMATIZACIÓN DE TESTS (FUTURO)

Para testing automatizado en el futuro, considerar:

```php
// Laravel Feature Tests - Ejemplo
public function test_puede_registrar_documento_yo_mismo()
{
    $user = User::factory()->create(['idPersona' => $persona->idPersona]);
    
    $response = $this->actingAs($user)->post('/documentos', [
        'opcion_remitente' => 'yo_mismo',
        'asunto' => 'Test',
        'tipo_documento' => 1,
        'nivel_urgencia' => 1,
        'departamento' => 1,
        'responsable_destino' => 1,
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('correspondencia', ['asunto' => 'Test']);
}

public function test_acepta_nombre_con_acentos()
{
    $response = $this->actingAs($user)->post('/documentos', [
        'opcion_remitente' => 'otra_persona',
        'nombre_remitente' => 'María Fernanda López-García',
        'ci_remitente' => '1234567-8',
        // ... otros campos ...
    ]);
    
    $response->assertSessionHasNoErrors();
}
```

---

## CHECKLIST FINAL DE VALIDACIÓN

### Before Deployment

- [ ] Todos los archivos PHP compilan sin errores
- [ ] Blade templates no tienen errores de sintaxis
- [ ] Se pueden registrar documentos con "Yo Mismo"
- [ ] Se pueden registrar documentos con "Otra Persona"
- [ ] Nombres con acentos se aceptan correctamente
- [ ] Validación condicional funciona correctamente
- [ ] No hay errores de validación falsos
- [ ] Base de datos se actualiza correctamente
- [ ] Interfaz de usuario responde apropiadamente
- [ ] Mensajes de error son claros y útiles

### After Deployment

- [ ] Monitorear errores en logs
- [ ] Verificar tasa de éxito de registros
- [ ] Recopilar feedback de usuarios
- [ ] Validar que no hay nombres corruptos en BD

---

**FIN DEL PLAN DE TESTING**
