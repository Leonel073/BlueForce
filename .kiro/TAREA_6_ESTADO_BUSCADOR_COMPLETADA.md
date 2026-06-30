# TAREA 6: Sistema de Estados - Búsqueda de Remitentes ✅ COMPLETADA

## RESUMEN

Se completó la reescritura del JavaScript para implementar un **State Machine** que gestiona el flujo de búsqueda sin recargar la página. Los usuarios ahora pueden navegar entre 3 estados claramente diferenciados con transiciones suaves.

---

## ESTADOS IMPLEMENTADOS

### 1️⃣ ESTADO 1: BUSCADOR
**Div ID:** `estado_buscador`

Visible por defecto cuando se selecciona "Otra Persona"

**Elementos:**
- Campo de búsqueda: `buscar_persona_input`
- Resultados: `resultados_busqueda` + `lista_resultados`
- Mensaje "No encontrado": `no_encontrado`
- Botón "Crear Nueva Persona": `btn_crear_nueva_desde_busqueda`

**Transiciones:**
- → ESTADO 2: Al click "Seleccionar" en un resultado
- → ESTADO 3: Al click "Crear Nueva Persona" o cuando no hay resultados

---

### 2️⃣ ESTADO 2: PERSONA ENCONTRADA
**Div ID:** `estado_persona_encontrada`

Muestra tarjeta con información de persona seleccionada

**Elementos:**
- Tarjeta verde con datos: `sel_nombre`, `sel_ci`, `sel_tipo`, `sel_correo`, etc.
- Botones:
  - "Usar esta persona" → Finaliza selección
  - "Buscar otra" → Vuelve a ESTADO 1

**Datos Mostrados:**
- Nombre
- CI
- Tipo (INTERNO/EXTERNO)
- Correo
- Departamento/Cargo (si INTERNO)
- Institución (si EXTERNO)
- Teléfono Celular

---

### 3️⃣ ESTADO 3: CREAR NUEVA PERSONA
**Div ID:** `estado_crear_nueva`

Formulario para crear nuevo registro

**Campos (Siempre Visibles):**
- Nombre Completo *
- Carnet de Identidad *
- Teléfono Celular *
- Teléfono Fijo
- Correo
- Tipo de Remitente (INTERNO/EXTERNO) *

**Campos Condicionales:**
- Si INTERNO: Departamento, Cargo
- Si EXTERNO: Institución

**Botones:**
- "Registrar Nueva Persona" → Verifica duplicados y crea/envía
- "Cancelar" → Vuelve a ESTADO 1

---

## FUNCIONES PRINCIPALES

### `irAEstado(estado)`
**Descripción:** Transición central entre estados

```javascript
irAEstado(ESTADOS.BUSCADOR)           // Ir a estado 1
irAEstado(ESTADOS.PERSONA_ENCONTRADA) // Ir a estado 2
irAEstado(ESTADOS.CREAR_NUEVA)        // Ir a estado 3
```

**Qué hace:**
1. Oculta todos los estados
2. Muestra el estado solicitado
3. Ejecuta acciones específicas (ej: focus en buscador)

---

### `volverAlBuscador()`
**Descripción:** Retorna a ESTADO 1 limpiando TODO

**Limpia:**
- Campo de búsqueda: `buscar_persona_input`
- Resultados: `resultados_busqueda`, `lista_resultados`
- Alerta "No encontrado": `no_encontrado`
- Formulario: campos de "Crear Nueva"
- Cache: `personaSeleccionadaActual`
- Alerta de duplicados: `alerta_duplicados`

**Acciones Finales:**
- Focus en campo de búsqueda
- Ir a ESTADO 1 (BUSCADOR)

---

### `irAEstadoPersonaEncontrada()`
**Descripción:** Transición a ESTADO 2

**Qué hace:**
1. Rellena todos los campos ocultos con datos de `personaSeleccionadaActual`
2. Guarda ID en `persona_seleccionada_id`
3. Transiciona a ESTADO 2

**Campos Rellenados:**
```javascript
ci_remitente_otra
nombre_remitente_otra
telefono_celular_otra
telefono_fijo_otra
correo_remitente_otra
tipo_remitente_otra
cargo_remitente_otra (si INTERNO)
```

---

### `irAEstadoCrearNueva()`
**Descripción:** Transición a ESTADO 3

**Qué hace:**
1. Limpia formulario
2. Transiciona a ESTADO 3

---

### `volverAlBuscador()`
**Descripción:** Utilidad para navegar de forma consistente

Llamada desde:
- ESTADO 2: Botón "Buscar otra"
- ESTADO 3: Botón "Cancelar"

---

## CAMBIOS EN HTML

### Botones Agregados/Modificados

#### ESTADO 1 (BUSCADOR)
```html
<button type="button" class="btn btn-outline-primary w-100 rounded-3" 
        onclick="irAEstadoCrearNueva(); event.preventDefault();" 
        style="display: none;" 
        id="btn_crear_nueva_desde_busqueda">
    <i class="bi bi-plus-circle me-2"></i>Crear Nueva Persona
</button>
```

- Se muestra cuando NO hay resultados (`display: none` por defecto)
- Se actualiza en `buscarPersonasAvanzado()`

#### ESTADO 2 (PERSONA ENCONTRADA)
```html
<button type="button" class="btn btn-primary flex-grow-1 rounded-3" 
        onclick="irAEstadoPersonaEncontrada(); event.preventDefault();">
    <i class="bi bi-check-circle-fill me-2"></i>Usar esta persona
</button>

<button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" 
        onclick="volverAlBuscador(); event.preventDefault();">
    <i class="bi bi-arrow-counterclockwise me-2"></i>Buscar otra
</button>
```

#### ESTADO 3 (CREAR NUEVA)
```html
<button type="button" class="btn btn-primary flex-grow-1 rounded-3" 
        onclick="guardarNuevaPersona(); event.preventDefault();">
    <i class="bi bi-check-circle-fill me-2"></i>Registrar Nueva Persona
</button>

<button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" 
        onclick="volverAlBuscador(); event.preventDefault();">
    <i class="bi bi-x-lg me-2"></i>Cancelar
</button>
```

---

## VARIABLES GLOBALES

### `personaSeleccionadaActual`
**Tipo:** Object (null si no hay selección)

**Estructura:**
```javascript
{
    idPersona: number,
    nombre: string,
    ci: string,
    tipo: "INTERNO" | "EXTERNO",
    cargo: string | null,
    departamento: string | null,
    institucion: string | null,
    correo: string | null,
    telefono_celular: string | null,
    telefono_fijo: string | null
}
```

**Uso:** Almacenar datos durante navegación entre estados

---

### `ESTADOS` (Enum)
```javascript
const ESTADOS = {
    BUSCADOR: 'estado_buscador',
    PERSONA_ENCONTRADA: 'estado_persona_encontrada',
    CREAR_NUEVA: 'estado_crear_nueva'
};
```

---

### `debounceTimer`
**Tipo:** timeout ID | null

**Uso:** Debounce de 400ms en búsqueda

---

## FLUJOS COMPLETAMENTE SOPORTADOS

### ✅ Flujo 1: Búsqueda → Usar Persona → Envío
1. ESTADO 1: Escribir "Juan"
2. Resultados aparecen
3. Click "Seleccionar" → Va a ESTADO 2
4. Click "Usar esta persona" → Rellena campos
5. Completa resto del formulario
6. Envía

### ✅ Flujo 2: Búsqueda → Sin Resultados → Crear Nueva
1. ESTADO 1: Escribir "ZZZZZ"
2. "No encontrado" aparece
3. Click "Crear Nueva Persona" → ESTADO 3
4. Completa formulario
5. Envía

### ✅ Flujo 3: Búsqueda → Usar → Buscar Otra → Usar Diferente
1. ESTADO 1: Buscar "Juan" → ESTADO 2
2. Click "Buscar otra" → ESTADO 1 (limpio)
3. Buscar "María" → Resultados nuevos
4. Click "Seleccionar" → ESTADO 2 (datos de María)

### ✅ Flujo 4: Crear → Cancelar → Buscar
1. ESTADO 1: Sin resultados → ESTADO 3
2. Click "Cancelar" → ESTADO 1 (limpio)
3. Buscar nuevamente → Funciona correctamente

### ✅ Flujo 5: Ciclos Múltiples Sin Recargar
Permite repetir los flujos anteriores tantas veces como se necesite sin recarga de página

---

## CAMBIOS EN JAVASCRIPT

### Removidas (Obsoletas)
```javascript
- limpiarSeleccion()        // Reemplazado por volverAlBuscador()
- mostrarFormulario()       // Reemplazado por irAEstadoCrearNueva()
- fase_busqueda             // Reemplazado por estado_buscador
- persona_seleccionada_card // Reemplazado por estado_persona_encontrada
- fase_formulario           // Reemplazado por estado_crear_nueva
```

### Agregadas (Nuevas)
```javascript
- irAEstado(estado)                    // State manager central
- volverAlBuscador()                   // Limpia y retorna a ESTADO 1
- irAEstadoPersonaEncontrada()        // Transición a ESTADO 2
- irAEstadoCrearNueva()               // Transición a ESTADO 3
```

### Mejoradas (Existentes)
```javascript
- toggleRemitenteMode()    // Ahora llama a volverAlBuscador() si se selecciona "Otra Persona"
- buscarPersonasAvanzado() // Ahora maneja el botón "Crear Nueva" dinámicamente
- seleccionarPersona()     // Llama a mostrarPersonaSeleccionada()
- mostrarPersonaSeleccionada() // Ahora usa cache y llama a irAEstadoPersonaEncontrada()
```

---

## VALIDACIONES

### En ESTADO 1
- Mínimo 2 caracteres para buscar
- Debounce de 400ms
- Si sin resultados → Muestra "No encontrado" + botón "Crear Nueva"

### En ESTADO 3
- Campo Nombre: Obligatorio
- Campo CI: Obligatorio
- Campo Teléfono Celular: Obligatorio
- Campos condicionales según Tipo (INTERNO/EXTERNO)

---

## PUNTOS CLAVE

1. **Sin recargas**: Todo se gestiona con JavaScript
2. **Limpeza completa**: Al volver al buscador, TODO se limpia
3. **Cache inteligente**: Usa `personaSeleccionadaActual` para persistencia temporal
4. **Focus management**: El cursor va automáticamente al campo de búsqueda
5. **Prevención de eventos**: Todos los botones usan `event.preventDefault()`
6. **Estados visibles**: Solo 1 estado visible a la vez
7. **Transiciones claras**: El usuario siempre sabe en qué estado está

---

## ARCHIVO EDITADO

✅ `resources/views/correspondencia/documento-registro.blade.php`
- Reescritura completa de JavaScript (aprox. 200+ líneas)
- Agregado botón "Crear Nueva Persona" en ESTADO 1
- Actualización de handlers de botones
- Comentarios claros de estados

---

## PRUEBAS RECOMENDADAS

Ver: `.kiro/GUIA_TEST_ESTADO_BUSCADOR.md` (10 casos de prueba detallados)

---

## PRÓXIMOS PASOS

- Ejecutar casos de prueba de la guía
- Verificar que el estado se mantiene correcto durante ciclos múltiples
- Validar que los datos se envían correctamente al backend
- Confirmar que no hay errores en la consola del navegador

---

**ESTADO:** ✅ COMPLETADO - Listo para pruebas
**FECHA:** 29 de Junio, 2026
**SIN CAMBIOS EN:** Base de datos, Migraciones, Modelos, Controllers (backend)
