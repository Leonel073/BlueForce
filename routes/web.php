<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| IMPORTS DE CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentoPdfController;
use App\Http\Controllers\BandejaController;
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
use App\Http\Controllers\Admin\AnuncioController as AdminAnuncioController;
use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\AnuncioPdfController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->idRol === 1 ? 'admin.dashboard' : 'user.dashboard');
    }

    return view('welcome');
});

Route::get('/page', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->idRol === 1 ? 'admin.dashboard' : 'user.dashboard');
    }

    return view('auth.page');
})->name('page');

Route::get('/dashboard', function () {
    return redirect()->route(Auth::user()->idRol === 1 ? 'admin.dashboard' : 'user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| RUTAS DE PERFIL (AUTENTICADO)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/anuncios/{id}/pdf/previsualizar', [AnuncioPdfController::class, 'previsualizar'])->name('anuncios.pdf.previsualizar');
    Route::get('/anuncios/{id}/pdf/descargar', [AnuncioPdfController::class, 'descargar'])->name('anuncios.pdf.descargar');
    Route::post('/anuncios/{id}/visto', [AnuncioController::class, 'marcarVisto'])->name('anuncios.marcar-visto');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE USUARIO (no admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'nocache', 'user'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD PERSONAL USUARIO
    |--------------------------------------------------------------------------
    */
    
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    Route::get('/anuncios', [AnuncioController::class, 'index'])->name('user.anuncios.index');
    Route::get('/anuncios/{id}', [AnuncioController::class, 'show'])->name('user.anuncios.show');
    Route::get('/api/anuncios/pendiente', [AnuncioController::class, 'pendiente'])->name('api.anuncios.pendiente');

    /*
    |--------------------------------------------------------------------------
    | BANDEJA DE USUARIO (Mi correspondencia)
    |--------------------------------------------------------------------------
    */

    // Mi bandeja con filtros
    Route::get('/mi-bandeja', [EnvioController::class, 'bandeja'])->name('envios.bandeja');

    // Documentos en estado Pendiente
    Route::get('/bandeja/pendientes', [BandejaController::class, 'pendientes'])->name('bandeja.pendientes');

    // Documentos en estado Recibido
    Route::get('/bandeja/recibidos', [BandejaController::class, 'recibidos'])->name('bandeja.recibidos');

    // Documentos en estado Atendido
    Route::get('/bandeja/atendidos', [BandejaController::class, 'atendidos'])->name('bandeja.atendidos');

    // Documentos en estado Archivado
    Route::get('/bandeja/archivados', [BandejaController::class, 'archivados'])->name('bandeja.archivados');

    /*
    |--------------------------------------------------------------------------
    | RECIBIDAS (Correspondencia recibida)
    |--------------------------------------------------------------------------
    */

    Route::get('/recibidas', [RecibidasController::class, 'index'])->name('recibidas.index');
    Route::post('/recibidas/{id}/recibir', [RecibidasController::class, 'recibir'])->name('recibidas.recibir');
    Route::post('/recibidas/{id}/atender', [RecibidasController::class, 'atender'])->name('recibidas.atender');
    Route::post('/recibidas/{id}/archivar', [RecibidasController::class, 'archivar'])->name('recibidas.archivar');
    Route::post('/recibidas/{id}/finalizar', [RecibidasController::class, 'finalizar'])->name('recibidas.finalizar');

    /*
    |--------------------------------------------------------------------------
    | ENVÍOS (Correspondencia enviada)
    |--------------------------------------------------------------------------
    */

    Route::get('/envios', [EnvioController::class, 'index'])->name('envios.index');
    Route::get('/envios/{id}/derivar', [EnvioController::class, 'derivarForm'])->name('envios.derivar.form');
    Route::post('/envios/{id}/derivar', [EnvioController::class, 'derivar'])->name('envios.derivar');
    Route::put('/envios/{id}/finalizar', [EnvioController::class, 'finalizar'])->name('envios.finalizar');

    /*
    |--------------------------------------------------------------------------
    | CORRESPONDENCIA USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get('/correspondencia', [CorrespondenciaController::class, 'index'])->name('correspondencia.index');
    Route::get('/correspondencia/{id}', [CorrespondenciaController::class, 'show'])->name('correspondencia.show');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS DE USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/documentos/crear', [DocumentoController::class, 'show'])->name('documentos.crear');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{id}', [DocumentoController::class, 'detalle'])->name('documentos.detalle');

    /*
    |--------------------------------------------------------------------------
    | PDF DE DOCUMENTOS
    |--------------------------------------------------------------------------
    */

    Route::get('/documentos/{id}/pdf/descargar', [DocumentoPdfController::class, 'descargar'])->name('documentos.pdf.descargar');
    Route::get('/documentos/{id}/pdf/previsualizar', [DocumentoPdfController::class, 'previsualizar'])->name('documentos.pdf.previsualizar');

    /*
    |--------------------------------------------------------------------------
    | BÚSQUEDA DE PERSONAS (para formularios)
    |--------------------------------------------------------------------------
    */

    Route::get('/persona/buscar/{ci}', [DocumentoController::class, 'buscarPersona'])->name('persona.buscar');
    Route::get('/documentos/departamento/{idDepartamento}/personas', [DocumentoController::class, 'obtenerPersonasPorDepartamento'])->name('documentos.departamento.personas');
    Route::get('/documentos/responsables-departamento/{idDepartamento}', [DocumentoController::class, 'cargarResponsablesPorDepartamento'])->name('documentos.responsables-departamento');
    
    // Búsqueda avanzada multicampo para "Otra Persona"
    Route::get('/personas/buscar-avanzado', [DocumentoController::class, 'buscarPersonasAvanzado'])->name('personas.buscar-avanzado');
    
    // Verificar duplicados antes de crear nueva persona
    Route::post('/personas/verificar-duplicados', [DocumentoController::class, 'verificarDuplicados'])->name('personas.verificar-duplicados');

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN DE USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get('/user/configuracion', function () {
        return view('user.configuracion');
    })->name('user.configuracion');

});

/*
|--------------------------------------------------------------------------
| RUTAS DE ADMINISTRACIÓN (solo ADMIN: idRol = 1)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'nocache', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard-estadisticas', [DashboardController::class, 'estadisticasDashboard'])->name('api.estadisticas.dashboard');
    Route::get('/api/dashboard-departamentos', [DashboardController::class, 'estadisticasDepartamentos'])->name('api.estadisticas.departamentos');
    Route::get('/documentos', [DocumentoController::class, 'adminIndex'])->name('documentos.index');
    
    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS ADMIN - EDICIÓN
    |--------------------------------------------------------------------------
    */
    
    Route::get('/documentos/{id}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit');
    Route::put('/documentos/{id}', [DocumentoController::class, 'update'])->name('documentos.update');
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE USUARIOS
    |--------------------------------------------------------------------------
    */

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/buscar-personas', [UsuarioController::class, 'buscarPersonas'])->name('usuarios.buscar-personas');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::put('/usuarios/{id}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE DEPARTAMENTOS
    |--------------------------------------------------------------------------
    */

    Route::get('/departamentos', [DepartamentoController::class, 'index'])->name('departamentos.index');
    Route::get('/departamentos/create', [DepartamentoController::class, 'create'])->name('departamentos.create');
    Route::post('/departamentos', [DepartamentoController::class, 'store'])->name('departamentos.store');
    Route::get('/departamentos/{id}/edit', [DepartamentoController::class, 'edit'])->name('departamentos.edit');
    Route::put('/departamentos/{id}', [DepartamentoController::class, 'update'])->name('departamentos.update');
    Route::put('/departamentos/{id}/toggle', [DepartamentoController::class, 'toggle'])->name('departamentos.toggle');
    Route::get('/departamentos/personas/buscar', [DepartamentoController::class, 'buscarPersonas'])->name('departamentos.personas.buscar');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE PERSONAS
    |--------------------------------------------------------------------------
    */

    Route::get('/personas', [PersonaController::class, 'index'])->name('personas.index');
    Route::get('/personas/create', [PersonaController::class, 'create'])->name('personas.create');
    Route::post('/personas', [PersonaController::class, 'store'])->name('personas.store');
    Route::get('/personas/{id}/edit', [PersonaController::class, 'edit'])->name('personas.edit');
    Route::put('/personas/{id}', [PersonaController::class, 'update'])->name('personas.update');
    Route::put('/personas/{id}/toggle', [PersonaController::class, 'toggle'])->name('personas.toggle');
    Route::get('/personas/buscar-cargos', [PersonaController::class, 'buscarCargos'])->name('personas.buscar-cargos');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE ANUNCIOS
    |--------------------------------------------------------------------------
    */

    Route::get('/anuncios', [AdminAnuncioController::class, 'index'])->name('anuncios.index');
    Route::get('/anuncios/create', [AdminAnuncioController::class, 'create'])->name('anuncios.create');
    Route::post('/anuncios', [AdminAnuncioController::class, 'store'])->name('anuncios.store');
    Route::get('/anuncios/{id}', [AdminAnuncioController::class, 'show'])->name('anuncios.show');
    Route::put('/anuncios/{id}/toggle', [AdminAnuncioController::class, 'toggle'])->name('anuncios.toggle');
    Route::delete('/anuncios/{id}', [AdminAnuncioController::class, 'destroy'])->name('anuncios.destroy');

    /*
    |--------------------------------------------------------------------------
    | CORRESPONDENCIA ADMIN (TOTAL ACCESO)
    |--------------------------------------------------------------------------
    */

    Route::get('/correspondencia', [CorrespondenciaController::class, 'index'])->name('correspondencia');
    Route::get('/correspondencia/{id}', [CorrespondenciaController::class, 'show'])->name('correspondencia.show');
    Route::post('/correspondencia/{id}/derivar', [CorrespondenciaController::class, 'derivar'])->name('correspondencia.derivar');

    /*
    |--------------------------------------------------------------------------
    | BANDEJA ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/bandeja', [EnvioController::class, 'bandeja'])->name('bandeja');
    Route::get('/bandeja/{id}', [EnvioController::class, 'derivarForm'])->name('bandeja.derivar.form');
    Route::post('/bandeja/{id}/derivar', [EnvioController::class, 'derivar'])->name('bandeja.derivar');

    /*
    |--------------------------------------------------------------------------
    | ENVÍOS ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/envios', [EnvioController::class, 'index'])->name('envios');
    Route::put('/envios/{id}/finalizar', [EnvioController::class, 'finalizar'])->name('envios.finalizar');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS ADMIN - DETALLES Y PDF
    |--------------------------------------------------------------------------
    */

    Route::get('/documentos/{id}', [DocumentoController::class, 'detalle'])->name('documentos.detalle');
    Route::post('/documentos/{id}/pdf', [DocumentoController::class, 'subirPdf'])->name('documentos.pdf.subir');
    Route::delete('/documentos/{id}/pdf', [DocumentoController::class, 'eliminarPdf'])->name('documentos.pdf.eliminar');

    /*
    |--------------------------------------------------------------------------
    | REPORTES ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/usuarios', [ReporteController::class, 'usuarios'])->name('reportes.usuarios');
    Route::get('/reportes/usuarios/pdf', [ReporteController::class, 'usuariosPDF'])->name('reportes.usuarios.pdf');
    Route::get('/reportes/documentos', [ReporteController::class, 'documentos'])->name('reportes.documentos');
    Route::get('/reportes/documentos/pdf', [ReporteController::class, 'documentosPDF'])->name('reportes.documentos.pdf');
    Route::get('/reportes/departamentos', [ReporteController::class, 'departamentos'])->name('reportes.departamentos');
    Route::get('/reportes/departamentos/pdf', [ReporteController::class, 'departamentosPDF'])->name('reportes.departamentos.pdf');
    Route::get('/reportes/derivaciones', [ReporteController::class, 'derivaciones'])->name('reportes.derivaciones');
    Route::get('/reportes/derivaciones/pdf', [ReporteController::class, 'derivacionesPDF'])->name('reportes.derivaciones.pdf');
    Route::get('/reportes/personas', [ReporteController::class, 'personas'])->name('reportes.personas');
    Route::get('/reportes/personas/pdf', [ReporteController::class, 'personasPDF'])->name('reportes.personas.pdf');

    // API Endpoints para gráficos
    Route::get('/api/estadisticas/dashboard', [ReporteController::class, 'getEstadisticasDashboard'])->name('api.reportes.estadisticas.dashboard');
    Route::get('/api/estadisticas/departamentos', [ReporteController::class, 'getEstadisticasDepartamentos'])->name('api.reportes.estadisticas.departamentos');
    Route::get('/api/estadisticas/personas', [ReporteController::class, 'getEstadisticasPersonas'])->name('api.reportes.estadisticas.personas');

    /*
    |--------------------------------------------------------------------------
    | AUDITORÍA (solo ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::prefix('auditoria')->name('auditoria.')->group(function () {
        Route::get('/', [AuditoriaController::class, 'index'])->name('index');
        Route::get('/{idAuditoria}', [AuditoriaController::class, 'show'])->whereNumber('idAuditoria')->name('show');
        Route::get('/estadisticas', [AuditoriaController::class, 'estadisticas'])->name('estadisticas');
        Route::get('/historial/{modelo}/{idRegistro}', [AuditoriaController::class, 'registroHistorial'])->name('historial');
        Route::get('/exportar/csv', [AuditoriaController::class, 'exportar'])->name('exportar');
        Route::get('/api/data', [AuditoriaController::class, 'api'])->name('api');
    });

});
/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
