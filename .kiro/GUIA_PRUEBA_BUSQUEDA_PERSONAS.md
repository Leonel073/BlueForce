# GUÍA DE PRUEBA: Sistema de Búsqueda de Personas

## 🎯 OBJETIVO

Verificar que el nuevo sistema de búsqueda inteligente y prevención de duplicados funciona correctamente en todas las situaciones.

---

## 📋 SETUP PREVIO

1. ✅ Tener personas existentes en tabla PERSONA
2. ✅ Navegador web actualizado (Chrome, Firefox, Safari, Edge)
3. ✅ Abrir Registro de Documentos: `/documentos/crear`
4. ✅ Asegurar que hay token CSRF (`<meta name="csrf-token">`)

---

## 🧪 CASOS DE PRUEBA

### TEST 1: Búsqueda por Nombre (Persona Existente)

**Precondición**: Existe "Juan García" en la tabla PERSONA

**Pasos**:
1. Abrir Registro de Documentos
2. Seleccionar opción "Otra Persona"
3. En campo "Buscar Persona", escribir: "juan"
4. Esperar 400ms (debounce)

**Resultado Esperado**:
- ✅ Aparece lista de coincidencias
- ✅ "Juan García" aparece en los resultados
- ✅ Muestra badge "INTERNO" o "EXTERNO"
- ✅ Muestra CI, departamento/institución, correo
- ✅ Botón "Usar" disponible

**Fallo Posible**:
- ❌ No aparecen resultados → Verificar que persona está activa (`activo = 1`)
- ❌ Demora >1 segundo → Verificar índices en BD

---

### TEST 2: Búsqueda por CI (Persona Existente)

**Precondición**: Existe persona con CI "1234567-8"

**Pasos**:
1. En campo "Buscar Persona", escribir: "1234567"
2. Esperar 400ms

**Resultado Esperado**:
- ✅ Aparece la persona con ese CI
- ✅ Coincidencia exacta o parcial
- ✅ Información completa visible

---

### TEST 3: Búsqueda por Correo (Persona Existente)

**Precondición**: Existe "juan@epab.bo"

**Pasos**:
1. En campo "Buscar Persona", escribir: "juan@epab"
2. Esperar 400ms

**Resultado Esperado**:
- ✅ Aparece la persona con ese correo
- ✅ Información completa incluida

---

### TEST 4: Búsqueda No Encontrada

**Precondición**: Escribir nombre que NO existe

**Pasos**:
1. En campo "Buscar Persona", escribir: "ZZZ_NOEXISTE"
2. Esperar 400ms

**Resultado Esperado**:
- ✅ Aparece mensaje: "No existe una persona registrada con esa información"
- ✅ Se habilita formulario para crear nueva
- ✅ Fase 1 (búsqueda) desaparece
- ✅ Fase 3 (crear nueva) aparece

---

### TEST 5: Seleccionar Persona Existente

**Pasos**:
1. Buscar "juan"
2. Hacer clic en botón "Usar" en uno de los resultados
3. Esperar que se cargue card de confirmación

**Resultado Esperado**:
- ✅ Desaparece lista de búsqueda
- ✅ Aparece card verde: "PERSONA SELECCIONADA"
- ✅ Card muestra todos los datos correctamente:
  - Nombre completo
  - CI
  - Tipo (INTERNO/EXTERNO)
  - Departamento o Institución
  - Cargo o Institución según tipo
  - Correo
  - Teléfono celular
- ✅ Mensaje: "Esta persona ya existe en el sistema"
- ✅ Botón "Buscar otra persona" disponible

---

### TEST 6: Campos Ocultos Completos

**Pasos**:
1. Seleccionar una persona existente
2. Abrir DevTools (F12)
3. En Console, ejecutar:
   ```javascript
   console.log(document.getElementById('ci_remitente_otra').value);
   console.log(document.getElementById('nombre_remitente_otra').value);
   console.log(document.getElementById('telefono_celular_otra').value);
   console.log(document.getElementById('tipo_remitente_otra').value);
   ```

**Resultado Esperado**:
- ✅ CI: Relleno correctamente
- ✅ Nombre: Relleno correctamente
- ✅ Celular: Relleno correctamente
- ✅ Tipo: INTERNO o EXTERNO según persona

---

### TEST 7: Crear Nueva Persona - Tipo INTERNO

**Pasos**:
1. Buscar algo que no existe
2. En formulario:
   - Nombre: "Carlos López"
   - CI: "9876543-1"
   - Celular: "72345678"
   - Tipo: INTERNO
3. Hacer clic en "Registrar Nueva Persona"

**Resultado Esperado**:
- ✅ Aparecen campos: Departamento, Cargo
- ✅ Campo Institución está oculto
- ✅ Verificación de duplicados ejecutada
- ✅ Si no hay duplicados: formulario enviado

---

### TEST 8: Crear Nueva Persona - Tipo EXTERNO

**Pasos**:
1. Buscar algo que no existe
2. En formulario:
   - Nombre: "María Rodríguez"
   - CI: "5555555-5"
   - Celular: "71111111"
   - Tipo: EXTERNO
   - Institución: "ONG ABC"
3. Hacer clic en "Registrar Nueva Persona"

**Resultado Esperado**:
- ✅ Aparece campo: Institución
- ✅ Campos Departamento y Cargo están ocultos
- ✅ Verificación de duplicados ejecutada

---

### TEST 9: Prevención de Duplicados - CI Existente

**Precondición**: Existe persona con CI "1234567-8"

**Pasos**:
1. Buscar algo no existente
2. Intentar crear nueva persona con:
   - Nombre: "Otro Juan"
   - CI: "1234567-8" (ya existe)
   - Tipo: INTERNO
3. Hacer clic en "Registrar Nueva Persona"

**Resultado Esperado**:
- ✅ Aparece alerta amarilla: "Se encontraron personas similares"
- ✅ Muestra la persona existente con CI coincidente
- ✅ Botón: "Usar esta persona"
- ✅ Botón: "Crear de todos modos"
- ✅ Hacer clic en "Usar esta persona" carga esa persona

---

### TEST 10: Prevención de Duplicados - Nombre Similar

**Precondición**: Existe "Juan Carlos García"

**Pasos**:
1. Buscar algo no existente
2. Intentar crear: "Juan Carlos García López"
3. Hacer clic en "Registrar Nueva Persona"

**Resultado Esperado**:
- ✅ Sistema detecta similitud de nombre
- ✅ Muestra alerta con persona similar
- ✅ Razón: "Similitud por nombre"

---

### TEST 11: Debounce Function (No Excesivas Búsquedas)

**Pasos**:
1. Abrir DevTools → Network
2. En campo "Buscar Persona", escribir letra por letra: "j-u-a-n"
3. Observar requests en Network

**Resultado Esperado**:
- ✅ Solo aparecen 1-2 requests (al dejar de escribir)
- ❌ NO deberían aparecer 4 requests (uno por cada letra)
- ✅ Debounce evita consultaspara cada carácter

---

### TEST 12: Flujo Completo - Del Búsqueda a Documento Guardado

**Pasos**:
1. Abrir Registro de Documentos
2. Llenar datos del documento (asunto, tipo, urgencia)
3. Seleccionar "Otra Persona"
4. Buscar "juan"
5. Seleccionar resultado
6. Seleccionar departamento y responsable
7. Hacer clic en "Registrar Documento"

**Resultado Esperado**:
- ✅ Documento se guarda correctamente
- ✅ Remitente es la persona seleccionada (ID correcto)
- ✅ Sin duplicados en tabla PERSONA
- ✅ Derivación automática creada
- ✅ Mensaje de éxito

---

### TEST 13: CI Ingresado - Onblur Automático

**Pasos**:
1. Seleccionar "Otra Persona"
2. En formulario nueva persona:
   - Ingresa CI: "1234567-8" (existente)
   - Presiona TAB (sale del campo)
3. Esperar 400ms

**Resultado Esperado**:
- ✅ Automáticamente busca por CI
- ✅ Si existe: carga en búsqueda
- ✅ Si no existe: permanece en formulario

---

### TEST 14: Búsqueda por Departamento (Interno)

**Pasos**:
1. En campo "Buscar Persona", escribir: "Legal" (nombre del departamento)

**Resultado Esperado**:
- ✅ Aparecen personas del departamento "Legal"
- ✅ Se busca en la relación departamento

---

### TEST 15: Limpiar Selección

**Pasos**:
1. Buscar y seleccionar una persona
2. Hacer clic en "Buscar otra persona"

**Resultado Esperado**:
- ✅ Campo de búsqueda se limpia
- ✅ Vuelve a Fase 1
- ✅ Card de selección desaparece

---

## 🐛 DEBUGGING

### Consola del Navegador

Para monitorear qué ocurre:

```javascript
// Ver todas las búsquedas
document.getElementById('buscar_persona_input').addEventListener('input', (e) => {
    console.log('Búsqueda:', e.target.value);
});

// Ver personas seleccionadas
console.log(document.getElementById('persona_seleccionada_id').value);

// Ver datos rellenados
console.log({
    ci: document.getElementById('ci_remitente_otra').value,
    nombre: document.getElementById('nombre_remitente_otra').value,
    tipo: document.getElementById('tipo_remitente_otra').value
});
```

### Network Tab

Filtrar por `/personas/` para ver:
- `/personas/buscar-avanzado?q=...`
- `/personas/verificar-duplicados` (POST)

### Backend Logs

En `storage/logs/laravel.log`:
```
[timestamp] local.INFO: Query SELECT * FROM PERSONA WHERE ...
```

---

## ✅ CHECKLIST FINAL

- [ ] Búsqueda funciona con mínimo 2 caracteres
- [ ] Debounce evita excesivas consultas
- [ ] Persona existente se selecciona correctamente
- [ ] Campos se rellenan automáticamente
- [ ] Persona no existente habilita formulario
- [ ] Prevención de duplicados funciona
- [ ] Campos condicionales (INTERNO/EXTERNO) funcionan
- [ ] Documento se guarda correctamente
- [ ] No hay duplicados en BD
- [ ] Mensajes de error son claros
- [ ] UX es intuitivo y rápido

---

## 🎉 RESULTADO

Si todos los tests pasan: **LISTO PARA PRODUCCIÓN** ✅

Si algún test falla: Revisar logs y reportar issue específico.

---

**Generado**: 2026-06-29
**Versión**: 1.0
