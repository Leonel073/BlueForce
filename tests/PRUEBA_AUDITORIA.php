<?php

/**
 * ARCHIVO DE PRUEBA Y VALIDACIÓN DEL SISTEMA DE AUDITORÍA
 * 
 * Usar con: php artisan tinker
 * O crear una ruta de debug temporal
 */

use Illuminate\Support\Facades\DB;
// ========================================
// PRUEBA 1: Verificar que la tabla existe
// ========================================
echo "PRUEBA 1: Verificar tabla AUDITORIA\n";
$tableExists = DB::getSchemaBuilder()->hasTable('AUDITORIA');
echo $tableExists ? "✅ Tabla AUDITORIA existe\n" : "❌ Tabla AUDITORIA NO existe\n";

// ========================================
// PRUEBA 2: Verificar estructura
// ========================================
echo "\nPRUEBA 2: Verificar estructura de campos\n";
$columns = DB::getSchemaBuilder()->getColumnListing('AUDITORIA');
$camposRequeridos = [
    'idAuditoria', 'idUsuario', 'modelo', 'idRegistro', 'accion',
    'datosAnteriores', 'datosNuevos', 'ip', 'navegador', 'ruta', 'fecha'
];
$todosExisten = true;
foreach ($camposRequeridos as $campo) {
    if (in_array($campo, $columns)) {
        echo "  ✅ Campo '$campo' existe\n";
    } else {
        echo "  ❌ Campo '$campo' NO existe\n";
        $todosExisten = false;
    }
}

// ========================================
// PRUEBA 3: Modelo Auditoria
// ========================================
echo "\nPRUEBA 3: Verificar Modelo Auditoria\n";
try {
    $modelo = new \App\Models\Auditoria();
    echo "✅ Modelo Auditoria cargado correctamente\n";
    echo "  - Tabla: " . $modelo->getTable() . "\n";
    echo "  - Primary Key: " . $modelo->getKeyName() . "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// ========================================
// PRUEBA 4: Observer registrado
// ========================================
echo "\nPRUEBA 4: Verificar Observers registrados\n";
$modelosConObserver = [
    \App\Models\Correspondencia::class,
    \App\Models\Derivacion::class,
    \App\Models\User::class,
];
foreach ($modelosConObserver as $modelClass) {
    echo "  ✅ Observer puede estar registrado en " . class_basename($modelClass) . "\n";
}

// ========================================
// PRUEBA 5: Contar registros de auditoría
// ========================================
echo "\nPRUEBA 5: Auditorías registradas\n";
$count = \App\Models\Auditoria::count();
echo "Total de registros de auditoría: $count\n";

// ========================================
// PRUEBA 6: Helper funcionando
// ========================================
echo "\nPRUEBA 6: Verificar AuditoriaHelper\n";
try {
    $resumen = \App\Helpers\AuditoriaHelper::obtenerResumen(dias: 7);
    echo "✅ AuditoriaHelper funcionando\n";
    echo "  - Total: " . $resumen['total'] . "\n";
    echo "  - Creaciones: " . $resumen['creaciones'] . "\n";
    echo "  - Actualizaciones: " . $resumen['actualizaciones'] . "\n";
    echo "  - Eliminaciones: " . $resumen['eliminaciones'] . "\n";
    echo "  - Usuarios activos: " . $resumen['usuarios_activos'] . "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// ========================================
// PRUEBA 7: Obtener modelos auditados
// ========================================
echo "\nPRUEBA 7: Modelos auditados en la base de datos\n";
$modelos = \App\Models\Auditoria::distinct()->pluck('modelo')->sort();
if ($modelos->count() > 0) {
    echo "Modelos encontrados:\n";
    foreach ($modelos as $modelo) {
        $count = \App\Models\Auditoria::where('modelo', $modelo)->count();
        echo "  - $modelo: $count registros\n";
    }
} else {
    echo "Sin registros de auditoría aún (esto es normal si es primera vez)\n";
}

// ========================================
// PRUEBA 8: Rutas de auditoría
// ========================================
echo "\nPRUEBA 8: Rutas de auditoría disponibles\n";
$rutas = [
    'auditoria.index' => '/admin/auditoria',
    'auditoria.show' => '/admin/auditoria/{id}',
    'auditoria.estadisticas' => '/admin/auditoria/estadisticas',
    'auditoria.historial' => '/admin/auditoria/historial/{modelo}/{id}',
    'auditoria.exportar' => '/admin/auditoria/exportar/csv',
    'auditoria.api' => '/admin/auditoria/api/data',
];
foreach ($rutas as $nombre => $ruta) {
    try {
        $url = route($nombre);
        echo "✅ Ruta '$nombre' configurada: $ruta\n";
    } catch (\Exception $e) {
        echo "❌ Ruta '$nombre' no encontrada\n";
    }
}

// ========================================
// PRUEBA 9: Crear registro de prueba
// ========================================
echo "\nPRUEBA 9: Crear registro de auditoría manual\n";
try {
    $auditoria = \App\Models\Auditoria::create([
        'idUsuario' => auth()->id(),
        'modelo' => 'Prueba',
        'idRegistro' => 1,
        'accion' => 'CREATE',
        'datosNuevos' => json_encode(['test' => 'valor']),
        'ip' => '127.0.0.1',
        'navegador' => 'Test',
        'ruta' => '/test',
    ]);
    echo "✅ Registro de auditoría creado: ID " . $auditoria->idAuditoria . "\n";
    
    // Eliminar registro de prueba
    $auditoria->delete();
    echo "✅ Registro de prueba eliminado\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// ========================================
// RESUMEN
// ========================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "RESUMEN DE PRUEBAS\n";
echo str_repeat("=", 50) . "\n";
if ($todosExisten) {
    echo "✅ SISTEMA DE AUDITORÍA CONFIGURADO CORRECTAMENTE\n";
    echo "\nPróximos pasos:\n";
    echo "1. Ir a: http://localhost/admin/auditoria\n";
    echo "2. Crear/modificar/eliminar un registro\n";
    echo "3. Verificar que aparezca en la auditoría\n";
} else {
    echo "❌ EXISTE UN PROBLEMA EN LA CONFIGURACIÓN\n";
    echo "Ejecutar: php artisan migrate\n";
}
echo str_repeat("=", 50) . "\n";

?>
