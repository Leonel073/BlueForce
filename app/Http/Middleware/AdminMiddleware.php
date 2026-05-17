<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('login');
        }

        // Verificar si el usuario es administrador (rol 1)
        $user = auth()->user();

        if ($user->idRol !== 1) { // Asumiendo que rol 1 es administrador
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
