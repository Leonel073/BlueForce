# AUDITORÍA COMPLETA: Flujo "Otra Persona" en Registro de Documentos

## 🔴 PROBLEMA IDENTIFICADO

Al seleccionar "Otra Persona" y completar todos los campos, los errores de validación indican que los campos están vacíos, aunque fueron rellenados.

---

## 📋 ANÁLISIS DETALLADO

### 1. NOMBRES DE CAMPOS EN LA VISTA (documento-registro.blade.php)

#### Bloque "Yo Mismo" (Oculto por defecto)
```html
<!-- CAMPOS OCULTOS (type="hidden") -->
<input type="hidden" name="ci_remitente" ...>
<input type="hidden" name="nombre_remitente" ...>
<input type="hidden" name="telefono_celular" ...>
<input type="hidden" name="telefono_fijo" ...>
<input type="hidden" name="correo_remitente" ...>
<input type="hidden" name="cargo_remitente" ...>
<input type="hidden" name="institucion_remitente" ...>
<input type="hidden" name="tipo_remitente" ...>
```

#### Bloque "Otra Persona" (Visible cuando seleccionado)
```html
<!-- CAMPOS CON NOMBRES DIFERENTES (_manual) -->
<input name="ci_remitente_manual" ...>
<input name="nombre_remitente_manual" ...>
<input name="telefono_celular_manual" ...>
<input name="telefono_fijo_manual" ...>
<input name="correo_remitente_manual" ...>
<input name="cargo_remitente_manual" ...>
<input name="institucion_remitente_manual" ...>
<select name="tipo_remitente_manual" ...> <!-- PROBLEMA CRÍTICO: Es SELECT, no input -->
```

### 2. VALIDACIÓN ESPERADA EN StoreDocumentoRequest.php

```php
public function rules(): array
{
    return [
        'nombre_remitente' => 'required|string|max:200|regex:/^[\pL\s]+$/u',
        'ci_remitente' => 'required|string|max:20|regex:/^[0-9A-Za-z\-]+$/',
        'telefono_celular' => 'required|string|max:20|regex:/^[0-9\+\-\s]+$/',
        'tipo_remitente' => 'required|in:INTERNO,EXTERNO',
        // ... más campos
    ];
}
```

**Campos esperados por la validación:**
- `nombre_remitente` (NO `nombre_remitente_manual`)
- `ci_remitente` (NO `ci_remitente_manual`)
- `telefono_celular` (NO `telefono_celular_manual`)
- `tipo_remitente` (NO `tipo_remitente_manual`)

### 3. FLUJO DE DATOS EN JAVASCRIPT (toggleRemitenteMode)

```javascript
// Cuando se selecciona "Otra Persona"
} else {
    bloqueYoMismo.style.display = 'none';
    bloqueOtra.style.display = 'block';
    
    // Deshabilita campos ocultos
    document.querySelectorAll('#bloque_yo_mismo input[type="hidden"]')
        .forEach(input => {
            input.disabled = true;  // ❌ PROBLEMA: Campos disabled NO se envían
        });
}
```

**El código intenta copiar valores:**
```javascript
document.getElementById('bloque_otra_persona').addEventListener('input', function(e) {
    const yoMismoDiv = document.getElementById('bloque_yo_mismo');
    if (e.target.name === 'ci_remitente_manual') {
        yoMismoDiv.querySelector('input[name="ci_remitente"]').value = e.target.value;
    }
    // ... más copies
});
```

❌ **PROBLEMA 1**: El evento `input` se vincula al DIV `bloque_otra_persona` DESPUÉS de ocultar el bloque `yo_mismo`.
❌ **PROBLEMA 2**: Se intenta copiar a campos que están **disabled**, por lo que no se enviarán.
❌ **PROBLEMA 3**: Se busca copiar a campos dentro de `bloque_yo_mismo` que está oculto.

### 4. ¿POR QUÉ FALLA LA VALIDACIÓN?

Cuando el usuario selecciona "Otra Persona" y completa:
- CI: `1234567-8`
- Nombre: `Juan García`
- Celular: `71234567`
- Tipo: `EXTERNO`

**El formulario REALMENTE ENVÍA:**
```
ci_remitente_manual=1234567-8
nombre_remitente_manual=Juan García
telefono_celular_manual=71234567
tipo_remitente_manual=EXTERNO
```

**Pero el FormRequest ESPERA:**
```
ci_remitente=1234567-8          ← FALTA
nombre_remitente=Juan García    ← FALTA
telefono_celular=71234567       ← FALTA
tipo_remitente=EXTERNO          ← FALTA
```

**Resultado:** Validación falla porque los campos esperados están **vacíos**.

---

## 🔧 SOLUCIÓN

### OPCIÓN 1: Usar campos SIN sufijo "_manual" (RECOMENDADO)

**Cambiar EN LA VISTA todos los nombres `*_manual` por el nombre correcto:**

```html
<!-- CAMBIAR DE: -->
<input name="ci_remitente_manual" ...>

<!-- A: -->
<input name="ci_remitente" ...>
```

**Ventajas:**
- Los nombres coinciden directamente con la validación
- No se necesita JavaScript complejo para copiar
- Eliminamos los campos ocultos del bloque "Yo Mismo"
- Proceso más limpio

### OPCIÓN 2: Convertir campos ocultos a inputs normales (ALTERNATIVA)

Si se desea mantener la separación, los campos ocultos deben:
1. NO estar `disabled`
2. Estar FUERA del formulario visual o usar `readonly`
3. Recibir valores del JavaScript correctamente

---

## ✅ IMPLEMENTACIÓN RECOMENDADA

**Eliminar:**
1. Todos los `input[type="hidden"]` del bloque "Yo Mismo"
2. El código JavaScript complejo que intenta copiar valores
3. Los sufijos `_manual` de los campos

**Cambiar:**
1. Los inputs del bloque "Otra Persona" de `name="*_manual"` a `name="*"`
2. El select de `name="tipo_remitente_manual"` a `name="tipo_remitente"`

**Resultado:**
- Formulario envía exactamente lo que la validación espera
- No hay confusión de nombres
- JavaScript simplificado

---

## 📊 MATRIZ ANTES/DESPUÉS

| Campo | Bloque "Yo Mismo" | Bloque "Otra Persona" | Validación Espera |
|-------|-------------------|----------------------|-------------------|
| CI | `hidden` (name) | `text` (_manual) ❌ | `ci_remitente` |
| Nombre | `hidden` (name) | `text` (_manual) ❌ | `nombre_remitente` |
| Celular | `hidden` (name) | `text` (_manual) ❌ | `telefono_celular` |
| Tipo | `hidden` (name) | `select` (_manual) ❌ | `tipo_remitente` |

**Solución:**
1. Eliminar los `hidden` del bloque "Yo Mismo"
2. Renombrar todos los `_manual` a sin sufijo
3. Cambiar `type="hidden"` a inputs normales en el bloque "Yo Mismo" si se necesita mantener valores por defecto

---

## 🎯 ACCIONES A REALIZAR

1. Abrir `resources/views/correspondencia/documento-registro.blade.php`
2. Reemplazar TODOS los nombres `*_manual` en el bloque "Otra Persona"
3. Cambiar `name="tipo_remitente_manual"` en el SELECT
4. Simplificar el JavaScript (eliminar la copia compleja)
5. Verificar que los valores se envíen correctamente

**ARCHIVOS A MODIFICAR:**
- `resources/views/correspondencia/documento-registro.blade.php` (ÚNICO archivo)

**ARCHIVOS QUE NO CAMBIAN:**
- `app/Http/Requests/StoreDocumentoRequest.php` (validación correcta)
- `app/Http/Controllers/DocumentoController.php` (controlador correcto)

