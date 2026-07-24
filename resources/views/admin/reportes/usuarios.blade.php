@extends('layouts.app')

@section('title', 'Reporte de Usuarios (Auditoria)')

@section('content')
@include('admin.reportes.partials.styles')

<div class="report-container">
    <div class="report-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
                <div>
                    <span class="report-hero-badge">Control de actividad</span>
                    <h1 class="mt-2"><i class="bi bi-people-fill"></i> Auditoria de Usuarios</h1>
                    <p>Cuentas del sistema, perfil institucional y documentos gestionados por usuario.</p>
                </div>
            </div>
            <div class="report-hero-badge">
                {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.usuarios') }}" id="filtros-auditoria">
            <div class="row g-3">
                <div class="col-md-2">
                    <label>Usuario</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de usuario" value="{{ request('nombre') }}">
                </div>
                <div class="col-md-2">
                    <label>Correo electronico</label>
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
                    <label>Cite documento</label>
                    <input type="text" name="cite" class="form-control" placeholder="Buscar por cite" value="{{ request('cite') }}">
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

    @if(request()->hasAny(['nombre', 'correo', 'estado', 'cite', 'fecha_inicio', 'fecha_fin']))
        <div class="report-card">
            <div class="report-card-body py-3">
                <strong style="color:var(--bf-navy);">Contexto del reporte:</strong>
                <span class="text-muted">
                    {{ request('nombre') ? 'Usuario: ' . request('nombre') . ' | ' : '' }}
                    {{ request('correo') ? 'Correo: ' . request('correo') . ' | ' : '' }}
                    {{ request('estado') !== null && request('estado') !== '' ? 'Estado: ' . (request('estado') == '1' ? 'Activo' : 'Inactivo') . ' | ' : '' }}
                    {{ request('cite') ? 'Cite: ' . request('cite') . ' | ' : '' }}
                    {{ request('fecha_inicio') ? 'Desde: ' . request('fecha_inicio') . ' | ' : '' }}
                    {{ request('fecha_fin') ? 'Hasta: ' . request('fecha_fin') : '' }}
                </span>
            </div>
        </div>
    @endif

    @if($estadisticas['total_usuarios'] > 0)
    <div id="estadisticas-auditoria" style="display: none;">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_usuarios'] }}</div>
                    <div class="stat-label">Usuarios incluidos</div>
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
                <div class="stat-card stat-gold">
                    <div class="stat-icon"><i class="bi bi-activity"></i></div>
                    <div class="stat-value">{{ $estadisticas['usuarios_con_actividad'] }}</div>
                    <div class="stat-label">Con actividad visible</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="stat-value">{{ $estadisticas['documentos_en_reporte'] }}</div>
                    <div class="stat-label">Docs. en reporte</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="report-card">
        <div class="report-card-header justify-content-between flex-wrap">
            <div>
                <h5 class="report-card-title"><i class="bi bi-shield-check"></i> Detalle de usuarios y actividad</h5>
                <small class="text-muted">{{ $usuarios->count() }} usuarios evaluados - {{ $estadisticas['total_documentos'] }} documentos historicos registrados.</small>
            </div>
            <span class="badge-bf badge-bf-navy">Promedio: {{ $estadisticas['promedio_documentos_por_usuario'] }} docs/usuario</span>
        </div>
        <div class="report-card-body">
            <div class="report-accordion accordion" id="usuariosAccordion">
                @forelse($usuarios as $usuario)
                    @php
                        $persona = $usuario->persona;
                        $departamento = $persona?->departamento?->nombre ?? 'Sin departamento';
                        $cargo = $persona?->cargos_nombres ?? 'Sin cargo';
                        $rol = $usuario->rol?->nombre ?? ($usuario->idRol == 1 ? 'Administrador' : 'Usuario');
                        $iniciales = collect(explode(' ', $usuario->name))->filter()->take(2)->map(fn($p) => strtoupper(substr($p, 0, 1)))->implode('');
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#usuario{{ $usuario->id }}">
                                <span class="persona-avatar me-3">{{ $iniciales ?: 'U' }}</span>
                                <span class="flex-grow-1">
                                    <strong>{{ $usuario->name }}</strong>
                                    <span class="d-block small text-muted">{{ $usuario->email }}</span>
                                </span>
                                <span class="badge-bf badge-bf-purple me-2">{{ $rol }}</span>
                                <span class="badge-bf {{ $usuario->activo ? 'badge-bf-success' : 'badge-bf-danger' }} me-2">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span>
                                <span class="badge-bf badge-bf-navy">{{ $usuario->documentos_en_reporte }} visibles / {{ $usuario->total_documentos }} total</span>
                            </button>
                        </h2>
                        <div id="usuario{{ $usuario->id }}" class="accordion-collapse collapse" data-bs-parent="#usuariosAccordion">
                            <div class="accordion-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <h6 class="fw-bold mb-3" style="color:var(--bf-navy);">Cuenta</h6>
                                                <p class="mb-2"><strong>Correo:</strong><br><span class="text-muted">{{ $usuario->email }}</span></p>
                                                <p class="mb-2"><strong>Rol:</strong><br><span class="badge-bf badge-bf-purple">{{ $rol }}</span></p>
                                                <p class="mb-0"><strong>Estado:</strong><br><span class="badge-bf {{ $usuario->activo ? 'badge-bf-success' : 'badge-bf-danger' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <h6 class="fw-bold mb-3" style="color:var(--bf-navy);">Perfil institucional</h6>
                                                <p class="mb-2"><strong>Persona vinculada:</strong><br><span class="text-muted">{{ $persona?->nombre ?? 'Sin persona vinculada' }}</span></p>
                                                <p class="mb-2"><strong>Departamento:</strong><br><span class="text-muted">{{ $departamento }}</span></p>
                                                <p class="mb-0"><strong>Cargo:</strong><br><span class="text-muted">{{ $cargo }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <h6 class="fw-bold mb-3" style="color:var(--bf-navy);">Actividad</h6>
                                                <p class="mb-2"><strong>Documentos historicos:</strong> <span class="badge-bf badge-bf-navy">{{ $usuario->total_documentos }}</span></p>
                                                <p class="mb-2"><strong>Documentos visibles:</strong> <span class="badge-bf badge-bf-blue">{{ $usuario->documentos_en_reporte }}</span></p>
                                                <p class="mb-0"><strong>Ultimos registros:</strong> <span class="badge-bf badge-bf-gold">{{ $usuario->ultimos_cambios->count() }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold mt-3 mb-2" style="color:var(--bf-navy);">Documentos recientes</h6>
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
                                                    <td><small>{{ \Illuminate\Support\Str::limit($cambio['asunto'], 70) }}</small></td>
                                                    <td><span class="badge-bf badge-bf-gray">{{ $cambio['tipo'] }}</span></td>
                                                    <td><span class="badge-bf badge-bf-navy">{{ $cambio['estado'] }}</span></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-4 text-center text-muted" style="background:var(--bf-light);border:1px dashed var(--bf-border);border-radius:12px;">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Sin actividad documental visible con los filtros aplicados.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                        No existen usuarios con registros para los filtros seleccionados.
                    </div>
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
