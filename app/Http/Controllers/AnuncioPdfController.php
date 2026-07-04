<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnuncioPdfController extends Controller
{
    public function previsualizar(int $id)
    {
        $anuncio = Anuncio::findOrFail($id);
        $this->verificarAcceso($anuncio);
        $this->verificarArchivo($anuncio);

        $contenido = Storage::disk('local')->get($anuncio->ruta_pdf);

        return response($contenido, 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="' . ($anuncio->archivo_pdf ?? 'anuncio.pdf') . '"'
            )
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'private, no-store');
    }

    public function descargar(int $id)
    {
        $anuncio = Anuncio::findOrFail($id);
        $this->verificarAcceso($anuncio);
        $this->verificarArchivo($anuncio);

        return Storage::disk('local')->download(
            $anuncio->ruta_pdf,
            $anuncio->archivo_pdf ?? 'anuncio.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    private function verificarAcceso(Anuncio $anuncio): void
    {
        if (!Auth::check()) {
            abort(401, 'Debe estar autenticado.');
        }

        if (Auth::user()->isAdmin()) {
            return;
        }

        if (!$anuncio->activo) {
            abort(403, 'Este anuncio no está disponible.');
        }
    }

    private function verificarArchivo(Anuncio $anuncio): void
    {
        if (!$anuncio->tienePdf()) {
            abort(404, 'Este anuncio no tiene un PDF adjunto.');
        }

        if (!Storage::disk('local')->exists($anuncio->ruta_pdf)) {
            abort(404, 'El archivo PDF no se encuentra en el servidor.');
        }

        $ruta = Storage::disk('local')->path($anuncio->ruta_pdf);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($ruta);

        if ($mimeReal !== 'application/pdf') {
            abort(403, 'El archivo almacenado no es un PDF válido.');
        }
    }
}
