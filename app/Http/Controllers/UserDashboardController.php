<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use Illuminate\Support\Facades\Auth;


class UserDashboardController extends Controller
{
    public function index()
{
    $usuario = Auth::user();

    // TOTAL DOCUMENTOS
    $totalDocumentos = Correspondencia::where(
        'idUsuario',
        $usuario->id
    )->count();

    // APROBADOS
    $aprobados = Correspondencia::where(
        'idUsuario',
        $usuario->id
    )
    ->where('idEstado', 1)
    ->count();

    // VIGENTES
    $vigentes = Correspondencia::where(
        'idUsuario',
        $usuario->id
    )
    ->where('idEstado', 2)
    ->count();

    // EN REVISIÓN
    $revision = Correspondencia::where(
        'idUsuario',
        $usuario->id
    )
    ->where('idEstado', 3)
    ->count();

    return view('user.dashboard', compact(
        'totalDocumentos',
        'aprobados',
        'vigentes',
        'revision'
    ));
}
}