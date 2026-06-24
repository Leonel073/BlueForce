<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * IsUser Middleware
 * 
 * Verifica que el usuario NO sea administrador (idRol !== 1)
 * Se aplica a rutas de usuario normal para evitar que admin acceda a vistas de usuario
 */
class IsUser
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
        
        // USER: idRol !== 1 (cualquier rol que no sea admin)
        if ($user->idRol === 1) {
            // Admin no puede acceder a rutas de usuario
            return redirect('/admin/dashboard')->with('warning', 'Debe usar el panel de administración.');
        }

        return $next($request);
    }
}
