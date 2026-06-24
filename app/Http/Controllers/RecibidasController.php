<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\EstadoDocumento;
use App\Models\Seguimiento;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * RecibidasController
 *
 * Gestiona el flujo de recepción y atención de documentos derivados.
 *
 * Flujo oficial de estados:
 *   Pendiente → [Recibir] → Recibido → [Atender] → Atendido → [Archivar] → Archivado
 */
class RecibidasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HELPERS PRIVADOS
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el ID de un estado por nombre exacto.
     * Si no existe, lanza excepción descriptiva.
     */
    private function idEstado(string $nombre): int
    {
        $estado = EstadoDocumento::where('nombre', $nombre)->first();

        if (!$estado) {
            throw new Exception(
                "El estado '{$nombre}' no existe en la base de datos. "
                . "Ejecute: php artisan db:seed --class=EstadoDocumentoSeeder"
            );
        }

        return $estado->idEstado;
    }

    /*
    |--------------------------------------------------------------------------
    | BANDEJA RECIBIDAS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Correspondencia::with([
            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',
            'ultimaDerivacion.departamentoOrigen',
            'ultimaDerivacion.departamentoDestino',
        ]);

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('idEstado', $request->estado);
        }

        $documentos = $query
            ->orderByDesc('idDocumento')
            ->paginate(10)
            ->withQueryString();

        $estados = EstadoDocumento::orderBy('nombre')->get();

        return view('recibidas.index', compact('documentos', 'estados'));
    }

    /*
    |--------------------------------------------------------------------------
    | RECIBIR DOCUMENTO
    | Pendiente → Recibido
    |--------------------------------------------------------------------------
    */

    public function recibir(int $id)
    {
        try {
            DB::transaction(function () use ($id) {

                $documento = Correspondencia::findOrFail($id);
                $idRecibido = $this->idEstado('Recibido');

                // Marcar derivación como recibida
                $derivacion = Derivacion::where('idDocumento', $id)
                    ->orderByDesc('orden')
                    ->lockForUpdate()
                    ->first();

                if ($derivacion && !$derivacion->fechaRecepcion) {
                    $derivacion->fechaRecepcion = now();
                    $derivacion->save();
                }

                // Cambiar estado
                $documento->update(['idEstado' => $idRecibido]);

                // Registrar seguimiento con usuario, fecha y hora
                Seguimiento::create([
                    'idDocumento' => $id,
                    'fecha'       => now(),
                    'ubicacion'   => 'Documento recibido por ' . Auth::user()->name,
                    'idEstado'    => $idRecibido,
                    'activo'      => true,
                ]);
            });

            return back()->with('success', 'Documento marcado como Recibido.');

        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ATENDER DOCUMENTO
    | Recibido → Atendido
    |--------------------------------------------------------------------------
    */

    public function atender(int $id)
    {
        try {
            DB::transaction(function () use ($id) {

                $documento = Correspondencia::findOrFail($id);
                $idRecibido = $this->idEstado('Recibido');
                $idAtendido = $this->idEstado('Atendido');

                if ($documento->idEstado !== $idRecibido) {
                    throw new Exception(
                        'Solo se pueden atender documentos en estado Recibido. '
                        . 'Estado actual: ' . ($documento->estado->nombre ?? 'desconocido')
                    );
                }

                $documento->update(['idEstado' => $idAtendido]);

                Seguimiento::create([
                    'idDocumento' => $id,
                    'fecha'       => now(),
                    'ubicacion'   => 'Documento atendido por ' . Auth::user()->name,
                    'idEstado'    => $idAtendido,
                    'activo'      => true,
                ]);
            });

            return back()->with('success', 'Documento marcado como Atendido.');

        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVAR DOCUMENTO
    | Atendido → Archivado
    |--------------------------------------------------------------------------
    */

    public function archivar(int $id)
    {
        try {
            DB::transaction(function () use ($id) {

                $documento = Correspondencia::findOrFail($id);
                $idAtendido  = $this->idEstado('Atendido');
                $idArchivado = $this->idEstado('Archivado');

                if ($documento->idEstado !== $idAtendido) {
                    throw new Exception(
                        'Solo se pueden archivar documentos en estado Atendido. '
                        . 'Estado actual: ' . ($documento->estado->nombre ?? 'desconocido')
                    );
                }

                $documento->update(['idEstado' => $idArchivado]);

                Seguimiento::create([
                    'idDocumento' => $id,
                    'fecha'       => now(),
                    'ubicacion'   => 'Documento archivado por ' . Auth::user()->name,
                    'idEstado'    => $idArchivado,
                    'activo'      => true,
                ]);
            });

            return back()->with('success', 'Documento archivado correctamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR (alias de Archivar — mantiene compatibilidad con rutas antiguas)
    |--------------------------------------------------------------------------
    */

    public function finalizar(int $id)
    {
        return $this->archivar($id);
    }
}
