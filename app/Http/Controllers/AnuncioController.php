<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\AnuncioVisto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnuncioController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $anuncios = Anuncio::activos()
            ->with(['creador', 'vistas' => fn($q) => $q->where('idUsuario', $userId)])
            ->orderByDesc('fechaCreacion')
            ->paginate(10);

        return view('user.anuncios.index', compact('anuncios'));
    }

    public function show(int $id)
    {
        $userId = Auth::id();

        $anuncio = Anuncio::activos()
            ->with(['creador', 'vistas' => fn($q) => $q->where('idUsuario', $userId)])
            ->findOrFail($id);

        $visto = $anuncio->vistas->isNotEmpty();

        return view('user.anuncios.show', compact('anuncio', 'visto'));
    }

    public function marcarVisto(Request $request, int $id)
    {
        $anuncio = Anuncio::activos()->findOrFail($id);

        AnuncioVisto::firstOrCreate(
            [
                'idAnuncio' => $anuncio->idAnuncio,
                'idUsuario' => Auth::id(),
            ],
            ['fechaVisto' => now()]
        );

        $redirect = $request->input('redirect');
        if (!$redirect || !str_starts_with($redirect, url('/'))) {
            $redirect = route('user.anuncios.index');
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect($redirect)
            ->with('success', 'Anuncio marcado como visto.');
    }

    public function pendiente()
    {
        $userId = Auth::id();

        $pendientes = Anuncio::activos()
            ->whereDoesntHave('vistas', fn($q) => $q->where('idUsuario', $userId))
            ->orderBy('fechaCreacion')
            ->get();

        if ($pendientes->isEmpty()) {
            return response()->json([
                'hayPendiente' => false,
                'cantidadPendientes' => 0,
            ]);
        }

        $anuncio = $pendientes->first();

        return response()->json([
            'hayPendiente' => true,
            'cantidadPendientes' => $pendientes->count(),
            'anuncio' => [
                'idAnuncio' => $anuncio->idAnuncio,
                'titulo' => $anuncio->titulo,
                'asunto' => $anuncio->asunto,
                'tienePdf' => $anuncio->tienePdf(),
                'archivoPdf' => $anuncio->archivo_pdf,
                'fechaCreacion' => $anuncio->fechaCreacion->format('d/m/Y H:i'),
                'pdfUrl' => $anuncio->tienePdf()
                    ? route('anuncios.pdf.previsualizar', $anuncio->idAnuncio)
                    : null,
                'marcarVistoUrl' => route('anuncios.marcar-visto', $anuncio->idAnuncio),
                'detalleUrl' => route('user.anuncios.show', $anuncio->idAnuncio),
            ],
        ]);
    }
}
