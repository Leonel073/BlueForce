<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Correspondencia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

/**
 * DocumentoPdfController
 *
 * Descarga y previsualización segura de PDFs.
 * - Archivos en disco privado (local), nunca en public/
 * - Verificación MIME real con finfo antes de servir
 * - Auditoría de descarga registrada
 */
class DocumentoPdfController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DESCARGAR PDF
    | GET /documentos/{id}/pdf/descargar
    |--------------------------------------------------------------------------
    */

    public function descargar(int $id)
    {
        $documento = Correspondencia::findOrFail($id);

        $this->verificarAcceso($documento);
        $this->verificarArchivoExiste($documento);

        $nombreDescarga = $documento->archivo_pdf
            ?? ('documento_' . $documento->idDocumento . '.pdf');

        // Auditoría: registrar descarga
        $this->registrarAuditoria($documento, 'DESCARGA_PDF');

        return Storage::disk('local')->download(
            $documento->ruta_pdf,
            $nombreDescarga,
            ['Content-Type' => 'application/pdf']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PREVISUALIZAR PDF (inline en el navegador)
    | GET /documentos/{id}/pdf/previsualizar
    |--------------------------------------------------------------------------
    */

    public function previsualizar(int $id)
    {
        $documento = Correspondencia::findOrFail($id);

        $this->verificarAcceso($documento);
        $this->verificarArchivoExiste($documento);

        $contenido = Storage::disk('local')->get($documento->ruta_pdf);

        return response($contenido, 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="' . ($documento->archivo_pdf ?? 'documento.pdf') . '"'
            )
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'private, no-store');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS PRIVADOS
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica acceso: admin total, creador, o departamento en derivación activa.
     */
    private function verificarAcceso(Correspondencia $documento): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Debe estar autenticado.');
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($documento->idUsuario === $user->id) {
            return;
        }

        $deptoUsuario = $user->persona?->idDepartamento;

        if ($deptoUsuario) {
            $acceso = $documento->derivaciones()
                ->where('idDepartamentoDestino', $deptoUsuario)
                ->where('activo', true)
                ->exists();

            if ($acceso) {
                return;
            }
        }

        abort(403, 'No tiene permiso para acceder a este archivo.');
    }

    /**
     * Verifica que el archivo exista en disco y sea PDF real.
     */
    private function verificarArchivoExiste(Correspondencia $documento): void
    {
        if (!$documento->tiene_archivo || !$documento->ruta_pdf) {
            abort(404, 'Este documento no tiene un archivo PDF adjunto.');
        }

        if (!Storage::disk('local')->exists($documento->ruta_pdf)) {
            abort(404, 'El archivo PDF no se encuentra en el servidor.');
        }

        $ruta     = Storage::disk('local')->path($documento->ruta_pdf);
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($ruta);

        if ($mimeReal !== 'application/pdf') {
            abort(403, 'El archivo almacenado no es un PDF válido.');
        }
    }

    /**
     * Registra una acción PDF en la tabla AUDITORIA.
     */
    private function registrarAuditoria(Correspondencia $documento, string $accion): void
    {
        try {
            Auditoria::create([
                'idUsuario'      => Auth::id(),
                'modelo'         => 'Correspondencia',
                'idRegistro'     => $documento->idDocumento,
                'accion'         => 'UPDATE',
                'datosAnteriores'=> null,
                'datosNuevos'    => [
                    'accion_pdf'   => $accion,
                    'archivo'      => $documento->archivo_pdf,
                    'documento'    => $documento->cite,
                    'fecha'        => now()->toDateTimeString(),
                ],
                'ip'        => request()->ip(),
                'navegador' => request()->userAgent(),
                'ruta'      => request()->getRequestUri(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Auditoría PDF fallida: ' . $e->getMessage());
        }
    }
}
