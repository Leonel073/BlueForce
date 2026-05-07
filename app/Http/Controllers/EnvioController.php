<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\User;
use App\Models\Derivacion;
use App\Models\Seguimiento;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\Correspondencia;

class EnvioController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS CREADOS POR EL USUARIO
        |--------------------------------------------------------------------------
        */

        $documentos = Correspondencia::with([
            'estado',
            'urgencia',
            'tipoDocumento',
            'seguimientos',
            'derivaciones'
        ])
        ->where('idUsuario', $usuario->id)
        ->orderBy('idDocumento', 'desc')
        ->get();

        return view(
            'envio.index',
            compact('documentos')
        );
    }
    public function bandeja()
{
    $usuario = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | OBTENER DOCUMENTOS
    |--------------------------------------------------------------------------
    */

    $documentos = Correspondencia::with([
        'estado',
        'urgencia',
        'tipoDocumento',
        'derivaciones.departamentoDestino',
    ])
    ->get()
    ->filter(function ($doc) use ($usuario) {

        return $doc->puedeDerivar(
            $usuario->id
        );

    });

    return view(
        'envio.bandeja',
        compact('documentos')
    );
    }

    public function derivarForm($id)
{
    $documento = Correspondencia::with([
        'derivaciones'
    ])->findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR CONTROL
    |--------------------------------------------------------------------------
    */

    if (!$documento->puedeDerivar(Auth::id())) {

        abort(403);

    }

    $departamentos = Departamento::all();

    $usuarios = User::where(
        'activo',
        1
    )->get();

    return view(
        'envio.derivar',
        compact(
            'documento',
            'departamentos',
            'usuarios'
        )
    );
}

public function derivar(Request $request, $id)
{
    $request->validate([

        'idDepartamentoDestino'
            => 'required',

    ]);

    $documento = Correspondencia::with([
        'derivaciones'
    ])->findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR CONTROL
    |--------------------------------------------------------------------------
    */

    if (!$documento->puedeDerivar(Auth::id())) {

        abort(403);

    }

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
    | DEPARTAMENTO ORIGEN
    |--------------------------------------------------------------------------
    */

    $ultimaDerivacion = Derivacion::where(
        'idDocumento',
        $id
    )
    ->orderByDesc('orden')
    ->first();

    $departamentoOrigen =
        $ultimaDerivacion
        ? $ultimaDerivacion->idDepartamentoDestino
        : 1;

    /*
    |--------------------------------------------------------------------------
    | CREAR DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    Derivacion::create([

        'idDocumento' => $id,

        'orden' => $nuevoOrden,

        'idDepartamentoOrigen'
            => $departamentoOrigen,

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
    | SEGUIMIENTO
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

    return redirect()
        ->route('envios.bandeja')
        ->with(
            'success',
            'Documento derivado correctamente.'
        );
}
}