<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Anuncio;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TIEMPO DE CACHÉ
    |--------------------------------------------------------------------------
    */

    private const CACHE_TTL = 60;

    /*
    |--------------------------------------------------------------------------
    | IDS DE ESTADOS
    |--------------------------------------------------------------------------
    | AJUSTA ESTOS IDS SEGÚN TU BASE DE DATOS
    |--------------------------------------------------------------------------
    */
    private const ESTADO_PENDIENTE   = 1;
    private const ESTADO_ATENDIDO = 2;
    private const ESTADO_ARCHIVADO  = 3;

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | CONTADORES GENERALES
        |--------------------------------------------------------------------------
        */

        [
            $totalDocumentos,
            $totalUsuarios,
            $totalDerivaciones,
            $totalDepartamentos,

        ] = Cache::remember(

            'dashboard.contadores',

            self::CACHE_TTL,

            function () {

                return [

                    Correspondencia::count(),

                    User::count(),

                    Derivacion::count(),

                    Departamento::count(),

                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS POR ESTADO
        |--------------------------------------------------------------------------
        */

        $estadisticasEstados = Cache::remember(

            'dashboard.estados',

            self::CACHE_TTL,

            function () {

                return Correspondencia::selectRaw('
                    
                    SUM(
                        CASE
                            WHEN idEstado = ?
                            THEN 1
                            ELSE 0
                        END
                    ) AS finalizados,

                    SUM(
                        CASE
                            WHEN idEstado = ?
                            THEN 1
                            ELSE 0
                        END
                    ) AS archivados,

                    SUM(
                        CASE
                            WHEN idEstado NOT IN (?, ?)
                            THEN 1
                            ELSE 0
                        END
                    ) AS pendientes

                ', [

                    self::ESTADO_PENDIENTE,

                    self::ESTADO_ARCHIVADO,

                    self::ESTADO_ATENDIDO,
                    self::ESTADO_ARCHIVADO,

                ])->first();
            }
        );

        $documentosFinalizados =
            $estadisticasEstados->finalizados ?? 0;

        $documentosArchivados =
            $estadisticasEstados->archivados ?? 0;

        $documentosPendientes =
            $estadisticasEstados->pendientes ?? 0;

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS URGENTES
        |--------------------------------------------------------------------------
        */

        $documentosUrgentes = Cache::remember(

            'dashboard.urgentes',

            self::CACHE_TTL,

            function () {

                return Correspondencia::whereHas(

                    'urgencia',

                    function ($q) {

                        $q->where(
                            'nombre',
                            'LIKE',
                            '%alta%'
                        );
                    }

                )->count();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTOS MÁS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $departamentosActivos = Cache::remember(

            'dashboard.departamentos_activos_v2',

            self::CACHE_TTL,

            function () {

                $filas = Derivacion::query()

                    ->selectRaw(
                        'idDepartamentoDestino, COUNT(*) as total'
                    )

                    ->where('activo', true)

                    ->whereNotNull('idDepartamentoDestino')

                    ->groupBy('idDepartamentoDestino')

                    ->orderByDesc('total')

                    ->take(5)

                    ->get();

                return $filas->map(function ($fila) {

                    $dep = Departamento::query()

                        ->where(
                            'idDepartamento',
                            $fila->idDepartamentoDestino
                        )

                        ->first();

                    return (object) [

                        'total' => $fila->total,

                        'departamentoDestino' => $dep,

                    ];

                });

            }

        );

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS RECIENTES
        |--------------------------------------------------------------------------
        */

        $documentosRecientes = Correspondencia::select(

                'idDocumento',
                'cite',
                'asunto',
                'fecha',
                'idEstado'

            )
            ->latest('idDocumento')

            ->take(5)

            ->get();

        /*
        |--------------------------------------------------------------------------
        | DERIVACIONES RECIENTES
        |--------------------------------------------------------------------------
        */

        $derivacionesRecientes = Derivacion::with([

                'documento:idDocumento,cite',

                'departamentoOrigen:idDepartamento,nombre',

                'departamentoDestino:idDepartamento,nombre',

                'usuarioEnvio:id,name',

            ])
            ->latest('idDerivacion')

            ->take(5)

            ->get();

        /*
        |--------------------------------------------------------------------------
        | ANUNCIOS NO VISTOS
        |--------------------------------------------------------------------------
        */

        $anuncioPendiente = Anuncio::activos()
            ->with('creador')
            ->where('idUsuarioCreador', '!=', Auth::id())
            ->whereDoesntHave('vistas', fn($q) => $q->where('idUsuario', Auth::id()))
            ->orderBy('fechaCreacion')
            ->first();

        $documentosPendientesAsignados = Correspondencia::whereHas('ultimaDerivacion',
                fn($q) => $q->where('idUsuarioAsignado', Auth::id())
            )
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente'))
            ->with(['ultimaDerivacion.departamentoDestino', 'remitente', 'urgencia'])
            ->orderByDesc('fecha')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETORNO
        |--------------------------------------------------------------------------
        */

        return view(

            'admin.dashboard',

            compact(

                'totalDocumentos',
                'totalUsuarios',
                'totalDerivaciones',
                'totalDepartamentos',

                'documentosFinalizados',
                'documentosArchivados',
                'documentosPendientes',
                'documentosUrgentes',

                'departamentosActivos',

                'documentosRecientes',
                'derivacionesRecientes',
                'anuncioPendiente',
                'documentosPendientesAsignados'
            )
        );
    }
     /*
|--------------------------------------------------------------------------
| API ESTADÍSTICAS DASHBOARD
|--------------------------------------------------------------------------
*/

public function estadisticasDashboard()
{
    $estados = [
        [
            'nombre'   => 'Archivados',
            'cantidad' => Correspondencia::where(
                'idEstado',
                self::ESTADO_ARCHIVADO
            )->count()
        ],
        [
            'nombre'   => 'Atendido',
            'cantidad' => Correspondencia::where(
                'idEstado',
                self::ESTADO_ATENDIDO
            )->count()
        ],
        [
            'nombre'   => 'Pendientes',
            'cantidad' => Correspondencia::whereNotIn(
                'idEstado',
                [
                   
                    self::ESTADO_ARCHIVADO
                ]
            )->count()
        ]
    ];

    /*
    |--------------------------------------------------------------------------
    | TIPOS DOCUMENTOS
    |--------------------------------------------------------------------------
    */

   $tipos = Correspondencia::select(
        'idTipoDocumento',
        DB::raw('COUNT(*) as cantidad')
    )
    ->with('tipoDocumento:idTipoDocumento,nombre')
    ->groupBy('idTipoDocumento')
    ->get()
    ->map(function ($item) {

        return [
            'nombre' => $item->tipoDocumento->nombre ?? 'Sin Tipo',
            'cantidad' => $item->cantidad
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS POR MES
    |--------------------------------------------------------------------------
    */

    $meses = $this->documentosPorMes();

    return response()->json([
        'estados' => $estados,
        'tipos'   => $tipos,
        'meses'   => $meses
    ]);
}

private function documentosPorMes()
{
    $inicio = Carbon::now()->startOfMonth()->subMonths(5);
    $fin = Carbon::now()->endOfMonth();

    $registros = Correspondencia::whereBetween('fecha', [$inicio, $fin])
        ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as periodo, COUNT(*) as cantidad')
        ->groupByRaw('DATE_FORMAT(fecha, "%Y-%m")')
        ->pluck('cantidad', 'periodo');

    $mesesNombres = [
        1 => 'Ene',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dic',
    ];

    return collect(range(0, 5))->map(function ($offset) use ($inicio, $registros, $mesesNombres) {
        $mes = $inicio->copy()->addMonths($offset);
        $periodo = $mes->format('Y-m');

        return [
            'mes' => $mesesNombres[(int) $mes->month] . ' ' . $mes->format('Y'),
            'cantidad' => (int) ($registros[$periodo] ?? 0),
        ];
    });
}
 /*
|--------------------------------------------------------------------------
| API DEPARTAMENTOS
|--------------------------------------------------------------------------
*/

public function estadisticasDepartamentos()
{
    try {

        $filas = Derivacion::query()

            ->selectRaw(
                'idDepartamentoDestino, ' .
                'COUNT(*) as derivaciones, ' .
                'COUNT(DISTINCT idDocumento) as documentos'
            )

            ->whereNotNull('idDepartamentoDestino')

            ->groupBy('idDepartamentoDestino')

            ->orderByDesc('derivaciones')

            ->take(8)

            ->get();

        $resultado = [];

        foreach ($filas as $fila) {

            $departamento = Departamento::find(
                $fila->idDepartamentoDestino
            );

            $resultado[] = [

                'nombre' =>
                    $departamento->nombre ?? 'Sin nombre',

                'documentos' =>
                    (int) ($fila->documentos ?? 0),

                'derivaciones' =>
                    (int) ($fila->derivaciones ?? 0),

            ];
        }

        return response()->json($resultado);

    } catch (\Exception $e) {

        return response()->json([

            'error' => true,
            'mensaje' => $e->getMessage()

        ], 500);
    }
}
}
