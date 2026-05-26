<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\RecibidasController;
use App\Http\Controllers\UserDashboardController;

use App\Http\Controllers\CorrespondenciaController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\AuditoriaController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/



Route::get('/', function () {

    return view('welcome');

});

Route::get('/page', function () {

    return view('auth.page');

})->name('page');

/*
|--------------------------------------------------------------------------
| RUTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/


Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});
Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('admin.index');

        Route::get(
            '/api/dashboard-estadisticas',
            [DashboardController::class, 'estadisticasDashboard']
        )->name('admin.api.estadisticas.dashboard');

        Route::get(
            '/api/dashboard-departamentos',
            [DashboardController::class, 'estadisticasDepartamentos']
        )->name('admin.api.estadisticas.departamentos');

});

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/user/dashboard',
        [UserDashboardController::class, 'index']
    )->name('user.dashboard');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS
    |--------------------------------------------------------------------------
    */

    // LISTADO DOCUMENTOS
    Route::get(
        '/documentos',
        [DocumentoController::class, 'index']
    )->name('documentos.index');

    // FORMULARIO CREAR
    Route::get(
        '/documentos/crear',
        [DocumentoController::class, 'show']
    )->name('documentos.crear');

    // GUARDAR DOCUMENTO
    Route::post(
        '/documentos',
        [DocumentoController::class, 'store']
    )->name('documentos.store');

    // DETALLE DOCUMENTsO
    Route::get(
        '/documentos/{id}',
        [DocumentoController::class, 'detalle']
    )->name('documentos.detalle');

    /*
    |--------------------------------------------------------------------------
    | BUSCAR PERSONA
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/persona/buscar/{ci}',
        [DocumentoController::class, 'buscarPersona']
    )->name('persona.buscar');

    /*
    |--------------------------------------------------------------------------
    | OBTENER PERSONAS POR DEPARTAMENTO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/documentos/departamento/{idDepartamento}/personas',
        [DocumentoController::class, 'obtenerPersonasPorDepartamento']
    )->name('documentos.departamento.personas');

    /*
    |--------------------------------------------------------------------------
    | CORRESPONDENCIA
    |--------------------------------------------------------------------------
    */

    // LISTADO GENERAL
    Route::get(
        '/correspondencia',
        [CorrespondenciaController::class, 'index']
    )->name('correspondencia.index');

    // DETALLE
    Route::get(
        '/correspondencia/{id}',
        [CorrespondenciaController::class, 'show']
    )->name('correspondencia.show');

    /*
    |--------------------------------------------------------------------------
    | ENVÍOS
    |--------------------------------------------------------------------------
    */

    // HISTORIAL ENVIADOS
    Route::get(
        '/envios',
        [EnvioController::class, 'index']
    )->name('envios.index');

    // MI BANDEJA
    Route::get(
        '/mi-bandeja',
        [EnvioController::class, 'bandeja']
    )->name('envios.bandeja');

    // FORMULARIO DERIVAR
    Route::get(
        '/envios/{id}/derivar',
        [EnvioController::class, 'derivarForm']
    )->name('envios.derivar.form');

    // GUARDAR DERIVACIÓN
    Route::post(
        '/envios/{id}/derivar',
        [EnvioController::class, 'derivar']
    )->name('envios.derivar');

    // FINALIZAR DOCUMENTO
    Route::put(
        '/envios/{id}/finalizar',
        [EnvioController::class, 'finalizar']
    )->name('envios.finalizar');

    /*
    |--------------------------------------------------------------------------
    | RECIBIDAS
    |--------------------------------------------------------------------------
    */

    // LISTADO
    Route::get(
        '/recibidas',
        [RecibidasController::class, 'index']
    )->name('recibidas.index');

    // RECIBIR DOCUMENTO
    Route::post(
        '/recibidas/{id}/recibir',
        [RecibidasController::class, 'recibir']
    )->name('recibidas.recibir');

    // FINALIZAR
    Route::post(
        '/recibidas/{id}/finalizar',
        [RecibidasController::class, 'finalizar']
    )->name('recibidas.finalizar');

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/user/configuracion',
        function () {

            return view('user.configuracion');

        }
    )->name('user.configuracion');

});

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | USUARIOS
    |--------------------------------------------------------------------------
    */

    // LISTADO
    Route::get(
        '/usuarios',
        [UsuarioController::class, 'index']
    )->name('admin.usuarios');

    // DETALLE
    Route::get(
        '/usuarios/{id}',
        [UsuarioController::class, 'show']
    )->name('admin.usuarios.show');

    // EDITAR
    Route::get(
        '/usuarios/{id}/edit',
        [UsuarioController::class, 'edit']
    )->name('admin.usuarios.edit');

    // ACTUALIZAR
    Route::put(
        '/usuarios/{id}',
        [UsuarioController::class, 'update']
    )->name('admin.usuarios.update');

    // ACTIVAR / DESACTIVAR
    Route::put(
        '/usuarios/{id}/toggle',
        [UsuarioController::class, 'toggle']
    )->name('admin.usuarios.toggle');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/documentos/{id}',
        [DocumentoController::class, 'detalle']
    )->name('admin.documentos.detalle');

    /*
    |--------------------------------------------------------------------------
    | CORRESPONDENCIA ADMIN
    |--------------------------------------------------------------------------
    */

    // LISTADO
    Route::get(
        '/correspondencia',
        [CorrespondenciaController::class, 'index']
    )->name('admin.correspondencia');

    // DETALLE
    Route::get(
        '/correspondencia/{id}',
        [CorrespondenciaController::class, 'show']
    )->name('admin.correspondencia.show');

    // DERIVAR
    Route::post(
        '/correspondencia/{id}/derivar',
        [CorrespondenciaController::class, 'derivar']
    )->name('admin.correspondencia.derivar');

    Route::get('/admin', function () {
    return view('admin.index');
    })->name('admin.index');
    
    /*
    |--------------------------------------------------------------------------
    | REPORTES (USUARIO: DOCUMENTOS Y DEPARTAMENTOS)
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/reportes/documentos',
        [ReporteController::class, 'documentos']
    )->name('admin.reportes.documentos');
    Route::get('/reportes/documentos/pdf', [ReporteController::class, 'documentosPDF'])->name('admin.reportes.documentos.pdf');

    Route::get(
        '/reportes/departamentos',
        [ReporteController::class, 'departamentos']
    )->name('admin.reportes.departamentos');
    Route::get('/reportes/departamentos/pdf', [ReporteController::class, 'departamentosPDF'])->name('admin.reportes.departamentos.pdf');

    Route::middleware('admin')->group(function () {
        // PANEL REPORTES
        Route::get(
            '/reportes',
            [ReporteController::class, 'index']
        )->name('admin.reportes.index');

        // REPORTE USUARIOS
        Route::get(
            '/reportes/usuarios',
            [ReporteController::class, 'usuarios']
        )->name('admin.reportes.usuarios');
        Route::get('/reportes/usuarios/pdf', [ReporteController::class, 'usuariosPDF'])->name('admin.reportes.usuarios.pdf');

        // REPORTE DERIVACIONES
        Route::get(
            '/reportes/derivaciones',
            [ReporteController::class, 'derivaciones']
        )->name('admin.reportes.derivaciones');
        Route::get('/reportes/derivaciones/pdf', [ReporteController::class, 'derivacionesPDF'])->name('admin.reportes.derivaciones.pdf');

        // REPORTE PERSONAS
        Route::get('/reportes/personas', [ReporteController::class, 'personas'])->name('admin.reportes.personas');
        Route::get('/reportes/personas/pdf', [ReporteController::class, 'personasPDF'])->name('admin.reportes.personas.pdf');

        // API ENDPOINTS PARA GRÁFICOS - PROTEGIDOS
        Route::get(
            '/api/estadisticas/dashboard',
            [ReporteController::class, 'getEstadisticasDashboard']
        )->name('admin.api.estadisticas.dashboard');

        Route::get(
            '/api/estadisticas/departamentos',
            [ReporteController::class, 'getEstadisticasDepartamentos']
        )->name('admin.api.estadisticas.departamentos');

        Route::get(
            '/api/estadisticas/personas',
            [ReporteController::class, 'getEstadisticasPersonas']
        )->name('admin.api.estadisticas.personas');
    });

    /*=================== */
    //     creacion de modulos de reportes 
    //adicionales para personas y documentos,
    //
    // con sus respectivas rutas para vista y generación de PDF. Esto permitirá al administrador generar informes detallados sobre las personas registradas en el sistema y los documentos gestionados, facilitando la toma de decisiones y el análisis de datos.
    /*=================== */
    Route::get(
    '/departamentos',
    [DepartamentoController::class, 'index']
)->name('admin.departamentos.index');

Route::get(
    '/departamentos/create',
    [DepartamentoController::class, 'create']
)->name('admin.departamentos.create');

Route::post(
    '/departamentos',
    [DepartamentoController::class, 'store']
)->name('admin.departamentos.store');
// BUSCAR PERSONAS
Route::get(
    '/departamentos/personas/buscar',
    [DepartamentoController::class, 'buscarPersonas']
)->name('admin.departamentos.personas.buscar');


Route::get(
    '/departamentos/{id}/edit',
    [DepartamentoController::class, 'edit']
)->name('admin.departamentos.edit');


Route::put(
    '/departamentos/{id}',
    [DepartamentoController::class, 'update']
)->name('admin.departamentos.update');

/*
|--------------------------------------------------------------------------
| RUTAS DE AUDITORÍA
|--------------------------------------------------------------------------
*/

Route::prefix('admin/auditoria')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ESTADÍSTICAS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/estadisticas',
            [AuditoriaController::class, 'estadisticas']
        )->name('auditoria.estadisticas');

        /*
        |--------------------------------------------------------------------------
        | HISTORIAL
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/historial/{modelo}/{idRegistro}',
            [AuditoriaController::class, 'registroHistorial']
        )->name('auditoria.historial');

        /*
        |--------------------------------------------------------------------------
        | EXPORTAR CSV
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/exportar/csv',
            [AuditoriaController::class, 'exportar']
        )->name('auditoria.exportar');

        /*
        |--------------------------------------------------------------------------
        | API JSON
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/api/data',
            [AuditoriaController::class, 'api']
        )->name('auditoria.api');

        /*
        |--------------------------------------------------------------------------
        | INDEX
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [AuditoriaController::class, 'index']
        )->name('auditoria.index');

        /*
        |--------------------------------------------------------------------------
        | SHOW
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{idAuditoria}',
            [AuditoriaController::class, 'show']
        )
        ->whereNumber('idAuditoria')
        ->name('auditoria.show');
    });


Route::put(
    '/departamentos/{id}/toggle',
    [DepartamentoController::class, 'toggle']
)->name('admin.departamentos.toggle');

/*
|--------------------------------------------------------------------------
| PERSONAS
|--------------------------------------------------------------------------
*/

Route::get(
    '/personas',
    [PersonaController::class, 'index']
)->name('admin.personas.index');

Route::get(
    '/personas/create',
    [PersonaController::class, 'create']
)->name('admin.personas.create');

Route::post(
    '/personas',
    [PersonaController::class, 'store']
)->name('admin.personas.store');

Route::get(
    '/personas/{id}/edit',
    [PersonaController::class, 'edit']
)->name('admin.personas.edit');

Route::put(
    '/personas/{id}',
    [PersonaController::class, 'update']
)->name('admin.personas.update');

Route::post(
    '/personas/{id}/disable',
    [PersonaController::class, 'disable']
)->name('admin.personas.disable');

Route::post(
    '/personas/{id}/enable',
    [PersonaController::class, 'enable']
)->name('admin.personas.enable');

Route::put(
    '/personas/{id}/toggle',
    [PersonaController::class, 'toggle']
)->name('admin.personas.toggle');

Route::get(
    '/personas/buscar',
    [PersonaController::class, 'buscar']
)->name('admin.personas.buscar');
//para admins
/*
|--------------------------------------------------------------------------
| GESTIÓN DOCUMENTAL
|--------------------------------------------------------------------------
*/

Route::get(
    '/documentos',
    [DocumentoController::class, 'adminIndex']
)->name('admin.documentos.index');

Route::get(
    '/documentos/{id}/edit',
    [DocumentoController::class, 'edit']
)->name('admin.documentos.edit');

Route::put(
    '/documentos/{id}',
    [DocumentoController::class, 'update']
)->name('admin.documentos.update');

Route::put(
    '/documentos/{id}/toggle',
    [DocumentoController::class, 'toggle']
)->name('admin.documentos.toggle');
/*
|--------------------------------------------------------------------------
| BUSCADOR REMITENTE ADMIN
|--------------------------------------------------------------------------
*/

Route::get(
    '/personas/buscar',
    [PersonaController::class, 'buscar']
)->name('admin.personas.buscar');


});

/*
|--------------------------------------------------------------------------
| PERFIL
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
