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
        | FILTRO DE SEGURIDAD: Usuario normal solo ve sus documentos
        |--------------------------------------------------------------------------
        */

        if ($usuario->idRol != 1) {
            $query->where('idUsuario', $usuario->id);
        }

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

        if ($usuario->idRol != 1) {

            $query->where('activo', 1);

        }

        $totalDocumentos = (clone $query)->count();

        $pendientes = (clone $query)

            ->whereHas('estado', function ($q) {

                $q->whereRaw('LOWER(nombre) = ?', ['pendiente']);

            })

            ->count();

        $finalizados = (clone $query)

            ->whereHas('estado', function ($q) {

                $q->whereRaw('LOWER(nombre) = ?', ['finalizado']);

            })

            ->count();

        $urgentes = (clone $query)

            ->whereHas('urgencia', function ($q) {

                $q->whereRaw('LOWER(nombre) LIKE ?', ['%alta%']);

            })

            ->count();

        $documentos = (clone $query)

            ->orderByDesc('fecha')

            ->paginate(10)

            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | DATOS FILTROS
        |--------------------------------------------------------------------------
        */

        $estados = EstadoDocumento::all();

        $urgencias = NivelUrgencia::all();

        /*
        |--------------------------------------------------------------------------
        | RETORNO - VISTA SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        $vista = $usuario->idRol == 1 
            ? 'admin.correspondencia.index' 
            : 'correspondencia.index';

        return view(
            $vista,
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
            'derivaciones.usuarioEnvio:id,name',

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
        | RETORNO - VISTA SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        $vista = Auth::user()->idRol == 1 
            ? 'admin.correspondencia.show' 
            : 'correspondencia.show';

        return view(
            $vista,
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
            'idUsuarioAsignado'
                => 'nullable|exists:users,id',

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
        | VALIDACIÓN DE RESPONSABILIDAD - Solo responsable actual puede derivar
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();
        if ($user->idRol != 1) {
            // Usuario normal: verificar que sea el responsable actual
            $ultimaDerivacion = Derivacion::where('idDocumento', $id)
                ->orderByDesc('orden')
                ->first();

            if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
                return back()->with('error', 'No tiene permisos para derivar este documento.');
            }

            if ($ultimaDerivacion->idUsuarioAsignado != $user->id) {
                return back()->with('error', 'Este documento ya fue asignado a otro usuario y ya no se encuentra bajo su responsabilidad.');
            }
        }

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
        | VALIDACIONES DE DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        // Validar que departamento destino es diferente del origen
        if ($departamentoOrigen == $request->idDepartamentoDestino) {
            return back()->with('error', 'No puede derivar un documento al mismo departamento.');
        }

        // Validar que idUsuarioAsignado (si se proporciona) existe y es válido
        if ($request->filled('idUsuarioAsignado')) {
            $usuarioDestino = \App\Models\User::find($request->idUsuarioAsignado);
            
            if (!$usuarioDestino) {
                return back()->with('error', 'El usuario destino no existe.');
            }

            if (!$usuarioDestino->activo) {
                return back()->with('error', 'El usuario destino está inactivo.');
            }

            // Validar que el usuario no se derive a sí mismo
            if ($usuarioDestino->id == $user->id) {
                return back()->with('error', 'No puede derivar un documento a sí mismo.');
            }
        }

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

            'idUsuarioEnvio'
                => Auth::id(),

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
        | VALIDACIÓN DE PROPIEDAD - Solo admin puede finalizar en correspondencia admin
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();
        if ($user->idRol != 1) {
            return back()->with('error', 'No tiene permisos para finalizar este documento.');
        }

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