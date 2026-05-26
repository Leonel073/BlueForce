<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\DerivacionResource;
use App\Models\Derivacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * DerivacionController
 * Controlador CRUD para derivaciones de documentos
 */
class DerivacionController extends BaseController
{
    protected array $searchableColumns = ['instruccion'];
    protected array $filterableColumns = ['idDocumento', 'idDepartamentoOrigen', 'idDepartamentoDestino', 'activo'];
    protected array $sortableColumns = ['idDerivacion', 'fechaEnvio', 'fechaRecepcion', 'orden'];

    public function index(): JsonResponse
    {
        try {
            $query = Derivacion::query();
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new DerivacionResource($item)),
                'Derivaciones obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener derivaciones', $e);
        }
    }

    public function store(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $data = request()->validate([
                'idDocumento' => 'required|exists:CORRESPONDENCIA,idDocumento',
                'idDepartamentoOrigen' => 'required|exists:DEPARTAMENTO,idDepartamento',
                'idDepartamentoDestino' => 'required|exists:DEPARTAMENTO,idDepartamento|different:idDepartamentoOrigen',
                'idUsuarioAsignado' => 'required|exists:users,id',
                'instruccion' => 'required|string|min:10',
            ]);

            // Obtener orden máxima para el documento
            $maxOrden = Derivacion::where('idDocumento', $data['idDocumento'])->max('orden') ?? 0;

            $derivacion = Derivacion::create([
                ...$data,
                'orden' => $maxOrden + 1,
                'idUsuarioEnvio' => $user->id,
                'fechaEnvio' => now(),
                'activo' => 1,
            ]);

            $derivacion->load('documento', 'departamentoOrigen', 'departamentoDestino', 'usuarioAsignado', 'usuarioEnvio');

            return $this->respondCreated(
                new DerivacionResource($derivacion),
                'Derivación creada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al crear derivación', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $derivacion = Derivacion::with([
                'documento',
                'departamentoOrigen',
                'departamentoDestino',
                'usuarioAsignado',
                'usuarioEnvio',
            ])->find($id);

            if (!$derivacion) {
                return $this->respondNotFound('Derivación no encontrada');
            }

            return $this->respondSuccess(
                new DerivacionResource($derivacion),
                'Derivación obtenida correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener derivación', $e);
        }
    }

    public function update(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $derivacion = Derivacion::find($id);

            if (!$derivacion) {
                return $this->respondNotFound('Derivación no encontrada');
            }

            $data = request()->validate([
                'idUsuarioAsignado' => 'sometimes|required|exists:users,id',
                'instruccion' => 'sometimes|required|string|min:10',
            ]);

            $datosAnteriores = $derivacion->toArray();
            $derivacion->update($data);

            return $this->respondSuccess(
                new DerivacionResource($derivacion->load('documento', 'departamentoOrigen', 'departamentoDestino', 'usuarioAsignado')),
                'Derivación actualizada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al actualizar derivación', $e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $derivacion = Derivacion::find($id);

            if (!$derivacion) {
                return $this->respondNotFound('Derivación no encontrada');
            }

            $derivacion->update(['activo' => 0]);

            return $this->respondNoContent();

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al eliminar derivación', $e);
        }
    }

    /**
     * POST /api/v1/derivaciones/{id}/recibir
     * Marca una derivación como recibida
     */
    public function recibir(int $id): JsonResponse
    {
        try {
            $derivacion = Derivacion::find($id);

            if (!$derivacion) {
                return $this->respondNotFound('Derivación no encontrada');
            }

            if ($derivacion->fechaRecepcion) {
                return $this->respondError('La derivación ya fue recibida', 409);
            }

            $derivacion->update(['fechaRecepcion' => now()]);

            return $this->respondSuccess(
                new DerivacionResource($derivacion->load('documento', 'departamentoOrigen', 'departamentoDestino', 'usuarioAsignado')),
                'Derivación recibida correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al recibir derivación', $e);
        }
    }

    /**
     * POST /api/v1/derivaciones/{id}/rechazar
     * Rechaza una derivación
     */
    public function rechazar(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $derivacion = Derivacion::find($id);

            if (!$derivacion) {
                return $this->respondNotFound('Derivación no encontrada');
            }

            if ($derivacion->fechaRecepcion) {
                return $this->respondError('No se puede rechazar una derivación ya recibida', 409);
            }

            // Marcar como inactiva
            $derivacion->update(['activo' => 0]);

            return $this->respondSuccess(
                new DerivacionResource($derivacion),
                'Derivación rechazada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al rechazar derivación', $e);
        }
    }

    /**
     * POST /api/v1/derivaciones/{id}/re-derivar
     * Re-deriva un documento a otro departamento
     */
    public function rederiva(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $derivacionOriginal = Derivacion::find($id);

            if (!$derivacionOriginal) {
                return $this->respondNotFound('Derivación no encontrada');
            }

            $data = request()->validate([
                'idDepartamentoDestino' => 'required|exists:DEPARTAMENTO,idDepartamento',
                'idUsuarioAsignado' => 'required|exists:users,id',
                'instruccion' => 'required|string|min:10',
            ]);

            // Obtener orden máxima
            $maxOrden = Derivacion::where('idDocumento', $derivacionOriginal->idDocumento)->max('orden') ?? 0;

            // Crear nueva derivación
            $nuevaDerivacion = Derivacion::create([
                'idDocumento' => $derivacionOriginal->idDocumento,
                'orden' => $maxOrden + 1,
                'idDepartamentoOrigen' => $derivacionOriginal->idDepartamentoDestino,
                'idDepartamentoDestino' => $data['idDepartamentoDestino'],
                'idUsuarioAsignado' => $data['idUsuarioAsignado'],
                'idUsuarioEnvio' => $user->id,
                'instruccion' => $data['instruccion'],
                'fechaEnvio' => now(),
                'activo' => 1,
            ]);

            $nuevaDerivacion->load('documento', 'departamentoOrigen', 'departamentoDestino', 'usuarioAsignado');

            return $this->respondCreated(
                new DerivacionResource($nuevaDerivacion),
                'Derivación re-enviada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al re-derivar', $e);
        }
    }
}
