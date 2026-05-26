<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\Derivacion;
use App\Models\Seguimiento;
use Illuminate\Support\Facades\DB;
use Exception;

class RecibidasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BANDEJA RECIBIDAS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $documentos = Correspondencia::with([

            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',
            'ultimaDerivacion.departamentoOrigen',
            'ultimaDerivacion.departamentoDestino',

        ])
        ->orderByDesc('idDocumento')
        ->paginate(10)
        ->withQueryString();

        return view(
            'recibidas.index',
            compact('documentos')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RECIBIR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function recibir($id) {
    try {
        DB::transaction(function () use ($id) {
            $derivacion = Derivacion::where('idDocumento', $id)
                ->orderByDesc('orden')
                ->lockForUpdate()  // ← Evita race conditions
                ->first();
            
            if (!$derivacion) {
                throw new Exception('El documento no tiene derivación.');
            }
            
            $derivacion->fechaRecepcion = now();
            $derivacion->save();
            
            Seguimiento::create([
                'idDocumento' => $id,
                'fecha' => now(),
                'ubicacion' => 'Documento recibido',
                'idEstado' => 2,
                'activo' => true,
            ]);
        });
        
        return back()->with('success', 'Documento recibido correctamente.');
    } catch (Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function finalizar($id) {
    try {
        DB::transaction(function () use ($id) {
            $documento = Correspondencia::findOrFail($id);
            
            // Validar que está en estado correcto
            if ($documento->idEstado !== 2) {  // No está en tránsito
                throw new Exception('Solo documentos en tránsito pueden finalizarse.');
            }
            
            $documento->idEstado = 3;  // Finalizado
            $documento->save();
            
            Seguimiento::create([
                'idDocumento' => $id,
                'fecha' => now(),
                'ubicacion' => 'Documento finalizado',
                'idEstado' => 3,
                'activo' => true,
            ]);
        });
        
        return back()->with('success', 'Documento finalizado correctamente.');
    } catch (Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
}