<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\Derivacion;

class UserDashboardController extends Controller
{
    public function index()
    {
        // TOTAL DOCUMENTOS
        $totalDocumentos = Correspondencia::count();

        // APROBADOS
        $aprobados = Correspondencia::where('idEstado', 1)->count();

        // VIGENTES
        $vigentes = Correspondencia::where('idEstado', 2)->count();

        // EN REVISIÓN
        $revision = Correspondencia::where('idEstado', 3)->count();

        // DATOS PARA GRÁFICOS - Estados de documentos
        $estadosPorTipo = Correspondencia::with('estado')
            ->get()
            ->groupBy('idEstado')
            ->map(fn($group) => [
                'nombre' => $group->first()->estado->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        // ÚLTIMOS DOCUMENTOS
        $ultimosDocumentos = Correspondencia::with(['tipoDocumento', 'estado'])
            ->latest('fecha')
            ->take(5)
            ->get();

        // DOCUMENTOS POR MES (últimos 6 meses)
        $documentosPorMes = Correspondencia::selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as cantidad')
            ->groupByRaw('DATE_FORMAT(fecha, "%Y-%m")')
            ->orderBy('mes', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        // DOCUMENTOS POR TIPO
        $documentosPorTipo = Correspondencia::with('tipoDocumento')
            ->get()
            ->groupBy('idTipoDocumento')
            ->map(fn($group) => [
                'nombre' => $group->first()->tipoDocumento->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        // DERIVACIONES REALIZADAS
        $derivacionesRealizadas = Derivacion::count();

        // URGENCIAS
        $urgentes = Correspondencia::where('idUrgencia', 1)->count();

        // ESTADO GENERAL DEL SISTEMA
        $estadoDocumentos = EstadoDocumento::all()
            ->map(fn($estado) => [
                'nombre' => $estado->nombre,
                'cantidad' => Correspondencia::where('idEstado', $estado->idEstado)->count()
            ]);

        return view('user.dashboard', compact(
            'totalDocumentos',
            'aprobados',
            'vigentes',
            'revision',
            'estadosPorTipo',
            'ultimosDocumentos',
            'documentosPorMes',
            'documentosPorTipo',
            'derivacionesRealizadas',
            'urgentes',
            'estadoDocumentos'
        ));
    }
}
