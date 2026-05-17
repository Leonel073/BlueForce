<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use App\Helpers\AuditoriaHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditoriaController extends Controller
{
    /**
     * Mostrar listado de auditorías
     */
    public function index(Request $request)
    {
        // Obtener filtros
        $idUsuario = $request->get('idUsuario');
        $accion = $request->get('accion', 'todas');
        $modelo = $request->get('modelo', 'todos');
        $fechaInicio = $request->get('fechaInicio');
        $fechaFin = $request->get('fechaFin');
        $busqueda = $request->get('busqueda');

        // Construir query
        $query = Auditoria::query();

        if ($idUsuario) {
            $query->where('idUsuario', $idUsuario);
        }

        if ($accion && $accion !== 'todas') {
            $query->where('accion', $accion);
        }

        if ($modelo && $modelo !== 'todos') {
            $query->where('modelo', $modelo);
        }

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay(),
            ]);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('ruta', 'like', "%{$busqueda}%")
                  ->orWhere('navegador', 'like', "%{$busqueda}%")
                  ->orWhere('ip', 'like', "%{$busqueda}%");
            });
        }

        // Eager loading y paginación
        $auditorias = $query->with('usuario')
            ->orderByDesc('fecha')
            ->paginate(20)
            ->appends($request->query());

        // Obtener datos para los filtros
        $usuarios = User::orderBy('name')->pluck('name', 'id');
        $modelos = AuditoriaHelper::obtenerModelosAuditados();
        $acciones = ['CREATE', 'UPDATE', 'DELETE'];
        $resumen = AuditoriaHelper::obtenerResumen();

        return view('auditoria.index', compact(
            'auditorias',
            'usuarios',
            'modelos',
            'acciones',
            'resumen',
            'idUsuario',
            'accion',
            'modelo',
            'fechaInicio',
            'fechaFin',
            'busqueda'
        ));
    }

    /**
     * Mostrar detalles de una auditoría
     */
    public function show($idAuditoria)
    {
        $auditoria = Auditoria::with('usuario')
            ->findOrFail($idAuditoria);

        $cambios = AuditoriaHelper::extraerCambios($auditoria);
        $auditoriasDeMismo = AuditoriaHelper::obtenerAuditoriasDe(
            $auditoria->modelo,
            $auditoria->idRegistro
        );

        return view('auditoria.show', compact(
            'auditoria',
            'cambios',
            'auditoriasDeMismo'
        ));
    }

    /**
     * API: Obtener auditorías en JSON (útil para dashboards)
     */
    public function api(Request $request)
    {
        $filtros = [
            'idUsuario' => $request->get('idUsuario'),
            'accion' => $request->get('accion'),
            'modelo' => $request->get('modelo'),
        ];

        $auditorias = AuditoriaHelper::exportarAuditorias($filtros);

        return response()->json($auditorias);
    }

    /**
     * Vista de estadísticas
     */
    public function estadisticas(Request $request)
    {
        $dias = $request->get('dias', 30);

        $resumen = AuditoriaHelper::obtenerResumen($dias);
        $actividadPorUsuario = AuditoriaHelper::obtenerActividadPorUsuario($dias);
        $actividadPorModelo = AuditoriaHelper::obtenerActividadPorModelo($dias);
        $estadisticasPorAccion = AuditoriaHelper::obtenerEstadisticasPorAccion($dias);

        return view('auditoria.estadisticas', compact(
            'resumen',
            'actividadPorUsuario',
            'actividadPorModelo',
            'estadisticasPorAccion',
            'dias'
        ));
    }

    /**
     * Auditoría de un registro específico
     */
    public function registroHistorial($modelo, $idRegistro)
    {
        $auditorias = AuditoriaHelper::obtenerAuditoriasDe($modelo, $idRegistro);

        if ($auditorias->isEmpty()) {
            return response()->json(['error' => 'No hay historial de auditoría'], 404);
        }

        return view('auditoria.historial', compact('modelo', 'idRegistro', 'auditorias'));
    }

    /**
     * Exportar auditorías a CSV
     */

public function exportar(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    $query = Auditoria::with('usuario');

    if ($request->filled('idUsuario')) {

        $query->where(
            'idUsuario',
            $request->idUsuario
        );
    }

    if (
        $request->filled('accion') &&
        $request->accion !== 'todas'
    ) {

        $query->where(
            'accion',
            $request->accion
        );
    }

    if (
        $request->filled('modelo') &&
        $request->modelo !== 'todos'
    ) {

        $query->where(
            'modelo',
            $request->modelo
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR FECHAS
    |--------------------------------------------------------------------------
    */

    if (
        $request->filled('fechaInicio') &&
        $request->filled('fechaFin')
    ) {

        $query->whereBetween('fecha', [

            \Carbon\Carbon::parse(
                $request->fechaInicio
            )->startOfDay(),

            \Carbon\Carbon::parse(
                $request->fechaFin
            )->endOfDay(),

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER DATOS
    |--------------------------------------------------------------------------
    */

    $auditorias = $query
        ->orderByDesc('fecha')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | NOMBRE ARCHIVO
    |--------------------------------------------------------------------------
    */

    $filename =
        'auditoria_' .
        now()->format('Y_m_d_H_i_s') .
        '.csv';

    /*
    |--------------------------------------------------------------------------
    | HEADERS CSV
    |--------------------------------------------------------------------------
    */

    $headers = [

        'Content-Type' =>
            'text/csv; charset=UTF-8',

        'Content-Disposition' =>
            "attachment; filename={$filename}",

    ];

    /*
    |--------------------------------------------------------------------------
    | CALLBACK STREAM
    |--------------------------------------------------------------------------
    */

    $callback = function () use ($auditorias) {

        $handle = fopen('php://output', 'w');

        /*
        |--------------------------------------------------------------------------
        | UTF-8 BOM
        |--------------------------------------------------------------------------
        */

        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        /*
        |--------------------------------------------------------------------------
        | ENCABEZADOS
        |--------------------------------------------------------------------------
        */

        fputcsv($handle, [

            'ID',
            'Usuario',
            'Modelo',
            'Registro',
            'Acción',
            'Fecha',
            'IP',
            'Ruta',
            'Cambios'

        ]);

        /*
        |--------------------------------------------------------------------------
        | FILAS
        |--------------------------------------------------------------------------
        */

        foreach ($auditorias as $auditoria) {

            /*
            |--------------------------------------------------------------------------
            | FORMATEAR CAMBIOS
            |--------------------------------------------------------------------------
            */

            $cambios = '';

            if ($auditoria->accion === 'CREATE') {

                $cambios = 'Registro creado';

            } elseif ($auditoria->accion === 'DELETE') {

                $cambios = 'Registro eliminado';

            } else {

                $anteriores =
                    $auditoria->datosAnteriores ?? [];

                $nuevos =
                    $auditoria->datosNuevos ?? [];

                $listaCambios = [];

                foreach ($nuevos as $campo => $valorNuevo) {

                    $valorAnterior =
                        $anteriores[$campo] ?? null;

                    if ($valorAnterior != $valorNuevo) {

                        $listaCambios[] =
                            "{$campo}: '{$valorAnterior}' => '{$valorNuevo}'";
                    }
                }

                $cambios = implode(
                    ' | ',
                    $listaCambios
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ESCRIBIR FILA
            |--------------------------------------------------------------------------
            */

            fputcsv($handle, [

                $auditoria->idAuditoria,

                $auditoria->usuario?->name
                    ?? 'Sistema',

                $auditoria->modelo,

                $auditoria->idRegistro,

                $auditoria->accion,

                $auditoria->fecha
                    ? $auditoria->fecha->format(
                        'd/m/Y H:i:s'
                    )
                    : '',

                $auditoria->ip,

                $auditoria->ruta,

                $cambios,

            ]);
        }

        fclose($handle);
    };

    /*
    |--------------------------------------------------------------------------
    | DESCARGA
    |--------------------------------------------------------------------------
    */

    return response()->stream(
        $callback,
        200,
        $headers
    );
}


}
