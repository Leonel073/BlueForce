<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CorrespondenciaController;
use App\Http\Controllers\ConfiguracionController;

// Inicio
Route::get('/', fn() => redirect()->route('dashboard'));

// Vistas básicas
Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
Route::get('/enviadas', fn() => view('user.enviadas'))->name('enviadas');
Route::get('/recibidas', fn() => view('user.recibidas'))->name('recibidas');
Route::get('/reportes', fn() => view('user.reportes'))->name('reportes');


// 🔥 CONFIGURACIÓN (USANDO CONTROLLER)
Route::prefix('configuracion')->group(function () {

    Route::get('/', [ConfiguracionController::class, 'index'])
        ->name('configuracion');

    Route::post('/perfil', [ConfiguracionController::class, 'updatePerfil'])
        ->name('configuracion.perfil');

    Route::post('/password', [ConfiguracionController::class, 'updatePassword'])
        ->name('configuracion.password');

});


// 📄 DOCUMENTOS
Route::get('/documentos', [CorrespondenciaController::class, 'index'])
    ->name('documentos');

Route::prefix('documento')->group(function () {

    Route::get('/create', [CorrespondenciaController::class, 'create'])
        ->name('documento.create');

    Route::post('/store', [CorrespondenciaController::class, 'store'])
        ->name('documento.store');

    Route::get('/{id}', [CorrespondenciaController::class, 'show'])
        ->name('documento.show');

    Route::get('/{id}/edit', [CorrespondenciaController::class, 'edit'])
        ->name('documento.edit');

});