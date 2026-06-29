@extends('layouts.app')

@section('title', 'Reporte de Derivaciones')

@section('content')

@include('admin.reportes.partials.styles')

<div class="report-container">

    {{-- HERO --}}
    <div class="report-hero">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
            <div>
                <h1><i class="bi bi-arrow-left-right"></i> Reporte de Derivaciones</h1>
                <p>Seguimiento y trazabilidad documental</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.derivaciones') }}" id="filtros-derivaciones">
            <div class="row g-3">
                <div class="col-md-2">
                    <label>Documento</label>
                    <input type="text" name="documento" class="form-control" placeholder="Cite o asunto" value="{{ request('documento') }}">
                </div>
                <div class="col-md-2">
                    <label>Remitente</label>
                    <select name="idRemitente" class="form-select">
                        <option value="">Todos</option>
                        @foreach($personas as $p)
                            <option value="{{ $p->idPersona }}" {{ request('idRemitente') == $p->idPersona ? 'selected' : '' }}>
                                {{ substr($p->nombre, 0, 20) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Origen</label>
                    <select name="origen" class="form-select">
                        <option value="">Todos</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->idDepartamento }}" {{ request('origen') == $dep->idDepartamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Destino</label>
                    <select name="destino" class="form-select">
                        <option value="">Todos</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->idDepartamento }}" {{ request('destino') == $dep->idDepartamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
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
            </div>
            <div class="filter-actions mt-3">
                <button type="submit" class="btn-bf-primary"><i class="bi bi-search"></i> Buscar</button>
                <button type="button" class="btn-bf-secondary" onclick="resetFiltrosDerivaciones()"><i class="bi bi-arrow-clockwise"></i> Limpiar</button>
                <button type="button" class="btn-bf-secondary" onclick="mostrarEstadisticasDerivaciones()"><i class="bi bi-bar-chart"></i> Estadisticas</button>
                <a href="{{ route('admin.reportes.derivaciones.pdf', request()->query()) }}" class="btn-bf-danger"><i class="bi bi-file-earmark-pdf-fill"></i> Exportar PDF</a>
                <button type="button" class="btn-bf-secondary" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
            </div>
        </form>
    </div>

    {{-- ESTADISTICAS --}}
    @if($estadisticas['total_derivaciones'] > 0)
    <div id="estadisticas-derivaciones" style="display: none;">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_derivaciones'] }}</div>
                    <div class="stat-label">Total Derivaciones</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-check-all"></i></div>
                    <div class="stat-value">{{ $estadisticas['derivaciones_completadas'] }}</div>
                    <div class="stat-label">Completadas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-value">{{ $estadisticas['departamentos_involucrados'] }}</div>
                    <div class="stat-label">Departamentos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-gold">
                    <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
                    <div class="stat-value">{{ $estadisticas['promedio_derivaciones_por_depto'] }}</div>
                    <div class="stat-label">Promedio por Depto</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA --}}
    <div class="report-card">
        <div class="report-card-header">
            <h5 class="report-card-title"><i class="bi bi-list-ul"></i> Detalle de Derivaciones ({{ $derivaciones->count() }})</h5>
        </div>
        <div class="report-card-body p-0">
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Remitente</th>
                            <th>Usuario Derivacion</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Fecha Envio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($derivaciones as $derivacion)
                            <tr>
                                <td>
                                    <strong>{{ $derivacion->documento->cite ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ substr($derivacion->documento->asunto ?? '', 0, 30) }}...</small>
                                </td>
                                <td>
                                    <span class="badge-bf badge-bf-navy">{{ $derivacion->persona_remitente ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge-bf badge-bf-gold">{{ $derivacion->usuario_derivacion ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge-bf badge-bf-blue">{{ $derivacion->departamentoOrigen->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge-bf badge-bf-success">{{ $derivacion->departamentoDestino->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($derivacion->fechaEnvio)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No existen derivaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function resetFiltrosDerivaciones() {
    document.getElementById('filtros-derivaciones').reset();
    document.getElementById('filtros-derivaciones').submit();
}
function mostrarEstadisticasDerivaciones() {
    const s = document.getElementById('estadisticas-derivaciones');
    if (s) s.style.display = s.style.display === 'none' ? 'block' : 'none';
}
function cerrar() {
    if (confirm('Desea cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>

@endsection
