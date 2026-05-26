<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\StoreCorrespondenciaRequest;
use App\Http\Requests\Api\UpdateCorrespondenciaRequest;
use App\Http\Resources\CorrespondenciaResource;
use App\Http\Resources\DerivacionResource;
use App\Http\Resources\SeguimientoResource;
use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * CorrespondenciaController
 * 
 * Controlador CRUD completo para gestión de documentos/correspondencia
 * Endpoints:
 * - GET    /api/v1/documentos              → Lista paginada
 * - POST   /api/v1/documentos              → Crear
 * - GET    /api/v1/documentos/{id}         → Detalle
 * - PUT    /api/v1/documentos/{id}         → Actualizar
 * - DELETE /api/v1/documentos/{id}         → Eliminar
 * - GET    /api/v1/documentos/{id}/derivaciones
 * - GET    /api/v1/documentos/{id}/seguimiento
 * - POST   /api/v1/documentos/{id}/cambiar-estado
 */
class CorrespondenciaController extends BaseController
{
    protected array $searchableColumns = ['cite', 'asunto'];
    protected array $filterableColumns = ['idTipoDocumento', 'idEstado', 'idUrgencia', 'activo'];
    protected array $sortableColumns = ['idDocumento', 'cite', 'fecha', 'idEstado'];

    /**
     * GET /api/v1/documentos
     * 
     * Lista todos los documentos con paginación, búsqueda y filtrado
     * 
     * Query parameters:
     * - page=1              Número de página
     * - per_page=10         Documentos por página (máx 100)
     * - search=...          Búsqueda en cite y asunto
     * - sort=fecha          Ordenar por campo
     * - sort=-fecha         Ordenar descendente
     * - idEstado=1          Filtrar por estado
     * - idUrgencia=2        Filtrar por urgencia
     * - activo=1            Solo activos (default)
     * - with=tipoDocumento,estado    Incluir relaciones
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Construir query base
            $query = Correspondencia::query();

            // Si no es admin, solo ver documentos donde es usuario creador
            // Descomenta según tu lógica de negocio
            // if ($user->idRol !== 1) {
            //     $query->where('idUsuario', $user->id);
            // }

            // Aplicar query builder con filtros
            $paginated = $this->buildFullQuery($query);

            return $this->respondPaginated(
                $paginated->through(fn($item) => new CorrespondenciaResource($item)),
                'Documentos obtenidos correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener documentos', $e);
        }
    }

    /**
     * POST /api/v1/documentos
     * 
     * Crea un nuevo documento
     * 
     * @param StoreCorrespondenciaRequest $request
     * @return JsonResponse
     */
    public function store(StoreCorrespondenciaRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            // Crear documento
            $documento = Correspondencia::create([
                ...$data,
                'idUsuario' => $user->id, // Usuario que crea es el usuario autenticado
                'activo' => 1,
            ]);

            // Cargar relaciones
            $documento->load('tipoDocumento', 'estado', 'urgencia', 'remitente', 'usuario');

            // Registrar auditoría
            $this->registrarAuditoria(
                $user->id,
                'CORRESPONDENCIA',
                'CREATE',
                $documento->idDocumento,
                null,
                $data
            );

            return $this->respondCreated(
                new CorrespondenciaResource($documento),
                'Documento creado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al crear documento', $e);
        }
    }

    /**
     * GET /api/v1/documentos/{id}
     * 
     * Obtiene los detalles de un documento específico
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $documento = Correspondencia::with([
                'tipoDocumento',
                'estado',
                'urgencia',
                'remitente',
                'usuario',
            ])->find($id);

            if (!$documento) {
                return $this->respondNotFound('Documento no encontrado');
            }

            return $this->respondSuccess(
                new CorrespondenciaResource($documento),
                'Documento obtenido correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener documento', $e);
        }
    }

    /**
     * PUT /api/v1/documentos/{id}
     * 
     * Actualiza un documento existente
     * 
     * @param int $id
     * @param UpdateCorrespondenciaRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateCorrespondenciaRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $documento = Correspondencia::find($id);

            if (!$documento) {
                return $this->respondNotFound('Documento no encontrado');
            }

            // Guardar datos anteriores para auditoría
            $datosAnteriores = $documento->toArray();

            // Actualizar
            $data = $request->validated();
            $documento->update($data);

            // Cargar relaciones
            $documento->load('tipoDocumento', 'estado', 'urgencia', 'remitente', 'usuario');

            // Registrar auditoría
            $this->registrarAuditoria(
                $user->id,
                'CORRESPONDENCIA',
                'UPDATE',
                $documento->idDocumento,
                $datosAnteriores,
                $documento->toArray()
            );

            return $this->respondSuccess(
                new CorrespondenciaResource($documento),
                'Documento actualizado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al actualizar documento', $e);
        }
    }

    /**
     * DELETE /api/v1/documentos/{id}
     * 
     * Elimina un documento (soft delete)
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $documento = Correspondencia::find($id);

            if (!$documento) {
                return $this->respondNotFound('Documento no encontrado');
            }

            $datosAnteriores = $documento->toArray();

            // Soft delete: marcar como inactivo
            $documento->update(['activo' => 0]);

            // Registrar auditoría
            $this->registrarAuditoria(
                $user->id,
                'CORRESPONDENCIA',
                'DELETE',
                $documento->idDocumento,
                $datosAnteriores,
                null
            );

            return $this->respondNoContent();

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al eliminar documento', $e);
        }
    }

    /**
     * GET /api/v1/documentos/{id}/derivaciones
     * 
     * Obtiene todas las derivaciones de un documento
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function derivaciones(int $id): JsonResponse
    {
        try {
            $documento = Correspondencia::find($id);

            if (!$documento) {
                return $this->respondNotFound('Documento no encontrado');
            }

            $derivaciones = $documento->derivaciones()
                ->with([
                    'departamentoOrigen',
                    'departamentoDestino',
                    'usuarioAsignado',
                    'usuarioEnvio',
                ])
                ->orderBy('orden', 'asc')
                ->get();

            return $this->respondSuccess(
                DerivacionResource::collection($derivaciones),
                'Derivaciones obtenidas correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener derivaciones', $e);
        }
    }

    /**
     * GET /api/v1/documentos/{id}/seguimiento
     * 
     * Obtiene el historial de seguimiento de un documento
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function seguimiento(int $id): JsonResponse
    {
        try {
            $documento = Correspondencia::find($id);

            if (!$documento) {
                return $this->respondNotFound('Documento no encontrado');
            }

            $seguimientos = $documento->seguimientos()
                ->with('documento')
                ->orderBy('fecha', 'desc')
                ->get();

            return $this->respondSuccess(
                SeguimientoResource::collection($seguimientos),
                'Seguimientos obtenidos correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener seguimiento', $e);
        }
    }

    /**
     * POST /api/v1/documentos/{id}/cambiar-estado
     * 
     * Cambia el estado de un documento
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function cambiarEstado(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $documento = Correspondencia::find($id);

            if (!$documento) {
                return $this->respondNotFound('Documento no encontrado');
            }

            // Validar nuevo estado
            $data = request()->validate([
                'idEstado' => 'required|exists:ESTADO_DOCUMENTO,idEstado',
            ]);

            $estadoAnterior = $documento->idEstado;

            // Actualizar estado
            $documento->update(['idEstado' => $data['idEstado']]);

            // Registrar auditoría
            $this->registrarAuditoria(
                $user->id,
                'CORRESPONDENCIA',
                'UPDATE',
                $documento->idDocumento,
                ['idEstado' => $estadoAnterior],
                ['idEstado' => $data['idEstado']]
            );

            return $this->respondSuccess(
                new CorrespondenciaResource($documento->load('tipoDocumento', 'estado', 'urgencia', 'remitente', 'usuario')),
                'Estado actualizado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al cambiar estado', $e);
        }
    }

    /**
     * Registra una operación en auditoría
     */
    private function registrarAuditoria(
        int $idUsuario,
        string $tabla,
        string $operacion,
        int $registroId,
        ?array $datosAnteriores,
        ?array $datosNuevos
    ): void {
        try {
            // Descomenta cuando Auditoria esté configurado
            // \App\Models\Auditoria::create([
            //     'idUsuario' => $idUsuario,
            //     'tabla' => $tabla,
            //     'operacion' => $operacion,
            //     'registro_id' => $registroId,
            //     'datos_anteriores' => json_encode($datosAnteriores),
            //     'datos_nuevos' => json_encode($datosNuevos),
            //     'fecha' => now(),
            //     'ip' => request()->ip(),
            // ]);
        } catch (\Exception $e) {
            \Log::warning('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
