<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\User;
use App\Models\Derivacion;
use App\Models\EstadoDocumento;
use App\Models\Departamento;
use App\Models\TipoDocumento;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Persona;

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

    public function personas(Request $request)
    {
        $query = Persona::query();

        // Filtros de Persona
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $personas = $query->orderBy('idPersona', 'desc')->get();

        // Buscar los documentos de cada persona (Evitamos errores de relación)
        foreach ($personas as $persona) {
            $docQuery = Correspondencia::with(['tipoDocumento', 'estado'])
                                       ->where('idRemitente', $persona->idPersona);

            // Filtros de Fecha del Documento
            if ($request->filled('fecha_inicio')) {
                $docQuery->whereDate('fecha', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $docQuery->whereDate('fecha', '<=', $request->fecha_fin);
            }

            $persona->documentos = $docQuery->get();
        }

        return view('admin.reportes.personas', compact('personas'));
    }

    public function personasPDF(Request $request)
    {
        $query = Persona::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $personas = $query->orderBy('idPersona', 'desc')->get();

        foreach ($personas as $persona) {
            $docQuery = Correspondencia::with(['tipoDocumento', 'estado'])
                                       ->where('idRemitente', $persona->idPersona);

            if ($request->filled('fecha_inicio')) {
                $docQuery->whereDate('fecha', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $docQuery->whereDate('fecha', '<=', $request->fecha_fin);
            }

            $persona->documentos = $docQuery->get();
        }

        $pdf = Pdf::loadView('admin.reportes.pdf.personas', compact('personas'));

        return $pdf->download('reporte-integral-personas.pdf');
    }
 public function usuarios(Request $request)
    {
        $query = User::query();

        if ($request->filled('nombre')) $query->where('name', 'like', "%{$request->nombre}%");
        if ($request->filled('correo')) $query->where('email', 'like', "%{$request->correo}%");
        if ($request->filled('estado')) $query->where('activo', $request->estado);

        $usuarios = $query->withCount(['correspondencias'])->get();
        return view('admin.reportes.usuarios', compact('usuarios'));
    }

    public function usuariosPDF(Request $request)
    {
        $query = User::query();

        if ($request->filled('nombre')) $query->where('name', 'like', "%{$request->nombre}%");
        if ($request->filled('correo')) $query->where('email', 'like', "%{$request->correo}%");
        if ($request->filled('estado')) $query->where('activo', $request->estado);

        $usuarios = $query->withCount(['correspondencias'])->get();
        $pdf = Pdf::loadView('admin.reportes.pdf.usuarios', compact('usuarios'));
        return $pdf->download('reporte-usuarios.pdf');
    }
    public function documentos(Request $request)
{
$query = Correspondencia::with(['tipoDocumento', 'estado', 'remitente']);
    // Filtros
    if ($request->filled('q')) {
        $query->where(function($f) use ($request) {
            $f->where('cite', 'like', "%{$request->q}%")
              ->orWhere('asunto', 'like', "%{$request->q}%");
        });
    }
if ($request->filled('idTipo')) $query->where('idTipoDocumento', $request->idTipo);
    if ($request->filled('idEstado')) $query->where('idEstado', $request->idEstado);
    if ($request->filled('fecha_inicio')) $query->whereDate('fecha', '>=', $request->fecha_inicio);
    if ($request->filled('fecha_fin')) $query->whereDate('fecha', '<=', $request->fecha_fin);

    $documentos = $query->latest('idDocumento')->get();
    $tipos = TipoDocumento::all();
    $estados = EstadoDocumento::all();

    return view('admin.reportes.documentos', compact('documentos', 'tipos', 'estados'));
}

public function documentosPDF(Request $request)
{
    $query = Correspondencia::with(['tipoDocumento', 'estado', 'remitente']);

    if ($request->filled('q')) {
        $query->where(function($f) use ($request) {
            $f->where('cite', 'like', "%{$request->q}%")
              ->orWhere('asunto', 'like', "%{$request->q}%");
        });
    }
if ($request->filled('idTipo')) $query->where('idTipoDocumento', $request->idTipo);
    if ($request->filled('idEstado')) $query->where('idEstado', $request->idEstado);
    if ($request->filled('fecha_inicio')) $query->whereDate('fecha', '>=', $request->fecha_inicio);
    if ($request->filled('fecha_fin')) $query->whereDate('fecha', '<=', $request->fecha_fin);

    $documentos = $query->latest('idDocumento')->get();

    $pdf = Pdf::loadView('admin.reportes.pdf.documentos', compact('documentos'));
    return $pdf->setPaper('letter', 'landscape')->download('reporte-general-documentos.pdf');
}
    public function departamentos(Request $request)
    {
        $query = Departamento::query();
        if ($request->filled('nombre')) $query->where('nombre', 'like', "%{$request->nombre}%");
        
        $departamentos = $query->get();

        foreach ($departamentos as $dep) {
            $qRecibidos = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento);
            $qEnviados = Derivacion::where('idDepartamentoOrigen', $dep->idDepartamento);

            // Filtro de fechas para ver el flujo en un periodo específico
            if ($request->filled('fecha_inicio')) {
                $qRecibidos->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
                $qEnviados->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $qRecibidos->whereDate('fechaEnvio', '<=', $request->fecha_fin);
                $qEnviados->whereDate('fechaEnvio', '<=', $request->fecha_fin);
            }

            $dep->recibidos = $qRecibidos->count();
            $dep->enviados = $qEnviados->count();
        }

        return view('admin.reportes.departamentos', compact('departamentos'));
    }

    public function departamentosPDF(Request $request)
    {
        $query = Departamento::query();
        if ($request->filled('nombre')) $query->where('nombre', 'like', "%{$request->nombre}%");
        
        $departamentos = $query->get();

        foreach ($departamentos as $dep) {
            $qRecibidos = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento);
            $qEnviados = Derivacion::where('idDepartamentoOrigen', $dep->idDepartamento);

            if ($request->filled('fecha_inicio')) {
                $qRecibidos->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
                $qEnviados->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $qRecibidos->whereDate('fechaEnvio', '<=', $request->fecha_fin);
                $qEnviados->whereDate('fechaEnvio', '<=', $request->fecha_fin);
            }

            $dep->recibidos = $qRecibidos->count();
            $dep->enviados = $qEnviados->count();
        }

        $pdf = Pdf::loadView('admin.reportes.pdf.departamentos', compact('departamentos'));
        return $pdf->download('reporte-flujo-departamentos.pdf');
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
        'departamentoDestino'
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
        'departamentoDestino'
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