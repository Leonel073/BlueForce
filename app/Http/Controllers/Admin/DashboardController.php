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

            'dashboard.departamentos_activos',

            self::CACHE_TTL,

            function () {

                return Derivacion::select(

                        'idDepartamentoDestino',

                        DB::raw('COUNT(*) as total')

                    )
                    ->where('activo', true)

                    ->with([

                        'departamentoDestino:idDepartamento,nombre'

                    ])

                    ->groupBy('idDepartamentoDestino')

                    ->orderByDesc('total')

                    ->take(5)

                    ->get();
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
}