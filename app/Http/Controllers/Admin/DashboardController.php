<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\User;
use App\Models\Departamento;

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

    private const ESTADO_FINALIZADO = 3;
    private const ESTADO_ARCHIVADO  = 4;

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

                    self::ESTADO_FINALIZADO,

                    self::ESTADO_ARCHIVADO,

                    self::ESTADO_FINALIZADO,
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
                'derivacionesRecientes'
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
            'nombre'   => 'Finalizados',
            'cantidad' => Correspondencia::where(
                'idEstado',
                self::ESTADO_FINALIZADO
            )->count()
        ],
        [
            'nombre'   => 'Archivados',
            'cantidad' => Correspondencia::where(
                'idEstado',
                self::ESTADO_ARCHIVADO
            )->count()
        ],
        [
            'nombre'   => 'Pendientes',
            'cantidad' => Correspondencia::whereNotIn(
                'idEstado',
                [
                    self::ESTADO_FINALIZADO,
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

    $meses = Correspondencia::selectRaw('
            MONTH(fecha) as numero_mes,
            COUNT(*) as cantidad
        ')
        ->whereYear('fecha', now()->year)
        ->groupBy('numero_mes')
        ->orderBy('numero_mes')
        ->get()
        ->map(function ($item) {

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

            return [
                'mes'      => $mesesNombres[$item->numero_mes],
                'cantidad' => $item->cantidad
            ];
        });

    return response()->json([
        'estados' => $estados,
        'tipos'   => $tipos,
        'meses'   => $meses
    ]);
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