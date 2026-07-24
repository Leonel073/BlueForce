@extends('layouts.app')

@section('title', 'Reporte de Control Documental')

@section('content')
@include('admin.reportes.partials.styles')

<div class="report-container">
    <div class="report-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
                <div>
                    <span class="report-hero-badge">Control documental</span>
                    <h1 class="mt-2"><i class="bi bi-file-earmark-text-fill"></i> Reporte de Documentos</h1>
                    <p>Estado, prioridad, remitente y trazabilidad operativa de la correspondencia registrada.</p>
                </div>
            </div>
            <div class="report-hero-badge">
                {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.documentos') }}" id="filtros-documentos">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Documento</label>
                    <input type="text" name="q" class="form-control" placeholder="Cite o asunto" value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <label>Tipo</label>
                    <select name="idTipo" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->idTipoDocumento }}" @selected(request('idTipo') == $t->idTipoDocumento)>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="idEstado" class="form-select">
                        <option value="">Todos</option>
                        @foreach($estados as $e)
                            <option value="{{ $e->idEstado }}" @selected(request('idEstado') == $e->idEstado)>{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Urgencia</label>
                    <select name="idUrgencia" class="form-select">
                        <option value="">Todas</option>
                        @foreach($urgencias as $u)
                            <option value="{{ $u->idUrgencia }}" @selected(request('idUrgencia') == $u->idUrgencia)>{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Remitente</label>
                    <select name="idRemitente" class="form-select">
                        <option value="">Todos</option>
                        @foreach($personas as $p)
                            <option value="{{ $p->idPersona }}" @selected(request('idRemitente') == $p->idPersona)>{{ \Illuminate\Support\Str::limit($p->nombre, 32) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-8 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn-bf-primary btn-sm"><i class="bi bi-search"></i> Buscar</button>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="resetFiltrosDocumentos()"><i class="bi bi-arrow-clockwise"></i> Limpiar</button>
                    <a href="{{ route('admin.reportes.documentos.pdf', request()->query()) }}" class="btn-bf-danger btn-sm"><i class="bi bi-file-pdf-fill"></i> PDF</a>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
                </div>
            </div>
        </form>
    </div>

    @if(request()->hasAny(['q', 'idTipo', 'idEstado', 'idUrgencia', 'idRemitente', 'fecha_inicio', 'fecha_fin']))
        <div class="report-card">
            <div class="report-card-body py-3">
                <strong style="color:var(--bf-navy);">Contexto del reporte:</strong>
                <span class="text-muted">
                    {{ request('q') ? 'Busqueda: ' . request('q') . ' | ' : '' }}
                    {{ request('fecha_inicio') ? 'Desde: ' . request('fecha_inicio') . ' | ' : '' }}
                    {{ request('fecha_fin') ? 'Hasta: ' . request('fecha_fin') : '' }}
                </span>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-navy">
                <div class="stat-icon"><i class="bi bi-files"></i></div>
                <div class="stat-value">{{ $estadisticas['total_documentos'] }}</div>
                <div class="stat-label">Documentos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-gold">
                <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div class="stat-value">{{ $estadisticas['derivaciones_total'] }}</div>
                <div class="stat-label">Derivaciones</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-value">{{ $estadisticas['documentos_urgentes'] }}</div>
                <div class="stat-label">Urgentes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                <div class="stat-value">{{ $estadisticas['con_pdf'] }}</div>
                <div class="stat-label">Con archivo PDF</div>
            </div>
        </div>
    </div>

    <div class="report-card">
        <div class="report-card-header justify-content-between flex-wrap">
            <div>
                <h5 class="report-card-title"><i class="bi bi-clipboard-data"></i> Lectura ejecutiva</h5>
                <small class="text-muted">Indicadores para revisar carga, prioridad y trazabilidad.</small>
            </div>
            <span class="badge-bf badge-bf-navy">Promedio: {{ $estadisticas['promedio_derivaciones'] }} derivaciones/documento</span>
        </div>
        <div class="report-card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong style="color:var(--bf-navy);">Pendientes</strong><br>
                    <span class="badge-bf badge-bf-gold mt-2">{{ $estadisticas['documentos_pendientes'] }} documentos</span>
                </div>
                <div class="col-md-3">
                    <strong style="color:var(--bf-navy);">Resueltos o cerrados</strong><br>
                    <span class="badge-bf badge-bf-success mt-2">{{ $estadisticas['documentos_finalizados'] }} documentos</span>
                </div>
                <div class="col-md-3">
                    <strong style="color:var(--bf-navy);">Con trazabilidad</strong><br>
                    <span class="badge-bf badge-bf-blue mt-2">{{ $estadisticas['documentos_derivados'] }} documentos</span>
                </div>
                <div class="col-md-3">
                    <strong style="color:var(--bf-navy);">Sin movimiento</strong><br>
                    <span class="badge-bf badge-bf-gray mt-2">{{ $estadisticas['documentos_sin_derivacion'] }} documentos</span>
                </div>
            </div>
        </div>
    </div>

    <div class="report-card">
        <div class="report-card-header justify-content-between flex-wrap">
            <div>
                <h5 class="report-card-title"><i class="bi bi-list-ul"></i> Documentos incluidos</h5>
                <small class="text-muted">{{ $documentos->count() }} registros encontrados.</small>
            </div>
            <span class="badge-bf badge-bf-blue">{{ $estadisticas['documentos_archivados'] }} archivados</span>
        </div>
        <div class="report-card-body p-0">
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Origen</th>
                            <th>Clasificacion</th>
                            <th>Control</th>
                            <th>Trazabilidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            @php
                                $estadoNombre = $doc->estado->nombre ?? 'N/A';
                                $estadoClass = $estadoNombre === 'Archivado' ? 'badge-bf-gray' : (in_array($estadoNombre, ['Finalizado', 'Atendido']) ? 'badge-bf-success' : ($estadoNombre === 'Pendiente' ? 'badge-bf-gold' : 'badge-bf-blue'));
                                $urgenciaNombre = $doc->urgencia->nombre ?? 'Sin urgencia';
                                $urgenciaClass = strtolower($urgenciaNombre) === 'urgente' ? 'badge-bf-danger' : 'badge-bf-navy';
                            @endphp
                            <tr>
                                <td>
                                    <strong style="color:var(--bf-navy);">{{ $doc->cite ?? 'S/C' }}</strong><br>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($doc->asunto ?? 'Sin asunto', 68) }}</small>
                                </td>
                                <td>
                                    <strong>{{ $doc->remitente->nombre ?? 'N/A' }}</strong><br>
                                    <span class="badge-bf badge-bf-gray">CI: {{ $doc->remitente->ci ?? 'S/R' }}</span>
                                    <span class="badge-bf badge-bf-blue mt-1">{{ $doc->remitente->departamento->nombre ?? 'Externo/Independiente' }}</span>
                                </td>
                                <td>
                                    <span class="badge-bf badge-bf-navy">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</span><br>
                                    <span class="badge-bf {{ $urgenciaClass }} mt-1">{{ $urgenciaNombre }}</span>
                                </td>
                                <td>
                                    <span class="badge-bf {{ $estadoClass }}">{{ $estadoNombre }}</span><br>
                                    <small class="text-muted d-block mt-1">{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</small>
                                    <small class="text-muted">{{ $doc->dias_registro ?? 0 }} dias registrado</small>
                                </td>
                                <td>
                                    <strong>{{ $doc->ubicacion_actual_reporte }}</strong><br>
                                    <small class="text-muted">{{ $doc->ultimo_movimiento_reporte }}</small><br>
                                    <span class="badge-bf badge-bf-blue mt-1">{{ $doc->total_derivaciones }} movimientos</span>
                                    <span class="badge-bf badge-bf-gray mt-1">{{ $doc->estado_fisico_reporte }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="background:#F8FAFC;">
                                    <div class="px-2 py-2">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <strong style="color:var(--bf-navy);">
                                                <i class="bi bi-diagram-3"></i> Registro de derivaciones
                                            </strong>
                                            <span class="badge-bf badge-bf-navy">{{ $doc->total_derivaciones }} registros</span>
                                        </div>

                                        @if($doc->derivaciones_reporte->count() > 0)
                                            <div class="table-responsive">
                                                <table class="report-table" style="background:white;border:1px solid var(--bf-border);">
                                                    <thead>
                                                        <tr>
                                                            <th>Orden</th>
                                                            <th>Flujo</th>
                                                            <th>Fechas</th>
                                                            <th>Responsables</th>
                                                            <th>Instruccion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($doc->derivaciones_reporte as $derivacion)
                                                            <tr>
                                                                <td>
                                                                    <span class="badge-bf badge-bf-blue">#{{ $derivacion->orden ?? $loop->iteration }}</span>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $derivacion->departamentoOrigen->nombre ?? 'Origen no identificado' }}</strong><br>
                                                                    <small class="text-muted"><i class="bi bi-arrow-right"></i> {{ $derivacion->departamentoDestino->nombre ?? 'Destino no identificado' }}</small>
                                                                </td>
                                                                <td>
                                                                    <small class="text-muted d-block">Envio: {{ $derivacion->fechaEnvio ? \Carbon\Carbon::parse($derivacion->fechaEnvio)->format('d/m/Y H:i') : 'S/F' }}</small>
                                                                    <small class="text-muted d-block">Recepcion: {{ $derivacion->fechaRecepcion ? \Carbon\Carbon::parse($derivacion->fechaRecepcion)->format('d/m/Y H:i') : 'Pendiente' }}</small>
                                                                </td>
                                                                <td>
                                                                    <small class="text-muted d-block">Envia: {{ $derivacion->usuarioEnvio->name ?? 'No registrado' }}</small>
                                                                    <small class="text-muted d-block">Asignado: {{ $derivacion->usuarioAsignado->name ?? 'No asignado' }}</small>
                                                                </td>
                                                                <td>
                                                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($derivacion->instruccion ?? 'Sin instruccion registrada', 90) }}</small>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="text-muted">Este documento aun no tiene derivaciones registradas.</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                                    No se encontraron documentos para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function resetFiltrosDocumentos() {
    window.location.href = '{{ route("admin.reportes.documentos") }}';
}
function cerrar() {
    if (confirm('Desea cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection
