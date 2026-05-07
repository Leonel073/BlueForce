<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\CorespondenciaController;
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

    Route::get('/admin/dashboard', function () {

        return view('admin.dashboard');

    })->name('admin.dashboard');


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


require __DIR__.'/auth.php';