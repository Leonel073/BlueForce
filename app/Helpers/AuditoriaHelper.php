<?php

namespace App\Helpers;

use App\Models\Auditoria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AuditoriaHelper
{
    /**
     * Obtener auditorías con filtros
     */
    public static function obtenerAuditorias(
        $idUsuario = null,
        $accion = null,
        $modelo = null,
        $fechaInicio = null,
        $fechaFin = null,
        $perPage = 15
    ) {
        $query = Auditoria::query();

        // Filtrar por usuario
        if ($idUsuario) {
            $query->where('idUsuario', $idUsuario);
        }

        // Filtrar por acción
        if ($accion && $accion !== 'todas') {
            $query->where('accion', $accion);
        }

        // Filtrar por modelo
        if ($modelo && $modelo !== 'todos') {
            $query->where('modelo', $modelo);
        }

        // Filtrar por rango de fechas
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay(),
            ]);
        }

        return $query->with('usuario')
            ->orderByDesc('fecha')
            ->paginate($perPage);
    }

    /**
     * Obtener resumen de actividad
     */
    public static function obtenerResumen($dias = 7)
    {
        $fechaInicio = Carbon::now()->subDays($dias)->startOfDay();

        $resumen = [
            'total' => Auditoria::where('fecha', '>=', $fechaInicio)->count(),
            'creaciones' => Auditoria::where('fecha', '>=', $fechaInicio)->where('accion', 'CREATE')->count(),
            'actualizaciones' => Auditoria::where('fecha', '>=', $fechaInicio)->where('accion', 'UPDATE')->count(),
            'eliminaciones' => Auditoria::where('fecha', '>=', $fechaInicio)->where('accion', 'DELETE')->count(),
            'usuarios_activos' => Auditoria::where('fecha', '>=', $fechaInicio)
                ->distinct('idUsuario')
                ->count('idUsuario'),
        ];

        return $resumen;
    }

    /**
     * Obtener actividad por usuario
     */
    public static function obtenerActividadPorUsuario($dias = 7)
    {
        $fechaInicio = Carbon::now()->subDays($dias)->startOfDay();

        return Auditoria::where('fecha', '>=', $fechaInicio)
            ->selectRaw('idUsuario, COUNT(*) as total')
            ->groupBy('idUsuario')
            ->with('usuario')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Obtener actividad por modelo
     */
    public static function obtenerActividadPorModelo($dias = 7)
    {
        $fechaInicio = Carbon::now()->subDays($dias)->startOfDay();

        return Auditoria::where('fecha', '>=', $fechaInicio)
            ->selectRaw('modelo, accion, COUNT(*) as total')
            ->groupBy('modelo', 'accion')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Obtener auditorías de un registro específico
     */
    public static function obtenerAuditoriasDe($modelo, $idRegistro)
    {
        return Auditoria::where('modelo', $modelo)
            ->where('idRegistro', $idRegistro)
            ->with('usuario')
            ->orderByDesc('fecha')
            ->get();
    }

    /**
     * Obtener cambios entre auditorías
     */
    public static function extraerCambios($auditoria)
{
    // Convertir JSON a array
    $antes = is_array($auditoria->datosAnteriores)
        ? $auditoria->datosAnteriores
        : json_decode($auditoria->datosAnteriores ?? '[]', true);

    $despues = is_array($auditoria->datosNuevos)
        ? $auditoria->datosNuevos
        : json_decode($auditoria->datosNuevos ?? '[]', true);

    if ($auditoria->accion === 'CREATE') {

        return [
            'tipo' => 'Creación',
            'cambios' => $despues,
        ];
    }

    if ($auditoria->accion === 'DELETE') {

        return [
            'tipo' => 'Eliminación',
            'cambios' => $antes,
        ];
    }

    if ($auditoria->accion === 'UPDATE') {

        $cambios = [];

        foreach ($despues as $campo => $valorNuevo) {

            $valorAntiguo = $antes[$campo] ?? null;

            if ($valorAntiguo != $valorNuevo) {

                $cambios[$campo] = [
                    'anterior' => $valorAntiguo,
                    'nuevo' => $valorNuevo,
                ];
            }
        }

        return [
            'tipo' => 'Actualización',
            'cambios' => $cambios,
        ];
    }

    return [
        'tipo' => 'Desconocido',
        'cambios' => [],
    ];
}

    /**
     * Obtener estadísticas por acción
     */
    public static function obtenerEstadisticasPorAccion($dias = 30)
    {
        $fechaInicio = Carbon::now()->subDays($dias)->startOfDay();

        return Auditoria::where('fecha', '>=', $fechaInicio)
            ->selectRaw('accion, COUNT(*) as total')
            ->groupBy('accion')
            ->get()
            ->pluck('total', 'accion')
            ->toArray();
    }

    /**
     * Limpiar auditorías antiguas
     */
    public static function limpiarAuditoriasAntiguas($diasRetener = 90)
    {
        $fechaLimite = Carbon::now()->subDays($diasRetener);

        return Auditoria::where('fecha', '<', $fechaLimite)->delete();
    }

    /**
     * Obtener modelos auditados
     */
    public static function obtenerModelosAuditados()
    {
        return Auditoria::distinct()
            ->pluck('modelo')
            ->sort()
            ->toArray();
    }

    /**
     * Validar si un modelo debe ser auditado
     */
    public static function debeSerAuditado($modelo)
    {
        $modelosAuditados = [
            'Correspondencia',
            'Derivacion',
            'User',
            'Departamento',
            'Persona',
            'EstadoDocumento',
            'NivelUrgencia',
            'TipoDocumento',
            'Seguimiento',
        ];

        return in_array($modelo, $modelosAuditados);
    }

    /**
     * Exportar auditorías a array
     */
    public static function exportarAuditorias($filtros = [])
    {
        $query = Auditoria::query();

        if (isset($filtros['idUsuario'])) {
            $query->where('idUsuario', $filtros['idUsuario']);
        }

        if (isset($filtros['accion'])) {
            $query->where('accion', $filtros['accion']);
        }

        if (isset($filtros['modelo'])) {
            $query->where('modelo', $filtros['modelo']);
        }

        return $query->with('usuario')
            ->orderByDesc('fecha')
            ->get()
            ->map(function ($auditoria) {
                return [
                    'id' => $auditoria->idAuditoria,
                    'usuario' => $auditoria->usuario?->name ?? 'Sin usuario',
                    'modelo' => $auditoria->modelo,
                    'accion' => $auditoria->accion,
                    'fecha' => $auditoria->fecha->format('Y-m-d H:i:s'),
                    'ip' => $auditoria->ip,
                    'cambios' => $auditoria->cambios_resumo,
                ];
            });
    }
}
