<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Derivacion;

/**
 * Policy para autorizar acciones sobre Derivaciones
 * 
 * Roles:
 * - Admin: Acceso total
 * - Usuario: Solo puede ver/gestionar derivaciones de su departamento
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class DerivacionPolicy
{
    /**
     * Determine whether the user can view any derivacion.
     */
    public function viewAny(User $user): bool
    {
        return auth()->check();
    }

    /**
     * Determine whether the user can view the derivacion.
     */
    public function view(User $user, Derivacion $derivacion): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        $userDepartamento = $user->persona?->idDepartamento;

        // El usuario está en departamento origen o destino
        return $derivacion->idDepartamentoOrigen == $userDepartamento
            || $derivacion->idDepartamentoDestino == $userDepartamento;
    }

    /**
     * Determine whether the user can create a derivacion.
     */
    public function create(User $user): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // El usuario debe estar asignado a un departamento
        return $user->persona?->idDepartamento != null;
    }

    /**
     * Determine whether the user can update the derivacion.
     */
    public function update(User $user, Derivacion $derivacion): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        $userDepartamento = $user->persona?->idDepartamento;

        // El usuario está en departamento origen (que envía)
        if ($derivacion->idDepartamentoOrigen == $userDepartamento) {
            return true;
        }

        // El usuario está asignado a esta derivación
        if ($derivacion->idUsuarioAsignado == $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the derivacion.
     */
    public function delete(User $user, Derivacion $derivacion): bool
    {
        // Solo admin puede eliminar
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can recibir (receive) the derivacion.
     */
    public function recibir(User $user, Derivacion $derivacion): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        $userDepartamento = $user->persona?->idDepartamento;

        // El usuario está en el departamento destino y la derivación es para él
        return $derivacion->idDepartamentoDestino == $userDepartamento
            && ($derivacion->idUsuarioAsignado == $user->id || $derivacion->idUsuarioAsignado == null);
    }
}
