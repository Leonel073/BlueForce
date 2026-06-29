@extends('layouts.app')
@section('title', 'Reporte de Usuarios (Auditoria)')
@section('content')

@include('admin.reportes.partials.styles')

<div class="report-container">

    {{-- HERO --}}
    <div class="report-hero">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
            <div>
                <h1><i class="bi bi-people-fill"></i> Auditoria de Usuarios</h1>
                <p>Registro detallado de actividad documental por usuario</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.usuarios') }}" id="filtros-auditoria">
            <div class="row g-3">
                <div class="col-md-2">
                    <label>Nombre del Usuario</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Administrador..." value="{{ request('nombre') }}">
                </div>
                <div class="col-md-2">
                    <label>Correo Electronico</label>
                    <input type="text" name="correo" class="form-control" placeholder="usuario@armada.mil.bo" value="{{ request('correo') }}">
                </div>
                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Cite Documento</label>
                    <input type="text" name="cite" class="form-control" placeholder="Buscar por cite..." value="{{ request('cite') }}">
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
                <button type="button" class="btn-bf-secondary" onclick="resetFiltrosAuditoria()"><i class="bi bi-arrow-clockwise"></i> Limpiar</button>
                <button type="button" class="btn-bf-secondary" onclick="mostrarEstadisticasAuditoria()"><i class="bi bi-bar-chart"></i> Estadisticas</button>
                <a href="{{ route('admin.reportes.usuarios.pdf', request()->query()) }}" class="btn-bf-danger"><i class="bi bi-file-pdf-fill"></i> Exportar a PDF</a>
                <button type="button" class="btn-bf-secondary" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
            </div>
        </form>
    </div>

    {{-- ESTADISTICAS --}}
    @if($estadisticas['total_usuarios'] > 0)
    <div id="estadisticas-auditoria" style="display: none;">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_usuarios'] }}</div>
                    <div class="stat-label">Total Usuarios</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                    <div class="stat-value">{{ $estadisticas['usuarios_activos'] }}</div>
                    <div class="stat-label">Activos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-danger">
                    <div class="stat-icon"><i class="bi bi-person-x"></i></div>
                    <div class="stat-value">{{ $estadisticas['usuarios_inactivos'] }}</div>
                    <div class="stat-label">Inactivos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-file-earmark"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_documentos'] }}</div>
                    <div class="stat-label">Total Documentos</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA --}}
    <div class="report-card">
        <div class="report-card-header">
            <h5 class="report-card-title"><i class="bi bi-list-ul"></i> Detalle de Usuarios y Actividad</h5>
        </div>
        <div class="report-card-body p-0">
            <div class="report-accordion accordion" id="usuariosAccordion">
                @forelse($usuarios as $usuario)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#usuario{{ $usuario->id }}">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong>{{ $usuario->name }}</strong>
                            <span class="ms-2 badge-bf {{ $usuario->activo ? 'badge-bf-success' : 'badge-bf-danger' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="ms-2 badge-bf badge-bf-navy">{{ $usuario->total_documentos }} documentos</span>
                        </button>
                    </h2>
                    <div id="usuario{{ $usuario->id }}" class="accordion-collapse collapse" data-bs-parent="#usuariosAccordion">
                        <div class="accordion-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Correo:</strong> {{ $usuario->email }}</p>
                                    <p><strong>Estado:</strong>
                                        @if($usuario->activo)
                                            <span class="badge-bf badge-bf-success">Activo</span>
                                        @else
                                            <span class="badge-bf badge-bf-danger">Inactivo</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p><strong>Total Documentos:</strong> <span class="badge-bf badge-bf-navy" style="font-size:0.9rem;">{{ $usuario->total_documentos }}</span></p>
                                    <p><strong>Ultimas Actividades:</strong> <span class="badge-bf badge-bf-blue">{{ $usuario->ultimos_cambios->count() }}</span></p>
                                </div>
                            </div>

                            <h6 class="fw-bold mt-3 mb-2">Documentos Recientes:</h6>
                            @if($usuario->ultimos_cambios->count() > 0)
                                <div class="report-table-wrapper">
                                    <table class="report-table">
                                        <thead>
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
                                                <td><strong>{{ $cambio['cite'] }}</strong></td>
                                                <td><small>{{ substr($cambio['asunto'], 0, 40) }}...</small></td>
                                                <td><small>{{ $cambio['tipo'] }}</small></td>
                                                <td><span class="badge-bf badge-bf-navy">{{ $cambio['estado'] }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted small">Sin actividad en el periodo especificado.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-3"><span class="badge-bf badge-bf-gray">No existen usuarios con registros.</span></div>
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
    const s = document.getElementById('estadisticas-auditoria');
    s.style.display = s.style.display === 'none' ? 'block' : 'none';
}
function cerrar() {
    if (confirm('Desea cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection
