<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\AuditoriaResource;
use App\Models\Auditoria;
use Illuminate\Http\JsonResponse;

/**
 * AuditoriaController
 * Controlador para consulta de auditorías
 */
class AuditoriaController extends BaseController
{
    protected array $searchableColumns = ['tabla', 'operacion'];
    protected array $filterableColumns = ['idUsuario', 'tabla', 'operacion'];
    protected array $sortableColumns = ['idAuditoria', 'fecha'];

    public function index(): JsonResponse
    {
        try {
            $query = Auditoria::query();
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new AuditoriaResource($item)),
                'Auditorías obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener auditorías', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $auditoria = Auditoria::with('usuario')->find($id);

            if (!$auditoria) {
                return $this->respondNotFound('Auditoría no encontrada');
            }

            return $this->respondSuccess(
                new AuditoriaResource($auditoria),
                'Auditoría obtenida correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener auditoría', $e);
        }
    }

    /**
     * GET /api/v1/auditorias/usuario/{userId}
     */
    public function porUsuario(int $userId): JsonResponse
    {
        try {
            $auditorias = Auditoria::where('idUsuario', $userId)
                ->with('usuario')
                ->orderBy('fecha', 'desc')
                ->paginate(10);

            return $this->respondPaginated(
                $auditorias->through(fn($item) => new AuditoriaResource($item)),
                'Auditorías del usuario obtenidas'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener auditorías', $e);
        }
    }

    /**
     * GET /api/v1/auditorias/documento/{docId}
     */
    public function porDocumento(int $docId): JsonResponse
    {
        try {
            $auditorias = Auditoria::where('tabla', 'CORRESPONDENCIA')
                ->where('registro_id', $docId)
                ->with('usuario')
                ->orderBy('fecha', 'desc')
                ->paginate(10);

            return $this->respondPaginated(
                $auditorias->through(fn($item) => new AuditoriaResource($item)),
                'Auditorías del documento obtenidas'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener auditorías', $e);
        }
    }

    /**
     * GET /api/v1/auditorias/tabla/{tabla}
     */
    public function porTabla(string $tabla): JsonResponse
    {
        try {
            $auditorias = Auditoria::where('tabla', strtoupper($tabla))
                ->with('usuario')
                ->orderBy('fecha', 'desc')
                ->paginate(10);

            return $this->respondPaginated(
                $auditorias->through(fn($item) => new AuditoriaResource($item)),
                'Auditorías obtenidas'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener auditorías', $e);
        }
    }

    /**
     * GET /api/v1/admin/auditorias-full
     * Solo para admin - retorna todas las auditorías
     */
    public function indexFull(): JsonResponse
    {
        try {
            $auditorias = Auditoria::with('usuario')
                ->orderBy('fecha', 'desc')
                ->paginate(50);

            return $this->respondPaginated(
                $auditorias->through(fn($item) => new AuditoriaResource($item)),
                'Todas las auditorías obtenidas'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener auditorías', $e);
        }
    }
}
