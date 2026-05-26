<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\DepartamentoResource;
use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * DepartamentoController
 */
class DepartamentoController extends BaseController
{
    protected array $searchableColumns = ['nombre', 'descripcion'];
    protected array $filterableColumns = ['activo'];
    protected array $sortableColumns = ['idDepartamento', 'nombre'];

    public function index(): JsonResponse
    {
        try {
            $query = Departamento::query();
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new DepartamentoResource($item)),
                'Departamentos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener departamentos', $e);
        }
    }

    public function store(): JsonResponse
    {
        try {
            $data = request()->validate([
                'nombre' => 'required|string|max:255|unique:DEPARTAMENTO,nombre',
                'descripcion' => 'nullable|string',
            ]);

            $departamento = Departamento::create([
                ...$data,
                'activo' => 1,
            ]);

            return $this->respondCreated(
                new DepartamentoResource($departamento),
                'Departamento creado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al crear departamento', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $departamento = Departamento::find($id);

            if (!$departamento) {
                return $this->respondNotFound('Departamento no encontrado');
            }

            return $this->respondSuccess(
                new DepartamentoResource($departamento),
                'Departamento obtenido correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener departamento', $e);
        }
    }

    public function update(int $id): JsonResponse
    {
        try {
            $departamento = Departamento::find($id);

            if (!$departamento) {
                return $this->respondNotFound('Departamento no encontrado');
            }

            $data = request()->validate([
                'nombre' => "sometimes|required|string|max:255|unique:DEPARTAMENTO,nombre,{$id},idDepartamento",
                'descripcion' => 'nullable|string',
            ]);

            $departamento->update($data);

            return $this->respondSuccess(
                new DepartamentoResource($departamento),
                'Departamento actualizado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al actualizar departamento', $e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $departamento = Departamento::find($id);

            if (!$departamento) {
                return $this->respondNotFound('Departamento no encontrado');
            }

            $departamento->update(['activo' => 0]);

            return $this->respondNoContent();

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al eliminar departamento', $e);
        }
    }

    /**
     * GET /api/v1/departamentos/{id}/usuarios
     */
    public function usuarios(int $id): JsonResponse
    {
        try {
            $departamento = Departamento::find($id);

            if (!$departamento) {
                return $this->respondNotFound('Departamento no encontrado');
            }

            // Aquí asumimos que existe relación de usuarios por departamento
            // Ajusta según tu estructura
            $usuarios = User::where('activo', 1)
                ->paginate(10);

            return $this->respondPaginated(
                $usuarios->through(fn($item) => new \App\Http\Resources\UserResource($item)),
                'Usuarios del departamento obtenidos'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener usuarios', $e);
        }
    }

    /**
     * GET /api/v1/departamentos/{id}/derivaciones-pendientes
     */
    public function derivacionesPendientes(int $id): JsonResponse
    {
        try {
            $departamento = Departamento::find($id);

            if (!$departamento) {
                return $this->respondNotFound('Departamento no encontrado');
            }

            $derivaciones = \App\Models\Derivacion::where('idDepartamentoDestino', $id)
                ->whereNull('fechaRecepcion')
                ->where('activo', 1)
                ->with('documento', 'usuarioAsignado')
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
