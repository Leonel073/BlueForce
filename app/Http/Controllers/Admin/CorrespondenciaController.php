<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\NivelUrgencia;
use App\Models\User;

class CorrespondenciaController extends Controller
{
    public function index(Request $request)
    {
        // QUERY BASE
        $query = Correspondencia::with([
            'usuario',
            'estado',
            'urgencia',
            'tipoDocumento',
            'seguimientos'
        ]);

        /*
        |--------------------------------------------------------------------------
        | BUSCADOR GENERAL
        |--------------------------------------------------------------------------
        */

        if ($request->buscar) {

            $query->where(function ($q) use ($request) {

                $q->where('cite', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('asunto', 'LIKE', '%' . $request->buscar . '%');

            });

        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO ESTADO
        |--------------------------------------------------------------------------
        */

        if ($request->estado) {

            $query->where(
                'idEstado',
                $request->estado
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO URGENCIA
        |--------------------------------------------------------------------------
        */

        if ($request->urgencia) {

            $query->where(
                'idUrgencia',
                $request->urgencia
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO USUARIO
        |--------------------------------------------------------------------------
        */

        if ($request->usuario) {

            $query->where(
                'idUsuario',
                $request->usuario
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO FECHA
        |--------------------------------------------------------------------------
        */

        if ($request->fecha_inicio && $request->fecha_fin) {

            $query->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER DATOS
        |--------------------------------------------------------------------------
        */

        $documentos = $query
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | DATOS PARA FILTROS
        |--------------------------------------------------------------------------
        */

        $estados = EstadoDocumento::all();
        $urgencias = NivelUrgencia::all();
        $usuarios = User::all();

        /*
        |--------------------------------------------------------------------------
        | ESTADÍSTICAS
        |--------------------------------------------------------------------------
        */

        $totalDocumentos = Correspondencia::count();

        $totalUrgentes = Correspondencia::where(
            'idUrgencia',
            1
        )->count();

        $totalRevision = Correspondencia::where(
            'idEstado',
            3
        )->count();

        return view(
            'admin.correspondencia.index',
            compact(
                'documentos',
                'estados',
                'urgencias',
                'usuarios',
                'totalDocumentos',
                'totalUrgentes',
                'totalRevision'
            )
        );
    }

    public function show($id)
{
    $documento = Correspondencia::with([
        'usuario',
        'estado',
        'urgencia',
        'tipoDocumento',
        'remitente',
        'seguimientos'
    ])->findOrFail($id);

    return view(
        'admin.correspondencia.show',
        compact('documento')
    );
}
}