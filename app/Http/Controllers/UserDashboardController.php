<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\Derivacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // DATOS PARA GRÁFICOS - Estados de documentos
        $estadosPorTipo = Correspondencia::where('idUsuario', $usuario->id)
            ->with('estado')
            ->get()
            ->groupBy('idEstado')
            ->map(fn($group) => [
                'nombre' => $group->first()->estado->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        // ÚLTIMOS DOCUMENTOS
        $ultimosDocumentos = Correspondencia::where('idUsuario', $usuario->id)
            ->with(['tipoDocumento', 'estado'])
            ->latest('fecha')
            ->take(5)
            ->get();

        // DOCUMENTOS POR MES (últimos 6 meses)
        $documentosPorMes = Correspondencia::where('idUsuario', $usuario->id)
            ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as cantidad')
            ->groupByRaw('DATE_FORMAT(fecha, "%Y-%m")')
            ->orderBy('mes', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        // DOCUMENTOS POR TIPO
        $documentosPorTipo = Correspondencia::where('idUsuario', $usuario->id)
            ->with('tipoDocumento')
            ->get()
            ->groupBy('idTipoDocumento')
            ->map(fn($group) => [
                'nombre' => $group->first()->tipoDocumento->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        // DERIVACIONES REALIZADAS
        $derivacionesRealizadas = Derivacion::whereHas('documento', function($q) use ($usuario) {
            $q->where('idUsuario', $usuario->id);
        })->count();

        // URGENCIAS
        $urgentes = Correspondencia::where('idUsuario', $usuario->id)
            ->where('idUrgencia', 1) // Asumiendo que 1 es Urgente
            ->count();

        // ESTADO GENERAL DEL SISTEMA
        $estadoDocumentos = EstadoDocumento::all()
            ->map(fn($estado) => [
                'nombre' => $estado->nombre,
                'cantidad' => Correspondencia::where('idUsuario', $usuario->id)
                    ->where('idEstado', $estado->idEstado)
                    ->count()
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