<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Correspondencia;
use App\Models\Departamento;
use App\Models\Derivacion;
use App\Models\Seguimiento;
use App\Models\EstadoDocumento;

class EnvioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS ENVIADOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $usuario = Auth::user();

        $documentos = Correspondencia::with([

            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',
            'derivaciones.departamentoOrigen',
            'derivaciones.departamentoDestino',

        ])
        ->where('idUsuario', $usuario->id)
        ->orderByDesc('idDocumento')
        ->get();

        return view(
            'envio.index',
            compact('documentos')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BANDEJA GENERAL
    |--------------------------------------------------------------------------
    */

    public function bandeja()
    {
        $documentos = Correspondencia::with([

            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',
            'ultimaDerivacion.departamentoDestino',

        ])
        ->orderByDesc('idDocumento')
        ->get();

        return view(
            'envio.bandeja',
            compact('documentos')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DERIVAR
    |--------------------------------------------------------------------------
    */

    public function derivarForm($id)
    {
        $documento = Correspondencia::with([

            'derivaciones.departamentoOrigen',
            'derivaciones.departamentoDestino',

        ])->findOrFail($id);

        $departamentos = Departamento::all();

        return view(
            'envio.derivar',
            compact(
                'documento',
                'departamentos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DERIVAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function derivar(Request $request, $id)
{
    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    $request->validate([

        'idDepartamentoDestino' =>
            'required|exists:DEPARTAMENTO,idDepartamento',

        'instruccion' =>
            'nullable|string|max:1000',

    ]);

    /*
    |--------------------------------------------------------------------------
    | OBTENER DOCUMENTO
    |--------------------------------------------------------------------------
    */

    $documento = Correspondencia::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | OBTENER ÚLTIMA DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    $ultimaDerivacion = Derivacion::where(
        'idDocumento',
        $id
    )
    ->orderByDesc('orden')
    ->first();

    /*
    |--------------------------------------------------------------------------
    | OBTENER NUEVO ORDEN
    |--------------------------------------------------------------------------
    */

    $nuevoOrden = $ultimaDerivacion
        ? $ultimaDerivacion->orden + 1
        : 1;

    /*
    |--------------------------------------------------------------------------
    | DEPARTAMENTO ORIGEN
    |--------------------------------------------------------------------------
    */

    $departamentoOrigen = $ultimaDerivacion
        ? $ultimaDerivacion->idDepartamentoDestino
        : 1;

    /*
    |--------------------------------------------------------------------------
    | CERRAR DERIVACIÓN ANTERIOR
    |--------------------------------------------------------------------------
    */

    if ($ultimaDerivacion) {

        $ultimaDerivacion->update([

            'fechaRecepcion' => now()

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | CREAR NUEVA DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    Derivacion::create([

        'idDocumento' => $documento->idDocumento,

        'orden' => $nuevoOrden,

        'idDepartamentoOrigen' =>
            $departamentoOrigen,

        'idDepartamentoDestino' =>
            $request->idDepartamentoDestino,

        'instruccion' =>
            $request->instruccion,

        'fechaEnvio' => now(),

        'activo' => true,

    ]);

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ESTADO
    |--------------------------------------------------------------------------
    */

    $estadoDerivado = EstadoDocumento::where(
        'nombre',
        'DERIVADO'
    )->first();

    if ($estadoDerivado) {

        $documento->update([

            'idEstado' =>
                $estadoDerivado->idEstado

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR SEGUIMIENTO
    |--------------------------------------------------------------------------
    */

    Seguimiento::create([

        'idDocumento' => $documento->idDocumento,

        'fecha' => now(),

        'ubicacion' =>
            'Documento derivado a otro departamento',

        'idEstado' =>
            $documento->idEstado,

        'activo' => true,

    ]);

    /*
    |--------------------------------------------------------------------------
    | REDIRECCIÓN
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('envios.bandeja')
        ->with(
            'success',
            'Documento derivado correctamente.'
        );
}
    public function finalizar($id)
{
    /*
    |--------------------------------------------------------------------------
    | OBTENER DOCUMENTO
    |--------------------------------------------------------------------------
    */

    $documento = Correspondencia::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | BUSCAR ESTADO FINALIZADO
    |--------------------------------------------------------------------------
    */

    $estadoFinalizado = EstadoDocumento::where(
        'nombre',
        'Archivado'
    )->first();

    /*
    |--------------------------------------------------------------------------
    | SI NO EXISTE EL ESTADO
    |--------------------------------------------------------------------------
    */

    if (!$estadoFinalizado) {

        return back()->with(
            'error',
            'No existe el estado FINALIZADO.'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    $documento->update([

        'idEstado' => $estadoFinalizado->idEstado

    ]);

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR SEGUIMIENTO
    |--------------------------------------------------------------------------
    */

    Seguimiento::create([

        'idDocumento' => $documento->idDocumento,

        'fecha' => now(),

        'ubicacion' => 'Documento finalizado',

        'idEstado' => $estadoFinalizado->idEstado,

        'activo' => true,

    ]);

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ÚLTIMA DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    $ultimaDerivacion = Derivacion::where(
        'idDocumento',
        $documento->idDocumento
    )
    ->orderByDesc('orden')
    ->first();

    if ($ultimaDerivacion) {

        $ultimaDerivacion->update([

            'fechaRecepcion' => now()

        ]);

    }

    return redirect()
        ->route('envios.bandeja')
        ->with(
            'success',
            'Documento finalizado correctamente.'
        );
}
}