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
        'user.correspondencia.index',
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

        $departamentos = Departamento::all();

        return view(
            'user.documento-registro',
            compact(
                'tiposDocumento',
                'nivelesUrgencia',
                'departamentos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO
            |--------------------------------------------------------------------------
            */

            

            'asunto' => 'required|string|max:500',

            'tipo_documento' =>
                'required|exists:TIPO_DOCUMENTO,idTipoDocumento',

            'nivel_urgencia' =>
                'required|exists:NIVEL_URGENCIA,idUrgencia',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE
            |--------------------------------------------------------------------------
            */

            'nombre_remitente' =>
                'required|string|max:200',

            'correo_remitente' =>
                'nullable|email|max:150',

            'cargo_remitente' =>
                'nullable|string|max:150',

            'institucion_remitente' =>
                'nullable|string|max:200',

            'tipo_remitente' =>
                'required|in:INTERNO,EXTERNO',
            'ci_remitente' => 'required|string|max:20',
'telefono_celular' => 'required|string|max:20',
'telefono_fijo' => 'nullable|string|max:20',
'departamento_remitente' => 'nullable|exists:DEPARTAMENTO,idDepartamento',
            /*
            |--------------------------------------------------------------------------
            | DESTINO
            |--------------------------------------------------------------------------
            */

            'departamento' =>
                'required|exists:DEPARTAMENTO,idDepartamento',

        ]);

        try {

            DB::transaction(function () use ($validated) {

                /*
                |--------------------------------------------------------------------------
                | CREAR REMITENTE
                |--------------------------------------------------------------------------
                */

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

if(!$persona)
{
    $persona = Persona::create([

        'nombre' => $validated['nombre_remitente'],

        'correo' =>
            $validated['correo_remitente'] ?? null,

        'telefono_celular' =>
            $validated['telefono_celular'],

        'telefono_fijo' =>
            $validated['telefono_fijo'] ?? null,

        'ci' =>
            $validated['ci_remitente'],

        'cargo' =>
            $validated['cargo_remitente'] ?? null,

        'institucion' =>
            $validated['institucion_remitente'] ?? null,

        'idDepartamento' =>
            $validated['departamento_remitente'] ?? null,

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
                | CREAR Ns
                |--------------------------------------------------------------------------
                */
                $tipo = TipoDocumento::find(
                    $validated['tipo_documento']
                );

                $urgencia = NivelUrgencia::find(
                    $validated['nivel_urgencia']
                );

                $cite = strtoupper(substr($tipo->nombre,0,1))
                    . strtoupper(substr($urgencia->nombre,0,1))
                    . '-'
                    . now()->format('Y-m-d-His');
        /*
                |--------------------------------------------------------------------------
                | CREAR DOCUMENTO
                |--------------------------------------------------------------------------
                */
                $documento = Correspondencia::create([

                   
                        'cite' => $cite,
                    'asunto' =>
                        $validated['asunto'],

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

                if ($departamentoDestino->idPersonaEncargada) {

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

                    'instruccion' =>
                        'Derivación automática inicial',

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
                        $departamentoDestino->nombre,

                    'idEstado' =>
                        $estado->idEstado,

                    'activo' => true,

                ]);
            });

            return redirect()
                ->route('documentos.show')
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
                    'Error al registrar: ' .
                    $e->getMessage()
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
        $persona = Persona::where('ci', $ci)->first();

        if (!$persona) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'persona' => $persona
        ]);
    }
}