<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\CorespondenciaController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\RecibidasController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\CorrespondenciaController as UserCorrespondenciaController;


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
| RUTAS USUARIO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |-----------------------------------
    | DASHBOARD USUARIO
    |-----------------------------------
    */

    Route::get(
        '/user/dashboard',
        [UserDashboardController::class, 'index']
    )->name('user.dashboard');


    /*
    |-----------------------------------
    | DOCUMENTOS
    |-----------------------------------
    */
    

    // LISTADO
    Route::get(
        '/user/documentos',
        [DocumentoController::class, 'index']
    )->name('documentos.show');

    // FORMULARIO CREAR
    Route::get(
        '/user/documentos/crear',
        [DocumentoController::class, 'show']
    )->name('documentos.crear');

    // GUARDAR DOCUMENTO
    Route::post(
        '/user/documentos',
        [DocumentoController::class, 'store']
    )->name('documentos.store');


    /*Correspondencia */
    Route::get(
    '/user/correspondencia',
    [UserCorrespondenciaController::class, 'index']
)->name('user.correspondencia');

    //derivacion de documentacion envio
    Route::get(
    '/user/correspondencia/{id}',
    [UserCorrespondenciaController::class, 'show']
)->name('user.correspondencia.show');

    /*
    |-----------------------------------
    | ENVIADAS / RECIBIDAS
    |-----------------------------------
    */

    Route::get('/user/enviadas', function () {

        return "Aquí verás la tabla de correspondencia enviada.";

    })->name('enviadas');

    Route::get('/user/recibidas', function () {

        return "Aquí verás la tabla de correspondencia recibida.";

    })->name('recibidas');
    Route::post(
    '/envios/{id}/finalizar',
    [EnvioController::class, 'finalizar']
)->name('envios.finalizar');
    Route::put(
    '/envios/{id}/finalizar',
    [EnvioController::class, 'finalizar']
)->name('envios.finalizar');

    /*
    |-----------------------------------
    | CONFIGURACIÓN USUARIO
    |-----------------------------------
    */

    Route::get('/user/configuracion', function () {

        return view('user.configuracion');

    })->name('user.configuracion');

});


/*
|--------------------------------------------------------------------------
| RUTAS ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |-----------------------------------
    | DASHBOARD ADMIN
    |-----------------------------------
    */



Route::get(
    '/admin/dashboard',
    [DashboardController::class, 'index']
)->middleware(['auth', 'verified'])
 ->name('admin.dashboard');


    /*
    |-----------------------------------
    | USUARIOS
    |-----------------------------------
    */

    // LISTADO
    Route::get(
        '/admin/usuarios',
        [UsuarioController::class, 'index']
    )->name('admin.usuarios');

    // DETALLE USUARIO
    Route::get(
        '/admin/usuarios/{id}',
        [UsuarioController::class, 'show']
    )->name('admin.usuarios.show');

    // EDITAR USUARIO
    Route::get(
        '/admin/usuarios/{id}/edit',
        [UsuarioController::class, 'edit']
    )->name('admin.usuarios.edit');

    // ACTUALIZAR USUARIO
    Route::put(
        '/admin/usuarios/{id}',
        [UsuarioController::class, 'update']
    )->name('admin.usuarios.update');

    // ACTIVAR / DESACTIVAR
    Route::put(
        '/admin/usuarios/{id}/toggle',
        [UsuarioController::class, 'toggle']
    )->name('admin.usuarios.toggle');


    /*
    |-----------------------------------
    | DOCUMENTOS ADMIN
    |-----------------------------------
    */

    Route::get(
        '/admin/documentos/{id}',
        [DocumentoController::class, 'detalle']
    )->name('admin.documentos.detalle');

     /*
    |-----------------------------------
    | CORRESPONDENCIA ADMIN
    |-----------------------------------
    */
    Route::get(
    '/admin/correspondencia',
    [\App\Http\Controllers\Admin\CorrespondenciaController::class, 'index']


    )->name('admin.correspondencia');

    //documentacion detallado 
    Route::get(
    '/admin/correspondencia/{id}',
    [\App\Http\Controllers\Admin\CorrespondenciaController::class, 'show']
    )->name('admin.correspondencia.show');

    //derivacion por admin 
    Route::post(
    '/admin/correspondencia/{id}/derivar',
    [\App\Http\Controllers\Admin\CorrespondenciaController::class, 'derivar']

  
)->name('admin.correspondencia.derivar');
// buscar persona por ci para derivacion y creacion de correspondencia
Route::get(
    '/persona/buscar/{ci}',
    [DocumentoController::class, 'buscarPersona']
)->name('persona.buscar');



        /*
        |-----------------------------------
        | REPORTES
        |-----------------------------------
        */
    
        Route::get(
            '/admin/reportes',
            [ReporteController::class, 'index']
        )->name('admin.reportes.index');

        //reporte de usuarios 
        Route::get(
        '/admin/reportes/usuarios',
        [ReporteController::class, 'usuarios']
    )->name('admin.reportes.usuarios');
    //reporte de departamentos  
    Route::get(
    '/admin/reportes/departamentos',
    [ReporteController::class, 'departamentos']
)->name('admin.reportes.departamentos');
    //reporte de derivaciones
    Route::get(
        '/admin/reportes/derivaciones',
        [ReporteController::class, 'derivaciones']
    )->name('admin.reportes.derivaciones');
    /*Descarga PDF */
    Route::get(
    '/admin/reportes/derivaciones/pdf',
    [ReporteController::class, 'derivacionesPDF']
)->name('admin.reportes.derivaciones.pdf');
});

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | MÓDULO ENVÍOS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/envios',
        [EnvioController::class, 'index']
    )->name('envios.index');

    //bandehja
    Route::get(
    '/mi-bandeja',
    [EnvioController::class, 'bandeja']
)->name('envios.bandeja');



    //formulario derivar
Route::get(
    '/envios/{id}/derivar',
    [EnvioController::class, 'derivarForm']
)->name('envios.derivar.form');
//guardar derivacion
Route::post(
    '/envios/{id}/derivar',
    [EnvioController::class, 'derivar']
)->name('envios.derivar');
});



//recibos 
Route::get(
    '/recibidas',
    [RecibidasController::class, 'index']
)->name('recibidas.index');

Route::post(
    '/recibidas/{id}/recibir',
    [RecibidasController::class, 'recibir']
)->name('recibidas.recibir');

Route::post(
    '/recibidas/{id}/finalizar',
    [RecibidasController::class, 'finalizar']
)->name('recibidas.finalizar');
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


require __DIR__.'/auth.php';