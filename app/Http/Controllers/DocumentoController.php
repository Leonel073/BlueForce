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
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DOCUMENTOS
    |--------------------------------------------------------------------------
    */

public function index()
{
    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS DEL SISTEMA
    |--------------------------------------------------------------------------
    */

    $query = Correspondencia::with([

        'tipoDocumento',
        'estado',
        'urgencia',
        'remitente',
        'derivaciones.departamentoDestino'

    ])->orderByDesc('fecha');

    /*
    |--------------------------------------------------------------------------
    | FILTRO DE SEGURIDAD: Usuario normal solo ve sus documentos
    |--------------------------------------------------------------------------
    */

    $user = Auth::user();
    if ($user->idRol != 1) {
        $query->where('idUsuario', $user->id);
    }

    /*
    |--------------------------------------------------------------------------
    | CONTADORES
    |--------------------------------------------------------------------------
    */

    $totalDocumentos =
        (clone $query)->count();

    $pendientes =
        (clone $query)
            ->whereHas('estado', function ($q) {
                $q->whereRaw('LOWER(nombre) = ?', ['pendiente']);
            })
            ->count();

    $finalizados =
        (clone $query)
            ->whereHas('estado', function ($q) {
                $q->whereRaw('LOWER(nombre) = ?', ['finalizado']);
            })
            ->count();

    $urgentes =
        (clone $query)
            ->whereHas('urgencia', function ($q) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%urgente%']);
            })
            ->count();

    $documentos = $query->paginate(10);

    /*
    |--------------------------------------------------------------------------
    | RETORNO
    |--------------------------------------------------------------------------
    */

    $viewName = $user->idRol == 1
        ? 'admin.documentos.index'
        : 'correspondencia.index';

    return view(
        $viewName,
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
        | DETERMINAR OPCIÓN DE REMITENTE
        |--------------------------------------------------------------------------
        */

        $opcionRemitente = $request->input('opcion_remitente');

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

            DB::transaction(function () use ($validated, $opcionRemitente) {

                /*
                |--------------------------------------------------------------------------
                | DETERMINAR LA PERSONA REMITENTE
                |--------------------------------------------------------------------------
                | CASO 1: "Yo Mismo" → Usar autenticado
                | CASO 2: "Otra Persona" → Usar datos del formulario
                |--------------------------------------------------------------------------
                */

                if ($opcionRemitente === 'yo_mismo') {
                    // CASO 1: "Yo Mismo" - Usar la persona autenticada
                    $persona = Auth::user()->persona;
                    
                    if (!$persona) {
                        throw new \Exception('El usuario autenticado no tiene una persona asociada.');
                    }
                } else {
                    // CASO 2: "Otra Persona" - Buscar por CI o crear
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
                }

            /*
            |--------------------------------------------------------------------------
            | ESTADO INICIAL — Pendiente
            |--------------------------------------------------------------------------
            */

            $estado = EstadoDocumento::where('nombre', 'Pendiente')->first()
                   ?? EstadoDocumento::first();

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
            | RESPONSABLE DESTINO - NUEVO
            |--------------------------------------------------------------------------
            */

            $responsableDestino = Persona::where('idPersona', $validated['responsable_destino'])
                ->where('idDepartamento', $departamentoDestino->idDepartamento)
                ->where('tipo', 'INTERNO')
                ->where('activo', true)
                ->firstOrFail();

            $usuarioResponsable = User::where('idPersona', $responsableDestino->idPersona)
                ->where('activo', true)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | OBTENER USUARIO RESPONSABLE DEL DEPARTAMENTO DESTINO (LEGACY - MANTENER)
            |--------------------------------------------------------------------------
            */

            $usuarioDestino = null;
            if ($departamentoDestino->idPersonaEncargada) {
                $personaEncargada = Persona::find($departamentoDestino->idPersonaEncargada);
                if ($personaEncargada) {
                    $usuarioDestino = User::where('idPersona', $personaEncargada->idPersona)
                        ->where('activo', true)
                        ->first();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PERSONA ENCARGADA - AHORA ES EL RESPONSABLE DESTINO SELECCIONADO
            |--------------------------------------------------------------------------
            */

            CorrespondenciaDestinatario::create([

                'idDocumento' =>
                    $documento->idDocumento,

                'idPersona' =>
                    $responsableDestino->idPersona,

                'activo' => true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | DERIVACIÓN AUTOMÁTICA - ASIGNAR AL RESPONSABLE SELECCIONADO
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
                    $usuarioResponsable?->id,

                'idUsuarioEnvio' =>
                    Auth::id(),

                'instruccion' =>
                    e('Derivación automática inicial hacia: ' . $responsableDestino->nombre),

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

            /*
            |--------------------------------------------------------------------------
            | ARCHIVO PDF (OPCIONAL) - GUARDADO CON NOMBRE DEL CITE
            |--------------------------------------------------------------------------
            */

            if (request()->hasFile('archivo_pdf')) {

                $file = request()->file('archivo_pdf');

                // Verificar MIME real con finfo (no solo extensión)
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeReal = $finfo->file($file->getRealPath());

                if ($mimeReal !== 'application/pdf') {
                    throw new \Exception('El archivo adjunto no es un PDF válido.');
                }

                // Guardar con el nombre del CITE para fácil identificación
                $nombreArchivo = $cite . '.pdf';
                $ruta = $file->storeAs('documentos', $nombreArchivo, 'local');

                $documento->update([
                    'archivo_pdf'    => $file->getClientOriginalName(),
                    'ruta_pdf'       => $ruta,
                    'mime_type'      => $mimeReal,
                    'tamano_archivo' => $file->getSize(),
                    'fecha_subida'   => now(),
                    'idUsuarioPdf'   => Auth::id(),
                ]);
            }

        });

        $redirectRoute = Auth::user()->idRol == 1
            ? 'admin.correspondencia'
            : 'correspondencia.index';

        return redirect()
            ->route($redirectRoute)
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

        $viewName = Auth::user()->idRol == 1
            ? 'admin.documentos.detalle'
            : 'correspondencia.show';

        return view(
            $viewName,
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
                'cargo' => $persona->tipo === 'INTERNO'
                    ? $persona->cargos_nombres
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
                    'cargo' => $persona->cargos_nombres
                ];
            });

        return response()->json($personas);
    }

    /*
    |--------------------------------------------------------------------------
    | CARGAR RESPONSABLES DEL DEPARTAMENTO PARA DERIVACIÓN
    |--------------------------------------------------------------------------
    */
    
    public function cargarResponsablesPorDepartamento($idDepartamento)
    {
        $departamento = Departamento::findOrFail($idDepartamento);

        $responsables = Persona::where('idDepartamento', $idDepartamento)
            ->where('tipo', 'INTERNO')
            ->where('activo', true)
            ->with('cargo')
            ->orderBy('nombre')
            ->get([
                'idPersona',
                'nombre',
                'ci',
                'idCargo'
            ])
            ->map(function ($persona) {
                return [
                    'idPersona' => $persona->idPersona,
                    'nombre' => $persona->nombre,
                    'ci' => $persona->ci,
                    'cargo' => $persona->cargos_nombres
                ];
            });

        return response()->json($responsables);
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
        ->paginate(10)
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
    /**
     * BÚSQUEDA INTELIGENTE DE PERSONAS
     * Búsqueda avanzada y multicampo con debounce
     */
    public function buscarPersonasAvanzado(Request $request)
    {
        $buscar = trim($request->input('q', ''));

        // Mínimo 2 caracteres para búsqueda
        if (strlen($buscar) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Ingrese al menos 2 caracteres',
                'resultados' => []
            ]);
        }

        $query = Persona::where('activo', true);

        // Búsqueda multicampo con OR
        $query->where(function ($q) use ($buscar) {
            $q->where('nombre', 'LIKE', "%{$buscar}%")
              ->orWhere('ci', 'LIKE', "%{$buscar}%")
              ->orWhere('correo', 'LIKE', "%{$buscar}%")
              ->orWhere('institucion', 'LIKE', "%{$buscar}%")
              ->orWhereHas('cargo', function ($q) use ($buscar) {
                  $q->where('nombre', 'LIKE', "%{$buscar}%");
              })
              ->orWhereHas('departamento', function ($q) use ($buscar) {
                  $q->where('nombre', 'LIKE', "%{$buscar}%");
              });
        });

        // Cargar relaciones
        $resultados = $query
            ->with(['cargo', 'departamento'])
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($persona) {
                return [
                    'idPersona' => $persona->idPersona,
                    'nombre' => $persona->nombre,
                    'ci' => $persona->ci,
                    'tipo' => $persona->tipo,
                    'correo' => $persona->correo,
                    'telefono_celular' => $persona->telefono_celular,
                    'telefono_fijo' => $persona->telefono_fijo,
                    'cargo' => $persona->cargos_nombres ?: null,
                    'departamento' => $persona->departamento?->nombre ?? null,
                    'institucion' => $persona->institucion,
                ];
            });

        return response()->json([
            'success' => true,
            'resultados' => $resultados,
            'cantidad' => $resultados->count()
        ]);
    }

    /**
     * VERIFICAR DUPLICADOS ANTES DE CREAR
     * Búsqueda por CI, correo, nombre, etc.
     */
    public function verificarDuplicados(Request $request)
    {
        $ci = trim($request->input('ci', ''));
        $correo = trim($request->input('correo', ''));
        $nombre = trim($request->input('nombre', ''));
        $institucion = trim($request->input('institucion', ''));
        $cargo = trim($request->input('cargo', ''));

        $query = Persona::where('activo', true);

        $encontrados = [];

        // Búsqueda por CI (prioridad 1)
        if (!empty($ci)) {
            $porCI = (clone $query)->where('ci', $ci)->first();
            if ($porCI) {
                $encontrados[] = [
                    'razon' => 'Coincidencia exacta por CI',
                    'persona' => $this->formatearPersona($porCI),
                ];
            }
        }

        // Búsqueda por correo (prioridad 2)
        if (!empty($correo) && empty($encontrados)) {
            $porCorreo = (clone $query)->where('correo', $correo)->first();
            if ($porCorreo) {
                $encontrados[] = [
                    'razon' => 'Coincidencia exacta por correo',
                    'persona' => $this->formatearPersona($porCorreo),
                ];
            }
        }

        // Búsqueda por nombre + institución (prioridad 3)
        if (!empty($nombre) && !empty($institucion) && empty($encontrados)) {
            $porNombreInstitucion = (clone $query)
                ->where('nombre', 'LIKE', "%{$nombre}%")
                ->where('institucion', 'LIKE', "%{$institucion}%")
                ->first();

            if ($porNombreInstitucion) {
                $encontrados[] = [
                    'razon' => 'Coincidencia por nombre e institución',
                    'persona' => $this->formatearPersona($porNombreInstitucion),
                ];
            }
        }

        // Búsqueda por nombre + cargo (prioridad 4)
        if (!empty($nombre) && !empty($cargo) && empty($encontrados)) {
            $porNombreCargo = (clone $query)
                ->where('nombre', 'LIKE', "%{$nombre}%")
                ->whereHas('cargo', function ($q) use ($cargo) {
                    $q->where('nombre', 'LIKE', "%{$cargo}%");
                })
                ->first();

            if ($porNombreCargo) {
                $encontrados[] = [
                    'razon' => 'Coincidencia por nombre y cargo',
                    'persona' => $this->formatearPersona($porNombreCargo),
                ];
            }
        }

        // Búsqueda por nombre similar (prioridad 5)
        if (!empty($nombre) && empty($encontrados)) {
            $similar = (clone $query)
                ->where('nombre', 'LIKE', "%{$nombre}%")
                ->limit(3)
                ->get();

            if ($similar->count() > 0) {
                foreach ($similar as $p) {
                    $encontrados[] = [
                        'razon' => 'Similitud por nombre',
                        'persona' => $this->formatearPersona($p),
                    ];
                }
            }
        }

        return response()->json([
            'encontrados' => $encontrados,
            'tiene_duplicados' => count($encontrados) > 0
        ]);
    }

    /**
     * Helper para formatear datos de persona
     */
    private function formatearPersona($persona)
    {
        return [
            'idPersona' => $persona->idPersona,
            'nombre' => $persona->nombre,
            'ci' => $persona->ci,
            'tipo' => $persona->tipo,
            'correo' => $persona->correo,
            'telefono_celular' => $persona->telefono_celular,
            'telefono_fijo' => $persona->telefono_fijo,
            'cargo' => $persona->cargos_nombres ?: null,
            'departamento' => $persona->departamento?->nombre ?? null,
            'institucion' => $persona->institucion,
        ];
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

    /*
    |--------------------------------------------------------------------------
    | SUBIR / REEMPLAZAR PDF EN EDICIÓN (ADMIN)
    |--------------------------------------------------------------------------
    */

    public function subirPdf(Request $request, $id)
    {
        $documento = Correspondencia::findOrFail($id);

        $request->validate([
            'archivo_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'max:' . config('app.max_pdf_size_kb', 10240),
            ],
        ], [
            'archivo_pdf.required' => 'Debe seleccionar un archivo PDF.',
            'archivo_pdf.mimes'    => 'Solo se permiten archivos PDF.',
            'archivo_pdf.max'      => 'El archivo no puede superar los 10 MB.',
        ]);

        $file = $request->file('archivo_pdf');

        // Verificar MIME real
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($file->getRealPath());

        if ($mimeReal !== 'application/pdf') {
            return back()->with('error', 'El archivo no es un PDF válido (verificación de contenido fallida).');
        }

        // Eliminar PDF anterior si existe
        if ($documento->ruta_pdf && Storage::disk('local')->exists($documento->ruta_pdf)) {
            Storage::disk('local')->delete($documento->ruta_pdf);
        }

        // Guardar con el nombre del CITE para fácil identificación
        $nombreArchivo = $documento->cite . '.pdf';
        $ruta = $file->storeAs('documentos', $nombreArchivo, 'local');

        $documento->update([
            'archivo_pdf'    => $file->getClientOriginalName(),
            'ruta_pdf'       => $ruta,
            'mime_type'      => $mimeReal,
            'tamano_archivo' => $file->getSize(),
            'fecha_subida'   => now(),
            'idUsuarioPdf'   => Auth::id(),
        ]);

        // Auditoría subida PDF
        try {
            \App\Models\Auditoria::create([
                'idUsuario'       => Auth::id(),
                'modelo'          => 'Correspondencia',
                'idRegistro'      => $documento->idDocumento,
                'accion'          => 'UPDATE',
                'datosAnteriores' => null,
                'datosNuevos'     => [
                    'accion_pdf' => 'SUBIDA_PDF',
                    'archivo'    => $file->getClientOriginalName(),
                    'tamano'     => $file->getSize(),
                    'fecha'      => now()->toDateTimeString(),
                ],
                'ip'        => request()->ip(),
                'navegador' => request()->userAgent(),
                'ruta'      => request()->getRequestUri(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Auditoría subida PDF fallida: ' . $e->getMessage());
        }

        return back()->with('success', 'Archivo PDF actualizado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR PDF (ADMIN)
    |--------------------------------------------------------------------------
    */

    public function eliminarPdf($id)
    {
        $documento = Correspondencia::findOrFail($id);

        $archivoAnterior = $documento->archivo_pdf;

        if ($documento->ruta_pdf && Storage::disk('local')->exists($documento->ruta_pdf)) {
            Storage::disk('local')->delete($documento->ruta_pdf);
        }

        $documento->update([
            'archivo_pdf'    => null,
            'ruta_pdf'       => null,
            'mime_type'      => null,
            'tamano_archivo' => null,
            'fecha_subida'   => null,
            'idUsuarioPdf'   => null,
        ]);

        // Auditoría eliminación PDF
        try {
            \App\Models\Auditoria::create([
                'idUsuario'       => Auth::id(),
                'modelo'          => 'Correspondencia',
                'idRegistro'      => $documento->idDocumento,
                'accion'          => 'UPDATE',
                'datosAnteriores' => ['archivo_pdf' => $archivoAnterior],
                'datosNuevos'     => [
                    'accion_pdf' => 'ELIMINACION_PDF',
                    'fecha'      => now()->toDateTimeString(),
                ],
                'ip'        => request()->ip(),
                'navegador' => request()->userAgent(),
                'ruta'      => request()->getRequestUri(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Auditoría eliminación PDF fallida: ' . $e->getMessage());
        }

        return back()->with('success', 'Archivo PDF eliminado correctamente.');
    }public function update(Request $request, $id)
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
