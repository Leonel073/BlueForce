<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\NivelUrgencia;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Derivacion;
use App\Models\Seguimiento;
use Illuminate\Support\Facades\Auth;
class CorrespondenciaController extends Controller
{
public function index()
{
    $usuario = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS DEL USUARIO
    |--------------------------------------------------------------------------
    */

    $documentos = Correspondencia::with([

        'tipoDocumento',
        'estado',
        'urgencia',
        'remitente',
        'derivaciones.departamentoDestino'

    ])

    ->where('idUsuario', $usuario->id)

    ->orderByDesc('fecha')

    ->get();

    /*
    |--------------------------------------------------------------------------
    | CONTADORES
    |--------------------------------------------------------------------------
    */

    $totalDocumentos =
        $documentos->count();

    $pendientes =
        $documentos
            ->where('estado.nombre', 'Pendiente')
            ->count();

    $finalizados =
        $documentos
            ->where('estado.nombre', 'Finalizado')
            ->count();

    $urgentes =
        $documentos
            ->where('urgencia.nombre', 'Urgente')
            ->count();

    /*
    |--------------------------------------------------------------------------
    | RETORNO
    |--------------------------------------------------------------------------
    */

    return view(
        'user.correspondencia.index',
        compact(
            'documentos',
            'totalDocumentos',
            'pendientes',
            'finalizados',
            'urgentes'
        )
    );
}

public function show($id)
{
    $documento = Correspondencia::with([

        'usuario',
        'estado',
        'urgencia',
        'tipoDocumento',
        'remitente',

        'derivaciones.departamentoOrigen',
        'derivaciones.departamentoDestino'

    ])->findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | ÚLTIMA DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    $ultimaDerivacion = $documento->derivaciones
        ->sortByDesc('orden')
        ->first();

    return view(
        'admin.correspondencia.show',
        compact(
            'documento',
            'ultimaDerivacion'
        )
    );
}

        public function derivar(Request $request, $id)
    {
        $request->validate([

            'idDepartamentoDestino' => 'required',

        ]);

        $documento = Correspondencia::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | OBTENER ÚLTIMO ORDEN
        |--------------------------------------------------------------------------
        */

        $ultimoOrden = Derivacion::where(
            'idDocumento',
            $id
        )->max('orden');

        $nuevoOrden = $ultimoOrden
            ? $ultimoOrden + 1
            : 1;

        /*
        |--------------------------------------------------------------------------
        | CREAR DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        Derivacion::create([

            'idDocumento' => $id,

            'orden' => $nuevoOrden,

            'idDepartamentoOrigen' => 1,

            'idDepartamentoDestino'
                => $request->idDepartamentoDestino,

            'idUsuarioAsignado'
                => $request->idUsuarioAsignado,

            'instruccion'
                => $request->instruccion,

            'fechaEnvio' => now(),

            'activo' => true,

        ]);

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR SEGUIMIENTO
        |--------------------------------------------------------------------------
        */

        Seguimiento::create([

            'idDocumento' => $id,

            'fecha' => now(),

            'ubicacion'
                => 'Documento derivado',

            'idEstado'
                => $documento->idEstado,

            'activo' => true,

        ]);

        return back()->with(
            'success',
            'Documento derivado correctamente.'
        );
    }
}