<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Verificar que el usuario esté autenticado y tenga rol de admin
         * 
         * Roles esperados: 
         * - ADMIN (idRol = 1)
         * - ADMINISTRADOR
         */
        
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Debe estar autenticado');
        }

        $user = auth()->user();
        
        // Verificar si el rol es admin
        // Se asume que el rol ADMIN tiene idRol = 1 o nombre = 'ADMIN'
        $isAdmin = $user->rol && (
            $user->rol->nombre === 'ADMIN' || 
            $user->rol->nombre === 'Administrador' ||
            $user->rol->nombre === 'ADMINISTRADOR'
        );

        if (!$isAdmin) {
            abort(403, 'Acceso denegado. Solo administradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
