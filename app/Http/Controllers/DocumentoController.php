<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use App\Models\CorrespondenciaDestinatario;
use App\Models\Persona;
use App\Models\TipoDocumento;
use App\Models\NivelUrgencia;
use App\Models\EstadoDocumento;
use App\Models\Departamento;
use App\Models\Derivacion;
use App\Models\Seguimiento;
use App\Http\Requests\StoreDocumentoRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DOCUMENTOS
    |--------------------------------------------------------------------------
    */

public function index()
{
    $usuario = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS DEL USUARIO
    |--------------------------------------------------------------------------
    */

    $documentos = Correspondencia::with([

        'tipoDocumento',
        'estado',
        'urgencia',
        'remitente',
        'derivaciones.departamentoDestino'

    ])

    ->where('idUsuario', $usuario->id)

    ->orderByDesc('fecha')

    ->get();

    /*
    |--------------------------------------------------------------------------
    | CONTADORES
    |--------------------------------------------------------------------------
    */

    $totalDocumentos =
        $documentos->count();

    $pendientes =
        $documentos
            ->where('estado.nombre', 'Pendiente')
            ->count();

    $finalizados =
        $documentos
            ->where('estado.nombre', 'Finalizado')
            ->count();

    $urgentes =
        $documentos
            ->where('urgencia.nombre', 'Urgente')
            ->count();

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
            'urgentes'
        )
    );
}

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO
    |--------------------------------------------------------------------------
    */

    public function show()
    {
        $tiposDocumento = TipoDocumento::all();

        $nivelesUrgencia = NivelUrgencia::all();

        $departamentos = Departamento::where('activo', true)->get();

        return view('correspondencia.documento-registro', compact(
            'tiposDocumento',
            'nivelesUrgencia',
            'departamentos'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function store(StoreDocumentoRequest $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN - YA HECHA POR EL REQUEST
        |--------------------------------------------------------------------------
        */

        $validated = $request->validated();

    /*
    |--------------------------------------------------------------------------
    | LIMPIEZA DE DATOS
    |--------------------------------------------------------------------------
    */

    $validated = array_map(function ($value) {

        if (is_string($value)) {

            $value = strip_tags($value);

            $value = trim($value);

            $value = preg_replace('/\s+/', ' ', $value);
        }

        return $value;

    }, $validated);

    try {

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | BUSCAR PERSONA POR CI
            |--------------------------------------------------------------------------
            */

            $persona = Persona::where(
                'ci',
                $validated['ci_remitente']
            )->first();

            /*
            |--------------------------------------------------------------------------
            | SI NO EXISTE → CREAR
            |--------------------------------------------------------------------------
            */

            if (!$persona)
            {
                $persona = Persona::create([

                    'nombre' =>
                        e($validated['nombre_remitente']),

                    'correo' =>
                        $validated['correo_remitente'] ?? null,

                    'telefono_celular' =>
                        $validated['telefono_celular'],

                    'telefono_fijo' =>
                        $validated['telefono_fijo'] ?? null,

                    'ci' =>
                        $validated['ci_remitente'],

                    'cargo' =>
                        isset($validated['cargo_remitente'])
                            ? e($validated['cargo_remitente'])
                            : null,

                    'institucion' =>
                        isset($validated['institucion_remitente'])
                            ? e($validated['institucion_remitente'])
                            : null,

                    'idDepartamento' => null,

                    'tipo' =>
                        $validated['tipo_remitente'],

                    'activo' => true,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ESTADO INICIAL
            |--------------------------------------------------------------------------
            */

            $estado = EstadoDocumento::first();

            /*
            |--------------------------------------------------------------------------
            | GENERAR CITE
            |--------------------------------------------------------------------------
            */

            $tipo = TipoDocumento::find(
                $validated['tipo_documento']
            );

            $urgencia = NivelUrgencia::find(
                $validated['nivel_urgencia']
            );

            $cite =
                strtoupper(substr($tipo->nombre, 0, 1))
                . strtoupper(substr($urgencia->nombre, 0, 1))
                . '-'
                . now()->format('Y-m-d-His');

            /*
            |--------------------------------------------------------------------------
            | CREAR DOCUMENTO
            |--------------------------------------------------------------------------
            */

            $documento = Correspondencia::create([

                'cite' =>
                    $cite,

                'asunto' =>
                    e($validated['asunto']),

                'fecha' =>
                    now(),

                'idTipoDocumento' =>
                    $validated['tipo_documento'],

                'idEstado' =>
                    $estado->idEstado,

                'idUrgencia' =>
                    $validated['nivel_urgencia'],

                'idUsuario' =>
                    Auth::id(),

                'idRemitente' =>
                    $persona->idPersona,

                'activo' => true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTO DESTINO
            |--------------------------------------------------------------------------
            */

            $departamentoDestino = Departamento::findOrFail(
                $validated['departamento']
            );

            /*
            |--------------------------------------------------------------------------
            | PERSONA ENCARGADA
            |--------------------------------------------------------------------------
            */

            if ($departamentoDestino->idPersonaEncargada)
            {
                CorrespondenciaDestinatario::create([

                    'idDocumento' =>
                        $documento->idDocumento,

                    'idPersona' =>
                        $departamentoDestino->idPersonaEncargada,

                    'activo' => true,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DERIVACIÓN AUTOMÁTICA
            |--------------------------------------------------------------------------
            */

            Derivacion::create([

                'idDocumento' =>
                    $documento->idDocumento,

                'orden' => 1,

                'idDepartamentoOrigen' => 1,

                'idDepartamentoDestino' =>
                    $departamentoDestino->idDepartamento,

                'idUsuarioAsignado' =>
                    Auth::id(),

                'idUsuarioEnvio' =>
                    Auth::id(),

                'instruccion' =>
                    e('Derivación automática inicial'),

                'fechaEnvio' => now(),

                'activo' => true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | SEGUIMIENTO
            |--------------------------------------------------------------------------
            */

            Seguimiento::create([

                'idDocumento' =>
                    $documento->idDocumento,

                'fecha' => now(),

                'ubicacion' =>
                    e($departamentoDestino->nombre),

                'idEstado' =>
                    $estado->idEstado,

                'activo' => true,

            ]);

        });

        return redirect()
            ->route('admin.correspondencia')
            ->with(
                'success',
                'Documento registrado correctamente.'
            );

    } catch (\Exception $e) {

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Error al registrar: ' . $e->getMessage()
            );
    }
}

    /*
    |--------------------------------------------------------------------------
    | DETALLE ADMIN
    |--------------------------------------------------------------------------
    */

    public function detalle($id)
    {
        $documento = Correspondencia::with([

                'usuario',
                'tipoDocumento',
                'estado',
                'urgencia',
                'remitente',
                'seguimientos',
                'derivaciones'

            ])
            ->findOrFail($id);

        return view(
            'admin.documentos.detalle',
            compact('documento')
        );
    }

 /*
    |--------------------------------------------------------------------------
    | Busqueda de persona por CI para derivación y creación de correspondencia
    |--------------------------------------------------------------------------
    */
    public function buscarPersona($ci)
    {
        $persona = Persona::with('cargo', 'departamento')
            ->where('ci', $ci)
            ->where('activo', true)
            ->first();

        if (!$persona) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'persona' => [
                'nombre' => $persona->nombre ?? '',
                'correo' => $persona->correo ?? '',
                'cargo' => $persona->tipo === 'INTERNO' && $persona->cargo 
                    ? $persona->cargo->nombre 
                    : null,
                'institucion' => $persona->institucion ?? '',
                'telefono_celular' => $persona->telefono_celular ?? '',
                'telefono_fijo' => $persona->telefono_fijo ?? '',
                'tipo' => $persona->tipo,
                'idDepartamento' => $persona->idDepartamento,
                'es_interno' => $persona->tipo === 'INTERNO'
            ]
        ]);
    }

    public function obtenerPersonasPorDepartamento($idDepartamento)
    {
        $personas = Persona::where('idDepartamento', $idDepartamento)
            ->where('tipo', 'INTERNO')
            ->where('activo', true)
            ->with('cargo')
            ->get([
                'idPersona',
                'nombre',
                'idCargo'
            ])
            ->map(function ($persona) {
                return [
                    'idPersona' => $persona->idPersona,
                    'nombre' => $persona->nombre,
                    'cargo' => $persona->cargo ? $persona->cargo->nombre : 'Sin cargo'
                ];
            });

        return response()->json($personas);
    }

    public function adminIndex()
    {
        $documentos = Correspondencia::with([

            'remitente',
            'tipoDocumento',
            'urgencia',
            'estado'

        ])
        ->orderByDesc('idDocumento')
        ->paginate(20)
        ->withQueryString();

        return view(
            'admin.documentos.index',
            compact('documentos')
        );
    }
    public function edit($id)
    {
        $documento = Correspondencia::findOrFail($id);

        $personas = Persona::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();

        $nivelesUrgencia = NivelUrgencia::orderBy('nombre')->get();

        $estados = EstadoDocumento::orderBy('nombre')->get();

        $departamentos = Departamento::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.documentos.edit',
            compact(
                'documento',
                'personas',
                'tiposDocumento',
                'nivelesUrgencia',
                'estados',
                'departamentos'
            )
        );
    }
    public function toggle($id)
    {
        $documento = Correspondencia::findOrFail($id);

        $documento->activo = !$documento->activo;

        $documento->save();

        return redirect()
            ->back()
            ->with(
                'success',
                'Estado documental actualizado.'
            );
    }
    public function buscarRemitente(Request $request)
{
    $buscar = trim($request->q);

    $personas = Persona::where('activo', true)

        ->where(function ($query) use ($buscar) {

            $query->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('ci', 'LIKE', "%{$buscar}%");

        })

        ->limit(10)

        ->get([
            'idPersona',
            'nombre',
            'ci',
            'correo',
            'telefono_celular',
            'cargo',
            'institucion'
        ]);

    return response()->json($personas);
}

public function update(Request $request, $id)
{
    $documento = Correspondencia::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | SI ESTÁ ARCHIVADO O FINALIZADO
    |--------------------------------------------------------------------------
    */

    if(
        strtoupper($documento->estado->nombre ?? '') == 'ARCHIVADO'
        || strtoupper($documento->estado->nombre ?? '') == 'FINALIZADO'
    )
    {

        /*
        |--------------------------------------------------------------------------
        | SOLO CAMBIAR ESTADO
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'idEstado' =>
                'required|exists:ESTADO_DOCUMENTO,idEstado',

        ]);

        $documento->update([

            'idEstado' => $request->idEstado,

        ]);

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
                'Administración',

            'idEstado' =>
                $request->idEstado,

            'activo' => true,

        ]);

        return redirect()
            ->route('admin.documentos.edit', $documento->idDocumento)
            ->with(
                'success',
                'Estado documental actualizado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN NORMAL
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        'asunto' =>
            'required|string|max:500',

        'idRemitente' =>
            'required|exists:PERSONA,idPersona',

        'idTipoDocumento' =>
            'required|exists:TIPO_DOCUMENTO,idTipoDocumento',

        'idUrgencia' =>
            'required|exists:NIVEL_URGENCIA,idUrgencia',

        'idEstado' =>
            'required|exists:ESTADO_DOCUMENTO,idEstado',

    ]);

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    $documento->update([

        'asunto' =>
            $validated['asunto'],

        'idRemitente' =>
            $validated['idRemitente'],

        'idTipoDocumento' =>
            $validated['idTipoDocumento'],

        'idUrgencia' =>
            $validated['idUrgencia'],

        'idEstado' =>
            $validated['idEstado'],

    ]);

    /*
    |--------------------------------------------------------------------------
    | SEGUIMIENTO
    |--------------------------------------------------------------------------
    */

    Seguimiento::create([

        'idDocumento' =>
            $documento->idDocumento,

        'fecha' =>
            now(),

        'ubicacion' =>
            'Administración',

        'idEstado' =>
            $validated['idEstado'],

        'activo' => true,

    ]);

    return redirect()
        ->route('admin.documentos.edit', $documento->idDocumento)
        ->with(
            'success',
            'Documento actualizado correctamente.'
        );
}
}