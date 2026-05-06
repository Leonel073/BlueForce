<?php

namespace App\Http\Controllers;

use App\Models\Correspondencia;
use App\Models\CorrespondenciaDestinatario;
use App\Models\Persona;
use App\Models\TipoDocumento;
use App\Models\NivelUrgencia;
use App\Models\EstadoDocumento;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentoController extends Controller
{
    /**
     * Listar todos los documentos
     */
    public function index()
    {
        $documentos = Correspondencia::with(['tipoDocumento', 'estado', 'urgencia', 'remitente'])->get();
        
        return view('user.correspondencia.index', [
            'documentos' => $documentos,
        ]);
    }

    /**
     * Mostrar el formulario de registro documental
     */
    public function show()
    {
        $tiposDocumento = TipoDocumento::all();
        $nivelesUrgencia = NivelUrgencia::all();
        $departamentos = Departamento::all();

        return view('user.documento-registro', [
            'tiposDocumento' => $tiposDocumento,
            'nivelesUrgencia' => $nivelesUrgencia,
            'departamentos' => $departamentos,
        ]);
    }

    /**
     * Guardar un nuevo documento (correspondencia) con transacciones
     */
    public function store(Request $request)
    {
        // Validar datos del formulario
        $validated = $request->validate([
            // DATOS DEL DOCUMENTO
            'codigo_ruta' => 'required|string|max:100',
            'asunto' => 'required|string|max:500',
            'tipo_documento' => 'required|exists:TIPO_DOCUMENTO,idTipoDocumento',
            'nivel_urgencia' => 'required|exists:NIVEL_URGENCIA,idUrgencia',

            // DATOS DEL REMITENTE
            'nombre_remitente' => 'required|string|max:200',
            'correo_remitente' => 'nullable|email|max:150',
            'cargo_remitente' => 'nullable|string|max:150',
            'institucion_remitente' => 'nullable|string|max:200',
            'tipo_remitente' => 'required|in:INTERNO,EXTERNO',

            // DEPARTAMENTO DESTINO (solo 1)
            'departamento' => 'required|exists:DEPARTAMENTO,idDepartamento',
        ], [
            'codigo_ruta.required' => 'El código de ruta es requerido',
            'asunto.required' => 'El asunto es requerido',
            'tipo_documento.required' => 'Debe seleccionar un tipo de documento',
            'tipo_documento.exists' => 'El tipo de documento seleccionado no es válido',
            'nivel_urgencia.required' => 'Debe seleccionar un nivel de urgencia',
            'nivel_urgencia.exists' => 'El nivel de urgencia seleccionado no es válido',
            'nombre_remitente.required' => 'El nombre del remitente es requerido',
            'tipo_remitente.required' => 'Debe indicar si el remitente es INTERNO o EXTERNO',
            'tipo_remitente.in' => 'El tipo de remitente solo puede ser INTERNO o EXTERNO',
            'departamento.required' => 'Debe seleccionar un departamento destino',
            'departamento.exists' => 'El departamento seleccionado no es válido',
        ]);

        try {
            // USAR TRANSACCIÓN PARA GARANTIZAR INTEGRIDAD
            DB::transaction(function () use ($validated) {
                // 1. CREAR LA PERSONA (REMITENTE)
                $persona = Persona::create([
                    'nombre' => $validated['nombre_remitente'],
                    'correo' => $validated['correo_remitente'] ?? null,
                    'cargo' => $validated['cargo_remitente'] ?? null,
                    'institucion' => $validated['institucion_remitente'] ?? null,
                    'tipo' => $validated['tipo_remitente'],
                    'activo' => true,
                ]);

                // 2. OBTENER EL ESTADO "RECIBIDO" (ID = 1)
                $estadoRecibido = EstadoDocumento::where('nombre', 'Recibido')->first();
                $idEstado = $estadoRecibido ? $estadoRecibido->idEstado : 1;

                // 3. CREAR LA CORRESPONDENCIA
                $correspondencia = Correspondencia::create([
                    'cite' => $validated['codigo_ruta'],
                    'asunto' => $validated['asunto'],
                    'fecha' => now(),
                    'idTipoDocumento' => $validated['tipo_documento'],
                    'idEstado' => $idEstado,
                    'idUrgencia' => $validated['nivel_urgencia'],
                    'idRemitente' => $persona->idPersona,
                    'activo' => true,
                ]);

                // 4. CREAR UN SOLO DESTINATARIO (el departamento seleccionado)
                CorrespondenciaDestinatario::create([
                    'idDocumento' => $correspondencia->idDocumento,
                    'idPersona' => $validated['departamento'],
                    'activo' => true,
                ]);

                // Guardar en sesión para flash message
                session()->put('documento_creado', [
                    'id' => $correspondencia->idDocumento,
                    'codigo_ruta' => $correspondencia->cite,
                ]);
            });

            // Redirigir con mensaje de éxito
            return redirect()->route('documentos.show')
                    ->with('success', 'Documento registrado correctamente con código de ruta: ' . $validated['codigo_ruta']);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el documento: ' . $e->getMessage());
        }
    }
}
