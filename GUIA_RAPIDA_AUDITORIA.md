# 🚀 GUÍA RÁPIDA - SISTEMA DE AUDITORÍA

## ⚡ Iniciar en 3 pasos

### Paso 1: Ejecutar Migración
```bash
php artisan migrate
```
✅ Esto crea la tabla AUDITORIA con la nueva estructura

### Paso 2: Verificar Instalación
```bash
php artisan auditoria:verify
```
✅ Verifica que todo esté configurado correctamente

### Paso 3: Acceder a Auditoría
```
http://localhost/admin/auditoria
```
✅ Debes ser usuario administrador

---

## 📌 Funciones Principales

### Ver Listado de Auditorías
```
GET /admin/auditoria
```
- Filtrar por usuario
- Filtrar por acción (CREATE, UPDATE, DELETE)
- Filtrar por fechas
- Buscar por IP o navegador

### Ver Detalles
```
GET /admin/auditoria/{id}
```
- Información completa del evento
- Cambios antes y después
- Historial del registro

### Ver Estadísticas
```
GET /admin/auditoria/estadisticas
```
- Gráficos de actividad
- Usuarios más activos
- Modelos más modificados
- Selectores de período

### Exportar a CSV
```
GET /admin/auditoria/exportar/csv
```
Descargar todos los registros en formato CSV

---

## 🔍 Casos de Uso

### Rastrear cambios en una correspondencia
1. Ir a `/admin/auditoria`
2. Filtrar por modelo: "Correspondencia"
3. Hacer clic en el ID de la auditoría

### Ver qué hizo un usuario
1. Filtrar por usuario específico
2. Ver todas sus acciones
3. Exportar a CSV si es necesario

### Auditoría de eliminaciones
1. Filtrar por acción: DELETE
2. Ver qué se eliminó
3. Recuperar datos si es necesario

### Investigar cambios recientes
1. Ir a estadísticas
2. Cambiar período a "Últimos 7 días"
3. Revisar usuarios activos

---

## 📊 Ejemplo: Crear un Documento

**Lo que sucede automáticamente:**

1. Usuario crea correspondencia
   ↓
2. Sistema captura:
   - Usuario autenticado
   - Datos del documento
   - IP y navegador
   - URL accedida
   - Timestamp
   ↓
3. Registra en tabla AUDITORIA
   ↓
4. Aparece en `/admin/auditoria`
   ↓
5. Puede ser consultado, filtrado, exportado

**Sin escribir código adicional** ✨

---

## 🛠️ Uso en Código

### Obtener auditorías con filtros
```php
use App\Helpers\AuditoriaHelper;

$auditorias = AuditoriaHelper::obtenerAuditorias(
    idUsuario: 1,
    accion: 'UPDATE',
    modelo: 'Correspondencia',
    perPage: 20
);

foreach($auditorias as $aud) {
    echo $aud->usuario->name;
    echo $aud->modelo;
    echo $aud->accion;
}
```

### Obtener resumen estadístico
```php
$resumen = AuditoriaHelper::obtenerResumen(dias: 30);

echo "Total: " . $resumen['total'];
echo "Creaciones: " . $resumen['creaciones'];
echo "Actualizaciones: " . $resumen['actualizaciones'];
echo "Eliminaciones: " . $resumen['eliminaciones'];
```

### Obtener historial de un registro
```php
$auditorias = AuditoriaHelper::obtenerAuditoriasDe('Correspondencia', 123);

foreach($auditorias as $aud) {
    echo $aud->fecha->format('d/m/Y H:i');
    echo $aud->usuario->name;
    echo $aud->accion;
}
```

### Ver cambios específicos
```php
$auditoria = Auditoria::find(1);
$cambios = AuditoriaHelper::extraerCambios($auditoria);

echo $cambios['tipo']; // CREATE, UPDATE, DELETE
foreach($cambios['cambios'] as $campo => $valor) {
    echo "$campo: $valor";
}
```

---

## 🔐 Seguridad

✅ Solo administradores pueden acceder
✅ Campos sensibles están filtrados
✅ IP y navegador registrados
✅ Todos los cambios son inmutables

---

## 📊 Modelos Auditados

Estos modelos son auditados automáticamente:

- Correspondencia
- Derivacion
- User
- Departamento
- Persona
- EstadoDocumento
- NivelUrgencia
- TipoDocumento
- Seguimiento

---

## ❓ Preguntas Frecuentes

### ¿Cómo agregar más modelos a auditar?

En `app/Providers/AppServiceProvider.php`:

```php
$modelos = [
    // ... existentes
    MiNuevoModelo::class,
];

foreach ($modelos as $modelo) {
    $modelo::observe(GenericAuditObserver::class);
}
```

### ¿Se registran todas las acciones?

Sí:
- ✅ Creación de registros
- ✅ Actualización de registros
- ✅ Eliminación de registros
- ✅ Cambios por cualquier usuario
- ✅ A través de API o web

### ¿Puedo borrar auditorías?

No recomendado, pero técnicamente sí:

```php
// Limpiar auditorías de más de 90 días
AuditoriaHelper::limpiarAuditoriasAntiguas(diasRetener: 90);
```

### ¿Se audita la auditoría?

No. La tabla AUDITORIA no es auditada para evitar loops.

### ¿Qué datos se registran?

**Siempre:**
- Usuario
- Modelo
- ID del registro
- Acción (CREATE/UPDATE/DELETE)
- Fecha
- IP y navegador
- URL accedida

**Según acción:**
- CREATE: Datos nuevos
- UPDATE: Datos anteriores + nuevos
- DELETE: Datos eliminados

**NO se registran:**
- password
- token
- secret
- api_key
- remember_token

---

## 📞 Soporte Rápido

**Problema**: No aparecen auditorías
**Solución**: 
1. Verificar con `php artisan auditoria:verify`
2. Ejecutar migración: `php artisan migrate`
3. Limpiar cache: `php artisan config:cache`

**Problema**: Error al acceder a `/admin/auditoria`
**Solución**:
1. Verificar que seas administrador
2. Verificar que tengas email verificado
3. Limpiar cache: `php artisan cache:clear`

**Problema**: No veo datos anteriores en UPDATE
**Solución**: Esto es normal en primeros cambios. El sistema captura datos a partir del primer cambio.

---

## 🎯 Próximos Pasos

1. ✅ Ejecutar migración
2. ✅ Verificar con comando
3. ✅ Crear/modificar un documento
4. ✅ Verificar en `/admin/auditoria`
5. ✅ Explorar filtros y estadísticas
6. ✅ Exportar datos

---

## 📚 Documentación Completa

Para más información detallada:
- `DOCUMENTACION_AUDITORIA.md`
- `IMPLEMENTACION_AUDITORIA_RESUMEN.md`

---

**¡Listo! Tu sistema de auditoría está operativo.** 🎉
