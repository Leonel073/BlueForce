<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Correspondencia;
use App\Models\Derivacion;
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
        /*
        |--------------------------------------------------------------------------
        | USUARIO LOGUEADO
        |--------------------------------------------------------------------------
        */

        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTO DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $documento = Correspondencia::where(
                'idUsuario',
                $usuario->id
            )
            ->where(
                'idDocumento',
                $id
            )
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | DERIVACIONES
        |--------------------------------------------------------------------------
        */

        $derivaciones = Derivacion::with([

                'departamentoOrigen',
                'departamentoDestino',
                'usuarioAsignado'

            ])
            ->where(
                'idDocumento',
                $documento->idDocumento
            )
            ->orderBy('orden')
            ->get();

        return view(
            'user.correspondencia.show',
            compact(
                'documento',
                'derivaciones'
            )
        );
}
}