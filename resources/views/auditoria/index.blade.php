@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('content')
<div class="container-fluid p-4">
    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #0B2D59, #2E608C);">
                    <i class="bi bi-shield-check text-white" style="font-size: 24px;"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 fw-bold" style="color: #0B2D59;">
                        <i class="bi bi-eye"></i> Auditoría del Sistema
                    </h1>
                    <p class="text-muted mb-0">Registros de todas las operaciones realizadas</p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.auditoria.estadisticas') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-graph-up"></i> Estadísticas
            </a>
            <a href="{{ route('admin.auditoria.exportar') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Exportar CSV
            </a>
        </div>
    </div>

    <!-- RESUMEN RÁPIDO -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Auditorías</p>
                            <h4 class="mb-0" style="color: #0B2D59;">{{ $resumen['total'] }}</h4>
                        </div>
                        <div class="p-3 rounded-circle" style="background: rgba(13, 110, 253, 0.2);">
                            <i class="bi bi-list-check text-primary" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(25, 135, 84, 0.05));">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Creaciones</p>
                            <h4 class="mb-0" style="color: #198754;">{{ $resumen['creaciones'] }}</h4>
                        </div>
                        <div class="p-3 rounded-circle" style="background: rgba(25, 135, 84, 0.2);">
                            <i class="bi bi-plus-circle text-success" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Actualizaciones</p>
                            <h4 class="mb-0" style="color: #FFC107;">{{ $resumen['actualizaciones'] }}</h4>
                        </div>
                        <div class="p-3 rounded-circle" style="background: rgba(255, 193, 7, 0.2);">
                            <i class="bi bi-pencil-square text-warning" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Eliminaciones</p>
                            <h4 class="mb-0" style="color: #DC3545;">{{ $resumen['eliminaciones'] }}</h4>
                        </div>
                        <div class="p-3 rounded-circle" style="background: rgba(220, 53, 69, 0.2);">
                            <i class="bi bi-trash text-danger" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.auditoria.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold" style="color: #0B2D59;">Usuario</label>
                    <select name="idUsuario" class="form-select form-select-sm">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $id => $nombre)
                            <option value="{{ $id }}" {{ $idUsuario == $id ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold" style="color: #0B2D59;">Acción</label>
                    <select name="accion" class="form-select form-select-sm">
                        <option value="todas">Todas</option>
                        @foreach($acciones as $acc)
                            <option value="{{ $acc }}" {{ $accion == $acc ? 'selected' : '' }}>
                                @if($acc === 'CREATE')
                                    <i class="bi bi-plus-circle"></i> Crear
                                @elseif($acc === 'UPDATE')
                                    <i class="bi bi-pencil-square"></i> Actualizar
                                @else
                                    <i class="bi bi-trash"></i> Eliminar
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold" style="color: #0B2D59;">Modelo</label>
                    <select name="modelo" class="form-select form-select-sm">
                        <option value="todos">Todos</option>
                        @foreach($modelos as $mod)
                            <option value="{{ $mod }}" {{ $modelo == $mod ? 'selected' : '' }}>
                                {{ $mod }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold" style="color: #0B2D59;">Desde</label>
                    <input type="date" name="fechaInicio" class="form-control form-control-sm" 
                           value="{{ $fechaInicio }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold" style="color: #0B2D59;">Hasta</label>
                    <input type="date" name="fechaFin" class="form-control form-control-sm" 
                           value="{{ $fechaFin }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLA DE AUDITORÍAS -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
            <h5 class="mb-0 text-white">
                <i class="bi bi-table"></i> Registro de Operaciones
            </h5>
        </div>
        <div class="card-body p-0">
            @if($auditorias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr style="background: #f8f9fa;">
                                <th class="small fw-semibold" style="color: #0B2D59;">Fecha</th>
                                <th class="small fw-semibold" style="color: #0B2D59;">Usuario</th>
                                <th class="small fw-semibold" style="color: #0B2D59;">Acción</th>
                                <th class="small fw-semibold" style="color: #0B2D59;">Modelo</th>
                                <th class="small fw-semibold" style="color: #0B2D59;">Cambios</th>
                                <th class="small fw-semibold" style="color: #0B2D59;">IP</th>
                                <th class="small fw-semibold" style="color: #0B2D59;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditorias as $auditoria)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td class="small">
                                        <span class="text-muted">
                                            {{ $auditoria->fecha->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        @if($auditoria->usuario)
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-person-circle"></i>
                                                {{ $auditoria->usuario->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sistema</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $auditoria->accion_badge !!}
                                    </td>
                                    <td class="small">
                                        <span class="text-primary fw-semibold">
                                            {{ $auditoria->modelo }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <span class="text-muted">
                                            {{ $auditoria->cambios_resumo }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                            {{ $auditoria->ip }}
                                        </code>
                                    </td>
                                    <td class="small">
                                        <a href="{{ route('admin.auditoria.show', $auditoria->idAuditoria) }}" 
                                           class="btn btn-xs btn-outline-primary btn-sm"
                                           title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <div class="p-3 border-top">
                    {{ $auditorias->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                    </div>
                    <p class="text-muted mb-0">No hay registros de auditoría que coincidan con los filtros</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.7rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(11, 45, 89, 0.05);
}
</style>
@endsection
