# GUÍA DE PRUEBA: Sistema de Estados - Búsqueda de Remitentes

## CAMBIOS IMPLEMENTADOS

### 1. Reescritura Completa del JavaScript
Se implementó un **State Machine** que gestiona 3 estados claramente diferenciados:

- **ESTADO 1 (BUSCADOR)**: Campo de búsqueda visible + resultados
- **ESTADO 2 (PERSONA_ENCONTRADA)**: Tarjeta con persona seleccionada + botones
- **ESTADO 3 (CREAR_NUEVA)**: Formulario para registrar nueva persona + botones

### 2. Funciones Principales (State Management)

```javascript
// Gestiona transición entre estados
irAEstado(estado)

// Vuelve al buscador limpiando TODO
volverAlBuscador()

// Transición al estado persona encontrada
irAEstadoPersonaEncontrada()

// Transición al estado crear nueva
irAEstadoCrearNueva()
```

### 3. Cambios en HTML

#### Buttons del ESTADO 1 (BUSCADOR)
- ✅ Agregar: Botón "Crear Nueva Persona" que aparece cuando no hay resultados

#### Buttons del ESTADO 2 (PERSONA_ENCONTRADA)
- ✅ "Usar esta persona" → `irAEstadoPersonaEncontrada()`
- ✅ "Buscar otra" → `volverAlBuscador()`

#### Buttons del ESTADO 3 (CREAR_NUEVA)
- ✅ "Registrar Nueva Persona" → `guardarNuevaPersona()`
- ✅ "Cancelar" → `volverAlBuscador()`

---

## CASOS DE PRUEBA

### CASO 1: Búsqueda Exitosa → Usar Persona → Formulario Completo
**Pasos:**
1. Seleccionar "Otra Persona"
2. Escribir "Juan" en el buscador
3. Esperar 400ms (debounce)
4. Ver resultados en ESTADO 1
5. Click en "Seleccionar" → Ir a ESTADO 2
6. Verificar tarjeta con datos de Juan
7. Click en "Usar esta persona" → Ir a ESTADO 2 (completo)
8. Completar resto del formulario
9. Enviar

**Esperado:**
- Los campos `ci_remitente`, `nombre_remitente`, etc. deben estar rellenados
- `idPersona_seleccionada` debe tener el ID
- Al enviar, debe usar la persona existente (NO crear nueva)

---

### CASO 2: Búsqueda sin Resultados → Crear Nueva
**Pasos:**
1. Seleccionar "Otra Persona"
2. Escribir "ZZZZZZZZZZ" (algo que no existe)
3. Ver mensaje "No encontrado" en ESTADO 1
4. Ver botón "Crear Nueva Persona"
5. Click en botón → Ir a ESTADO 3
6. Completar formulario
7. Enviar

**Esperado:**
- El formulario debe estar limpio
- Los campos deben estar vacios
- Al completar y enviar, debe crear nueva persona

---

### CASO 3: Búsqueda → Persona Encontrada → Buscar Otra (Ciclo 1)
**Pasos:**
1. Seleccionar "Otra Persona"
2. Escribir "Juan" → Click "Seleccionar" → ESTADO 2
3. Click "Buscar otra" → Volver a ESTADO 1
4. Verificar:
   - Campo búsqueda vacío ✓
   - Resultados vacios ✓
   - No hay tarjeta de persona ✓
   - Focus en campo búsqueda ✓

**Esperado:**
- Debe retornar a ESTADO 1 limpio
- El cursor debe estar en el campo de búsqueda

---

### CASO 4: Búsqueda → Persona Encontrada → Buscar Otra → Nueva Búsqueda (Ciclo 2)
**Pasos:**
1. ESTADO 1: Buscar "Juan" → Resultados
2. "Seleccionar Juan" → ESTADO 2
3. "Buscar otra" → ESTADO 1 (limpio)
4. Buscar "María" → Resultados diferentes
5. "Seleccionar María" → ESTADO 2
6. Verificar datos de María (no de Juan)

**Esperado:**
- Debe mostrar datos de María, NO los de Juan
- No debe haber "contaminación de datos"

---

### CASO 5: Búsqueda → Crear Nueva → Cancelar → Buscar Nuevamente
**Pasos:**
1. ESTADO 1: Buscar "ZZZZZ" → Sin resultados
2. "Crear Nueva Persona" → ESTADO 3
3. Escribir algo en el formulario:
   - Nombre: "Test Person"
   - CI: "12345678"
4. Click "Cancelar" → ESTADO 1
5. Verificar:
   - Campo búsqueda vacío ✓
   - Formulario limpio ✓
   - Alerta duplicados oculta ✓
   - Focus en buscador ✓
6. Buscar "Juan" nuevamente → Funcionan resultados

**Esperado:**
- Toda la información debe estar limpia
- No debe enviarse la persona "Test Person" parcialmente creada

---

### CASO 6: Búsqueda → Persona Encontrada → Usar → Volver Atrás (Cambio de Remitente)
**Pasos:**
1. Seleccionar "Otra Persona"
2. Buscar "Juan" → ESTADO 2 → "Usar esta persona"
3. Cambiar radio a "Yo Mismo"
4. Cambiar radio a "Otra Persona"
5. Verificar estado actual

**Esperado:**
- Debe retornar a ESTADO 1 (buscador)
- Campos limpios
- Cache de persona seleccionada debe estar vacío

---

### CASO 7: Múltiples Ciclos Completos Sin Recargar
**Pasos:**
1. Buscar "Juan" → Usar → Cambiar a buscar "María" → Usar
2. Buscar "Pedro" → Sin resultados → Crear → Cancelar → Buscar "Juan"
3. Usar "Juan" → Cambiar a "Yo Mismo" → Cambiar a "Otra Persona"
4. Buscar "María" → Usar

**Esperado:**
- Ningún paso debe requerir recarga
- Sin errores en consola
- Transiciones suaves entre estados

---

### CASO 8: Verificar que los Datos se Envían Correctamente
**Pasos:**
1. Buscar "Juan" → Seleccionar → "Usar esta persona"
2. Abrir DevTools (F12) → Network/Inspector
3. Completar formulario:
   - Asunto: "Test"
   - Tipo: "Correo"
   - Urgencia: "Normal"
   - Departamento destino: Seleccionar
   - Responsable: Seleccionar
4. Enviar formulario
5. Inspeccionar request enviado

**Esperado:**
- `idPersona_seleccionada`: ID de Juan
- `nombre_remitente`: Juan (del registro existente)
- `ci_remitente`: CI de Juan
- `tipo_remitente`: INTERNO o EXTERNO (según Juan)
- NO debe tener campos vacios innecesarios

---

### CASO 9: Crear Nueva Persona - Validar Campos Condicionales
**Pasos:**
1. Buscar "ZZZZZ" → "Crear Nueva Persona"
2. Tipo: "INTERNO"
3. Verificar: Mostrar "Departamento" y "Cargo", ocultar "Institución"
4. Tipo: "EXTERNO"
5. Verificar: Mostrar "Institución", ocultar "Departamento" y "Cargo"

**Esperado:**
- Los campos se muestran/ocultan correctamente
- No hay errores en consola

---

### CASO 10: Búsqueda Rápida (Mínimo 2 caracteres)
**Pasos:**
1. Seleccionar "Otra Persona"
2. Escribir "J" → NO debe buscar (< 2 caracteres)
3. Escribir "Ju" → Debe buscar después de 400ms

**Esperado:**
- Con 1 carácter: Sin búsqueda
- Con 2+ caracteres: Búsqueda con debounce

---

## INDICADORES DE ÉXITO

✅ Todos los estados se muestran correctamente sin superposición
✅ Las transiciones entre estados son suaves (sin parpadeos)
✅ Los datos se limpian al volver al buscador
✅ El focus está en el campo correcto después de transiciones
✅ Múltiples ciclos funcionan sin recargar la página
✅ Los datos se envían correctamente al formulario
✅ No hay errores en la consola del navegador

---

## NOTAS TÉCNICAS

### Cache de Persona
Se utiliza `personaSeleccionadaActual` para almacenar los datos de la persona mientras se navega entre estados.

### IDs de Estados
- `estado_buscador`
- `estado_persona_encontrada`
- `estado_crear_nueva`

### Campos Ocultos Importantes
- `idPersona_seleccionada` → Guarda el ID de la persona seleccionada
- `nombre_remitente`, `ci_remitente`, etc. → Se rellenan automáticamente

### Debounce
- 400ms de espera antes de buscar
- Evita consultas excesivas mientras el usuario sigue escribiendo

---

## ROLLBACK (Si algo falla)

Si es necesario volver a la versión anterior, las funciones antiguas que se removieron fueron:
- `limpiarSeleccion()`
- `mostrarFormulario()`
- Referencias a `fase_busqueda`, `persona_seleccionada_card`, `fase_formulario`

Todos reemplazados por el nuevo sistema de estados.
