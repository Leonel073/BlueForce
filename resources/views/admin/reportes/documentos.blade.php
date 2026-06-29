@extends('layouts.app')

@section('title', 'Reporte General de Documentos')

@section('content')

@include('admin.reportes.partials.styles')

<div class="report-container">

    {{-- HERO --}}
    <div class="report-hero">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
            <div>
                <h1><i class="bi bi-file-earmark-text-fill"></i> Reporte General de Documentos</h1>
                <p>Listado integral de correspondencia con datos de origen y destino</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.documentos') }}" id="filtros-documentos">
            <div class="row g-3">
                <div class="col-md-2">
                    <label>Buscar Documento</label>
                    <input type="text" name="q" class="form-control" placeholder="Cite o asunto..." value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <label>Tipo</label>
                    <select name="idTipo" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->idTipoDocumento }}" {{ request('idTipo') == $t->idTipoDocumento ? 'selected' : '' }}>
                                {{ $t->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="idEstado" class="form-select">
                        <option value="">Todos</option>
                        @foreach($estados as $e)
                            <option value="{{ $e->idEstado }}" {{ request('idEstado') == $e->idEstado ? 'selected' : '' }}>{{ $e->nombre }}</option>
                        @endforeach
                    </select>
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
                <div class="col-md-1">
                    <label>Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-1">
                    <label>Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-bf-primary w-100"><i class="bi bi-search"></i> Buscar</button>
                </div>
            </div>
            <div class="filter-actions mt-3">
                <button type="button" class="btn-bf-secondary" onclick="resetFiltrosDocumentos()"><i class="bi bi-arrow-clockwise"></i> Limpiar</button>
                <button type="button" class="btn-bf-secondary" onclick="mostrarEstadisticasDocumentos()"><i class="bi bi-bar-chart"></i> Estadisticas</button>
                <a href="{{ route('admin.reportes.documentos.pdf', request()->query()) }}" class="btn-bf-danger"><i class="bi bi-file-pdf-fill"></i> Exportar PDF</a>
                <button type="button" class="btn-bf-secondary" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
            </div>
        </form>
    </div>

    {{-- ESTADISTICAS --}}
    @if($estadisticas['total_documentos'] > 0)
    <div id="estadisticas-documentos" style="display: none;">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_documentos'] }}</div>
                    <div class="stat-label">Total Documentos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-gold">
                    <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-value">{{ $estadisticas['documentos_pendientes'] }}</div>
                    <div class="stat-label">Pendientes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-value">{{ $estadisticas['documentos_finalizados'] }}</div>
                    <div class="stat-label">Finalizados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-danger">
                    <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="stat-value">{{ $estadisticas['documentos_urgentes'] }}</div>
                    <div class="stat-label">Urgentes</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA --}}
    <div class="report-card">
        <div class="report-card-header d-flex justify-content-between align-items-center">
            <h5 class="report-card-title"><i class="bi bi-list-ul"></i> Resultados de la busqueda ({{ $documentos->count() }})</h5>
        </div>
        <div class="report-card-body p-0">
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Documento / Cite</th>
                            <th>Remitente (CI)</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td>
                                    <strong>{{ $doc->cite }}</strong><br>
                                    <small class="text-muted">{{ substr($doc->asunto, 0, 40) }}...</small>
                                </td>
                                <td>
                                    {{ $doc->remitente->nombre ?? 'N/A' }}<br>
                                    <span class="badge-bf badge-bf-gray">{{ $doc->remitente->ci ?? 'S/R' }}</span>
                                </td>
                                <td>
                                    <span class="badge-bf badge-bf-navy">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge-bf badge-bf-blue">{{ $doc->estado->nombre ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">No se encontraron documentos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function resetFiltrosDocumentos() {
    document.getElementById('filtros-documentos').reset();
    document.getElementById('filtros-documentos').submit();
}
function mostrarEstadisticasDocumentos() {
    const s = document.getElementById('estadisticas-documentos');
    if (s) s.style.display = s.style.display === 'none' ? 'block' : 'none';
}
function cerrar() {
    if (confirm('Desea cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection
