# 📋 SISTEMA DE AUDITORÍA PROFESIONAL - GUÍA COMPLETA

## 🎯 Descripción General

Se ha implementado un sistema completo de auditoría profesional para la plataforma de gestión documental. Este sistema registra automáticamente todas las operaciones realizadas en los modelos principales del sistema, proporcionando un registro integral de cambios con detalles completos.

---

## 📁 Archivos Creados/Modificados

### Migraciones
- **`database/migrations/2026_05_17_000000_mejorar_auditoria_table.php`**
  - Nueva migración mejorada para la tabla AUDITORIA
  - Incluye campos para auditoría empresarial completa
  - Índices optimizados para búsquedas rápidas

### Modelos
- **`app/Models/Auditoria.php`**
  - Modelo Eloquent para la tabla AUDITORIA
  - Relaciones con Usuario
  - Casts JSON para datosAnteriores y datosNuevos
  - Scopes para filtrado avanzado
  - Accesorios para renderizado de vistas

### Observers
- **`app/Observers/GenericAuditObserver.php`**
  - Observer genérico reutilizable
  - Registra automáticamente CREATE, UPDATE, DELETE
  - Captura datos antes y después
  - Incluye información técnica (IP, navegador, ruta)
  - Filtra campos sensibles

### Helpers
- **`app/Helpers/AuditoriaHelper.php`**
  - Funciones auxiliares para auditoría
  - Métodos para obtener estadísticas
  - Filtrado avanzado
  - Exportación a CSV
  - Limpieza de auditorías antiguas

### Controllers
- **`app/Http/Controllers/AuditoriaController.php`**
  - Controlador principal de auditoría
  - Métodos: index, show, estadisticas, registroHistorial, exportar, api
  - Filtros avanzados y paginación
  - Eager loading optimizado

### Middleware
- **`app/Http/Middleware/IsAdmin.php`** (modificado)
  - Protege acceso solo para administradores
  - Verifica rol del usuario

### Vistas
- **`resources/views/auditoria/index.blade.php`**
  - Listado principal con filtros
  - Resumen rápido de estadísticas
  - Tabla responsive con paginación

- **`resources/views/auditoria/show.blade.php`**
  - Vista detallada de una auditoría
  - Muestra cambios antes/después
  - Historial del registro

- **`resources/views/auditoria/estadisticas.blade.php`**
  - Dashboard de estadísticas
  - Gráficos con Chart.js
  - Usuarios más activos
  - Modelos más modificados

- **`resources/views/auditoria/historial.blade.php`**
  - Timeline visual del historial
  - Cronología completa de cambios

### Configuración
- **`app/Providers/AppServiceProvider.php`** (modificado)
  - Registro automático de observers
  - Vinculación de GenericAuditObserver a modelos

- **`routes/web.php`** (modificado)
  - Rutas de auditoría bajo `/admin/auditoria`
  - Protegidas con middleware 'admin'

---

## 🔧 Instalación y Configuración

### 1. Ejecutar Migración

```bash
php artisan migrate
```

Esta migración:
- Crea la nueva tabla AUDITORIA con estructura mejorada
- Renombra la tabla antigua si existe
- Configura índices para optimización

### 2. Verificar Observers Registrados

Los observers se registran automáticamente en `AppServiceProvider`:

```php
// Modelos auditados automáticamente:
- Correspondencia
- Derivacion
- User
- Departamento
- Persona
- EstadoDocumento
- NivelUrgencia
- TipoDocumento
- Seguimiento
```

### 3. Acceder al Sistema

URL: `http://localhost/admin/auditoria`

**Requisitos:**
- Usuario autenticado
- Rol: Administrador

---

## 📊 Estructura de la Tabla AUDITORIA

```sql
CREATE TABLE AUDITORIA (
    idAuditoria BIGINT PRIMARY KEY AUTO_INCREMENT,
    idUsuario BIGINT UNSIGNED NULLABLE (FK users),
    modelo VARCHAR(100) NOT NULL,           -- Nombre del modelo (Correspondencia, User, etc)
    idRegistro BIGINT UNSIGNED NOT NULL,    -- ID del registro afectado
    accion ENUM('CREATE','UPDATE','DELETE') NOT NULL,
    datosAnteriores LONGTEXT NULLABLE,      -- JSON con datos anteriores
    datosNuevos LONGTEXT NULLABLE,          -- JSON con datos nuevos
    ip VARCHAR(45) NULLABLE,                -- IPv4 o IPv6
    navegador VARCHAR(255) NULLABLE,        -- User Agent
    ruta VARCHAR(255) NULLABLE,             -- URL accedida
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEXES: idUsuario, modelo, accion, fecha, (modelo, idRegistro), (idUsuario, fecha), (accion, fecha)
)
```

---

## 🔍 Cómo Funciona

### Flujo Automático

1. **Usuario realiza acción** (crear, actualizar, eliminar)
   ↓
2. **Observer detecta evento** (created, updating/updated, deleted)
   ↓
3. **GenericAuditObserver procesa** la información
   ↓
4. **Captura datos:**
   - Usuario autenticado
   - Antes y después (UPDATE)
   - IP del cliente
   - Navegador
   - URL accedida
   - Timestamp
   ↓
5. **Guarda en tabla AUDITORIA**
   ↓
6. **Disponible en dashboard** para consulta

### Campos Sensibles Filtrados

Los siguientes campos no se registran:
- `password`
- `token`
- `secret`
- `api_key`
- `remember_token`
- `email_verified_at`

---

## 📌 Rutas Disponibles

```
GET    /admin/auditoria                          # Listado principal
GET    /admin/auditoria/{idAuditoria}            # Ver detalles
GET    /admin/auditoria/estadisticas             # Dashboard de estadísticas
GET    /admin/auditoria/historial/{modelo}/{id}  # Historial de un registro
GET    /admin/auditoria/exportar/csv             # Descargar CSV
GET    /admin/auditoria/api/data                 # API JSON
```

---

## 🎨 Características de la Interfaz

### 📈 Vista Principal (index)

- **Resumen Rápido**: Métricas de actividad reciente
- **Filtros Avanzados**:
  - Por usuario
  - Por acción (CREATE, UPDATE, DELETE)
  - Por modelo
  - Por rango de fechas
  
- **Tabla Responsive**:
  - Fecha y hora
  - Usuario responsable
  - Acción con badge de color
  - Modelo afectado
  - Resumen de cambios
  - IP de origen
  - Botón de detalles

- **Colores por Acción**:
  - CREATE: Verde ✅
  - UPDATE: Amarillo ⚠️
  - DELETE: Rojo ❌

### 📊 Vista de Estadísticas

- Métricas principales
- Gráfico de distribución (Pie/Doughnut)
- Resumen de operaciones
- Usuarios más activos (ranking)
- Modelos más modificados
- Filtro por período (7, 30, 90 días)

### 🔍 Vista de Detalles

- Información completa del evento
- Acción realizada con badge
- Usuario responsable
- Fecha exacta
- IP y navegador
- Ruta accedida
- Cambios detallados:
  - CREATE: Valores creados
  - UPDATE: Antes y después
  - DELETE: Datos eliminados
- Historial completo del registro

### ⏱️ Vista de Historial

- Timeline visual
- Cronología completa de cambios
- Descripción de cada evento
- Enlaces a detalles completos

---

## 🛠️ Uso de AuditoriaHelper

### Obtener Auditorías con Filtros

```php
use App\Helpers\AuditoriaHelper;

$auditorias = AuditoriaHelper::obtenerAuditorias(
    idUsuario: 1,
    accion: 'UPDATE',
    modelo: 'Correspondencia',
    fechaInicio: '2026-01-01',
    fechaFin: '2026-12-31',
    perPage: 15
);
```

### Obtener Resumen

```php
$resumen = AuditoriaHelper::obtenerResumen(dias: 7);
// Retorna: [total, creaciones, actualizaciones, eliminaciones, usuarios_activos]
```

### Obtener Auditorías de un Registro

```php
$auditorias = AuditoriaHelper::obtenerAuditoriasDe('Correspondencia', 123);
```

### Extraer Cambios

```php
$cambios = AuditoriaHelper::extraerCambios($auditoria);
// Retorna: [tipo => 'CREATE|UPDATE|DELETE', cambios => [...]]
```

### Exportar Auditorías

```php
$csv = AuditoriaHelper::exportarAuditorias([
    'idUsuario' => 1,
    'accion' => 'UPDATE'
]);
```

### Limpiar Auditorías Antiguas

```php
// Eliminar registros de auditoría más antiguos que 90 días
AuditoriaHelper::limpiarAuditoriasAntiguas(diasRetener: 90);
```

---

## 🔐 Seguridad

### Protección de Rutas

Todas las rutas están protegidas:
- ✅ Requerir autenticación
- ✅ Requerir verificación de email
- ✅ Requerir rol de Administrador

### Middleware Aplicado

```php
'middleware' => ['auth', 'verified', 'admin']
```

### Campos Filtrados

Se excluyen automáticamente campos sensibles para no comprometer la seguridad.

---

## 📈 Rendimiento

### Índices Creados

```sql
INDEX idx_idUsuario ON AUDITORIA(idUsuario)
INDEX idx_modelo ON AUDITORIA(modelo)
INDEX idx_accion ON AUDITORIA(accion)
INDEX idx_fecha ON AUDITORIA(fecha)
INDEX idx_modelo_idRegistro ON AUDITORIA(modelo, idRegistro)
INDEX idx_idUsuario_fecha ON AUDITORIA(idUsuario, fecha)
INDEX idx_accion_fecha ON AUDITORIA(accion, fecha)
```

### Optimizaciones

- ✅ Eager loading de relaciones
- ✅ Paginación de resultados
- ✅ Índices en campos frecuentes
- ✅ Queries eficientes
- ✅ Cacheability

---

## 📋 Modelos Auditados

Por defecto, se auditan los siguientes modelos:

| Modelo | Descripción |
|--------|-------------|
| Correspondencia | Documentos de correspondencia |
| Derivacion | Derivaciones de documentos |
| User | Usuarios del sistema |
| Departamento | Departamentos |
| Persona | Personas registradas |
| EstadoDocumento | Estados de documentos |
| NivelUrgencia | Niveles de urgencia |
| TipoDocumento | Tipos de documentos |
| Seguimiento | Seguimiento de documentos |

### Agregar más Modelos

Para auditar un modelo adicional:

1. En `app/Providers/AppServiceProvider.php`:

```php
$modelos = [
    // ... modelos existentes
    NuevoModelo::class,
];

foreach ($modelos as $modelo) {
    $modelo::observe(GenericAuditObserver::class);
}
```

2. En `app/Helpers/AuditoriaHelper.php`, actualizar método `debeSerAuditado()`:

```php
public static function debeSerAuditado($modelo)
{
    $modelosAuditados = [
        // ... existentes
        'NuevoModelo',
    ];
    return in_array($modelo, $modelosAuditados);
}
```

---

## 🐛 Troubleshooting

### No se registran cambios

**Causa**: Observer no está registrado
**Solución**: Verificar `AppServiceProvider.php` y ejecutar `php artisan config:cache`

### Error: "Acceso denegado"

**Causa**: Usuario no es administrador
**Solución**: Verificar rol del usuario en tabla `users`

### Tabla no existe

**Causa**: Migración no ejecutada
**Solución**: Ejecutar `php artisan migrate`

---

## 📝 Ejemplos de Uso

### Ejemplo 1: Ver auditoría de una correspondencia

```php
// En controlador
Route::get('/correspondencia/{id}/historial', function($id) {
    $auditorias = AuditoriaHelper::obtenerAuditoriasDe('Correspondencia', $id);
    return view('correspondencia.historial', compact('auditorias'));
});
```

### Ejemplo 2: Crear reporte personalizado

```php
$auditorias = AuditoriaHelper::obtenerAuditorias(
    accion: 'DELETE',
    fechaInicio: now()->subMonth(),
    fechaFin: now()
);

foreach($auditorias as $aud) {
    echo "{$aud->usuario->name} eliminó {$aud->modelo} en {$aud->fecha}\n";
}
```

### Ejemplo 3: Dashboard personalizado

```php
// En controlador
$resumen = AuditoriaHelper::obtenerResumen(dias: 30);
$usuariosActivos = AuditoriaHelper::obtenerActividadPorUsuario(dias: 30);
$modelosMasModificados = AuditoriaHelper::obtenerActividadPorModelo(dias: 30);

return view('dashboard', compact('resumen', 'usuariosActivos', 'modelosMasModificados'));
```

---

## 🚀 Mantenimiento

### Limpiar auditorías antiguas (Scheduler)

En `app/Console/Kernel.php`:

```php
$schedule->call(function () {
    AuditoriaHelper::limpiarAuditoriasAntiguas(diasRetener: 365);
})->daily()->at('02:00');
```

### Monitorear tamaño de tabla

```sql
SELECT 
    TABLE_NAME,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE TABLE_NAME = 'AUDITORIA';
```

---

## 📞 Soporte

Para problemas o preguntas:
1. Verificar logs en `storage/logs/`
2. Revisar base de datos directamente
3. Ejecutar `php artisan migrate:reset --path=database/migrations/2026_05_17_000000_mejorar_auditoria_table.php` para revertir si es necesario

---

## ✅ Checklist de Instalación

- [ ] Ejecutada migración `2026_05_17_000000_mejorar_auditoria_table.php`
- [ ] AppServiceProvider.php configurado con observers
- [ ] Rutas agregadas en web.php
- [ ] Acceso a `/admin/auditoria` funcionando
- [ ] Usuario administrador puede ver auditorías
- [ ] Cambios se registran automáticamente al crear/actualizar/eliminar registros
- [ ] Vistas se renderan correctamente
- [ ] Filtros y búsquedas funcionan
- [ ] Exportación a CSV funciona

---

**Sistema de Auditoría v1.0 - Implementado en Mayo 2026** 🎉
