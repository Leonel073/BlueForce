<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Correspondencia;
use App\Models\Departamento;
use App\Models\Derivacion;
use App\Models\EstadoDocumento;
use App\Models\NivelUrgencia;
use App\Models\Persona;
use App\Models\Seguimiento;
use App\Models\User;

class EnvioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS ENVIADOS
    |--------------------------------------------------------------------------
    */

public function index(Request $request)
{
    $user = Auth::user();

    $query = Derivacion::with([

        'documento.estado',
        'documento.urgencia',
        'documento.remitente',

        'departamentoOrigen',
        'departamentoDestino',

    ])
        ->orderByDesc('fechaEnvio');

    /*
    |--------------------------------------------------------------------------
    | FILTRO DE SEGURIDAD: Usuario normal solo ve derivaciones que envió
    |--------------------------------------------------------------------------
    */

    if ($user->idRol != 1) {
        $query->where('idUsuarioEnvio', $user->id);
    }

    if ($request->filled('buscar')) {

        $b = trim($request->buscar);

        $query->whereHas('documento', function ($q) use ($b) {

            $q->where('cite', 'LIKE', '%' . $b . '%')
                ->orWhere('asunto', 'LIKE', '%' . $b . '%');

        });

    }

    if ($request->filled('idDepartamentoDestino')) {

        $query->where(
            'idDepartamentoDestino',
            $request->idDepartamentoDestino
        );

    }

    if ($request->filled('idDepartamentoOrigen')) {

        $query->where(
            'idDepartamentoOrigen',
            $request->idDepartamentoOrigen
        );

    }

    if ($request->filled('transito')) {

        if ($request->transito === '1') {

            $query->whereNull('fechaRecepcion');

        } elseif ($request->transito === '0') {

            $query->whereNotNull('fechaRecepcion');

        }

    }

    if ($request->filled('idEstado')) {

        $query->whereHas('documento', function ($q) use ($request) {

            $q->where('idEstado', $request->idEstado);

        });

    }

    if ($request->filled('idUrgencia')) {

        $query->whereHas('documento', function ($q) use ($request) {

            $q->where('idUrgencia', $request->idUrgencia);

        });

    }

    $totalDocumentos = (clone $query)->count();

    $enTransito = (clone $query)->whereNull('fechaRecepcion')->count();

    $recibidos = (clone $query)->whereNotNull('fechaRecepcion')->count();

    $derivaciones = $query->paginate(10)->withQueryString();

    $departamentos = Departamento::where('activo', true)
        ->orderBy('nombre')
        ->get();

    $estados = EstadoDocumento::orderBy('nombre')->get();

    $urgencias = NivelUrgencia::orderBy('nombre')->get();

    $vista = $user->idRol == 1 
        ? 'admin.envios.index' 
        : 'user.envios.index';

    return view(
        $vista,
        compact(
            'derivaciones',
            'totalDocumentos',
            'enTransito',
            'recibidos',
            'departamentos',
            'estados',
            'urgencias'
        )
    );
}

    /*
    |--------------------------------------------------------------------------
    | BANDEJA GENERAL
    |--------------------------------------------------------------------------
    */

    public function bandeja(Request $request)
    {
        $user = Auth::user();

        $query = Correspondencia::with([
            'estado',
            'urgencia',
            'tipoDocumento',
            'remitente',
            'derivaciones.departamentoDestino',
        ])->orderByDesc('idDocumento');

        /*
        |--------------------------------------------------------------------------
        | FILTRO DE SEGURIDAD: Usuario normal solo ve sus documentos en bandeja
        |--------------------------------------------------------------------------
        */

        if ($user->idRol != 1) {
            $query->whereHas('ultimaDerivacion',
                fn($q) => $q->where('idUsuarioAsignado', $user->id)
            );
        }

        if ($request->filled('buscar')) {

            $b = trim($request->buscar);

            $query->where(function ($q) use ($b) {

                $q->where('cite', 'LIKE', '%' . $b . '%')
                ->orWhere('asunto', 'LIKE', '%' . $b . '%');

            });
        }

        if ($request->filled('idEstado')) {

            $query->where('idEstado', $request->idEstado);

        }

        if ($request->filled('idUrgencia')) {

            $query->where('idUrgencia', $request->idUrgencia);

        }

        if ($request->filled('idDepartamentoDestino')) {

            $idDep = $request->idDepartamentoDestino;

            $query->whereHas('derivaciones', function ($q) use ($idDep) {

                $q->where('idDepartamentoDestino', $idDep);

            });
        }

        $documentos = $query->paginate(10)->withQueryString();

        $estados = EstadoDocumento::orderBy('nombre')->get();

        $urgencias = NivelUrgencia::orderBy('nombre')->get();

        $departamentos = Departamento::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $viewName = $user->idRol == 1
            ? 'admin.envios.bandeja'
            : 'user.envios.bandeja';

        return view(
            $viewName,
            compact(
                'documentos',
                'estados',
                'urgencias',
                'departamentos',
                'user'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DERIVAR
    |--------------------------------------------------------------------------
    */

    public function derivarForm($id)
    {
        $documento = Correspondencia::with([

            'derivaciones.departamentoOrigen',
            'derivaciones.departamentoDestino',
            'derivaciones.usuarioAsignado.persona',

        ])->findOrFail($id);

        $departamentos = Departamento::all();

        $vista = Auth::user()->idRol == 1 
            ? 'admin.envios.derivar' 
            : 'user.envios.derivar';

        return view(
            $vista,
            compact(
                'documento',
                'departamentos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DERIVAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

   public function derivar(Request $request, $id)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'idDepartamentoDestino' =>
                'required|exists:DEPARTAMENTO,idDepartamento',

            'idPersonaResponsable' =>
                'nullable|exists:PERSONA,idPersona',

            'instruccion' =>
                'nullable|string|max:1000',

        ]);

        /*
        |--------------------------------------------------------------------------
        | OBTENER DOCUMENTO
        |--------------------------------------------------------------------------
        */

        $documento = Correspondencia::with('derivaciones')
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN: Usuario debe ser responsable actual (excepto admin)
        |--------------------------------------------------------------------------
        */

        if (Auth::user()->idRol != 1) {
            $ultimaDerivacion = Derivacion::where('idDocumento', $id)
                ->orderByDesc('orden')
                ->first();

            if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
                return back()->with('error', 'No tiene permisos para derivar este documento.');
            }

            if ($ultimaDerivacion->idUsuarioAsignado != Auth::id()) {
                return back()->with('error', 'Este documento ya fue asignado a otro usuario y ya no se encuentra bajo su responsabilidad.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER ÚLTIMA DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        $ultimaDerivacion = $documento->derivaciones
            ->sortByDesc('orden')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN: Auto-derivación bloqueada (no puede derivarse a sí mismo)
        |--------------------------------------------------------------------------
        */

        if ($request->filled('idPersonaResponsable'))
        {
            $personaResponsable = Persona::where(
                'idPersona',
                $request->idPersonaResponsable
            )
                ->where('idDepartamento', $request->idDepartamentoDestino)
                ->where('tipo', 'INTERNO')
                ->where('activo', true)
                ->first();

            if (!$personaResponsable)
            {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'La persona seleccionada no es un responsable válido del departamento destino.'
                    );
            }

            $idUsuarioAsignado = User::where(
                'idPersona',
                $personaResponsable->idPersona
            )
                ->where('activo', true)
                ->first();

            // ÚNICA RESTRICCIÓN: No puede derivarse a sí mismo
            if ($idUsuarioAsignado && $idUsuarioAsignado->id == Auth::id()) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'No puede derivar un documento a usted mismo.'
                    );
            }

            $idUsuarioAsignado = $idUsuarioAsignado?->id;
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER NUEVO ORDEN
        |--------------------------------------------------------------------------
        */

        $nuevoOrden = $ultimaDerivacion
            ? $ultimaDerivacion->orden + 1
            : 1;

        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTO ORIGEN
        |--------------------------------------------------------------------------
        */

        $departamentoOrigen = $ultimaDerivacion
            ? $ultimaDerivacion->idDepartamentoDestino
            : 1;

        /*
        |--------------------------------------------------------------------------
        | CERRAR DERIVACIÓN ANTERIOR
        |--------------------------------------------------------------------------
        */

        if ($ultimaDerivacion)
        {
            $ultimaDerivacion->update([

                'fechaRecepcion' => now()

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR NUEVA DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        Derivacion::create([

            'idDocumento' =>
                $documento->idDocumento,

            'orden' =>
                $nuevoOrden,

            'idDepartamentoOrigen' =>
                $departamentoOrigen,

            'idDepartamentoDestino' =>
                $request->idDepartamentoDestino,

            'idUsuarioAsignado' =>
                $idUsuarioAsignado,

            'idUsuarioEnvio' =>
                Auth::id(),

            'instruccion' =>
                $request->instruccion,

            'fechaEnvio' =>
                now(),

            'activo' => true,

        ]);

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR ESTADO — Al derivar, el documento queda Pendiente
        |--------------------------------------------------------------------------
        */

        $estadoPendiente = EstadoDocumento::where('nombre', 'Pendiente')->first();

        if ($estadoPendiente) {
            $documento->update([
                'idEstado' => $estadoPendiente->idEstado
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR SEGUIMIENTO
        |--------------------------------------------------------------------------
        */

        Seguimiento::create([

            'idDocumento' =>
                $documento->idDocumento,

            'fecha' =>
                now(),

            'ubicacion' =>
                'Documento derivado a otro departamento',

            'idEstado' =>
                $documento->fresh()->idEstado,

            'activo' => true,

        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('envios.bandeja')
            ->with(
                'success',
                'Documento derivado correctamente.'
            );
    }
    public function finalizar($id)
    {
        /*
        |--------------------------------------------------------------------------
        | OBTENER DOCUMENTO
        |--------------------------------------------------------------------------
        */

        $documento = Correspondencia::findOrFail($id);
        $user = Auth::user();

        /*
        |----------------------------------------------------------------------
        | VALIDACIÓN DE PROPIEDAD - Solo responsable actual puede finalizar
        |----------------------------------------------------------------------
        */

        if ($user->idRol != 1) {
            $ultimaDerivacion = Derivacion::where('idDocumento', $id)
                ->orderByDesc('orden')
                ->first();

            if (!$ultimaDerivacion || !$ultimaDerivacion->idUsuarioAsignado) {
                return back()->with('error', 'No tiene permisos para finalizar este documento.');
            }

            if ($ultimaDerivacion->idUsuarioAsignado != $user->id) {
                return back()->with('error', 'No es el responsable actual de este documento.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BUSCAR ESTADO FINALIZADO
        |--------------------------------------------------------------------------
        */

        $estadoFinalizado = EstadoDocumento::where(
            'nombre',
            'Archivado'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | SI NO EXISTE EL ESTADO
        |--------------------------------------------------------------------------
        */

        if (!$estadoFinalizado) {

            return back()->with(
                'error',
                'No existe el estado FINALIZADO.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR DOCUMENTO
        |--------------------------------------------------------------------------
        */

        $documento->update([

            'idEstado' => $estadoFinalizado->idEstado

        ]);

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR SEGUIMIENTO
        |--------------------------------------------------------------------------
        */

        Seguimiento::create([

            'idDocumento' => $documento->idDocumento,

            'fecha' => now(),

            'ubicacion' => 'Documento finalizado',

            'idEstado' => $estadoFinalizado->idEstado,

            'activo' => true,

        ]);

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR ÚLTIMA DERIVACIÓN
        |--------------------------------------------------------------------------
        */

        $ultimaDerivacion = Derivacion::where(
            'idDocumento',
            $documento->idDocumento
        )
        ->orderByDesc('orden')
        ->first();

        if ($ultimaDerivacion) {

            $ultimaDerivacion->update([

                'fechaRecepcion' => now()

            ]);

        }

        return redirect()
            ->route('envios.bandeja')
            ->with(
                'success',
                'Documento finalizado correctamente.'
            );
    }
}