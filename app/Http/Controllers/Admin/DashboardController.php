<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ESTADÍSTICAS
        |--------------------------------------------------------------------------
        */

        $totalDocumentos = Correspondencia::count();

        $totalUsuarios = User::count();

        $totalDerivaciones = Derivacion::count();

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS RECIENTES
        |--------------------------------------------------------------------------
        */

        $documentosRecientes = Correspondencia::latest('idDocumento')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DERIVACIONES RECIENTES
        |--------------------------------------------------------------------------
        */

        $derivacionesRecientes = Derivacion::with([

            'documento',
            'departamentoOrigen',
            'departamentoDestino'

        ])
        ->latest('idDerivacion')
        ->take(5)
        ->get();

        return view(
            'admin.dashboard',
            compact(
                'totalDocumentos',
                'totalUsuarios',
                'totalDerivaciones',
                'documentosRecientes',
                'derivacionesRecientes'
            )
        );
    }
}