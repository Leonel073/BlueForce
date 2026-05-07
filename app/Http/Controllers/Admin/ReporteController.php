<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\User;
use App\Models\Derivacion;
use App\Models\EstadoDocumento;
use App\Models\Departamento;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TOTALES GENERALES
        |--------------------------------------------------------------------------
        */

        $totalDocumentos =
            Correspondencia::count();

        $totalUsuarios =
            User::count();

        $totalDerivaciones =
            Derivacion::count();

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS FINALIZADOS
        |--------------------------------------------------------------------------
        */

        $estadoFinalizado = EstadoDocumento::where(
            'nombre',
            'Finalizado'
        )->first();

        $documentosFinalizados =
            $estadoFinalizado
            ? Correspondencia::where(
                'idEstado',
                $estadoFinalizado->idEstado
            )->count()
            : 0;

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS PENDIENTES
        |--------------------------------------------------------------------------
        */

        $documentosPendientes =
            $totalDocumentos
            - $documentosFinalizados;

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMOS DOCUMENTOS
        |--------------------------------------------------------------------------
        */

        $ultimosDocumentos =
            Correspondencia::latest(
                'idDocumento'
            )
            ->take(10)
            ->get();

        return view(
            'admin.reportes.index',
            compact(
                'totalDocumentos',
                'totalUsuarios',
                'totalDerivaciones',
                'documentosFinalizados',
                'documentosPendientes',
                'ultimosDocumentos'
            )
        );
    }

    public function usuarios()
    {
        $usuarios = User::withCount([
            'correspondencias'
        ])->get();

        return view(
            'admin.reportes.usuarios',
            compact('usuarios')
        );
    }
    public function departamentos()
    {
        /*
        |--------------------------------------------------------------------------
        | OBTENER DEPARTAMENTOS
        |--------------------------------------------------------------------------
        */

        $departamentos = Departamento::all();

        /*
        |--------------------------------------------------------------------------
        | CONTADORES
        |--------------------------------------------------------------------------
        */

        foreach ($departamentos as $departamento) {

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTOS RECIBIDOS
            |--------------------------------------------------------------------------
            */

            $departamento->recibidos =
                Derivacion::where(
                    'idDepartamentoDestino',
                    $departamento->idDepartamento
                )->count();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTOS ENVIADOS
            |--------------------------------------------------------------------------
            */

            $departamento->enviados =
                Derivacion::where(
                    'idDepartamentoOrigen',
                    $departamento->idDepartamento
                )->count();
        }

        return view(
            'admin.reportes.departamentos',
            compact('departamentos')
        );
    }
    public function derivaciones(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | QUERY BASE
    |--------------------------------------------------------------------------
    */

    $query = Derivacion::with([

        'documento',
        'departamentoOrigen',
        'departamentoDestino',
        'usuarioAsignado'

    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTRO DOCUMENTO
    |--------------------------------------------------------------------------
    */

    if ($request->filled('documento')) {

        $documento = $request->documento;

        $query->whereHas('documento', function ($q) use ($documento) {

            $q->where('cite', 'like', "%{$documento}%")
              ->orWhere('asunto', 'like', "%{$documento}%");

        });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO ORIGEN
    |--------------------------------------------------------------------------
    */

    if ($request->filled('origen')) {

        $query->where(
            'idDepartamentoOrigen',
            $request->origen
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO DESTINO
    |--------------------------------------------------------------------------
    */

    if ($request->filled('destino')) {

        $query->where(
            'idDepartamentoDestino',
            $request->destino
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO FECHA INICIO
    |--------------------------------------------------------------------------
    */

    if ($request->filled('fecha_inicio')) {

        $query->whereDate(
            'fechaEnvio',
            '>=',
            $request->fecha_inicio
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO FECHA FIN
    |--------------------------------------------------------------------------
    */

    if ($request->filled('fecha_fin')) {

        $query->whereDate(
            'fechaEnvio',
            '<=',
            $request->fecha_fin
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESULTADOS
    |--------------------------------------------------------------------------
    */

    $derivaciones = $query
        ->latest('idDerivacion')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | DEPARTAMENTOS
    |--------------------------------------------------------------------------
    */

    $departamentos = Departamento::all();

    return view(
        'admin.reportes.derivaciones',
        compact(
            'derivaciones',
            'departamentos'
        )
    );
}
public function derivacionesPDF(Request $request)
{
    $query = Derivacion::with([

        'documento',
        'departamentoOrigen',
        'departamentoDestino',
        'usuarioAsignado'

    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('documento')) {

        $documento = $request->documento;

        $query->whereHas('documento', function ($q) use ($documento) {

            $q->where('cite', 'like', "%{$documento}%")
              ->orWhere('asunto', 'like', "%{$documento}%");

        });
    }

    if ($request->filled('origen')) {

        $query->where(
            'idDepartamentoOrigen',
            $request->origen
        );
    }

    if ($request->filled('destino')) {

        $query->where(
            'idDepartamentoDestino',
            $request->destino
        );
    }

    if ($request->filled('fecha_inicio')) {

        $query->whereDate(
            'fechaEnvio',
            '>=',
            $request->fecha_inicio
        );
    }

    if ($request->filled('fecha_fin')) {

        $query->whereDate(
            'fechaEnvio',
            '<=',
            $request->fecha_fin
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESULTADOS
    |--------------------------------------------------------------------------
    */

    $derivaciones = $query
        ->latest('idDerivacion')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    $pdf = Pdf::loadView(
        'admin.reportes.pdf.derivaciones',
        compact('derivaciones')
    );

    return $pdf->download(
        'reporte-derivaciones.pdf'
    );
}
}