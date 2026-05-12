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

    // DETALLE DOCUMENTO
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

    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    */

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

    // REPORTE DEPARTAMENTOS
    Route::get(
        '/reportes/departamentos',
        [ReporteController::class, 'departamentos']
    )->name('admin.reportes.departamentos');
    Route::get('/admin/reportes/usuarios/pdf', [ReporteController::class, 'usuariosPDF'])->name('admin.reportes.usuarios.pdf');
Route::get('/admin/reportes/departamentos/pdf', [ReporteController::class, 'departamentosPDF'])->name('admin.reportes.departamentos.pdf');

    // REPORTE DERIVACIONES
    Route::get(
        '/reportes/derivaciones',
        [ReporteController::class, 'derivaciones']
    )->name('admin.reportes.derivaciones');
    Route::get('/admin/reportes/personas', [\App\Http\Controllers\Admin\ReporteController::class, 'personas'])->name('admin.reportes.personas');
Route::get('/admin/reportes/personas/pdf', [\App\Http\Controllers\Admin\ReporteController::class, 'personasPDF'])->name('admin.reportes.personas.pdf');

    // PDF DERIVACIONES
    Route::get(
        '/reportes/derivaciones/pdf',
        [ReporteController::class, 'derivacionesPDF']
    )->name('admin.reportes.derivaciones.pdf');

    Route::get('/admin/reportes/documentos', [ReporteController::class, 'documentos'])->name('admin.reportes.documentos');
Route::get('/admin/reportes/documentos/pdf', [ReporteController::class, 'documentosPDF'])->name('admin.reportes.documentos.pdf');
});

/*
|--------------------------------------------------------------------------
| PERFIL
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

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';