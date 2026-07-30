<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnuncioRequest;
use App\Models\Anuncio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnuncioController extends Controller
{
    private const ALLOWED_ATTACHMENT_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
    ];

    private const ALLOWED_ATTACHMENT_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

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
            $this->guardarArchivo($request->file('archivo_pdf'), $anuncio);
        }

        return redirect()
            ->route('admin.anuncios.index')
            ->with('success', 'Anuncio publicado correctamente. Usuarios y administradores lo veran al ingresar.');
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

    private function guardarArchivo($file, Anuncio $anuncio): void
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($file->getRealPath());

        if (!$this->archivoPermitido($file, $mimeReal)) {
            return;
        }

        // Guardar con nombre basado en ID y título del anuncio para fácil identificación
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($anuncio->titulo, 0, 30));
        $nombreArchivo = 'anuncio_' . $anuncio->idAnuncio . '_' . $nombreLimpio . '.' . $file->getClientOriginalExtension();
        $ruta = $file->storeAs('anuncios', $nombreArchivo, 'local');

        $anuncio->update([
            'archivo_pdf' => $file->getClientOriginalName(),
            'ruta_pdf' => $ruta,
            'mime_type' => $mimeReal,
            'tamano_archivo' => $file->getSize(),
        ]);
    }

    private function archivoPermitido($file, string $mimeReal): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return in_array($extension, self::ALLOWED_ATTACHMENT_EXTENSIONS, true)
            && in_array($mimeReal, self::ALLOWED_ATTACHMENT_MIMES, true);
    }
}
