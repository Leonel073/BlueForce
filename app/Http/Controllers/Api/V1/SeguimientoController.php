<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\SeguimientoResource;
use App\Models\Seguimiento;
use Illuminate\Http\JsonResponse;

/**
 * SeguimientoController
 */
class SeguimientoController extends BaseController
{
    protected array $searchableColumns = ['estado', 'observacion'];
    protected array $filterableColumns = ['idDocumento'];
    protected array $sortableColumns = ['idSeguimiento', 'fecha'];

    public function index(): JsonResponse
    {
        try {
            $query = Seguimiento::query();
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new SeguimientoResource($item)),
                'Seguimientos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener seguimientos', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $seguimiento = Seguimiento::with('documento')->find($id);

            if (!$seguimiento) {
                return $this->respondNotFound('Seguimiento no encontrado');
            }

            return $this->respondSuccess(
                new SeguimientoResource($seguimiento),
                'Seguimiento obtenido correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener seguimiento', $e);
        }
    }

    /**
     * GET /api/v1/seguimientos/documento/{idDocumento}
     */
    public function porDocumento(int $idDocumento): JsonResponse
    {
        try {
            $seguimientos = Seguimiento::where('idDocumento', $idDocumento)
                ->with('documento')
                ->orderBy('fecha', 'desc')
                ->paginate(10);

            return $this->respondPaginated(
                $seguimientos->through(fn($item) => new SeguimientoResource($item)),
                'Seguimientos del documento obtenidos'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener seguimientos', $e);
        }
    }
}
