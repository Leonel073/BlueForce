<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Correspondencia;

/**
 * Policy para autorizar acciones sobre Correspondencias
 * 
 * Define qué usuarios pueden ver, actualizar, eliminar documentos
 * 
 * Roles:
 * - Admin (idRol = 1): Acceso total
 * - Usuario (idRol = 2): Acceso a documentos asignados o en su departamento
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class CorrespondenciaPolicy
{
    /**
     * Determine whether the user can view any correspondencia.
     */
    public function viewAny(User $user): bool
    {
        return auth()->check();
    }

    /**
     * Determine whether the user can view the correspondencia.
     */
    public function view(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // El usuario es el remitente o creador
        if ($correspondencia->idUsuario == $user->id) {
            return true;
        }

        // El usuario está en el departamento de destino
        $inDepartamento = $correspondencia->derivaciones()
            ->where('idDepartamentoDestino', $user->persona->idDepartamento ?? null)
            ->where('activo', true)
            ->exists();

        if ($inDepartamento) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create a correspondencia.
     */
    public function create(User $user): bool
    {
        return auth()->check();
    }

    /**
     * Determine whether the user can update the correspondencia.
     */
    public function update(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // Solo el creador puede actualizar si aún está en estado inicial
        if ($correspondencia->idUsuario == $user->id && $correspondencia->idEstado == 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the correspondencia.
     */
    public function delete(User $user, Correspondencia $correspondencia): bool
    {
        // Solo admin puede eliminar
        if ($user->isAdmin()) {
            return true;
        }

        // El creador puede eliminar si está en estado inicial
        if ($correspondencia->idUsuario == $user->id && $correspondencia->idEstado == 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can derivate (reenviar) the correspondencia.
     */
    public function derivar(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // El usuario debe estar en el departamento actual del documento
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;

        if ($ultimaDerivacion) {
            return $ultimaDerivacion->idDepartamentoDestino == $user->persona?->idDepartamento;
        }

        // Si no hay derivación, solo el creador puede derivar
        return $correspondencia->idUsuario == $user->id;
    }

    /**
     * Determine whether the user can change status.
     */
    public function cambiarEstado(User $user, Correspondencia $correspondencia): bool
    {
        // Solo admin
        return $user->isAdmin();
    }

    /**
     * Helper: ¿El usuario es admin?
     */
}
