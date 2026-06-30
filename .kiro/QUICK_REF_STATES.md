# ⚡ QUICK REFERENCE: Estado Buscador

## 🗺️ MAPA MENTAL

```
┌─────────────────────────────────────────────────┐
│           OTRA PERSONA SELECCIONADA             │
└─────────────────────────────────────────────────┘
                        │
                        ↓ toggleRemitenteMode()
                        ↓ volverAlBuscador()
                        ↓
        ┌───────────────────────────┐
        │   ESTADO 1: BUSCADOR      │
        │  (estado_buscador)        │
        └───────────────────────────┘
                ↓               ↑
                │               │
    [Escribir]  │               │  [Buscar otra]
                │               │  [Cancelar]
                │               │
                ↓               │
        Resultados encontrados  │
                │               │
        [Seleccionar]───────────┤
                │               │
                ↓               │
        ┌───────────────────────────┐
        │ ESTADO 2: PERSONA ENCONTRADA
        │ (estado_persona_encontrada)│
        └───────────────────────────┘
                │
        [Usar esta persona]
                │
                ↓
        Rellena campos + Guarda ID
                │
                ↓
        USUARIO ENVÍA FORMULARIO
        
        
Si NO hay resultados:
        ↓
[Crear Nueva Persona]
        ↓
        ┌───────────────────────────┐
        │ ESTADO 3: CREAR NUEVA     │
        │ (estado_crear_nueva)      │
        └───────────────────────────┘
                │
    [Registrar] o [Cancelar]
                │
        Si [Cancelar] → ESTADO 1 limpio
```

---

## 🎮 CONTROLES PRINCIPALES

| Botón | Origen | Destino | Función |
|-------|--------|---------|---------|
| Seleccionar | ESTADO 1 | ESTADO 2 | Muestra persona encontrada |
| Usar esta persona | ESTADO 2 | Rellena | Completa campos y queda listo |
| Buscar otra | ESTADO 2 | ESTADO 1 | Limpia todo, vuelve a buscar |
| Crear Nueva Persona | ESTADO 1 | ESTADO 3 | Muestra formulario vacío |
| Registrar Nueva Persona | ESTADO 3 | Submit | Verifica duplicados y envía |
| Cancelar | ESTADO 3 | ESTADO 1 | Limpia formulario, vuelve |

---

## 🧠 FUNCIONES CLAVE

### `irAEstado(estado)`
**Uso:** Transición entre estados
```javascript
irAEstado(ESTADOS.BUSCADOR)            // Muestra buscador
irAEstado(ESTADOS.PERSONA_ENCONTRADA)  // Muestra tarjeta
irAEstado(ESTADOS.CREAR_NUEVA)         // Muestra formulario
```

**Qué hace:**
1. Oculta todos los estados
2. Muestra el solicitado
3. Si BUSCADOR → Focus en campo

---

### `volverAlBuscador()`
**Uso:** Reset completo a ESTADO 1
```javascript
volverAlBuscador()
```

**Limpia:**
- Campo búsqueda
- Resultados
- Formulario
- Cache persona
- Alerta duplicados
- Focus en búsqueda

**Llamado por:**
- "Buscar otra" button
- "Cancelar" button
- Cambio radio "Yo Mismo"

---

### `irAEstadoPersonaEncontrada()`
**Uso:** Transición a ESTADO 2 + Rellena campos
```javascript
irAEstadoPersonaEncontrada()
```

**Rellena campos ocultos:**
```
ci_remitente
nombre_remitente
telefono_celular
telefono_fijo
correo_remitente
tipo_remitente
cargo_remitente (si INTERNO)
persona_seleccionada_id
```

---

### `irAEstadoCrearNueva()`
**Uso:** Transición a ESTADO 3
```javascript
irAEstadoCrearNueva()
```

**Qué hace:**
1. Limpia formulario
2. Va a ESTADO 3

---

## 🎯 ESTADOS EN DETALLE

### ESTADO 1: BUSCADOR
```
┌─ estado_buscador ─────────────────┐
│                                   │
│ [Campo búsqueda]                  │
│ "Escriba al menos 2 caracteres"   │
│                                   │
│ Resultados (si encontrados):      │
│ ├─ [Juan García] [Seleccionar]   │
│ ├─ [María López] [Seleccionar]   │
│ └─ ...                            │
│                                   │
│ O: "No encontrado"                │
│     [Crear Nueva Persona]         │
│                                   │
└───────────────────────────────────┘
```

---

### ESTADO 2: PERSONA ENCONTRADA
```
┌─ estado_persona_encontrada ────────┐
│                                    │
│ ✓ PERSONA SELECCIONADA            │
│                                    │
│ Nombre:  Juan García              │
│ CI:      1234567-8                │
│ Tipo:    INTERNO                  │
│ Correo:  juan@ejemplo.com         │
│ Cargo:   Director Legal           │
│ Depto:   Dirección Legal          │
│                                    │
│ [Usar esta persona] [Buscar otra] │
│                                    │
└────────────────────────────────────┘
```

---

### ESTADO 3: CREAR NUEVA
```
┌─ estado_crear_nueva ──────────────┐
│                                   │
│ ✎ Registrar Nueva Persona        │
│                                   │
│ Nombre Completo: [________]       │
│ CI:              [________]       │
│ Teléfono:        [________]       │
│ Correo:          [________]       │
│ Tipo:   [INTERNO / EXTERNO]       │
│                                   │
│ Si INTERNO:                       │
│   Cargo:         [________]       │
│                                   │
│ Si EXTERNO:                       │
│   Institución:   [________]       │
│                                   │
│ [Registrar] [Cancelar]            │
│                                   │
└───────────────────────────────────┘
```

---

## 🔗 VARIABLE: `personaSeleccionadaActual`

**Tipo:** Object | null

**Estructura:**
```javascript
{
    idPersona: 123,
    nombre: "Juan García",
    ci: "1234567-8",
    tipo: "INTERNO",
    cargo: "Director",
    departamento: "Legal",
    institucion: null,
    correo: "juan@mail.com",
    telefono_celular: "70123456",
    telefono_fijo: "2345678"
}
```

**Lifecycle:**
1. NULL al inicio
2. Asignada en `mostrarPersonaSeleccionada()`
3. Usada en `irAEstadoPersonaEncontrada()`
4. Limpiada en `volverAlBuscador()`

---

## ⏱️ DEBOUNCE

**Valor:** 400ms

**Función:** Evita búsquedas excesivas

**Flujo:**
```
Usuario escribe "J" → (no busca < 2 caracteres)
Usuario escribe "Ju" → timer inicia (400ms)
Usuario escribe "Juan" → timer se reinicia (400ms)
Usuario espera → Después 400ms sin cambios → BUSCA
```

---

## 🛡️ PREVENCIÓN DE ERRORES

### Event.preventDefault()
Todos los botones tienen:
```javascript
onclick="funcionX(); event.preventDefault();"
```
Evita submit accidental del formulario padre

---

### Validaciones por Estado

#### ESTADO 1
- Mínimo 2 caracteres para buscar
- Debounce 400ms
- Auto-botón crear si sin resultados

#### ESTADO 3
- Nombre: Obligatorio
- CI: Obligatorio
- Teléfono: Obligatorio
- Tipo: Obligatorio
- Institución: Obligatorio si EXTERNO

---

## 📱 RESPONSIVENESS

Todos los botones:
- `flex-grow-1` - Ocupan espacio disponible
- `gap-2` - Separación entre botones
- `rounded-3` - Bordes redondeados

---

## 🔧 DEBUG TIPS

### Ver estado actual en consola
```javascript
console.log(document.getElementById('estado_buscador').style.display)
console.log(personaSeleccionadaActual)
```

### Simular clicks programáticamente
```javascript
document.getElementById('btn_crear_nueva_desde_busqueda').click()
```

### Ver datos rellenados
```javascript
console.log({
    ci: document.getElementById('ci_remitente_otra').value,
    nombre: document.getElementById('nombre_remitente_otra').value,
    tipo: document.getElementById('tipo_remitente_otra').value
})
```

---

## 🚀 NEXT STEPS

1. **Pruebas Manual:** Ejecutar 10 casos de prueba
2. **QA:** Verificar ciclos múltiples
3. **Performance:** Monitorear requests
4. **Responsividad:** Testar en mobile
5. **Accesibilidad:** Verificar focus management

---

## 📞 SOPORTE

Documentación completa:
- `.kiro/GUIA_TEST_ESTADO_BUSCADOR.md` - 10 casos de prueba
- `.kiro/RESUMEN_TASK_6.md` - Explicación completa
- `.kiro/TAREA_6_ESTADO_BUSCADOR_COMPLETADA.md` - Detalles técnicos

**Archivo Editado:** `documento-registro.blade.php`
**Líneas JavaScript:** ~900-1100 (State Machine + funciones)
