<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Correspondencia;
use App\Models\Derivacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * ReporteController
 * Controlador para generar reportes
 */
class ReporteController extends BaseController
{
    /**
     * GET /api/v1/reportes/documentos-por-estado
     * Reporte de documentos agrupados por estado
     */
    public function documentosPorEstado(): JsonResponse
    {
        try {
            $data = Correspondencia::selectRaw('idEstado, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->groupBy('idEstado')
                ->with('estado')
                ->get()
                ->map(function ($item) {
                    return [
                        'estado_id' => $item->idEstado,
                        'estado_nombre' => $item->estado?->nombre,
                        'cantidad' => $item->cantidad,
                        'porcentaje' => round(($item->cantidad / Correspondencia::where('activo', 1)->count()) * 100, 2),
                    ];
                });

            return $this->respondSuccess($data, 'Reporte de documentos por estado');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar reporte', $e);
        }
    }

    /**
     * GET /api/v1/reportes/documentos-por-urgencia
     */
    public function documentosPorUrgencia(): JsonResponse
    {
        try {
            $data = Correspondencia::selectRaw('idUrgencia, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->groupBy('idUrgencia')
                ->with('urgencia')
                ->get()
                ->map(function ($item) {
                    return [
                        'urgencia_id' => $item->idUrgencia,
                        'urgencia_nombre' => $item->urgencia?->nombre,
                        'cantidad' => $item->cantidad,
                        'porcentaje' => round(($item->cantidad / Correspondencia::where('activo', 1)->count()) * 100, 2),
                    ];
                });

            return $this->respondSuccess($data, 'Reporte de documentos por urgencia');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar reporte', $e);
        }
    }

    /**
     * GET /api/v1/reportes/documentos-por-tipo
     */
    public function documentosPorTipo(): JsonResponse
    {
        try {
            $data = Correspondencia::selectRaw('idTipoDocumento, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->groupBy('idTipoDocumento')
                ->with('tipoDocumento')
                ->get()
                ->map(function ($item) {
                    return [
                        'tipo_id' => $item->idTipoDocumento,
                        'tipo_nombre' => $item->tipoDocumento?->nombre,
                        'cantidad' => $item->cantidad,
                        'porcentaje' => round(($item->cantidad / Correspondencia::where('activo', 1)->count()) * 100, 2),
                    ];
                });

            return $this->respondSuccess($data, 'Reporte de documentos por tipo');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar reporte', $e);
        }
    }

    /**
     * GET /api/v1/reportes/derivaciones-pendientes
     * Derivaciones que aún no han sido recibidas
     */
    public function derivacionesPendientes(): JsonResponse
    {
        try {
            $data = Derivacion::whereNull('fechaRecepcion')
                ->where('activo', 1)
                ->with('documento', 'departamentoOrigen', 'departamentoDestino', 'usuarioAsignado')
                ->orderBy('fechaEnvio', 'asc')
                ->limit(50)
                ->get();

            return $this->respondSuccess($data, 'Derivaciones pendientes');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar reporte', $e);
        }
    }

    /**
     * GET /api/v1/reportes/documentos-por-periodo
     * Documentos creados en un período
     * Query: ?fecha_desde=2024-01-01&fecha_hasta=2024-12-31
     */
    public function documentosPorPeriodo(): JsonResponse
    {
        try {
            $desde = request()->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
            $hasta = request()->input('fecha_hasta', now()->format('Y-m-d'));

            $data = Correspondencia::selectRaw('DATE(fecha) as fecha, COUNT(*) as cantidad')
                ->whereBetween('fecha', [$desde, $hasta])
                ->where('activo', 1)
                ->groupBy('fecha')
                ->orderBy('fecha', 'asc')
                ->get();

            return $this->respondSuccess([
                'periodo' => [
                    'desde' => $desde,
                    'hasta' => $hasta,
                ],
                'datos' => $data,
            ], 'Reporte de documentos por período');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar reporte', $e);
        }
    }

    /**
     * GET /api/v1/reportes/performance-usuario
     * Performance de usuarios (documentos procesados, etc)
     */
    public function performanceUsuario(): JsonResponse
    {
        try {
            $data = DB::table('CORRESPONDENCIA')
                ->selectRaw('idUsuario, COUNT(*) as documentos_creados, MAX(fecha) as ultimo_documento')
                ->where('activo', 1)
                ->groupBy('idUsuario')
                ->orderByRaw('documentos_creados DESC')
                ->with('usuario')
                ->limit(20)
                ->get();

            return $this->respondSuccess($data, 'Performance de usuarios');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar reporte', $e);
        }
    }

    /**
     * GET /api/v1/reportes/exportar-pdf
     * Exportar reporte a PDF
     */
    public function exportarPdf(): JsonResponse
    {
        try {
            $tipoReporte = request()->input('tipo', 'documentos-por-estado');

            // Aquí irían llamadas a métodos de PDF
            // Usualmente con biblioteca como DomPDF

            return $this->respondSuccess([
                'mensaje' => 'Para descargar el PDF, usa el endpoint específico con &export=pdf',
                'tipo_reporte' => $tipoReporte,
            ], 'Generando PDF');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al generar PDF', $e);
        }
    }
}
