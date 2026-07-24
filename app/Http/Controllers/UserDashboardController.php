<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\EstadoDocumento;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Mostrar dashboard personal del usuario
     */
    public function index()
    {
        $userId = Auth::id();

        // DOCUMENTOS PENDIENTES DE RECEPCIÓN (responsable actual)
        $documentosPendientes = Correspondencia::whereHas('ultimaDerivacion', 
            fn($q) => $q->where('idUsuarioAsignado', $userId)
        )->whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente'))
        ->with(['ultimaDerivacion.departamentoDestino', 'remitente', 'urgencia'])
        ->get();

        // ESTADÍSTICAS PERSONALES DEL USUARIO
        $totalMis = Correspondencia::where('idUsuario', $userId)->count();

        // DOCUMENTOS POR ESTADO (personal)
        $pendientes = Correspondencia::where('idUsuario', $userId)
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente'))
            ->count();
        
        $recibidos = Correspondencia::where('idUsuario', $userId)
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Recibido'))
            ->count();
        
        $atendidos = Correspondencia::where('idUsuario', $userId)
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Atendido'))
            ->count();
        
        $archivados = Correspondencia::where('idUsuario', $userId)
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Archivado'))
            ->count();

        // DERIVACIONES REALIZADAS (personal)
        $derivacionesRealizadas = Derivacion::where('idUsuarioEnvio', $userId)->count();

        // DOCUMENTOS URGENTES (personal)
        $urgentes = Correspondencia::where('idUsuario', $userId)
            ->whereHas('urgencia', fn($q) => $q->whereRaw('LOWER(nombre) LIKE ?', ['%alta%']))
            ->count();

        // ÚLTIMOS DOCUMENTOS DEL USUARIO
        $ultimosDocumentos = Correspondencia::where('idUsuario', $userId)
            ->with(['tipoDocumento', 'estado', 'usuario'])
            ->latest('fecha')
            ->take(5)
            ->get();

        // DOCUMENTOS POR MES (últimos 6 meses - personal)
        $documentosPorMes = Correspondencia::where('idUsuario', $userId)
            ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as cantidad')
            ->groupByRaw('DATE_FORMAT(fecha, "%Y-%m")')
            ->orderBy('mes', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        // DOCUMENTOS POR TIPO (personal)
        $documentosPorTipo = Correspondencia::where('idUsuario', $userId)
            ->with('tipoDocumento')
            ->get()
            ->groupBy('idTipoDocumento')
            ->map(fn($group) => [
                'nombre' => $group->first()->tipoDocumento->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        // DOCUMENTOS POR ESTADO (para gráficos)
        $estadosPorTipo = EstadoDocumento::all()
            ->map(fn($estado) => [
                'nombre' => $estado->nombre,
                'cantidad' => Correspondencia::where('idUsuario', $userId)
                    ->where('idEstado', $estado->idEstado)
                    ->count()
            ]);

        // ANUNCIOS NO VISTOS (pantallazo al ingresar)
        $anuncioPendiente = Anuncio::activos()
            ->with('creador')
            ->whereDoesntHave('vistas', fn($q) => $q->where('idUsuario', $userId))
            ->orderBy('fechaCreacion')
            ->first();

        return view('user.dashboard', compact(
            'totalMis',
            'pendientes',
            'recibidos',
            'atendidos',
            'archivados',
            'derivacionesRealizadas',
            'urgentes',
            'ultimosDocumentos',
            'documentosPorMes',
            'documentosPorTipo',
            'estadosPorTipo',
            'documentosPendientes',
            'anuncioPendiente'
        ) + ['totalDocumentos' => $totalMis]);
    }
}
