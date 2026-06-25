# GUÍA DE TESTING: CORRECCIÓN DE PERMISOS EN DERIVACIONES

## 🧪 Pruebas Recomendadas

### TEST 1: Usuario No Responsable No Puede Derivar
**Objetivo:** Verificar que usuario A no pueda derivar documento de usuario B

**Pasos:**
1. Iniciar sesión como **Usuario A**
2. Ir a "Mi Bandeja"
3. Crear un documento o derivar uno que tenga
4. Derivar documento a **Usuario B**
5. Refrescar página (F5)
6. Buscar el documento en lista

**Resultado Esperado:**
- Botón "Ver" visible ✅
- Botón "Derivar" OCULTO ✅
- Botón "Finalizar" OCULTO ✅
- Mensaje: "Documento derivado a Usuario B"

---

### TEST 2: Responsable Actual Puede Derivar
**Objetivo:** Verificar que usuario B (responsable actual) sí pueda derivar

**Pasos:**
1. Iniciar sesión como **Usuario B**
2. Ir a "Mi Bandeja"
3. Buscar el documento derivado de Usuario A
4. Verificar que aparezca en la lista

**Resultado Esperado:**
- Documento aparece en bandeja ✅
- Botón "Ver" visible ✅
- Botón "Derivar" VISIBLE ✅
- Botón "Finalizar" VISIBLE ✅

---

### TEST 3: Ver Detalle Siempre Disponible
**Objetivo:** Verificar que cualquier usuario pueda ver el detalle

**Pasos:**
1. Iniciar sesión como **Usuario A** (NO responsable)
2. Ir a "Mi Bandeja"
3. Buscar documento derivado a Usuario B
4. Hacer clic en botón "Ver"

**Resultado Esperado:**
- Página de detalle carga ✅
- Se muestra información del documento ✅
- Se muestra historial de derivaciones ✅
- Se muestra seguimiento ✅
- Se muestra quién tiene actualmente el documento ✅

---

### TEST 4: Protección Backend
**Objetivo:** Verificar que backend bloquea acceso directo a URL

**Pasos (requiere herramientas de desarrollador):**
1. Iniciar sesión como **Usuario A** (NO responsable)
2. Abrir consola del navegador (F12)
3. Ir a Network / Formularios
4. Intentar enviar POST a:
   ```
   POST /envios/derivar/{id_documento}
   ```
5. Con datos: `idDepartamentoDestino=3&instruccion=test`

**Resultado Esperado:**
- Error HTTP 302 (redirección)
- Mensaje: "Este documento ya fue asignado a otro usuario..."

**Alternativa sin herramientas:**
1. Usuario A abre inspector de elementos (F12)
2. Busca formulario oculto en HTML
3. Intenta enviarlo directamente
4. Backend debe bloquear con error

---

### TEST 5: Admin Puede Todo
**Objetivo:** Verificar que admin tenga acceso total

**Pasos:**
1. Iniciar sesión como **ADMIN**
2. Ir a "Mi Bandeja"
3. Buscar cualquier documento

**Resultado Esperado:**
- Botón "Ver" visible ✅
- Botón "Derivar" VISIBLE (incluso para docs de otros) ✅
- Botón "Finalizar" VISIBLE ✅

---

### TEST 6: Estado Finalizado Bloquea Todo
**Objetivo:** Verificar que documento finalizado bloquea botones

**Pasos:**
1. Iniciar sesión como **Usuario B** (responsable actual)
2. Ir a "Mi Bandeja"
3. Seleccionar documento en estado "Atendido"
4. Hacer clic en "Finalizar"
5. Confirmar
6. Refrescar página

**Resultado Esperado:**
- Estado cambia a "Archivado" ✅
- Botón "Ver" VISIBLE ✅
- Botón "Derivar" OCULTO ✅
- Botón "Finalizar" OCULTO ✅
- Solo se muestra badge "Archivado" ✅

---

### TEST 7: Flujo Completo de Derivación
**Objetivo:** Verificar flujo completo entre múltiples usuarios

**Setup Previo:**
- Crear 3 usuarios: Alice, Bob, Charlie
- Todos deben tener usuario activo

**Pasos:**
1. **Alice** crea documento nuevo
2. **Alice** lo deriva a **Bob**
3. **Alice** intenta derivarlo de nuevo → Error "No es responsable"
4. **Bob** recibe documento en bandeja
5. **Bob** derivsa a **Charlie**
6. **Bob** intenta derivarlo de nuevo → Error "No es responsable"
7. **Charlie** recibe documento en bandeja
8. **Charlie** finaliza documento
9. **Charlie** intenta derivarlo de nuevo → Botón oculto (finalizado)
10. **Alice** intenta ver documento → Ver funciona, derivar bloqueado

**Resultado Esperado:**
```
ALICE: Ver ✅, Derivar ❌ (no responsable)
  ↓
BOB:   Ver ✅, Derivar ✅ (responsable)
  ↓
CHARLIE: Ver ✅, Derivar ✅ (responsable)
  ↓
FINALIZADO: Ver ✅, Derivar ❌ (finalizado)
```

---

### TEST 8: Botones Condicionales en Bandeja
**Objetivo:** Verificar que botones se muestren/oculten correctamente en tabla

**Pasos:**
1. Tener múltiples documentos en estados distintos:
   - Uno derivado a Usuario B (Usuario B es responsable)
   - Uno derivado a Usuario C (Usuario B no es responsable)
2. Iniciar sesión como **Usuario B**
3. Ir a "Mi Bandeja"
4. Observar tabla de documentos

**Resultado Esperado:**
| Documento | Ver | Derivar | Finalizar |
|-----------|-----|---------|-----------|
| De responsabilidad B | ✅ | ✅ | ✅ |
| De responsabilidad C | ✅ | ❌ | ❌ |

---

## 🔍 Verificación Visual

### Elementos Que Deben Estar Siempre Presentes
- ✅ Botón "Ver" en todas las filas
- ✅ Detalle del documento accesible
- ✅ Historial de derivaciones visible
- ✅ Seguimiento visible
- ✅ Información del remitente visible
- ✅ Estado del documento visible

### Elementos Que Deben Ocultarse (Si NO es responsable)
- ✅ Botón "Derivar" (cuando no es responsable)
- ✅ Botón "Atender" (cuando no es responsable)
- ✅ Botón "Archivar" (cuando no es responsable)
- ✅ Botón "Finalizar" (cuando no es responsable)

---

## 🐛 Troubleshooting

### Problema: Botón "Derivar" visible cuando no debería
**Causa posible:**
- `$ultimaDerivacion` es null
- `$ultimaDerivacion->idUsuarioAsignado` es null
- `Auth::id()` no se carga correctamente

**Solución:**
```blade
@php
dd($ultimaDerivacion, Auth::id());
@endphp
```

### Problema: Backend no bloquea acceso
**Causa posible:**
- Validación en controlador no se ejecuta
- Usuario es admin (debe permitir)
- Última derivación no existe

**Solución:**
- Revisar `EnvioController::derivar()`
- Comprobar que `Auth::user()->idRol != 1`
- Verificar que `ultimaDerivacion` existe en BD

### Problema: Admin no puede derivar
**Causa posible:**
- Rol no es 1
- Lógica de admin incorrecta
- Cache de sesión

**Solución:**
```blade
{{-- Verificar rol --}}
Auth::user()->idRol == 1
{{-- Debe ser 1 para admin --}}
```

---

## 📊 Matriz de Permisos

| Escenario | Ver | Derivar | Atender | Archivar | Finalizar |
|-----------|-----|---------|---------|----------|-----------|
| User = Responsable + No Finalizado | ✅ | ✅ | ✅ | ✅ | ✅ |
| User = Responsable + Finalizado | ✅ | ❌ | ❌ | ❌ | ❌ |
| User ≠ Responsable + No Finalizado | ✅ | ❌ | ❌ | ❌ | ❌ |
| User ≠ Responsable + Finalizado | ✅ | ❌ | ❌ | ❌ | ❌ |
| User = Admin + No Finalizado | ✅ | ✅ | ✅ | ✅ | ✅ |
| User = Admin + Finalizado | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## ✅ Checklist de Validación Final

- [ ] TEST 1: Usuario no responsable no puede derivar
- [ ] TEST 2: Responsable actual puede derivar
- [ ] TEST 3: Ver detalle siempre disponible
- [ ] TEST 4: Backend bloquea acceso directo
- [ ] TEST 5: Admin puede hacer todo
- [ ] TEST 6: Estado finalizado bloquea
- [ ] TEST 7: Flujo completo funciona
- [ ] TEST 8: Botones condicionales en tabla
- [ ] Botón "Ver" siempre visible
- [ ] Historial siempre visible
- [ ] Seguimiento siempre visible
- [ ] Botones de acción ocultos correctamente
- [ ] Mensajes de error claros
- [ ] No hay errores en consola

---

## 📝 Registro de Testing

```
Fecha de Testing: ___________
Probado por: ___________
Navegador: ___________
Versión: ___________

Resultado: ☐ PASÓ  ☐ FALLÓ

Bugs encontrados:
_________________________________
_________________________________
_________________________________

Observaciones:
_________________________________
_________________________________
```

---

**Última Actualización:** 2026-06-25  
**Versión:** 1.0  
**Estado:** Listo para Testing
