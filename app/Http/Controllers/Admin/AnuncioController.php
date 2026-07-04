<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnuncioRequest;
use App\Models\Anuncio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnuncioController extends Controller
{
    public function index()
    {
        $anuncios = Anuncio::with('creador')
            ->withCount('vistas')
            ->orderByDesc('fechaCreacion')
            ->paginate(15);

        return view('admin.anuncios.index', compact('anuncios'));
    }

    public function create()
    {
        return view('admin.anuncios.create');
    }

    public function store(StoreAnuncioRequest $request)
    {
        $data = [
            'titulo' => $request->titulo,
            'asunto' => $request->asunto,
            'activo' => true,
            'idUsuarioCreador' => Auth::id(),
            'fechaCreacion' => now(),
        ];

        $anuncio = Anuncio::create($data);

        if ($request->hasFile('archivo_pdf')) {
            $this->guardarPdf($request->file('archivo_pdf'), $anuncio);
        }

        return redirect()
            ->route('admin.anuncios.index')
            ->with('success', 'Anuncio publicado correctamente. Todos los usuarios lo verán al ingresar.');
    }

    public function show(int $id)
    {
        $anuncio = Anuncio::with(['creador', 'vistas.usuario.persona'])
            ->withCount('vistas')
            ->findOrFail($id);

        return view('admin.anuncios.show', compact('anuncio'));
    }

    public function toggle(int $id)
    {
        $anuncio = Anuncio::findOrFail($id);
        $anuncio->update(['activo' => !$anuncio->activo]);

        $estado = $anuncio->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Anuncio {$estado} correctamente.");
    }

    public function destroy(int $id)
    {
        $anuncio = Anuncio::findOrFail($id);

        if ($anuncio->ruta_pdf && Storage::disk('local')->exists($anuncio->ruta_pdf)) {
            Storage::disk('local')->delete($anuncio->ruta_pdf);
        }

        $anuncio->delete();

        return redirect()
            ->route('admin.anuncios.index')
            ->with('success', 'Anuncio eliminado correctamente.');
    }

    private function guardarPdf($file, Anuncio $anuncio): void
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($file->getRealPath());

        if ($mimeReal !== 'application/pdf') {
            return;
        }

        $nombreUnico = 'anuncio_' . $anuncio->idAnuncio . '_' . uniqid() . '.pdf';
        $ruta = $file->storeAs('anuncios', $nombreUnico, 'local');

        $anuncio->update([
            'archivo_pdf' => $file->getClientOriginalName(),
            'ruta_pdf' => $ruta,
            'mime_type' => $mimeReal,
            'tamano_archivo' => $file->getSize(),
        ]);
    }
}
