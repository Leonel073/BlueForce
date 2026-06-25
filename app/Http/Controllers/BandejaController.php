<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use Illuminate\Support\Facades\Auth;

class BandejaController extends Controller
{
    /**
     * Documentos en estado Pendiente
     * Filtra por responsable actual (última derivación con idUsuarioAsignado)
     */
    public function pendientes()
    {
        $userId = Auth::id();

        $documentos = Correspondencia::whereHas('ultimaDerivacion', fn($q) => $q->where('idUsuarioAsignado', $userId))
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente'))
            ->with(['tipoDocumento', 'estado', 'usuario', 'ultimaDerivacion.departamentoDestino'])
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('bandeja.pendientes', compact('documentos'));
    }

    /**
     * Documentos en estado Recibido
     * Filtra por responsable actual (última derivación con idUsuarioAsignado)
     */
    public function recibidos()
    {
        $userId = Auth::id();

        $documentos = Correspondencia::whereHas('ultimaDerivacion', fn($q) => $q->where('idUsuarioAsignado', $userId))
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Recibido'))
            ->with(['tipoDocumento', 'estado', 'usuario', 'ultimaDerivacion.departamentoDestino'])
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('bandeja.recibidos', compact('documentos'));
    }

    /**
     * Documentos en estado Atendido
     * Filtra por responsable actual (última derivación con idUsuarioAsignado)
     */
    public function atendidos()
    {
        $userId = Auth::id();

        $documentos = Correspondencia::whereHas('ultimaDerivacion', fn($q) => $q->where('idUsuarioAsignado', $userId))
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Atendido'))
            ->with(['tipoDocumento', 'estado', 'usuario', 'ultimaDerivacion.departamentoDestino'])
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('bandeja.atendidos', compact('documentos'));
    }

    /**
     * Documentos en estado Archivado
     * Filtra por responsable actual (última derivación con idUsuarioAsignado)
     */
    public function archivados()
    {
        $userId = Auth::id();

        $documentos = Correspondencia::whereHas('ultimaDerivacion', fn($q) => $q->where('idUsuarioAsignado', $userId))
            ->whereHas('estado', fn($q) => $q->where('nombre', 'Archivado'))
            ->with(['tipoDocumento', 'estado', 'usuario', 'ultimaDerivacion.departamentoDestino'])
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('bandeja.archivados', compact('documentos'));
    }
}
