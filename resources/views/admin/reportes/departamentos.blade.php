@extends('layouts.app')
@section('title', 'Reporte por Departamentos')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <h1 class="fw-bold text-white"><i class="bi bi-building-fill"></i> Reporte por Departamentos</h1>
            <p class="text-light mb-0">Flujo documental institucional por áreas - Estadísticas detalladas</p>
        </div>
    </div>

    {{-- FILTROS MEJORADOS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.departamentos') }}" id="filtros-departamentos">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nombre del Departamento</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Recursos Humanos..." value="{{ request('nombre') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Movimientos Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Movimientos Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFiltrosDepartamentos()">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-success" onclick="mostrarEstadisticasDepartamentos()">
                            <i class="bi bi-bar-chart"></i> Estadísticas
                        </button>
                        <a href="{{ route('admin.reportes.departamentos.pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="bi bi-file-pdf-fill"></i> PDF
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="cerrar()">
                            <i class="bi bi-x-lg"></i> Salida
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ESTADÍSTICAS --}}
    @if($estadisticas['total_departamentos'] > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="estadisticas-departamentos" style="display: none;">
        <div class="card-body">
            <h5 class="fw-bold mb-3">📊 Estadísticas Generales</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Departamentos</h6>
                            <h2 class="fw-bold">{{ $estadisticas['total_departamentos'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Derivaciones</h6>
                            <h2 class="fw-bold text-success">{{ $estadisticas['total_derivaciones'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Documentos</h6>
                            <h2 class="fw-bold text-primary">{{ $estadisticas['total_documentos'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Promedio por Depto</h6>
                            <h2 class="fw-bold text-info">{{ $estadisticas['promedio_documentos_por_depto'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA PRINCIPAL --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-3 fw-bold text-secondary">Detalle por Departamento ({{ $departamentos->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="departamentosAccordion">
                @forelse($departamentos as $dep)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#depto{{ $dep->idDepartamento }}">
                            <i class="bi bi-building me-2"></i>
                            <strong>{{ $dep->nombre }}</strong>
                            <span class="ms-3 badge bg-success">📥 {{ $dep->recibidos }}</span>
                            <span class="ms-2 badge bg-primary">📤 {{ $dep->enviados }}</span>
                            <span class="ms-2 badge bg-warning">📊 {{ $dep->recibidos + $dep->enviados }}</span>
                        </button>
                    </h2>
                    <div id="depto{{ $dep->idDepartamento }}" class="accordion-collapse collapse" data-bs-parent="#departamentosAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">📊 Información General</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Documentos Originarios:</strong> <span class="badge bg-primary">{{ $dep->documentos_originarios }}</span></li>
                                        <li><strong>Documentos Dirigidos:</strong> <span class="badge bg-success">{{ $dep->documentos_destinatarios }}</span></li>
                                        <li><strong>Derivaciones Recibidas:</strong> <span class="badge bg-info">{{ $dep->recibidos }}</span></li>
                                        <li><strong>Derivaciones Enviadas:</strong> <span class="badge bg-warning">{{ $dep->enviados }}</span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">👥 Personal del Departamento</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Total Personas:</strong> <span class="badge bg-secondary">{{ $dep->total_personas }}</span></li>
                                        <li><strong>Personas Internas:</strong> <span class="badge bg-success">{{ $dep->personas_internas }}</span></li>
                                        <li><strong>Personas Activas:</strong> <span class="badge bg-primary">{{ $dep->personas_activas }}</span></li>
                                        <li><strong>Movimiento Total:</strong> <span class="badge bg-dark">{{ $dep->recibidos + $dep->enviados }}</span></li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                            style="width: {{ ($dep->recibidos / max(1, $dep->recibidos + $dep->enviados)) * 100 }}%"
                                            aria-valuenow="{{ $dep->recibidos }}" aria-valuemin="0" aria-valuemax="{{ $dep->recibidos + $dep->enviados }}">
                                            📥 Recibidos: {{ $dep->recibidos }}
                                        </div>
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ ($dep->enviados / max(1, $dep->recibidos + $dep->enviados)) * 100 }}%"
                                            aria-valuenow="{{ $dep->enviados }}" aria-valuemin="0" aria-valuemax="{{ $dep->recibidos + $dep->enviados }}">
                                            📤 Enviados: {{ $dep->enviados }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-info">No existen departamentos registrados.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function resetFiltrosDepartamentos() {
    document.getElementById('filtros-departamentos').reset();
    document.getElementById('filtros-departamentos').submit();
}

function mostrarEstadisticasDepartamentos() {
    const seccion = document.getElementById('estadisticas-departamentos');
    if (seccion) {
        seccion.style.display = seccion.style.display === 'none' ? 'block' : 'none';
    }
}

function cerrar() {
    if (confirm('¿Deseas cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection