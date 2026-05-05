<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
Route::get('/enviadas', fn() => view('user.enviadas'))->name('enviadas');
Route::get('/recibidas', fn() => view('user.recibidas'))->name('recibidas');

// MÓDULO DE REGISTRO DOCUMENTAL
Route::get('/documentos', [DocumentoController::class, 'show'])->name('documentos.show');
Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');