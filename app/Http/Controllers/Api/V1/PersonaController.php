<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\PersonaResource;
use App\Models\Persona;
use App\Models\Correspondencia;
use Illuminate\Http\JsonResponse;

/**
 * PersonaController
 */
class PersonaController extends BaseController
{
    protected array $searchableColumns = ['nombre', 'ci', 'email'];
    protected array $filterableColumns = ['activo'];
    protected array $sortableColumns = ['idPersona', 'nombre'];

    public function index(): JsonResponse
    {
        try {
            $query = Persona::query();
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new PersonaResource($item)),
                'Personas obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener personas', $e);
        }
    }

    public function store(): JsonResponse
    {
        try {
            $data = request()->validate([
                'nombre' => 'required|string|max:255',
                'ci' => 'nullable|string|unique:PERSONA,ci',
                'cargo' => 'nullable|string|max:100',
                'email' => 'nullable|email:rfc,dns|unique:PERSONA,email',
                'telefono' => 'nullable|string|max:20',
            ]);

            $persona = Persona::create([
                ...$data,
                'activo' => 1,
            ]);

            return $this->respondCreated(
                new PersonaResource($persona),
                'Persona creada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al crear persona', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $persona = Persona::find($id);

            if (!$persona) {
                return $this->respondNotFound('Persona no encontrada');
            }

            return $this->respondSuccess(
                new PersonaResource($persona),
                'Persona obtenida correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener persona', $e);
        }
    }

    public function update(int $id): JsonResponse
    {
        try {
            $persona = Persona::find($id);

            if (!$persona) {
                return $this->respondNotFound('Persona no encontrada');
            }

            $data = request()->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'ci' => "nullable|string|unique:PERSONA,ci,{$id},idPersona",
                'cargo' => 'nullable|string|max:100',
                'email' => "nullable|email:rfc,dns|unique:PERSONA,email,{$id},idPersona",
                'telefono' => 'nullable|string|max:20',
            ]);

            $persona->update($data);

            return $this->respondSuccess(
                new PersonaResource($persona),
                'Persona actualizada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al actualizar persona', $e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $persona = Persona::find($id);

            if (!$persona) {
                return $this->respondNotFound('Persona no encontrada');
            }

            $persona->update(['activo' => 0]);

            return $this->respondNoContent();

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al eliminar persona', $e);
        }
    }

    /**
     * GET /api/v1/personas/{id}/documentos
     * Obtiene todos los documentos de una persona
     */
    public function documentos(int $id): JsonResponse
    {
        try {
            $persona = Persona::find($id);

            if (!$persona) {
                return $this->respondNotFound('Persona no encontrada');
            }

            $documentos = Correspondencia::where('idRemitente', $id)
                ->with('tipoDocumento', 'estado', 'urgencia', 'usuario')
                ->paginate(10);

            return $this->respondPaginated(
                $documentos->through(fn($item) => new \App\Http\Resources\CorrespondenciaResource($item)),
                'Documentos de persona obtenidos'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener documentos', $e);
        }
    }
}
