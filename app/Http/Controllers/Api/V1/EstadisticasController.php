<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * EstadisticasController
 * Controlador para estadísticas y dashboard
 */
class EstadisticasController extends BaseController
{
    /**
     * GET /api/v1/estadisticas/dashboard
     * Dashboard con estadísticas principales
     */
    public function dashboard(): JsonResponse
    {
        try {
            $hoy = now()->format('Y-m-d');
            $mesActual = now()->startOfMonth()->format('Y-m-d');

            $data = [
                'documentos' => [
                    'total' => Correspondencia::where('activo', 1)->count(),
                    'hoy' => Correspondencia::where('activo', 1)->whereDate('fecha', $hoy)->count(),
                    'mes' => Correspondencia::where('activo', 1)->whereDate('fecha', '>=', $mesActual)->count(),
                ],
                'derivaciones' => [
                    'total' => Derivacion::where('activo', 1)->count(),
                    'pendientes' => Derivacion::whereNull('fechaRecepcion')->where('activo', 1)->count(),
                    'procesadas' => Derivacion::whereNotNull('fechaRecepcion')->where('activo', 1)->count(),
                ],
                'usuarios' => [
                    'total' => User::where('activo', 1)->count(),
                    'admin' => User::where('activo', 1)->where('idRol', 1)->count(),
                ],
                'estados' => $this->obtenerEstados(),
                'urgencias' => $this->obtenerUrgencias(),
            ];

            return $this->respondSuccess($data, 'Dashboard obtenido correctamente');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener dashboard', $e);
        }
    }

    /**
     * GET /api/v1/estadisticas/documentos-mes
     * Documentos creados por día del mes actual
     */
    public function documentosPorMes(): JsonResponse
    {
        try {
            $mesActual = now()->startOfMonth()->format('Y-m-d');
            $mesProximo = now()->startOfMonth()->addMonth()->format('Y-m-d');

            $data = Correspondencia::selectRaw('DAY(fecha) as dia, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->whereBetween('fecha', [$mesActual, $mesProximo])
                ->groupBy('dia')
                ->orderBy('dia', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'dia' => $item->dia,
                        'cantidad' => $item->cantidad,
                    ];
                });

            return $this->respondSuccess($data, 'Documentos por día del mes');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener estadísticas', $e);
        }
    }

    /**
     * GET /api/v1/estadisticas/documentos-dia
     * Documentos por hora del día actual
     */
    public function documentosPorDia(): JsonResponse
    {
        try {
            $hoy = now()->format('Y-m-d');

            $data = Correspondencia::selectRaw('HOUR(fecha) as hora, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->whereDate('fecha', $hoy)
                ->groupBy('hora')
                ->orderBy('hora', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'hora' => str_pad($item->hora, 2, '0', STR_PAD_LEFT) . ':00',
                        'cantidad' => $item->cantidad,
                    ];
                });

            return $this->respondSuccess($data, 'Documentos por hora del día');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener estadísticas', $e);
        }
    }

    /**
     * GET /api/v1/estadisticas/derivaciones-tiempo-promedio
     * Tiempo promedio de procesamiento de derivaciones
     */
    public function derivacionesTiempoPromedio(): JsonResponse
    {
        try {
            $data = DB::table('DERIVACION')
                ->selectRaw('
                    AVG(DATEDIFF(fechaRecepcion, fechaEnvio)) as dias_promedio,
                    MIN(DATEDIFF(fechaRecepcion, fechaEnvio)) as dias_minimo,
                    MAX(DATEDIFF(fechaRecepcion, fechaEnvio)) as dias_maximo
                ')
                ->whereNotNull('fechaRecepcion')
                ->where('activo', 1)
                ->first();

            return $this->respondSuccess([
                'dias_promedio' => round($data->dias_promedio ?? 0, 2),
                'dias_minimo' => $data->dias_minimo ?? 0,
                'dias_maximo' => $data->dias_maximo ?? 0,
            ], 'Tiempo promedio de derivaciones');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener estadísticas', $e);
        }
    }

    /**
     * GET /api/v1/estadisticas/usuarios-actividad
     * Actividad de usuarios (documentos creados)
     */
    public function usuariosActividad(): JsonResponse
    {
        try {
            $data = DB::table('CORRESPONDENCIA')
                ->selectRaw('idUsuario, COUNT(*) as total_documentos, MAX(fecha) as ultimo_documento')
                ->where('activo', 1)
                ->groupBy('idUsuario')
                ->orderByRaw('total_documentos DESC')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    $usuario = User::find($item->idUsuario);
                    return [
                        'usuario_id' => $item->idUsuario,
                        'usuario_nombre' => $usuario?->name,
                        'total_documentos' => $item->total_documentos,
                        'ultimo_documento' => $item->ultimo_documento,
                    ];
                });

            return $this->respondSuccess($data, 'Actividad de usuarios');

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener estadísticas', $e);
        }
    }

    /**
     * Obtener estados con cantidades
     */
    private function obtenerEstados(): array
    {
        try {
            return DB::table('CORRESPONDENCIA')
                ->selectRaw('idEstado, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->groupBy('idEstado')
                ->get()
                ->map(function ($item) {
                    $estado = DB::table('ESTADO_DOCUMENTO')->find($item->idEstado);
                    return [
                        'id' => $item->idEstado,
                        'nombre' => $estado?->nombre ?? 'Desconocido',
                        'cantidad' => $item->cantidad,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtener urgencias con cantidades
     */
    private function obtenerUrgencias(): array
    {
        try {
            return DB::table('CORRESPONDENCIA')
                ->selectRaw('idUrgencia, COUNT(*) as cantidad')
                ->where('activo', 1)
                ->groupBy('idUrgencia')
                ->get()
                ->map(function ($item) {
                    $urgencia = DB::table('NIVEL_URGENCIA')->find($item->idUrgencia);
                    return [
                        'id' => $item->idUrgencia,
                        'nombre' => $urgencia?->nombre ?? 'Desconocido',
                        'cantidad' => $item->cantidad,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}
