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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
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
    ]);

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
            $persona->documentos_derivados = $persona->documentos->filter(fn($d) => $d->derivaciones->count() > 0)->count();
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
            'total_derivaciones' => $personas->sum('documentos_derivados')
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
        $query = Persona::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $personas = $query->orderBy('idPersona', 'desc')->get();

        foreach ($personas as $persona) {
            $docQuery = Correspondencia::with(['tipoDocumento', 'estado'])
                                       ->where('idRemitente', $persona->idPersona);

            if ($request->filled('fecha_inicio')) {
                $docQuery->whereDate('fecha', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $docQuery->whereDate('fecha', '<=', $request->fecha_fin);
            }

            $persona->documentos = $docQuery->get();
        }

        $pdf = Pdf::loadView('admin.reportes.pdf.personas', compact('personas'));

        return $pdf->download('reporte-integral-personas.pdf');
    }
 public function usuarios(Request $request)
    {
        $query = User::query();

        // Filtros básicos
        if ($request->filled('nombre')) $query->where('name', 'like', "%{$request->nombre}%");
        if ($request->filled('correo')) $query->where('email', 'like', "%{$request->correo}%");
        if ($request->filled('estado')) $query->where('activo', $request->estado);

        // AUDITORÍA - Cargar relaciones
        $usuarios = $query->withCount(['correspondencias'])
            ->with(['correspondencias' => function($q) {
                $q->latest('fecha')->take(10);
            }])
            ->get();

        // Enriquecer con información de auditoría
        foreach ($usuarios as $user) {
            // Total de documentos
            $user->total_documentos = $user->correspondencias_count;

            // Últimos cambios (basado en documentos recientes)
            $user->ultimos_cambios = $user->correspondencias->map(fn($doc) => [
                'fecha' => $doc->fecha,
                'asunto' => $doc->asunto,
                'estado' => $doc->estado->nombre ?? 'Desconocido'
            ]);

            // Filtrar por fecha si se requiere
            if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
                $user->correspondencias = $user->correspondencias->filter(function($doc) use ($request) {
                    $fecha = $doc->fecha;
                    if ($request->filled('fecha_inicio') && $fecha < $request->fecha_inicio) return false;
                    if ($request->filled('fecha_fin') && $fecha > $request->fecha_fin) return false;
                    return true;
                });
            }
        }

        // Obtener departamentos para filtro
        $departamentos = Departamento::all();

        // Estadísticas para pre-visualización
        $estadisticas = [
            'total_usuarios' => $usuarios->count(),
            'usuarios_activos' => $usuarios->filter(fn($u) => $u->activo)->count(),
            'usuarios_inactivos' => $usuarios->filter(fn($u) => !$u->activo)->count(),
            'total_documentos' => $usuarios->sum('total_documentos'),
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

        $usuarios = $query->withCount(['correspondencias'])->get();
        $pdf = Pdf::loadView('admin.reportes.pdf.usuarios', compact('usuarios'));
        return $pdf->download('reporte-usuarios.pdf');
    }
    public function documentos(Request $request)
    {
        $query = Correspondencia::with(['tipoDocumento', 'estado', 'remitente', 'derivaciones']);

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

        $documentos = $query->latest('idDocumento')->get();

        $tipos = TipoDocumento::all();
        $estados = EstadoDocumento::all();
        $personas = Persona::where('activo', true)->orderBy('nombre')->get();

        // Estadísticas para pre-visualización
        $estadisticas = [
            'total_documentos' => $documentos->count(),
            'documentos_pendientes' => $documentos->filter(fn($d) => $d->estado->nombre === 'Pendiente')->count(),
            'documentos_finalizados' => $documentos->filter(fn($d) => $d->estado->nombre === 'Finalizado')->count(),
            'documentos_urgentes' => $documentos->filter(fn($d) => $d->urgencia->nombre === 'Urgente')->count(),
            'documentos_derivados' => $documentos->filter(fn($d) => $d->derivaciones->count() > 0)->count()
        ];

        return view('admin.reportes.documentos', compact('documentos', 'tipos', 'estados', 'personas', 'estadisticas'));
    }

public function documentosPDF(Request $request)
{
    $query = Correspondencia::with(['tipoDocumento', 'estado', 'remitente']);

    if ($request->filled('q')) {
        $query->where(function($f) use ($request) {
            $f->where('cite', 'like', "%{$request->q}%")
              ->orWhere('asunto', 'like', "%{$request->q}%");
        });
    }
if ($request->filled('idTipo')) $query->where('idTipoDocumento', $request->idTipo);
    if ($request->filled('idEstado')) $query->where('idEstado', $request->idEstado);
    if ($request->filled('fecha_inicio')) $query->whereDate('fecha', '>=', $request->fecha_inicio);
    if ($request->filled('fecha_fin')) $query->whereDate('fecha', '<=', $request->fecha_fin);

    $documentos = $query->latest('idDocumento')->get();

    $pdf = Pdf::loadView('admin.reportes.pdf.documentos', compact('documentos'));
    return $pdf->setPaper('letter', 'landscape')->download('reporte-general-documentos.pdf');
}
    public function departamentos(Request $request)
    {
        $query = Departamento::query();
        if ($request->filled('nombre')) $query->where('nombre', 'like', "%{$request->nombre}%");
        
        $departamentos = $query->get();

        foreach ($departamentos as $dep) {
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

            // NUEVO: Contexto adicional
            $dep->total_personas = Persona::where('idDepartamento', $dep->idDepartamento)->count();
            $dep->personas_internas = Persona::where('idDepartamento', $dep->idDepartamento)
                ->where('tipo', 'INTERNO')->count();
            $dep->personas_activas = Persona::where('idDepartamento', $dep->idDepartamento)
                ->where('activo', true)->count();

            // Documentos originarios del departamento
            $dep->documentos_originarios = Correspondencia::whereHas('remitente', function($q) use ($dep) {
                $q->where('idDepartamento', $dep->idDepartamento);
            })->count();

            // Documentos dirigidos al departamento
            $dep->documentos_destinatarios = Derivacion::where('idDepartamentoDestino', $dep->idDepartamento)
                ->count();
        }

        // Estadísticas totales
        $estadisticas = [
            'total_departamentos' => $departamentos->count(),
            'total_derivaciones' => $departamentos->sum('recibidos'),
            'total_documentos' => $departamentos->sum('documentos_originarios'),
            'promedio_documentos_por_depto' => $departamentos->count() > 0 ? 
                round($departamentos->sum('documentos_originarios') / $departamentos->count(), 2) : 0
        ];

        return view('admin.reportes.departamentos', compact('departamentos', 'estadisticas'));
    }

    public function departamentosPDF(Request $request)
    {
        $query = Departamento::query();
        if ($request->filled('nombre')) $query->where('nombre', 'like', "%{$request->nombre}%");
        
        $departamentos = $query->get();

        foreach ($departamentos as $dep) {
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
        }

        $pdf = Pdf::loadView('admin.reportes.pdf.departamentos', compact('departamentos'));
        return $pdf->download('reporte-flujo-departamentos.pdf');
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

    $pdf = Pdf::loadView(
        'admin.reportes.pdf.derivaciones',
        compact('derivaciones')
    );

    return $pdf->download(
        'reporte-derivaciones.pdf'
    );
}
}