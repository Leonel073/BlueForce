@extends('layouts.app')
@section('title', 'Reporte de Usuarios (Auditoría)')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <h1 class="fw-bold text-white"><i class="bi bi-people-fill"></i> Auditoría de Usuarios</h1>
            <p class="text-light mb-0">Registro detallado de actividad documental por usuario</p>
        </div>
    </div>

    {{-- FILTROS MEJORADOS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.usuarios') }}" id="filtros-auditoria">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Nombre del Usuario</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Administrador..." value="{{ request('nombre') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Correo Electrónico</label>
                        <input type="text" name="correo" class="form-control" placeholder="usuario@armada.mil.bo" value="{{ request('correo') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Cite Documento</label>
                        <input type="text" name="cite" class="form-control" placeholder="Buscar por cite..." value="{{ request('cite') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-secondary shadow-sm" onclick="resetFiltrosAuditoria()">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-success shadow-sm" onclick="mostrarEstadisticasAuditoria()">
                            <i class="bi bi-bar-chart"></i> Ver Estadísticas
                        </button>
                        <a href="{{ route('admin.reportes.usuarios.pdf', request()->query()) }}" class="btn btn-danger shadow-sm">
                            <i class="bi bi-file-pdf-fill"></i> Exportar a PDF
                        </a>
                        <button type="button" class="btn btn-secondary shadow-sm" onclick="cerrar()">
                            <i class="bi bi-x-lg"></i> Salida
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ESTADÍSTICAS PRE-VISUALIZACIÓN --}}
    @if($estadisticas['total_usuarios'] > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="estadisticas-auditoria" style="display: none;">
        <div class="card-body">
            <h5 class="fw-bold mb-3">📊 Estadísticas de Auditoría</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Usuarios</h6>
                            <h2 class="fw-bold">{{ $estadisticas['total_usuarios'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Activos</h6>
                            <h2 class="fw-bold text-success">{{ $estadisticas['usuarios_activos'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Inactivos</h6>
                            <h2 class="fw-bold text-danger">{{ $estadisticas['usuarios_inactivos'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Documentos</h6>
                            <h2 class="fw-bold text-primary">{{ $estadisticas['total_documentos'] }}</h2>
                            <small class="text-muted">Promedio: {{ $estadisticas['promedio_documentos_por_usuario'] }} por usuario</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-3 fw-bold text-secondary">Detalle de Usuarios y Actividad</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="usuariosAccordion">
                @forelse($usuarios as $usuario)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#usuario{{ $usuario->id }}">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong>{{ $usuario->name }}</strong>
                            <span class="ms-2 badge {{ $usuario->activo ? 'bg-success' : 'bg-danger' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="ms-2 badge bg-primary">{{ $usuario->total_documentos }} documentos</span>
                        </button>
                    </h2>
                    <div id="usuario{{ $usuario->id }}" class="accordion-collapse collapse" data-bs-parent="#usuariosAccordion">
                        <div class="accordion-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Correo:</strong> {{ $usuario->email }}</p>
                                    <p><strong>Estado:</strong> 
                                        @if($usuario->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p><strong>Total Documentos:</strong> <span class="badge bg-primary fs-6">{{ $usuario->total_documentos }}</span></p>
                                    <p><strong>Últimas Actividades:</strong> <span class="badge bg-info">{{ $usuario->ultimos_cambios->count() }}</span></p>
                                </div>
                            </div>
                            
                            <h6 class="fw-bold mt-3">📋 Documentos Recientes:</h6>
                            @if($usuario->ultimos_cambios->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Cite</th>
                                                <th>Asunto</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($usuario->ultimos_cambios as $cambio)
                                            <tr>
                                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($cambio['fecha'])->format('d/m/Y H:i') }}</small></td>
                                                <td><small class="fw-bold text-primary">{{ $cambio['cite'] }}</small></td>
                                                <td><small>{{ substr($cambio['asunto'], 0, 40) }}...</small></td>
                                                <td><small>{{ $cambio['tipo'] }}</small></td>
                                                <td>
                                                    <span class="badge bg-info text-dark">{{ $cambio['estado'] }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted small">Sin actividad en el período especificado.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-info">No existen usuarios con registros.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function resetFiltrosAuditoria() {
    document.getElementById('filtros-auditoria').reset();
    document.getElementById('filtros-auditoria').submit();
}

function mostrarEstadisticasAuditoria() {
    const seccion = document.getElementById('estadisticas-auditoria');
    seccion.style.display = seccion.style.display === 'none' ? 'block' : 'none';
}

function cerrar() {
    if (confirm('¿Deseas cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection