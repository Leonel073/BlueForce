<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CorrespondenciaController;

// Inicio
Route::get('/', fn() => redirect()->route('dashboard'));

// Vistas básicas
Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
Route::get('/enviadas', fn() => view('user.enviadas'))->name('enviadas');
Route::get('/recibidas', fn() => view('user.recibidas'))->name('recibidas');
Route::get('/reportes', fn() => view('user.reportes'))->name('reportes');

// 🔥 DOCUMENTOS (CONTROLADOR)
Route::get('/documentos', [CorrespondenciaController::class, 'index'])->name('documentos');

// Crear documento
Route::get('/documento/create', [CorrespondenciaController::class, 'create'])->name('documento.create');
Route::post('/documento/store', [CorrespondenciaController::class, 'store'])->name('documento.store');
