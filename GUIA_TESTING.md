# 🧪 GUÍA DE TESTING - MÓDULO DE REGISTRO DOCUMENTAL

## ⚠️ REQUISITOS PREVIOS

Antes de hacer testing, asegúrate que:

1. **Base de Datos Migrada**
   ```bash
   php artisan migrate
   ```

2. **Seeders Ejecutados**
   ```bash
   php artisan db:seed
   ```

3. **Servidor Laravel Ejecutándose**
   ```bash
   php artisan serve
   ```
   → Accesible en: `http://localhost:8000`

---

## 🧪 CASOS DE PRUEBA

### TEST 1: Acceso al Formulario
**Objetivo:** Verificar que la ruta muestra el formulario correctamente

**Pasos:**
1. Abrir navegador
2. Ir a: `http://localhost:8000/documentos`

**Resultado Esperado:**
- ✅ Página carga correctamente
- ✅ Título: "📄 Registro de Documentos"
- ✅ Formulario visible con 3 secciones
- ✅ Sidebar muestra "Documentos" como activo
- ✅ Paleta de colores corporativa visible

---

### TEST 2: Registro Exitoso - Caso Completo

**Objetivo:** Guardar un documento con todos los datos

**Datos de Entrada:**
```
CITE: SGPA-2026-05-0001
Asunto: Solicitud de revisión de proyecto
Tipo Documento: Memorándum
Nivel Urgencia: Urgente
Nombre Remitente: Dra. Ana Rodríguez
Correo: ana.rodriguez@ejemplo.com
Cargo: Directora
Institución: Universidad Nacional
Tipo Remitente: EXTERNO
Departamentos: ✓ Dirección General, ✓ Secretaría
```

**Pasos:**
1. Completar cada campo con los datos
2. Seleccionar 2 departamentos (verificar contador = 2)
3. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ Mensaje verde: "¡Éxito! Documento registrado correctamente"
- ✅ Formulario se limpia (todos los campos vacíos)
- ✅ Contador de departamentos vuelve a 0
- ✅ No hay errores en consola del navegador (F12)

**Verificación en BD:**
```sql
-- Verificar que se creó la PERSONA
SELECT * FROM PERSONA WHERE nombre = 'Dra. Ana Rodríguez';

-- Verificar que se creó la CORRESPONDENCIA
SELECT * FROM CORRESPONDENCIA WHERE cite = 'SGPA-2026-05-0001';

-- Verificar que se crearon los DESTINATARIOS
SELECT * FROM CORRESPONDENCIA_DESTINATARIO 
WHERE idDocumento = (SELECT idDocumento FROM CORRESPONDENCIA WHERE cite = 'SGPA-2026-05-0001');
```

---

### TEST 3: Validación - Campo CITE Vacío

**Objetivo:** Verificar que el CITE es requerido

**Datos:**
```
CITE: [VACÍO]
Asunto: Test asunto
Tipo Documento: Memorándum
Nivel Urgencia: Normal
Nombre Remitente: Juan Pérez
Tipo Remitente: INTERNO
Departamentos: ✓ Dirección General
```

**Pasos:**
1. Dejar CITE en blanco
2. Completar otros campos
3. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ Mensaje de error: "El CITE es requerido"
- ✅ Campo CITE tiene borde rojo
- ✅ Formulario NO se envía
- ✅ Datos se preservan (withInput)

---

### TEST 4: Validación - Email Inválido

**Objetivo:** Verificar formato de email

**Datos:**
```
CITE: SGPA-2026-05-0002
Asunto: Test
Tipo Documento: Oficio
Nivel Urgencia: Normal
Nombre Remitente: Pedro López
Correo: algo@[INVÁLIDO]
Tipo Remitente: INTERNO
Departamentos: ✓ Recursos Humanos
```

**Pasos:**
1. En Correo, escribir: `algo@`
2. Completar otros campos
3. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ Validación HTML5 previene envío
- ✅ Mensaje: "Por favor ingresa una dirección de correo electrónico válida"
- ✅ Campo tiene feedback visual

**Alternativa (desactivar validación HTML5):**
```
Si logras enviar:
- Servidor rechaza con: "El correo debe ser válido"
```

---

### TEST 5: Validación - Sin Departamentos

**Objetivo:** Verificar que al menos 1 departamento es requerido

**Datos:**
```
CITE: SGPA-2026-05-0003
Asunto: Test
Tipo Documento: Resolución
Nivel Urgencia: Normal
Nombre Remitente: María García
Tipo Remitente: EXTERNO
Departamentos: [NINGUNO SELECCIONADO]
```

**Pasos:**
1. Completar formulario
2. NO seleccionar ningún departamento (contador debe estar en 0)
3. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ Alerta de error: "Debe seleccionar al menos un departamento destinatario"
- ✅ Sección de Destinatarios tiene borde rojo
- ✅ Documento NO se guarda
- ✅ Datos se preservan

---

### TEST 6: Validación - Todos los Campos Vacíos

**Objetivo:** Verificar múltiples validaciones simultáneas

**Pasos:**
1. NO completar nada
2. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ Se muestran TODOS los errores:
  - El CITE es requerido
  - El asunto es requerido
  - Debe seleccionar un tipo de documento
  - Debe seleccionar un nivel de urgencia
  - El nombre del remitente es requerido
  - Debe indicar si el remitente es INTERNO o EXTERNO
  - Debe seleccionar al menos un departamento destinatario

**Visual:**
- ✅ Alerta superior con lista de errores
- ✅ Campos rojos con validación
- ✅ Formulario NO se envía

---

### TEST 7: Datos Opcionales - Correo Vacío

**Objetivo:** Verificar que Correo, Cargo e Institución son opcionales

**Datos:**
```
CITE: SGPA-2026-05-0004
Asunto: Test con datos opcionales
Tipo Documento: Memorándum
Nivel Urgencia: Normal
Nombre Remitente: Oficial Anónimo
Correo: [VACÍO]
Cargo: [VACÍO]
Institución: [VACÍO]
Tipo Remitente: INTERNO
Departamentos: ✓ Secretaría
```

**Pasos:**
1. Llenar requeridos
2. Dejar Correo, Cargo, Institución vacíos
3. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ "¡Éxito! Documento registrado correctamente"
- ✅ En BD: correo = NULL, cargo = NULL, institucion = NULL

**Verificación BD:**
```sql
SELECT * FROM PERSONA 
WHERE nombre = 'Oficial Anónimo' 
AND correo IS NULL 
AND cargo IS NULL;
```

---

### TEST 8: Múltiples Departamentos

**Objetivo:** Verificar que se crean múltiples destinatarios

**Datos:**
```
CITE: SGPA-2026-05-0005
Asunto: Comunicado para todos los departamentos
Tipo Documento: Oficio
Nivel Urgencia: Urgente
Nombre Remitente: Director General
Tipo Remitente: INTERNO
Departamentos: 
  ✓ Ventanilla de Recepción
  ✓ Dirección General
  ✓ Secretaría
  ✓ Departamento Administrativo
  ✓ Departamento Financiero
  ✓ Recursos Humanos
```

**Pasos:**
1. Seleccionar los 6 departamentos (contador debe mostrar 6)
2. Completar formulario
3. Click en "Guardar Documento"

**Resultado Esperado:**
- ✅ Éxito: Documento registrado
- ✅ En BD se crean 6 registros en CORRESPONDENCIA_DESTINATARIO

**Verificación BD:**
```sql
SELECT COUNT(*) as destinatarios 
FROM CORRESPONDENCIA_DESTINATARIO 
WHERE idDocumento = (SELECT idDocumento FROM CORRESPONDENCIA WHERE cite = 'SGPA-2026-05-0005');
-- Debe retornar: 6
```

---

### TEST 9: Contador Dinámico de Departamentos

**Objetivo:** Verificar que el contador se actualiza en tiempo real

**Pasos:**
1. Abrir formulario
2. Observar contador: debe mostrar "0 departamento(s) seleccionado(s)"
3. Seleccionar 1 departamento: contador → 1
4. Seleccionar 2 más: contador → 3
5. Deseleccionar 1: contador → 2
6. Deseleccionar todas: contador → 0

**Resultado Esperado:**
- ✅ Contador actualiza instantáneamente
- ✅ Sin refrescar página
- ✅ Sin hacer POST

---

### TEST 10: Responsividad Móvil

**Objetivo:** Verificar que el formulario se adapta a pantallas pequeñas

**Pasos:**
1. F12 (Abrir Developer Tools)
2. Click en Responsive Design Mode (Ctrl+Shift+M)
3. Probar en diferentes tamaños:
   - 📱 Mobile (375px)
   - 📱 Tablet (768px)
   - 💻 Desktop (1920px)

**Resultado Esperado:**
- ✅ Desktop: 2 columnas (formulario + destinatarios)
- ✅ Tablet: Ajustado proporcionalmente
- ✅ Mobile: 1 columna, todo apilado
- ✅ Botones legibles y clickeables
- ✅ Scroll suave
- ✅ Sin overflow horizontal

---

### TEST 11: Preservación de Datos en Error

**Objetivo:** Verificar que los datos NO se pierden si hay error

**Pasos:**
1. Completar formulario
2. Intencionalmente dejar vacío 1 campo obligatorio
3. Click en "Guardar"
4. Ver que formulario muestra error

**Resultado Esperado:**
- ✅ Los campos que SÍSÉ completaron mantienen los datos
- ✅ El usuario NO tiene que escribir todo de nuevo
- ✅ Solo falta completar el campo errado

**Ejemplo:**
```
Escribiste CITE: SGPA-2026-05-0006
Olvidaste Nombre Remitente
Luego de error:
- CITE sigue mostrando: SGPA-2026-05-0006 ✅
- Nombre Remitente vacío con error
```

---

### TEST 12: Paleta de Colores

**Objetivo:** Verificar consistencia visual

**Elementos a Verificar:**

1. **Encabezados de Secciones**
   - ✅ Gradiente azul: #0d1b2a → #1b263b
   - ✅ Texto blanco

2. **Bordes de Cards**
   - ✅ Borde izquierdo 4px amarillo: #ffc107

3. **Botón "Guardar Documento"**
   - ✅ Fondo gradiente azul
   - ✅ Texto blanco

4. **Botón "Cancelar"**
   - ✅ Borde gris, fondo transparente

5. **Alertas**
   - ✅ Éxito: Verde con icono ✅
   - ✅ Error: Rojo con icono ❌
   - ✅ Validación: Amarillo con icono ⚠️

---

## 🐛 DEBUGGING / TROUBLESHOOTING

### Problema: "Class not found DocumentoController"
**Solución:**
```bash
# Borrar cache de rutas
php artisan route:clear
php artisan cache:clear

# Regenerar autoload
composer dump-autoload
```

### Problema: "Table not found TIPO_DOCUMENTO"
**Solución:**
```bash
# Migrar BD
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

### Problema: "419 CSRF Token Mismatch"
**Solución:**
- ✅ Verificar que el formulario tiene `@csrf`
- ✅ Limpiar cookies del navegador
- ✅ Usar incógnito si persiste

### Problema: Validación no funciona
**Solución:**
```php
// En DocumentoController, verificar:
$validated = $request->validate([
    // reglas correctas...
]);
```

### Problema: Transacción no guarda datos
**Solución:**
```php
// Verificar que dentro de transaction no hay exceptions
// Agregar logging:
DB::transaction(function () {
    \Log::info('Creando persona');
    // código...
    \Log::info('Persona creada con ID: ' . $persona->idPersona);
});
```

---

## 📊 MÉTRICAS DE ÉXITO

Después de todos los tests, deberías tener:

✅ **100% de Validaciones Pasadas**
✅ **6 Registros en CORRESPONDENCIA**
✅ **6 Registros en PERSONA**
✅ **16 Registros en CORRESPONDENCIA_DESTINATARIO**
✅ **0 Errores en Consola del Navegador**
✅ **0 Errores en Logs de Laravel**
✅ **Formulario Responsivo en Todas las Resoluciones**
✅ **Paleta de Colores Consistente**

---

## 📝 REPORTE DE TESTING

Usa este template para documentar tus pruebas:

```
PRUEBA #: TEST 1 - Acceso al Formulario
FECHA: [AAAA-MM-DD]
RESULTADO: ✅ PASÓ / ❌ FALLÓ
OBSERVACIONES: [Si hay problema, describir]
---

PRUEBA #: TEST 2 - Registro Exitoso
FECHA: [AAAA-MM-DD]
RESULTADO: ✅ PASÓ / ❌ FALLÓ
DATOS VERIFICADOS:
  - PERSONA creada: ✅
  - CORRESPONDENCIA creada: ✅
  - DESTINATARIOS creados (2): ✅
OBSERVACIONES: [...]
---

[Continuar para cada prueba]
```

---

## ✅ CHECKLIST FINAL

Antes de considerar el módulo "LISTO PARA PRODUCCIÓN":

- [ ] TEST 1: ✅ Acceso al formulario
- [ ] TEST 2: ✅ Registro completo exitoso
- [ ] TEST 3: ✅ Validación CITE
- [ ] TEST 4: ✅ Validación Email
- [ ] TEST 5: ✅ Validación Departamentos
- [ ] TEST 6: ✅ Múltiples validaciones
- [ ] TEST 7: ✅ Datos opcionales
- [ ] TEST 8: ✅ Múltiples departamentos
- [ ] TEST 9: ✅ Contador dinámico
- [ ] TEST 10: ✅ Responsividad móvil
- [ ] TEST 11: ✅ Preservación de datos
- [ ] TEST 12: ✅ Paleta de colores
- [ ] BD: ✅ Datos consistentes
- [ ] Logs: ✅ Sin errores
- [ ] Documentación: ✅ Actualizada

---

**Si todos los tests pasan: ✅ MÓDULO APROBADO PARA PRODUCCIÓN**

---

**Fecha de Testing:** ___________
**Responsable:** ___________
**Firma:** ___________
