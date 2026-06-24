<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CorrespondenciaController;
use App\Http\Controllers\Api\V1\DerivacionController;
use App\Http\Controllers\Api\V1\PersonaController;
use App\Http\Controllers\Api\V1\DepartamentoController;
use App\Http\Controllers\Api\V1\UsuarioController;
use App\Http\Controllers\Api\V1\SeguimientoController;
use App\Http\Controllers\Api\V1\AuditoriaController;
use App\Http\Controllers\Api\V1\ReporteController;
use App\Http\Controllers\Api\V1\EstadisticasController;
use App\Http\Controllers\EstadoTransicionController;

/*
|--------------------------------------------------------------------------
| API ROUTES - VERSION 1
|--------------------------------------------------------------------------
|
| Rutas base: /api/v1/
| Middleware: api
| Autenticación: Sanctum
|
*/

// ========================================
// RUTAS PÚBLICAS (Sin autenticación)
// ========================================

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
});

// ========================================
// RUTAS PROTEGIDAS (Con autenticación Sanctum)
// ========================================

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');
    });

    // Documentos (Correspondencia)
    Route::apiResource('documentos', CorrespondenciaController::class);
    Route::get('documentos/{id}/derivaciones', [CorrespondenciaController::class, 'derivaciones'])->name('api.documentos.derivaciones');
    Route::get('documentos/{id}/seguimiento', [CorrespondenciaController::class, 'seguimiento'])->name('api.documentos.seguimiento');
    Route::post('documentos/{id}/cambiar-estado', [CorrespondenciaController::class, 'cambiarEstado'])->name('api.documentos.cambiar-estado');

    // Transiciones de Estado de Documentos
    Route::prefix('documentos/{id}')->group(function () {
        Route::get('historial-transiciones', [EstadoTransicionController::class, 'obtenerHistorial'])->name('api.documentos.historial-transiciones');
        Route::post('recibir', [EstadoTransicionController::class, 'recibir'])->name('api.documentos.recibir');
        Route::post('atender', [EstadoTransicionController::class, 'atender'])->name('api.documentos.atender');
        Route::post('archivar', [EstadoTransicionController::class, 'archivar'])->name('api.documentos.archivar');
        Route::get('transiciones-permitidas', [EstadoTransicionController::class, 'obtenerTransicionesPermitidas'])->name('api.documentos.transiciones-permitidas');
    });

    // Derivaciones
    Route::apiResource('derivaciones', DerivacionController::class);
    Route::post('derivaciones/{id}/recibir', [DerivacionController::class, 'recibir'])->name('api.derivaciones.recibir');
    Route::post('derivaciones/{id}/rechazar', [DerivacionController::class, 'rechazar'])->name('api.derivaciones.rechazar');
    Route::post('derivaciones/{id}/re-derivar', [DerivacionController::class, 'rederiva'])->name('api.derivaciones.rederiva');

    // Personas
    Route::apiResource('personas', PersonaController::class);
    Route::get('personas/{id}/documentos', [PersonaController::class, 'documentos'])->name('api.personas.documentos');

    // Departamentos
    Route::apiResource('departamentos', DepartamentoController::class);
    Route::get('departamentos/{id}/usuarios', [DepartamentoController::class, 'usuarios'])->name('api.departamentos.usuarios');
    Route::get('departamentos/{id}/derivaciones-pendientes', [DepartamentoController::class, 'derivacionesPendientes'])->name('api.departamentos.derivaciones-pendientes');

    // Usuarios
    Route::apiResource('usuarios', UsuarioController::class);
    Route::post('usuarios/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol'])->name('api.usuarios.cambiar-rol');
    Route::post('usuarios/{id}/resetear-password', [UsuarioController::class, 'resetearPassword'])->name('api.usuarios.resetear-password');
    Route::get('usuarios/{id}/derivaciones-pendientes', [UsuarioController::class, 'derivacionesPendientes'])->name('api.usuarios.derivaciones-pendientes');

    // Seguimientos
    Route::apiResource('seguimientos', SeguimientoController::class, ['only' => ['index', 'show']]);
    Route::get('seguimientos/documento/{idDocumento}', [SeguimientoController::class, 'porDocumento'])->name('api.seguimientos.documento');

    // Auditorías
    Route::apiResource('auditorias', AuditoriaController::class, ['only' => ['index', 'show']]);
    Route::get('auditorias/usuario/{userId}', [AuditoriaController::class, 'porUsuario'])->name('api.auditorias.usuario');
    Route::get('auditorias/documento/{docId}', [AuditoriaController::class, 'porDocumento'])->name('api.auditorias.documento');
    Route::get('auditorias/tabla/{tabla}', [AuditoriaController::class, 'porTabla'])->name('api.auditorias.tabla');

    // Reportes
    Route::prefix('reportes')->group(function () {
        Route::get('documentos-por-estado', [ReporteController::class, 'documentosPorEstado'])->name('api.reportes.documentos-por-estado');
        Route::get('documentos-por-urgencia', [ReporteController::class, 'documentosPorUrgencia'])->name('api.reportes.documentos-por-urgencia');
        Route::get('documentos-por-tipo', [ReporteController::class, 'documentosPorTipo'])->name('api.reportes.documentos-por-tipo');
        Route::get('derivaciones-pendientes', [ReporteController::class, 'derivacionesPendientes'])->name('api.reportes.derivaciones-pendientes');
        Route::get('documentos-por-periodo', [ReporteController::class, 'documentosPorPeriodo'])->name('api.reportes.documentos-por-periodo');
        Route::get('performance-usuario', [ReporteController::class, 'performanceUsuario'])->name('api.reportes.performance-usuario');
        Route::get('exportar-pdf', [ReporteController::class, 'exportarPdf'])->name('api.reportes.exportar-pdf');
    });

    // Estadísticas
    Route::prefix('estadisticas')->group(function () {
        Route::get('dashboard', [EstadisticasController::class, 'dashboard'])->name('api.estadisticas.dashboard');
        Route::get('documentos-mes', [EstadisticasController::class, 'documentosPorMes'])->name('api.estadisticas.documentos-mes');
        Route::get('documentos-dia', [EstadisticasController::class, 'documentosPorDia'])->name('api.estadisticas.documentos-dia');
        Route::get('derivaciones-tiempo-promedio', [EstadisticasController::class, 'derivacionesTiempoPromedio'])->name('api.estadisticas.derivaciones-tiempo-promedio');
        Route::get('usuarios-actividad', [EstadisticasController::class, 'usuariosActividad'])->name('api.estadisticas.usuarios-actividad');
    });

    // ========================================
    // RUTAS SOLO PARA ADMIN
    // ========================================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Gestión de auditorías (lectura completa)
        Route::get('auditorias-full', [AuditoriaController::class, 'indexFull'])->name('api.admin.auditorias-full');
        
        // Limpiar caché
        Route::post('cache/clear', function () {
            \Illuminate\Support\Facades\Cache::flush();
            return response()->json([
                'success' => true,
                'message' => 'Caché limpiado correctamente'
            ]);
        })->name('api.admin.cache-clear');
    });

});

// ========================================
// RUTAS CATCH-ALL (Error 404)
// ========================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'status' => 404,
        'message' => 'Endpoint not found',
        'errors' => [],
        'timestamp' => now()->toIso8601String(),
    ], 404);
});
