@extends('layouts.app')

@section('title', 'Detalles de Auditoría')

@section('content')
<div class="container-fluid p-4">
    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('admin.auditoria.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
                <i class="bi bi-chevron-left"></i> Volver a auditoría
            </a>
            <h1 class="h3 fw-bold" style="color: #0B2D59;">
                <i class="bi bi-info-circle"></i> Detalles de Auditoría
            </h1>
        </div>
    </div>

    <!-- INFORMACIÓN PRINCIPAL -->
    <div class="row g-4 mb-4">
        <!-- RESUMEN -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-info-square"></i> Información del Evento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #0B2D59;">ID Auditoría</label>
                            <p class="text-muted mb-3">{{ $auditoria->idAuditoria }}</p>

                            <label class="form-label small fw-semibold" style="color: #0B2D59;">Fecha</label>
                            <p class="text-muted mb-3">
                                {{ $auditoria->fecha->format('d/m/Y H:i:s') }}
                            </p>

                            <label class="form-label small fw-semibold" style="color: #0B2D59;">Usuario</label>
                            <p class="text-muted mb-3">
                                @if($auditoria->usuario)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-person-circle"></i>
                                        {{ $auditoria->usuario->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #0B2D59;">Acción</label>
                            <p class="mb-3">
                                {!! $auditoria->accion_badge !!}
                            </p>

                            <label class="form-label small fw-semibold" style="color: #0B2D59;">Modelo Afectado</label>
                            <p class="text-muted mb-3">
                                <strong>{{ $auditoria->modelo }}</strong> (ID: {{ $auditoria->idRegistro }})
                            </p>

                            <label class="form-label small fw-semibold" style="color: #0B2D59;">IP de Origen</label>
                            <p class="text-muted mb-0">
                                <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px;">
                                    {{ $auditoria->ip }}
                                </code>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold" style="color: #0B2D59;">Navegador</label>
                        <p class="text-muted small mb-0">
                            {{ $auditoria->navegador }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TARJETA LATERAL -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(11, 45, 89, 0.1), rgba(46, 96, 140, 0.1));">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($auditoria->accion === 'CREATE')
                            <i class="bi bi-plus-circle text-success" style="font-size: 48px;"></i>
                        @elseif($auditoria->accion === 'UPDATE')
                            <i class="bi bi-pencil-square text-warning" style="font-size: 48px;"></i>
                        @else
                            <i class="bi bi-trash text-danger" style="font-size: 48px;"></i>
                        @endif
                    </div>
                    <h5 style="color: #0B2D59;">{{ $auditoria->accion }}</h5>
                    <p class="text-muted mb-0 small">
                        @if($auditoria->accion === 'CREATE')
                            Registro creado
                        @elseif($auditoria->accion === 'UPDATE')
                            Registro actualizado
                        @else
                            Registro eliminado
                        @endif
                    </p>
                </div>
            </div>

            <!-- RUTA -->
            <div class="card border-0 shadow-sm rounded-3 mt-3">
                <div class="card-body">
                    <label class="form-label small fw-semibold" style="color: #0B2D59;">Ruta Accedida</label>
                    <p class="text-muted small mb-0" style="word-break: break-all;">
                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                            {{ $auditoria->ruta }}
                        </code>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CAMBIOS DETALLADOS -->
    @if($auditoria->accion !== 'DELETE')
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-arrow-left-right"></i> 
                    @if($auditoria->accion === 'CREATE')
                        Valores Creados
                    @else
                        Cambios Realizados
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($auditoria->accion === 'CREATE')
                    <!-- Mostrar datos nuevos -->
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="color: #0B2D59;">Campo</th>
                                    <th style="color: #0B2D59;">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cambios['cambios'] ?? [] as $campo => $valor)
                                    <tr>
                                        <td class="small fw-semibold">{{ $campo }}</td>
                                        <td class="small">
                                            <span style="background: rgba(25, 135, 84, 0.1); padding: 4px 8px; border-radius: 4px; color: #198754;">
                                                {{ is_array($valor) || is_object($valor) ? json_encode($valor) : $valor }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Mostrar cambios UPDATE -->
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="color: #0B2D59;">Campo</th>
                                    <th style="color: #0B2D59;">Valor Anterior</th>
                                    <th style="color: #0B2D59;">Valor Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cambios['cambios'] ?? [] as $campo => $cambio)
                                    <tr>
                                        <td class="small fw-semibold">{{ $campo }}</td>
                                        <td class="small">
                                            <span style="background: rgba(220, 53, 69, 0.1); padding: 4px 8px; border-radius: 4px; color: #DC3545; text-decoration: line-through;">
                                                {{ is_array($cambio['anterior']) || is_object($cambio['anterior']) ? json_encode($cambio['anterior']) : ($cambio['anterior'] ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="small">
                                            <span style="background: rgba(25, 135, 84, 0.1); padding: 4px 8px; border-radius: 4px; color: #198754;">
                                                {{ is_array($cambio['nuevo']) || is_object($cambio['nuevo']) ? json_encode($cambio['nuevo']) : $cambio['nuevo'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No hay cambios detectados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Para DELETE, mostrar datos eliminados -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #DC3545, #c82333); border: none;">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-trash"></i> Datos Eliminados
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="color: #0B2D59;">Campo</th>
                                <th style="color: #0B2D59;">Valor Eliminado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cambios['cambios'] ?? [] as $campo => $valor)
                                <tr>
                                    <td class="small fw-semibold">{{ $campo }}</td>
                                    <td class="small">
                                        <span style="background: rgba(220, 53, 69, 0.1); padding: 4px 8px; border-radius: 4px; color: #DC3545;">
                                            {{ is_array($valor) || is_object($valor) ? json_encode($valor) : $valor }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- HISTORIAL DE CAMBIOS DEL REGISTRO -->
    @if($auditoriasDeMismo->count() > 1)
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-clock-history"></i> Historial Completo del Registro
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="color: #0B2D59;" class="small fw-semibold">Fecha</th>
                                <th style="color: #0B2D59;" class="small fw-semibold">Usuario</th>
                                <th style="color: #0B2D59;" class="small fw-semibold">Acción</th>
                                <th style="color: #0B2D59;" class="small fw-semibold">Cambios</th>
                                <th style="color: #0B2D59;" class="small fw-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditoriasDeMismo as $aud)
                                <tr style="background: {{ $aud->idAuditoria === $auditoria->idAuditoria ? 'rgba(11, 45, 89, 0.05)' : 'transparent' }};">
                                    <td class="small">
                                        {{ $aud->fecha->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="small">
                                        @if($aud->usuario)
                                            {{ $aud->usuario->name }}
                                        @else
                                            Sistema
                                        @endif
                                    </td>
                                    <td>
                                        {!! $aud->accion_badge !!}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $aud->cambios_resumo }}
                                    </td>
                                    <td class="small">
                                        @if($aud->idAuditoria !== $auditoria->idAuditoria)
                                            <a href="{{ route('admin.auditoria.show', $aud->idAuditoria) }}" 
                                               class="btn btn-xs btn-outline-primary btn-sm"
                                               title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge bg-light text-dark">Actual</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.7rem;
}
</style>
@endsection
