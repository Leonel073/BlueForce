<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\Seguimiento;

class RecibidasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BANDEJA RECIBIDAS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $documentos = Correspondencia::with([

            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',
            'ultimaDerivacion.departamentoOrigen',
            'ultimaDerivacion.departamentoDestino',

        ])
        ->orderByDesc('idDocumento')
        ->get();

        return view(
            'recibidas.index',
            compact('documentos')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RECIBIR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function recibir($id)
    {
        $derivacion = Derivacion::where(
            'idDocumento',
            $id
        )
        ->orderByDesc('orden')
        ->first();

        if (!$derivacion) {

            return back()->with(
                'error',
                'El documento no tiene derivación.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR RECEPCIÓN
        |--------------------------------------------------------------------------
        */

        $derivacion->fechaRecepcion = now();

        $derivacion->save();

        /*
        |--------------------------------------------------------------------------
        | SEGUIMIENTO
        |--------------------------------------------------------------------------
        */

        Seguimiento::create([

            'idDocumento' => $id,

            'fecha' => now(),

            'ubicacion'
                => 'Documento recibido',

            'idEstado' => 2,

            'activo' => true,

        ]);

        return back()->with(
            'success',
            'Documento recibido correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function finalizar($id)
    {
        $documento = Correspondencia::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | ESTADO FINALIZADO
        |--------------------------------------------------------------------------
        */

        $documento->idEstado = 3;

        $documento->save();

        /*
        |--------------------------------------------------------------------------
        | SEGUIMIENTO
        |--------------------------------------------------------------------------
        */

        Seguimiento::create([

            'idDocumento' => $id,

            'fecha' => now(),

            'ubicacion'
                => 'Documento finalizado',

            'idEstado' => 3,

            'activo' => true,

        ]);

        return back()->with(
            'success',
            'Documento finalizado correctamente.'
        );
    }
}