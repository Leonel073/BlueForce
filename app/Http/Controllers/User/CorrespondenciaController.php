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
        /*
        |--------------------------------------------------------------------------
        | USUARIO LOGUEADO
        |--------------------------------------------------------------------------
        */

        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $documentos = Correspondencia::where(
            'idUsuario',
            $usuario->id
        )
        ->latest('idDocumento')
        ->get();

        /*
        |--------------------------------------------------------------------------
        | ESTADÍSTICAS
        |--------------------------------------------------------------------------
        */

        $totalDocumentos = $documentos->count();

        return view(
            'user.correspondencia.index',
            compact(
                'documentos',
                'totalDocumentos'
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