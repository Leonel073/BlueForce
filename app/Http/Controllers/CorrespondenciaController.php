<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Correspondencia;
use App\Models\EstadoDocumento;
use App\Models\NivelUrgencia;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Derivacion;
use App\Models\Seguimiento;

class CorrespondenciaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX GENERAL
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | QUERY BASE
        |--------------------------------------------------------------------------
        */

        $query = Correspondencia::with([

            'tipoDocumento',
            'estado',
            'urgencia',
            'remitente',
            'derivaciones.departamentoDestino'

        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        if ($request->buscar) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'cite',
                    'LIKE',
                    '%' . $request->buscar . '%'
                )

                ->orWhere(
                    'asunto',
                    'LIKE',
                    '%' . $request->buscar . '%'
                );

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
        | ADMIN VE TODO
        |--------------------------------------------------------------------------
        */

        if ($usuario->idRol == 1) {

            $documentos = $query
                ->orderByDesc('fecha')
                ->get();

        }

        /*
        |--------------------------------------------------------------------------
        | USER VE DOCUMENTOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        else {

            $documentos = $query
                ->where('activo', 1)
                ->orderByDesc('fecha')
                ->get();

        }

        /*
        |--------------------------------------------------------------------------
        | CONTADORES
        |--------------------------------------------------------------------------
        */

        $totalDocumentos =
            $documentos->count();

        $pendientes =
            $documentos
                ->filter(function ($doc) {

                    return strtolower(
                        $doc->estado->nombre ?? ''
                    ) == 'pendiente';

                })
                ->count();

        $finalizados =
            $documentos
                ->filter(function ($doc) {

                    return strtolower(
                        $doc->estado->nombre ?? ''
                    ) == 'finalizado';

                })
                ->count();

        $urgentes =
            $documentos
                ->filter(function ($doc) {

                    return str_contains(
                        strtolower(
                            $doc->urgencia->nombre ?? ''
                        ),
                        'alta'
                    );

                })
                ->count();

        /*
        |--------------------------------------------------------------------------
        | DATOS FILTROS
        |--------------------------------------------------------------------------
        */

        $estados = EstadoDocumento::all();

        $urgencias = NivelUrgencia::all();

        /*
        |--------------------------------------------------------------------------
        | RETORNO
        |--------------------------------------------------------------------------
        */

        return view(
            'correspondencia.index',
            compact(
                'documentos',
                'totalDocumentos',
                'pendientes',
                'finalizados',
                'urgentes',
                'estados',
                'urgencias'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $documento = Correspondencia::with([

            'usuario',
            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',

            'derivaciones.departamentoOrigen',
            'derivaciones.departamentoDestino',
           

            'seguimientos'

        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMA DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        $ultimaDerivacion = $documento->derivaciones
            ->sortByDesc('orden')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTOS
        |--------------------------------------------------------------------------
        */

        $departamentos = Departamento::where(
            'activo',
            1
        )->get();

        /*
        |--------------------------------------------------------------------------
        | USUARIOS
        |--------------------------------------------------------------------------
        */

        $usuarios = User::where(
            'activo',
            1
        )->get();

        /*
        |--------------------------------------------------------------------------
        | RETORNO
        |--------------------------------------------------------------------------
        */

        return view(
            'correspondencia.show',
            compact(
                'documento',
                'ultimaDerivacion',
                'departamentos',
                'usuarios'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DERIVAR
    |--------------------------------------------------------------------------
    */

    public function derivar(Request $request, $id)
    {
        $request->validate([

            'idDepartamentoDestino'
                => 'required|exists:DEPARTAMENTO,idDepartamento',

        ]);

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTO
        |--------------------------------------------------------------------------
        */

        $documento = Correspondencia::with([
            'derivaciones'
        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR FINALIZADO
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                $documento->estado->nombre ?? ''
            ) == 'finalizado'
        ) {

            return back()->with(
                'error',
                'El documento ya fue finalizado.'
            );

        }

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
        | ÚLTIMA DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        $ultimaDerivacion = Derivacion::where(
            'idDocumento',
            $id
        )
        ->orderByDesc('orden')
        ->first();

        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTO ORIGEN
        |--------------------------------------------------------------------------
        */

        $departamentoOrigen =
            $ultimaDerivacion
            ? $ultimaDerivacion->idDepartamentoDestino
            : 1;

        /*
        |--------------------------------------------------------------------------
        | CREAR DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        Derivacion::create([

            'idDocumento' => $id,

            'orden' => $nuevoOrden,

            'idDepartamentoOrigen'
                => $departamentoOrigen,

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

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Documento derivado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function finalizar($id)
    {
        /*
        |--------------------------------------------------------------------------
        | DOCUMENTO
        |--------------------------------------------------------------------------
        */

        $documento = Correspondencia::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | ESTADO FINALIZADO
        |--------------------------------------------------------------------------
        */

        $estadoFinalizado = EstadoDocumento::where(
            'nombre',
            'Finalizado'
        )->first();

        if (!$estadoFinalizado) {

            return back()->with(
                'error',
                'No existe el estado Finalizado.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR DOCUMENTO
        |--------------------------------------------------------------------------
        */

        $documento->update([

            'idEstado'
                => $estadoFinalizado->idEstado

        ]);

        /*
        |--------------------------------------------------------------------------
        | SEGUIMIENTO
        |--------------------------------------------------------------------------
        */

        Seguimiento::create([

            'idDocumento'
                => $documento->idDocumento,

            'fecha'
                => now(),

            'ubicacion'
                => 'Documento finalizado',

            'idEstado'
                => $estadoFinalizado->idEstado,

            'activo'
                => true,

        ]);

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Documento finalizado correctamente.'
        );
    }
}