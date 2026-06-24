<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use App\Models\EstadoTransicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EstadoTransicionController extends Controller
{
    /**
     * Obtener el historial completo de transiciones de un documento
     * GET /api/correspondencia/{id}/historial-transiciones
     */
    public function obtenerHistorial($idDocumento)
    {
        try {
            $correspondencia = Correspondencia::findOrFail($idDocumento);

            $transiciones = $correspondencia->obtenerHistorialTransiciones();

            return response()->json([
                'success' => true,
                'data' => $transiciones->map(function ($transicion) {
                    return [
                        'idTransicion' => $transicion->idTransicion,
                        'accion' => $transicion->accion,
                        'accionLegible' => $transicion->accion_legible,
                        'estadoAnterior' => $transicion->estadoAnterior?->nombre,
                        'estadoNuevo' => $transicion->estadoNuevo?->nombre,
                        'usuario' => $transicion->usuario?->name,
                        'fecha' => $transicion->fecha_formateada,
                        'observacion' => $transicion->observacion,
                        'resumen' => $transicion->resumen,
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error al obtener historial: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Cambiar documento a estado RECIBIDO
     * POST /api/correspondencia/{id}/recibir
     */
    public function recibir(Request $request, $idDocumento)
    {
        try {
            $correspondencia = Correspondencia::findOrFail($idDocumento);

            // Validar que puede recibirse
            if (!$correspondencia->puedeRecibirse()) {
                return response()->json([
                    'success' => false,
                    'message' => "El documento no puede ser recibido desde el estado '{$correspondencia->estado?->nombre}'",
                ], 400);
            }

            // Cambiar estado
            $observacion = $request->input('observacion', 'Recepción registrada');
            $correspondencia->cambiarARecibido($observacion);

            return response()->json([
                'success' => true,
                'message' => 'Documento marcado como RECIBIDO',
                'data' => [
                    'idDocumento' => $correspondencia->idDocumento,
                    'estado' => $correspondencia->estado->nombre,
                    'fecha' => now()->format('d/m/Y H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al recibir documento: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => "Error al recibir documento: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Cambiar documento a estado ATENDIDO
     * POST /api/correspondencia/{id}/atender
     */
    public function atender(Request $request, $idDocumento)
    {
        try {
            $correspondencia = Correspondencia::findOrFail($idDocumento);

            // Validar que puede atenderse
            if (!$correspondencia->puedeAtenderse()) {
                return response()->json([
                    'success' => false,
                    'message' => "El documento no puede ser atendido desde el estado '{$correspondencia->estado?->nombre}'",
                ], 400);
            }

            // Cambiar estado
            $observacion = $request->input('observacion', 'Documento atendido');
            $correspondencia->cambiarAAtendido($observacion);

            return response()->json([
                'success' => true,
                'message' => 'Documento marcado como ATENDIDO',
                'data' => [
                    'idDocumento' => $correspondencia->idDocumento,
                    'estado' => $correspondencia->estado->nombre,
                    'fecha' => now()->format('d/m/Y H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al atender documento: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => "Error al atender documento: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Cambiar documento a estado ARCHIVADO
     * POST /api/correspondencia/{id}/archivar
     */
    public function archivar(Request $request, $idDocumento)
    {
        try {
            $correspondencia = Correspondencia::findOrFail($idDocumento);

            // Validar que puede archivarse
            if (!$correspondencia->puedeArchivarse()) {
                return response()->json([
                    'success' => false,
                    'message' => "El documento no puede ser archivado desde el estado '{$correspondencia->estado?->nombre}'",
                ], 400);
            }

            // Cambiar estado
            $observacion = $request->input('observacion', 'Documento archivado');
            $correspondencia->cambiarAArchivado($observacion);

            return response()->json([
                'success' => true,
                'message' => 'Documento marcado como ARCHIVADO',
                'data' => [
                    'idDocumento' => $correspondencia->idDocumento,
                    'estado' => $correspondencia->estado->nombre,
                    'fecha' => now()->format('d/m/Y H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al archivar documento: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => "Error al archivar documento: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Obtener transiciones permitidas para un documento
     * GET /api/correspondencia/{id}/transiciones-permitidas
     */
    public function obtenerTransicionesPermitidas($idDocumento)
    {
        try {
            $correspondencia = Correspondencia::findOrFail($idDocumento);

            $transiciones = $correspondencia->obtenerTransicionesPermitidas();

            return response()->json([
                'success' => true,
                'estadoActual' => $correspondencia->estado?->nombre,
                'transiciones' => $transiciones,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error: {$e->getMessage()}",
            ], 500);
        }
    }
}
