<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\User;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard admin
     */
    public function index()
    {
        // ESTADÍSTICAS GENERALES
        $totalDocumentos = Correspondencia::count();
        $totalUsuarios = User::count();
        $totalDepartamentos = Departamento::count();
        
        // DOCUMENTOS POR ESTADO
        $documentosPorEstado = EstadoDocumento::with(['correspondencias' => function($query) {
            $query->select('idEstado');
        }])
        ->get()
        ->map(fn($estado) => [
            'nombre' => $estado->nombre,
            'cantidad' => Correspondencia::where('idEstado', $estado->idEstado)->count()
        ]);

        // ÚLTIMOS DOCUMENTOS
        $ultimosDocumentos = Correspondencia::with(['usuario', 'estado', 'tipoDocumento'])
            ->latest('fecha')
            ->take(10)
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

        // DOCUMENTOS PENDIENTES
        $pendientes = Correspondencia::whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente'))->count();
        $recibidos = Correspondencia::whereHas('estado', fn($q) => $q->where('nombre', 'Recibido'))->count();
        $atendidos = Correspondencia::whereHas('estado', fn($q) => $q->where('nombre', 'Atendido'))->count();
        $archivados = Correspondencia::whereHas('estado', fn($q) => $q->where('nombre', 'Archivado'))->count();

        return view('admin.dashboard', compact(
            'totalDocumentos',
            'totalUsuarios',
            'totalDepartamentos',
            'documentosPorEstado',
            'ultimosDocumentos',
            'documentosPorMes',
            'documentosPorTipo',
            'pendientes',
            'recibidos',
            'atendidos',
            'archivados'
        ));
    }

    /**
     * API: Estadísticas del dashboard
     */
    public function estadisticasDashboard()
    {
        return response()->json([
            'totalDocumentos' => Correspondencia::count(),
            'totalUsuarios' => User::count(),
            'pendientes' => Correspondencia::whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente'))->count(),
            'recibidos' => Correspondencia::whereHas('estado', fn($q) => $q->where('nombre', 'Recibido'))->count(),
        ]);
    }

    /**
     * API: Estadísticas por departamento
     */
    public function estadisticasDepartamentos()
    {
        $departamentos = Departamento::with(['correspondencias' => function($query) {
            $query->select('idDepartamento');
        }])
        ->get()
        ->map(fn($dept) => [
            'nombre' => $dept->nombre,
            'cantidad' => Correspondencia::where('idDepartamento', $dept->idDepartamento)->count()
        ]);

        return response()->json($departamentos);
    }
}
