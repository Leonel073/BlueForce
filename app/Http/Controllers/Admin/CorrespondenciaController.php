<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\NivelUrgencia;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Derivacion;
use App\Models\Seguimiento;
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
                    'seguimientos',
                    'derivaciones.departamentoOrigen',
                    'derivaciones.departamentoDestino',
                    'derivaciones.usuarioAsignado',
                ])->findOrFail($id);
                $departamentos = Departamento::all();

                $usuarios = User::where('activo', 1)->get();

            return view(
                'admin.correspondencia.show',
                compact(
            'documento',
            'departamentos',
            'usuarios'
        )
            );


            
        }
        public function derivar(Request $request, $id)
{
    $request->validate([

        'idDepartamentoDestino' => 'required',

    ]);

    $documento = Correspondencia::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | OBTENER ÚLTIMO ORDEN
    |--------------------------------------------------------------------------
    */

    $ultimoOrden = Derivacion::where(
        'idDocumento',
        $id
    )->max('orden');

    $nuevoOrden = $ultimoOrden
        ? $ultimoOrden + 1
        : 1;

    /*
    |--------------------------------------------------------------------------
    | CREAR DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    Derivacion::create([

        'idDocumento' => $id,

        'orden' => $nuevoOrden,

        'idDepartamentoOrigen' => 1,

        'idDepartamentoDestino'
            => $request->idDepartamentoDestino,

        'idUsuarioAsignado'
            => $request->idUsuarioAsignado,

        'instruccion'
            => $request->instruccion,

        'fechaEnvio' => now(),

        'activo' => true,

    ]);

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR SEGUIMIENTO
    |--------------------------------------------------------------------------
    */

    Seguimiento::create([

        'idDocumento' => $id,

        'fecha' => now(),

        'ubicacion'
            => 'Documento derivado',

        'idEstado'
            => $documento->idEstado,

        'activo' => true,

    ]);

    return back()->with(
        'success',
        'Documento derivado correctamente.'
    );
}
}