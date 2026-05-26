<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * UsuarioController
 */
class UsuarioController extends BaseController
{
    protected array $searchableColumns = ['name', 'email'];
    protected array $filterableColumns = ['idRol', 'activo'];
    protected array $sortableColumns = ['id', 'name', 'email'];

    public function index(): JsonResponse
    {
        try {
            $query = User::query();
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new UserResource($item)),
                'Usuarios obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener usuarios', $e);
        }
    }

    public function store(): JsonResponse
    {
        try {
            $data = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email:rfc,dns|unique:users',
                'password' => 'required|string|min:8',
                'idPersona' => 'required|exists:PERSONA,idPersona',
                'idRol' => 'required|exists:ROL,idRol',
            ]);

            $user = User::create([
                ...$data,
                'password' => Hash::make($data['password']),
                'activo' => 1,
            ]);

            return $this->respondCreated(
                new UserResource($user),
                'Usuario creado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al crear usuario', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->respondNotFound('Usuario no encontrado');
            }

            return $this->respondSuccess(
                new UserResource($user),
                'Usuario obtenido correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener usuario', $e);
        }
    }

    public function update(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->respondNotFound('Usuario no encontrado');
            }

            $data = request()->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => "sometimes|required|email:rfc,dns|unique:users,email,{$id}",
                'idRol' => 'sometimes|required|exists:ROL,idRol',
            ]);

            $user->update($data);

            return $this->respondSuccess(
                new UserResource($user),
                'Usuario actualizado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al actualizar usuario', $e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->respondNotFound('Usuario no encontrado');
            }

            $user->update(['activo' => 0]);

            return $this->respondNoContent();

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al eliminar usuario', $e);
        }
    }

    /**
     * POST /api/v1/usuarios/{id}/cambiar-rol
     */
    public function cambiarRol(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->respondNotFound('Usuario no encontrado');
            }

            $data = request()->validate([
                'idRol' => 'required|exists:ROL,idRol',
            ]);

            $rolAnterior = $user->idRol;
            $user->update(['idRol' => $data['idRol']]);

            return $this->respondSuccess(
                new UserResource($user),
                'Rol actualizado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al cambiar rol', $e);
        }
    }

    /**
     * POST /api/v1/usuarios/{id}/resetear-password
     */
    public function resetearPassword(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->respondNotFound('Usuario no encontrado');
            }

            $data = request()->validate([
                'new_password' => 'required|string|min:8',
            ]);

            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);

            return $this->respondSuccess(
                new UserResource($user),
                'Contraseña resetada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al resetear contraseña', $e);
        }
    }

    /**
     * GET /api/v1/usuarios/{id}/derivaciones-pendientes
     */
    public function derivacionesPendientes(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->respondNotFound('Usuario no encontrado');
            }

            $derivaciones = \App\Models\Derivacion::where('idUsuarioAsignado', $id)
                ->whereNull('fechaRecepcion')
                ->where('activo', 1)
                ->with('documento', 'departamentoDestino')
                ->paginate(10);

            return $this->respondPaginated(
                $derivaciones->through(fn($item) => new \App\Http\Resources\DerivacionResource($item)),
                'Derivaciones pendientes obtenidas'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener derivaciones', $e);
        }
    }
}
