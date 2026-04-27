<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
Route::get('/enviadas', fn() => view('user.enviadas'))->name('enviadas');
Route::get('/recibidas', fn() => view('user.recibidas'))->name('recibidas');
Route::get('/documentos', fn() => view('user.documentos'))->name('documentos');