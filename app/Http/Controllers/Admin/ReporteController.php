<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Correspondencia;
use App\Models\User;
use App\Models\Derivacion;
use App\Models\EstadoDocumento;
use App\Models\Departamento;
use App\Models\TipoDocumento;
use App\Models\NivelUrgencia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ReporteController extends Controller
{
    private function buildPdfOrPrintableResponse(
        string $view,
        array $data,
        string $fileName,
        string $paper = 'letter',
        ?string $orientation = null
    ): Response
    {
        $data['logoDataUri'] = $this->getLogoDataUri();

        if (!extension_loaded('gd')) {
            $data['showPrintToolbar'] = true;

            return response()
                ->view($view, $data)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '.html"');
        }

        $data['showPrintToolbar'] = false;

        $pdf = Pdf::loadView($view, $data);

        if ($orientation) {
            $pdf->setPaper($paper, $orientation);
        } else {
            $pdf->setPaper($paper);
        }

        return $pdf->stream($fileName . '.pdf');
    }

    private function getLogoDataUri(): ?string
    {
        $logoPath = public_path('images/LogoEmpresa.png');

        if (!is_file($logoPath)) {
            return null;
        }

        $logoContent = file_get_contents($logoPath);

        if ($logoContent === false) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode($logoContent);
    }

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TOTALES GENERALES
        |--------------------------------------------------------------------------
        */

        $totalDocumentos =
            Correspondencia::count();

        $totalUsuarios =
            User::count();

        $totalDerivaciones =
            Derivacion::count();

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS FINALIZADOS
        |--------------------------------------------------------------------------
        */

        $estadoFinalizado = EstadoDocumento::where(
            'nombre',
            'Finalizado'
        )->first();

        $documentosFinalizados =
            $estadoFinalizado
            ? Correspondencia::where(
                'idEstado',
                $estadoFinalizado->idEstado
            )->count()
            : 0;

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS PENDIENTES
        |--------------------------------------------------------------------------
        */

        $documentosPendientes =
            $totalDocumentos
            - $documentosFinalizados;

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMOS DOCUMENTOS
        |--------------------------------------------------------------------------
        */

        $ultimosDocumentos =
            Correspondencia::latest(
                'idDocumento'
            )
            ->take(10)
            ->get();

        return view(
            'admin.reportes.index',
            compact(
                'totalDocumentos',
                'totalUsuarios',
                'totalDerivaciones',
                'documentosFinalizados',
                'documentosPendientes',
                'ultimosDocumentos'
            )
        );
    }

    public function personas(Request $request)
    {
          $query = Persona::with([
        'cargo',
        'departamento'
    ])->whereNull('idDepartamento');

        // FILTRO: Solo personas que hicieron trámites (tienen documentos)
        if ($request->filled('solo_con_tramites') && $request->solo_con_tramites == '1') {
            $query->whereHas('correspondenciasComoRemitente');
        }

        // Filtros de Persona
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('idDepartamento')) {
            $query->where('idDepartamento', $request->idDepartamento);
        }
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        $personas = $query->orderBy('nombre')->get();

        // Enriquecer cada persona con sus documentos
        foreach ($personas as $persona) {
            $docQuery = Correspondencia::with(['tipoDocumento', 'estado', 'derivaciones'])
                                       ->where('idRemitente', $persona->idPersona);

            // Filtros de Fecha del Documento
            if ($request->filled('fecha_inicio')) {
                $docQuery->whereDate('fecha', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $docQuery->whereDate('fecha', '<=', $request->fecha_fin);
            }

            $persona->documentos = $docQuery->get();
            $persona->total_documentos = $persona->documentos->count();
            $persona->documentos_enviados = $persona->total_documentos;
            $persona->documentos_derivados = $persona->documentos->filter(fn($d) => $d->derivaciones->count() > 0)->count();
            $persona->total_derivaciones = $persona->documentos->sum(fn($d) => $d->derivaciones->count());
        }

        // Filtrar personas sin documentos si se requiere
        if ($request->filled('solo_con_tramites') && $request->solo_con_tramites == '1') {
            $personas = $personas->filter(fn($p) => $p->total_documentos > 0);
        }

        // Obtener departamentos para filtro
        $departamentos = Departamento::all();

        // Estadísticas para pre-visualización
        $estadisticas = [
            'total_personas' => $personas->count(),
            'personas_internas' => $personas->filter(fn($p) => $p->tipo === 'INTERNO')->count(),
            'personas_externas' => $personas->filter(fn($p) => $p->tipo === 'EXTERNO')->count(),
            'total_documentos' => $personas->sum('total_documentos'),
            'total_documentos_enviados' => $personas->sum('documentos_enviados'),
            'personas_con_documentos' => $personas->filter(fn($p) => $p->total_documentos > 0)->count(),
            'promedio_documentos_por_persona' => $personas->count() > 0 ? round($personas->sum('total_documentos') / $personas->count(), 2) : 0,
            'documentos_con_derivacion' => $personas->sum('documentos_derivados'),
            'total_derivaciones' => $personas->sum('total_derivaciones')
        ];

        return view('admin.reportes.personas', compact('personas', 'departamentos', 'estadisticas'));
    }

    /**
     * MÉTODOS PARA ESTADÍSTICAS Y GRÁFICOS (JSON)
     */

    public function getEstadisticasDashboard()
    {
        $estadosPorTipo = Correspondencia::with('estado')
            ->get()
            ->groupBy('idEstado')
            ->map(fn($group) => [
                'nombre' => $group->first()->estado->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        $documentosPorMes = Correspondencia::selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as cantidad')
            ->groupByRaw('DATE_FORMAT(fecha, "%Y-%m")')
            ->orderBy('mes', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $documentosPorTipo = Correspondencia::with('tipoDocumento')
            ->get()
            ->groupBy('idTipoDocumento')
            ->map(fn($group) => [
                'nombre' => $group->first()->tipoDocumento->nombre ?? 'Desconocido',
                'cantidad' => $group->count()
            ])
            ->values();

        return response()->json([
            'estados' => $estadosPorTipo,
            'meses' => $documentosPorMes,
            'tipos' => $documentosPorTipo
        ]);
    }

   public function getEstadisticasDepartamentos()
{
    $departamentos = Departamento::select(
            'DEPARTAMENTO.idDepartamento',
            'DEPARTAMENTO.nombre'
        )

        ->leftJoin(
            'PERSONA',
            'PERSONA.idDepartamento',
            '=',
            'DEPARTAMENTO.idDepartamento'
        )

        ->leftJoin(
            'CORRESPONDENCIA',
            'CORRESPONDENCIA.idRemitente',
            '=',
            'PERSONA.idPersona'
        )

        ->leftJoin(
            'DERIVACION',
            'DERIVACION.idDepartamentoOrigen',
            '=',
            'DEPARTAMENTO.idDepartamento'
        )

        ->groupBy(
            'DEPARTAMENTO.idDepartamento',
            'DEPARTAMENTO.nombre'
        )

        ->selectRaw('
            COUNT(DISTINCT CORRESPONDENCIA.idDocumento) as documentos,
            COUNT(DISTINCT DERIVACION.idDerivacion) as derivaciones
        ')

        ->orderByDesc('documentos')

        ->get()

        ->map(function ($dep) {

            return [

                'nombre' => $dep->nombre,

                'documentos' => (int) $dep->documentos,

                'derivaciones' => (int) $dep->derivaciones
            ];
        });

    return response()->json($departamentos);
}

    public function getEstadisticasPersonas()
    {
        $top_personas = Correspondencia::with('remitente')
            ->get()
            ->groupBy('idRemitente')
            ->map(fn($group) => [
                'persona' => $group->first()->remitente->nombre ?? 'Desconocido',
                'documentos' => $group->count()
            ])
            ->sortByDesc('documentos')
            ->take(10)
            ->values();

        return response()->json($top_personas);
    }

public function personasPDF(Request $request)
    {
        $query = Persona::with([
            'cargo',
            'cargos',
            'departamento'
        ])->whereNull('idDepartamento');

        if ($request->filled('solo_con_tramites') && $request->solo_con_tramites == '1') {
            $query->whereHas('correspondenciasComoRemitente');
        }

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('idDepartamento')) {
            $query->where('idDepartamento', $request->idDepartamento);
        }
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        $personas = $query->orderBy('nombre')->get();

        foreach ($personas as $persona) {
            $docQuery = Correspondencia::with(['tipoDocumento', 'estado', 'derivaciones'])
                                       ->where('idRemitente', $persona->idPersona);

            if ($request->filled('fecha_inicio')) {
                $docQuery->whereDate('fecha', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $docQuery->whereDate('fecha', '<=', $request->fecha_fin);
            }

            $persona->documentos = $docQuery->get();
            $persona->total_documentos = $persona->documentos->count();
            $persona->documentos_enviados = $persona->total_documentos;
            $persona->documentos_derivados = $persona->documentos->filter(fn($d) => $d->derivaciones->count() > 0)->count();
            $persona->total_derivaciones = $persona->documentos->sum(fn($d) => $d->derivaciones->count());
        }

        if ($request->filled('solo_con_tramites') && $request->solo_con_tramites == '1') {
            $personas = $personas->filter(fn($p) => $p->total_documentos > 0);
        }

        $estadisticas = [
            'total_personas' => $personas->count(),
            'personas_internas' => $personas->filter(fn($p) => $p->tipo === 'INTERNO')->count(),
            'personas_externas' => $personas->filter(fn($p) => $p->tipo === 'EXTERNO')->count(),
            'total_documentos' => $personas->sum('total_documentos'),
            'total_documentos_enviados' => $personas->sum('documentos_enviados'),
            'personas_con_documentos' => $personas->filter(fn($p) => $p->total_documentos > 0)->count(),
            'promedio_documentos_por_persona' => $personas->count() > 0 ? round($personas->sum('total_documentos') / $personas->count(), 2) : 0,
            'documentos_con_derivacion' => $personas->sum('documentos_derivados'),
            'total_derivaciones' => $personas->sum('total_derivaciones')
        ];

        return $this->buildPdfOrPrintableResponse(
            'admin.reportes.pdf.personas',
            compact('personas', 'estadisticas'),
            'reporte-integral-personas'
        );
    }
 public function usuarios(Request $request)
    {
        $query = User::query();

        // Filtros básicos
        if ($request->filled('nombre')) $query->where('name', 'like', "%{$request->nombre}%");
        if ($request->filled('correo')) $query->where('email', 'like', "%{$request->correo}%");
        if ($request->filled('estado')) $query->where('activo', $request->estado);

        // AUDITORÍA - Cargar relaciones
        $usuarios = $query->with(['rol', 'persona.departamento', 'persona.cargo', 'persona.cargos'])
            ->withCount(['correspondencias'])
            ->with(['correspondencias' => function($q) {
                $q->with(['estado', 'tipoDocumento'])->latest('fecha')->take(10);
            }])
            ->get();

        // Enriquecer con información de auditoría
        foreach ($usuarios as $user) {
            // Total de documentos
            $user->total_documentos = $user->correspondencias_count;

            // Filtrar por fecha si se requiere
            if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
                $user->correspondencias = $user->correspondencias->filter(function($doc) use ($request) {
                    $fecha = $doc->fecha;
                    if ($request->filled('fecha_inicio') && $fecha < $request->fecha_inicio) return false;
                    if ($request->filled('fecha_fin') && $fecha > $request->fecha_fin) return false;
                    return true;
                });
            }

            // Filtro de cite si existe
            if ($request->filled('cite')) {
                $user->correspondencias = $user->correspondencias->filter(function($doc) use ($request) {
                    return stripos($doc->cite, $request->cite) !== false;
                });
            }

            $user->documentos_en_reporte = $user->correspondencias->count();

            // Últimos cambios visibles en el reporte, ya respetando filtros aplicados
            $user->ultimos_cambios = $user->correspondencias->map(fn($doc) => [
                'fecha' => $doc->fecha,
                'cite' => $doc->cite ?? 'S/C',
                'asunto' => $doc->asunto,
                'estado' => $doc->estado->nombre ?? 'Desconocido',
                'tipo' => $doc->tipoDocumento->nombre ?? 'Desconocido'
            ]);
        }

        // Obtener departamentos para filtro
        $departamentos = Departamento::all();

        // Estadísticas para pre-visualización
        $estadisticas = [
            'total_usuarios' => $usuarios->count(),
            'usuarios_activos' => $usuarios->filter(fn($u) => $u->activo)->count(),
            'usuarios_inactivos' => $usuarios->filter(fn($u) => !$u->activo)->count(),
            'total_documentos' => $usuarios->sum('total_documentos'),
            'usuarios_con_actividad' => $usuarios->filter(fn($u) => $u->documentos_en_reporte > 0)->count(),
            'documentos_en_reporte' => $usuarios->sum('documentos_en_reporte'),
            'promedio_documentos_por_usuario' => $usuarios->count() > 0 ? round($usuarios->sum('total_documentos') / $usuarios->count(), 2) : 0
        ];

        return view('admin.reportes.usuarios', compact('usuarios', 'departamentos', 'estadisticas'));
    }

    public function usuariosPDF(Request $request)
    {
        $query = User::query();

        if ($request->filled('nombre')) $query->where('name', 'like', "%{$request->nombre}%");
        if ($request->filled('correo')) $query->where('email', 'like', "%{$request->correo}%");
        if ($request->filled('estado')) $query->where('activo', $request->estado);

        $usuarios = $query->with(['rol', 'persona.departamento', 'persona.cargo', 'persona.cargos'])
            ->withCount(['correspondencias'])
            ->with(['correspondencias' => function($q) {
                $q->with(['estado', 'tipoDocumento'])->latest('fecha')->take(10);
            }])
            ->get();

        foreach ($usuarios as $user) {
            $user->total_documentos = $user->correspondencias_count;

            if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
                $user->correspondencias = $user->correspondencias->filter(function($doc) use ($request) {
                    $fecha = $doc->fecha;
                    if ($request->filled('fecha_inicio') && $fecha < $request->fecha_inicio) return false;
                    if ($request->filled('fecha_fin') && $fecha > $request->fecha_fin) return false;
                    return true;
                });
            }

            if ($request->filled('cite')) {
                $user->correspondencias = $user->correspondencias->filter(function($doc) use ($request) {
                    return stripos($doc->cite, $request->cite) !== false;
                });
            }

            $user->documentos_en_reporte = $user->correspondencias->count();
            $user->ultimos_cambios = $user->correspondencias->map(fn($doc) => [
                'fecha' => $doc->fecha,
                'cite' => $doc->cite ?? 'S/C',
                'asunto' => $doc->asunto,
                'estado' => $doc->estado->nombre ?? 'Desconocido',
                'tipo' => $doc->tipoDocumento->nombre ?? 'Desconocido'
            ]);
        }

        $estadisticas = [
            'total_usuarios' => $usuarios->count(),
            'usuarios_activos' => $usuarios->filter(fn($u) => $u->activo)->count(),
            'usuarios_inactivos' => $usuarios->filter(fn($u) => !$u->activo)->count(),
            'total_documentos' => $usuarios->sum('total_documentos'),
            'usuarios_con_actividad' => $usuarios->filter(fn($u) => $u->documentos_en_reporte > 0)->count(),
            'documentos_en_reporte' => $usuarios->sum('documentos_en_reporte'),
            'promedio_documentos_por_usuario' => $usuarios->count() > 0 ? round($usuarios->sum('total_documentos') / $usuarios->count(), 2) : 0
        ];

        return $this->buildPdfOrPrintableResponse(
            'admin.reportes.pdf.usuarios',
            compact('usuarios', 'estadisticas'),
            'reporte-usuarios',
            'letter',
            'landscape'
        );
    }
    public function documentos(Request $request)
    {
        $query = Correspondencia::with([
            'tipoDocumento',
            'estado',
            'urgencia',
            'remitente.departamento',
            'derivaciones.departamentoOrigen',
            'derivaciones.departamentoDestino',
            'derivaciones.usuarioAsignado.persona',
            'derivaciones.usuarioEnvio.persona'
        ]);

        // Filtros
        if ($request->filled('q')) {
            $query->where(function($f) use ($request) {
                $f->where('cite', 'like', "%{$request->q}%")
                  ->orWhere('asunto', 'like', "%{$request->q}%");
            });
        }

        // NUEVO: Filtro de personas (remitente)
        if ($request->filled('idRemitente')) {
            $query->where('idRemitente', $request->idRemitente);
        }

        if ($request->filled('idTipo')) $query->where('idTipoDocumento', $request->idTipo);
        if ($request->filled('idEstado')) $query->where('idEstado', $request->idEstado);
        if ($request->filled('idUrgencia')) $query->where('idUrgencia', $request->idUrgencia);
        if ($request->filled('fecha_inicio')) $query->whereDate('fecha', '>=', $request->fecha_inicio);
        if ($request->filled('fecha_fin')) $query->whereDate('fecha', '<=', $request->fecha_fin);

        $documentos = $this->enriquecerDocumentosReporte($query->latest('idDocumento')->get());

        $tipos = TipoDocumento::all();
        $estados = EstadoDocumento::all();
        $urgencias = NivelUrgencia::all();
        $personas = Persona::where('activo', true)->orderBy('nombre')->get();

        // Estadísticas para pre-visualización
        $estadisticas = $this->estadisticasDocumentosReporte($documentos);

        return view('admin.reportes.documentos', compact('documentos', 'tipos', 'estados', 'urgencias', 'personas', 'estadisticas'));
    }

public function documentosPDF(Request $request)
{
    $query = Correspondencia::with([
        'tipoDocumento',
        'estado',
        'urgencia',
        'remitente.departamento',
        'derivaciones.departamentoOrigen',
        'derivaciones.departamentoDestino',
        'derivaciones.usuarioAsignado.persona',
        'derivaciones.usuarioEnvio.persona'
    ]);

    if ($request->filled('q')) {
        $query->where(function($f) use ($request) {
            $f->where('cite', 'like', "%{$request->q}%")
              ->orWhere('asunto', 'like', "%{$request->q}%");
        });
    }
    if ($request->filled('idRemitente')) $query->where('idRemitente', $request->idRemitente);
    if ($request->filled('idTipo')) $query->where('idTipoDocumento', $request->idTipo);
    if ($request->filled('idEstado')) $query->where('idEstado', $request->idEstado);
    if ($request->filled('idUrgencia')) $query->where('idUrgencia', $request->idUrgencia);
    if ($request->filled('fecha_inicio')) $query->whereDate('fecha', '>=', $request->fecha_inicio);
    if ($request->filled('fecha_fin')) $query->whereDate('fecha', '<=', $request->fecha_fin);

    $documentos = $this->enriquecerDocumentosReporte($query->latest('idDocumento')->get());
    $estadisticas = $this->estadisticasDocumentosReporte($documentos);

    return $this->buildPdfOrPrintableResponse(
        'admin.reportes.pdf.documentos',
        compact('documentos', 'estadisticas'),
        'reporte-general-documentos',
        'letter',
        'landscape'
    );
}

    private function enriquecerDocumentosReporte($documentos)
    {
        foreach ($documentos as $doc) {
            $derivacionesOrdenadas = $doc->derivaciones
                ->sortByDesc(fn($derivacion) => $derivacion->orden ?? 0)
                ->values();
            $derivacionesCronologicas = $doc->derivaciones
                ->sortBy(fn($derivacion) => $derivacion->orden ?? 0)
                ->values();
            $ultimaDerivacion = $derivacionesOrdenadas->first();

            $doc->total_derivaciones = $derivacionesOrdenadas->count();
            $doc->derivaciones_reporte = $derivacionesCronologicas;
            $doc->ultima_derivacion_reporte = $ultimaDerivacion;
            $doc->ubicacion_actual_reporte = $ultimaDerivacion
                ? ($ultimaDerivacion->departamentoDestino->nombre ?? 'Destino no identificado')
                : 'Sin derivacion';
            $doc->ultimo_movimiento_reporte = $ultimaDerivacion
                ? (($ultimaDerivacion->departamentoOrigen->nombre ?? 'Origen no identificado') . ' -> ' . ($ultimaDerivacion->departamentoDestino->nombre ?? 'Destino no identificado'))
                : 'Sin movimiento';
            $doc->estado_fisico_reporte = !$ultimaDerivacion
                ? 'Registrado'
                : ($ultimaDerivacion->fechaRecepcion ? 'Recibido' : 'En transito');
            $doc->dias_registro = $doc->fecha
                ? \Carbon\Carbon::parse($doc->fecha)->startOfDay()->diffInDays(now()->startOfDay())
                : null;
        }

        return $documentos;
    }

    private function estadisticasDocumentosReporte($documentos): array
    {
        return [
            'total_documentos' => $documentos->count(),
            'documentos_pendientes' => $documentos->filter(fn($d) => ($d->estado->nombre ?? '') === 'Pendiente')->count(),
            'documentos_finalizados' => $documentos->filter(fn($d) => in_array(($d->estado->nombre ?? ''), ['Finalizado', 'Atendido', 'Archivado']))->count(),
            'documentos_archivados' => $documentos->filter(fn($d) => ($d->estado->nombre ?? '') === 'Archivado')->count(),
            'documentos_urgentes' => $documentos->filter(fn($d) => strtolower($d->urgencia->nombre ?? '') === 'urgente')->count(),
            'documentos_derivados' => $documentos->filter(fn($d) => $d->total_derivaciones > 0)->count(),
            'documentos_sin_derivacion' => $documentos->filter(fn($d) => $d->total_derivaciones === 0)->count(),
            'derivaciones_total' => $documentos->sum('total_derivaciones'),
            'promedio_derivaciones' => $documentos->count() > 0 ? round($documentos->sum('total_derivaciones') / $documentos->count(), 2) : 0,
            'con_pdf' => $documentos->filter(fn($d) => $d->tiene_archivo)->count()
        ];
    }

    public function departamentos(Request $request)
    {
        $query = Departamento::with(['personaEncargada']);
        if ($request->filled('nombre')) $query->where('nombre', 'like', "%{$request->nombre}%");
        
        $departamentos = $query->get();

        foreach ($departamentos as $dep) {
            // Estado archivado
            $estadoArchivado = EstadoDocumento::where('nombre', 'Archivado')->first();
            $estadoFinalizado = EstadoDocumento::where('nombre', 'Finalizado')->first();

            $qRecibidos = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento);
            $qEnviados = Derivacion::where('idDepartamentoOrigen', $dep->idDepartamento);

            // Filtro de fechas para ver el flujo en un periodo específico
            if ($request->filled('fecha_inicio')) {
                $qRecibidos->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
                $qEnviados->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $qRecibidos->whereDate('fechaEnvio', '<=', $request->fecha_fin);
                $qEnviados->whereDate('fechaEnvio', '<=', $request->fecha_fin);
            }

            // Contar documentos
            $dep->recibidos = $qRecibidos->count();
            $dep->enviados = $qEnviados->count();
            $dep->movimiento_total = $dep->recibidos + $dep->enviados;
            $dep->balance_flujo = $dep->recibidos - $dep->enviados;

            // Personal del departamento
            $personas = Persona::where('idDepartamento', $dep->idDepartamento)->get();
            $dep->total_personas = $personas->count();
            $dep->personas_internas = $personas->where('tipo', 'INTERNO')->count();
            $dep->personas_activas = $personas->where('activo', true)->count();
            $dep->personas_lista = $personas->map(fn($p) => $p->nombre)->toArray();

            // Encargado del departamento
            $dep->encargado_nombre = $dep->personaEncargada ? $dep->personaEncargada->nombre : 'No asignado';

            // Documentos originarios del departamento (TOTAL)
            $docsOriginarios = Correspondencia::whereHas('remitente', function($q) use ($dep) {
                $q->where('idDepartamento', $dep->idDepartamento);
            })->get();
            $dep->documentos_originarios = $docsOriginarios->count();

            // Documentos en curso (pendientes o derivados)
            $dep->documentos_en_curso = $docsOriginarios->filter(function($doc) {
                return $doc->estado->nombre !== 'Archivado' && $doc->estado->nombre !== 'Finalizado';
            })->count();

            // Documentos finalizados (derivados a otro departamento)
            $dep->documentos_derivados_finalizados = $docsOriginarios->filter(function($doc) use ($estadoFinalizado) {
                return $estadoFinalizado && $doc->idEstado === $estadoFinalizado->idEstado;
            })->count();

            // Documentos archivados
            $dep->documentos_archivados = $docsOriginarios->filter(function($doc) use ($estadoArchivado) {
                return $estadoArchivado && $doc->idEstado === $estadoArchivado->idEstado;
            })->count();

            // Documentos dirigidos al departamento
            $dep->documentos_destinatarios = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento)
                ->count();

            // Estadísticas detalladas por departamento
            $dep->estadisticas = [
                'total_documentos' => $dep->documentos_originarios,
                'en_curso' => $dep->documentos_en_curso,
                'finalizados' => $dep->documentos_derivados_finalizados,
                'archivados' => $dep->documentos_archivados,
                'tasa_completitud' => $dep->documentos_originarios > 0 ? 
                    round(($dep->documentos_derivados_finalizados / $dep->documentos_originarios) * 100, 2) : 0,
                'personal_total' => $dep->total_personas,
                'movimiento_total' => $dep->movimiento_total,
                'balance_flujo' => $dep->balance_flujo,
                'documentos_destinatarios' => $dep->documentos_destinatarios
            ];
        }

        // Estadísticas totales
        $estadisticas = [
            'total_departamentos' => $departamentos->count(),
            'total_derivaciones' => $departamentos->sum('recibidos'),
            'total_recibidos' => $departamentos->sum('recibidos'),
            'total_enviados' => $departamentos->sum('enviados'),
            'movimiento_total' => $departamentos->sum('movimiento_total'),
            'total_documentos' => $departamentos->sum('documentos_originarios'),
            'total_en_curso' => $departamentos->sum('documentos_en_curso'),
            'total_finalizados' => $departamentos->sum('documentos_derivados_finalizados'),
            'total_archivados' => $departamentos->sum('documentos_archivados'),
            'promedio_documentos_por_depto' => $departamentos->count() > 0 ? 
                round($departamentos->sum('documentos_originarios') / $departamentos->count(), 2) : 0,
            'total_personal' => $departamentos->sum('total_personas'),
            'departamentos_con_personal' => $departamentos->filter(fn($d) => $d->total_personas > 0)->count(),
            'departamentos_sin_movimiento' => $departamentos->filter(fn($d) => $d->movimiento_total === 0)->count()
        ];

        return view('admin.reportes.departamentos', compact('departamentos', 'estadisticas'));
    }

    public function departamentosPDF(Request $request)
    {
        $query = Departamento::with(['personaEncargada']);
        if ($request->filled('nombre')) $query->where('nombre', 'like', "%{$request->nombre}%");
        
        $departamentos = $query->get();

        foreach ($departamentos as $dep) {
            $estadoArchivado = EstadoDocumento::where('nombre', 'Archivado')->first();
            $estadoFinalizado = EstadoDocumento::where('nombre', 'Finalizado')->first();

            $qRecibidos = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento);
            $qEnviados = Derivacion::where('idDepartamentoOrigen', $dep->idDepartamento);

            if ($request->filled('fecha_inicio')) {
                $qRecibidos->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
                $qEnviados->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $qRecibidos->whereDate('fechaEnvio', '<=', $request->fecha_fin);
                $qEnviados->whereDate('fechaEnvio', '<=', $request->fecha_fin);
            }

            $dep->recibidos = $qRecibidos->count();
            $dep->enviados = $qEnviados->count();
            $dep->movimiento_total = $dep->recibidos + $dep->enviados;
            $dep->balance_flujo = $dep->recibidos - $dep->enviados;

            // Personal
            $personas = Persona::where('idDepartamento', $dep->idDepartamento)->get();
            $dep->total_personas = $personas->count();
            $dep->personas_internas = $personas->where('tipo', 'INTERNO')->count();
            $dep->personas_activas = $personas->where('activo', true)->count();
            $dep->personas_lista = $personas->map(fn($p) => $p->nombre)->toArray();

            // Encargado
            $dep->encargado_nombre = $dep->personaEncargada ? $dep->personaEncargada->nombre : 'No asignado';

            // Documentos
            $docsOriginarios = Correspondencia::whereHas('remitente', function($q) use ($dep) {
                $q->where('idDepartamento', $dep->idDepartamento);
            })->get();
            $dep->documentos_originarios = $docsOriginarios->count();

            $dep->documentos_en_curso = $docsOriginarios->filter(function($doc) {
                return $doc->estado->nombre !== 'Archivado' && $doc->estado->nombre !== 'Finalizado';
            })->count();

            $dep->documentos_derivados_finalizados = $docsOriginarios->filter(function($doc) use ($estadoFinalizado) {
                return $estadoFinalizado && $doc->idEstado === $estadoFinalizado->idEstado;
            })->count();

            $dep->documentos_archivados = $docsOriginarios->filter(function($doc) use ($estadoArchivado) {
                return $estadoArchivado && $doc->idEstado === $estadoArchivado->idEstado;
            })->count();

            $dep->documentos_destinatarios = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento)->count();

            $dep->estadisticas = [
                'total_documentos' => $dep->documentos_originarios,
                'en_curso' => $dep->documentos_en_curso,
                'finalizados' => $dep->documentos_derivados_finalizados,
                'archivados' => $dep->documentos_archivados,
                'tasa_completitud' => $dep->documentos_originarios > 0 ?
                    round(($dep->documentos_derivados_finalizados / $dep->documentos_originarios) * 100, 2) : 0,
                'personal_total' => $dep->total_personas,
                'movimiento_total' => $dep->movimiento_total,
                'balance_flujo' => $dep->balance_flujo,
                'documentos_destinatarios' => $dep->documentos_destinatarios
            ];
        }

        $estadisticas = [
            'total_departamentos' => $departamentos->count(),
            'total_recibidos' => $departamentos->sum('recibidos'),
            'total_enviados' => $departamentos->sum('enviados'),
            'movimiento_total' => $departamentos->sum('movimiento_total'),
            'total_documentos' => $departamentos->sum('documentos_originarios'),
            'total_en_curso' => $departamentos->sum('documentos_en_curso'),
            'total_finalizados' => $departamentos->sum('documentos_derivados_finalizados'),
            'total_archivados' => $departamentos->sum('documentos_archivados'),
            'total_personal' => $departamentos->sum('total_personas'),
            'promedio_documentos_por_depto' => $departamentos->count() > 0 ?
                round($departamentos->sum('documentos_originarios') / $departamentos->count(), 2) : 0,
            'departamentos_con_personal' => $departamentos->filter(fn($d) => $d->total_personas > 0)->count(),
            'departamentos_sin_movimiento' => $departamentos->filter(fn($d) => $d->movimiento_total === 0)->count(),
        ];

        return $this->buildPdfOrPrintableResponse(
            'admin.reportes.pdf.departamentos',
            compact('departamentos', 'estadisticas'),
            'reporte-flujo-departamentos',
            'letter',
            'landscape'
        );
    }
    public function derivaciones(Request $request)
    {
        $query = Derivacion::with([
            'documento',
            'departamentoOrigen',
            'departamentoDestino'
        ]);

        // NUEVO: Filtro de remitente
        if ($request->filled('idRemitente')) {
            $query->whereHas('documento', function($q) use ($request) {
                $q->where('idRemitente', $request->idRemitente);
            });
        }

        // FILTRO DOCUMENTO
        if ($request->filled('documento')) {
            $documento = $request->documento;
            $query->whereHas('documento', function ($q) use ($documento) {
                $q->where('cite', 'like', "%{$documento}%")
                  ->orWhere('asunto', 'like', "%{$documento}%");
            });
        }

        // FILTRO ORIGEN
        if ($request->filled('origen')) {
            $query->where('idDepartamentoOrigen', $request->origen);
        }

        // FILTRO DESTINO
        if ($request->filled('destino')) {
            $query->where('idDepartamentoDestino', $request->destino);
        }

        // FILTRO FECHA INICIO
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fechaEnvio', '>=', $request->fecha_inicio);
        }

        // FILTRO FECHA FIN
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fechaEnvio', '<=', $request->fecha_fin);
        }

        $derivaciones = $query->latest('idDerivacion')->get();

        // Enriquecer derivaciones con contexto
        $derivaciones = $derivaciones->map(function($der) {
            $der->usuario_derivacion = $der->documento->usuario->name ?? 'Desconocido';
            $der->persona_remitente = $der->documento->remitente->nombre ?? 'Desconocido';
            return $der;
        });

        // DEPARTAMENTOS
        $departamentos = Departamento::all();

        // Personas para filtro
        $personas = Persona::where('activo', true)->orderBy('nombre')->get();

        // Estadísticas
        $estadisticas = [
            'total_derivaciones' => $derivaciones->count(),
            'derivaciones_completadas' => $derivaciones->count(), // Asumiendo que todas en BD son completadas
            'departamentos_involucrados' => $departamentos->count(),
            'promedio_derivaciones_por_depto' => $departamentos->count() > 0 ? 
                round($derivaciones->count() / $departamentos->count(), 2) : 0
        ];

        return view(
            'admin.reportes.derivaciones',
            compact('derivaciones', 'departamentos', 'personas', 'estadisticas')
        );
    }
public function derivacionesPDF(Request $request)
{
    $query = Derivacion::with([

        'documento',
        'departamentoOrigen',
        'departamentoDestino'
    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('documento')) {

        $documento = $request->documento;

        $query->whereHas('documento', function ($q) use ($documento) {

            $q->where('cite', 'like', "%{$documento}%")
              ->orWhere('asunto', 'like', "%{$documento}%");

        });
    }

    if ($request->filled('origen')) {

        $query->where(
            'idDepartamentoOrigen',
            $request->origen
        );
    }

    if ($request->filled('destino')) {

        $query->where(
            'idDepartamentoDestino',
            $request->destino
        );
    }

    if ($request->filled('fecha_inicio')) {

        $query->whereDate(
            'fechaEnvio',
            '>=',
            $request->fecha_inicio
        );
    }

    if ($request->filled('fecha_fin')) {

        $query->whereDate(
            'fechaEnvio',
            '<=',
            $request->fecha_fin
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESULTADOS
    |--------------------------------------------------------------------------
    */

    $derivaciones = $query
        ->latest('idDerivacion')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    return $this->buildPdfOrPrintableResponse(
        'admin.reportes.pdf.derivaciones',
        compact('derivaciones'),
        'reporte-derivaciones'
    );
}
}
