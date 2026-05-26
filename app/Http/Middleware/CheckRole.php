<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * CheckRole Middleware
 * 
 * Valida que el usuario autenticado tenga el rol requerido
 * 
 * Uso en routes:
 * Route::middleware('role:admin')->group(function () {
 *     // Rutas solo para admin
 * });
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Unauthorized',
                'errors' => [],
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        // Verificar si el usuario tiene uno de los roles requeridos
        if (!in_array($user->idRol, $roles)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'Forbidden - Insufficient permissions',
                'errors' => [],
                'timestamp' => now()->toIso8601String(),
            ], 403);
        }

        return $next($request);
    }
}
