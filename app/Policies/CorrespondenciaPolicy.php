<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Correspondencia;
use App\Models\Derivacion;

/**
 * Policy para autorizar acciones sobre Correspondencias
 * 
 * REGLA FUNDAMENTAL:
 * - Responsable actual = usuario en ultimaDerivacion.idUsuarioAsignado
 * - Solo el responsable actual puede operar (derivar, atender, recibir, archivar)
 * - Creador y Admin pueden VER, pero no operar si ya han sido derivados
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
     * 
     * Permite ver si:
     * 1. Es Admin
     * 2. Es el creador del documento
     * 3. Es el responsable actual
     * 4. Ha sido responsable en algún momento (histórico)
     */
    public function view(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // El usuario es el creador
        if ($correspondencia->idUsuario == $user->id) {
            return true;
        }

        // El usuario es responsable actual
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;
        if ($ultimaDerivacion && $ultimaDerivacion->idUsuarioAsignado == $user->id) {
            return true;
        }

        // El usuario fue responsable en algún momento (histórico)
        $fueResponsable = Derivacion::where('idDocumento', $correspondencia->idDocumento)
            ->where('idUsuarioAsignado', $user->id)
            ->where('activo', true)
            ->exists();

        if ($fueResponsable) {
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
     * 
     * Solo permite actualizar si es el creador y está en estado Pendiente (inicial)
     */
    public function update(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: no debe actualizar así (solo via admin panel)
        if ($user->isAdmin()) {
            return false;
        }

        // Solo el creador puede actualizar si aún está en estado inicial
        if ($correspondencia->idUsuario == $user->id && $correspondencia->idEstado == 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the correspondencia.
     * 
     * Solo permite eliminar si es el creador y está en estado Pendiente
     */
    public function delete(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: no debe eliminar así (solo via admin panel)
        if ($user->isAdmin()) {
            return false;
        }

        // El creador puede eliminar si está en estado inicial
        if ($correspondencia->idUsuario == $user->id && $correspondencia->idEstado == 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can derivate (reenviar) the correspondencia.
     * 
     * CRÍTICO: Solo el RESPONSABLE ACTUAL puede derivar
     * Si ya ha sido derivado a otra persona, el usuario anterior NO puede seguir derivando
     */
    public function derivar(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // Usuario normal: SOLO si es responsable actual
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;

        if (!$ultimaDerivacion) {
            // Si no hay derivación, solo el creador puede derivar
            return $correspondencia->idUsuario == $user->id;
        }

        // Si hay derivación, SOLO el responsable actual puede derivar
        return $ultimaDerivacion->idUsuarioAsignado == $user->id;
    }

    /**
     * Determine whether the user can receive (recibir) the correspondencia.
     * 
     * Solo el RESPONSABLE ACTUAL puede recibir
     */
    public function recibir(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // Usuario normal: SOLO si es responsable actual
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;

        if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
            return false;
        }

        return $ultimaDerivacion->idUsuarioAsignado == $user->id;
    }

    /**
     * Determine whether the user can attend (atender) the correspondencia.
     * 
     * Solo el RESPONSABLE ACTUAL puede atender
     */
    public function atender(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // Usuario normal: SOLO si es responsable actual
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;

        if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
            return false;
        }

        return $ultimaDerivacion->idUsuarioAsignado == $user->id;
    }

    /**
     * Determine whether the user can archive (archivar) the correspondencia.
     * 
     * Solo el RESPONSABLE ACTUAL puede archivar
     */
    public function archivar(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // Usuario normal: SOLO si es responsable actual
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;

        if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
            return false;
        }

        return $ultimaDerivacion->idUsuarioAsignado == $user->id;
    }

    /**
     * Determine whether the user can finalize (finalizar) the correspondencia.
     * 
     * Solo el RESPONSABLE ACTUAL puede finalizar
     */
    public function finalizar(User $user, Correspondencia $correspondencia): bool
    {
        // Admin: Acceso total
        if ($user->isAdmin()) {
            return true;
        }

        // Usuario normal: SOLO si es responsable actual
        $ultimaDerivacion = $correspondencia->ultimaDerivacion;

        if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
            return false;
        }

        return $ultimaDerivacion->idUsuarioAsignado == $user->id;
    }

    /**
     * Determine whether the user can change status.
     */
    public function cambiarEstado(User $user, Correspondencia $correspondencia): bool
    {
        // Solo admin
        return $user->isAdmin();
    }
}

