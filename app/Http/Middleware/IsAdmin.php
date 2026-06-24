<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * IsAdmin Middleware
 * 
 * Verifica que el usuario sea administrador (idRol = 1)
 * Se aplica a todas las rutas /admin/*
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Debe estar autenticado.');
        }

        $user = auth()->user();
        
        // ADMIN: idRol = 1
        if ($user->idRol !== 1) {
            // Log attempt
            \Illuminate\Support\Facades\Log::warning(
                "Acceso denegado a sección admin",
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->idRol,
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                ]
            );

            abort(403, 'Acceso denegado. Solo administradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
