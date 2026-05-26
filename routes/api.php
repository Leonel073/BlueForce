<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
|
| Aquí se define la configuración general de API
| Las rutas específicas están organizadas por versión
|
*/

Route::prefix('v1')->group(base_path('routes/api/v1.php'));

// Futuras versiones pueden ir aquí
// Route::prefix('v2')->group(base_path('routes/api/v2.php'));
