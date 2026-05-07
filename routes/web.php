<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentoController; // <-- AQUÍ IMPORTAMOS TU CONTROLADOR
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/page', function () {
    return view('auth.page');
})->name('page');
// Ruta para el Administrador (conecta a resources/views/admin/dashboard.blade.php)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

// Ruta para el Usuario Normal (conecta a resources/views/user/dashboard.blade.php)
Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->middleware(['auth', 'verified'])->name('user.dashboard');


// MÓDULO DE REGISTRO DOCUMENTAL DE TU COMPAÑERO (Protegido por Auth)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Rutas temporales de Enviadas/Recibidas (hasta que las programen)
    Route::get('/user/enviadas', function () {
        return "Aquí verás la tabla de correspondencia enviada.";
    })->name('enviadas');

    Route::get('/user/recibidas', function () {
        return "Aquí verás la tabla de correspondencia recibida.";
    })->name('recibidas');

    // 🔴 RUTAS DEL CONTROLADOR REAL DE DOCUMENTOS 🔴
    Route::get('/user/documentos', [DocumentoController::class, 'index'])->name('documentos.show'); // Muestra la tabla de Documentos
    Route::get('/user/documentos/crear', [DocumentoController::class, 'show'])->name('documentos.crear'); // Muestra el formulario
    Route::post('/user/documentos', [DocumentoController::class, 'store'])->name('documentos.store'); // Guarda en BD
});


// Rutas de perfil (para que cualquier usuario logueado pueda editar su cuenta)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';