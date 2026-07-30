@extends('layouts.app')

@section('title', 'Detalle del Usuario')

@section('content')
@php
    $formatDate = function ($value, bool $withTime = false) {
        if (!$value) {
            return 'N/A';
        }

        return \Carbon\Carbon::parse($value)->format($withTime ? 'd/m/Y H:i' : 'd/m/Y');
    };

    $estadoClass = function (?string $estado) {
        $estado = strtolower($estado ?? '');

        if (str_contains($estado, 'archivado')) {
            return 'bg-secondary';
        }

        if (str_contains($estado, 'atendido')) {
            return 'bg-success';
        }

        if (str_contains($estado, 'recibido')) {
            return 'bg-primary';
        }

        if (str_contains($estado, 'pendiente')) {
            return 'bg-warning text-dark';
        }

        return 'bg-info text-dark';
    };

    $urgenciaClass = function (?string $urgencia) {
        $urgencia = strtolower($urgencia ?? '');

        if (str_contains($urgencia, 'alta') || str_contains($urgencia, 'urgente')) {
            return 'bg-danger';
        }

        if (str_contains($urgencia, 'media')) {
            return 'bg-warning text-dark';
        }

        return 'bg-light text-dark border';
    };

    $ultimoDocumento = $documentos->first();
    $ultimaDerivacionAsignada = $derivacionesAsignadas->first();
@endphp

<style>
    .user-detail-page {
        color: #132238;
    }

    .user-hero {
        background: linear-gradient(135deg, #0B2D59 0%, #2E608C 100%);
        border-radius: 8px;
        color: #fff;
    }

    .metric-card {
        border: 1px solid #d8e0ea;
        border-left: 4px solid #D9A23D;
        border-radius: 8px;
        background: #fff;
        min-height: 104px;
    }

    .metric-card .metric-value {
        color: #0B2D59;
        font-size: 1.85rem;
        line-height: 1;
    }

    .section-card {
        border: 1px solid #d8e0ea;
        border-radius: 8px;
        background: #fff;
    }

    .section-title {
        color: #0B2D59;
        font-size: 1rem;
    }

    .data-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .65rem 0;
        border-bottom: 1px solid #eef2f6;
    }

    .data-row:last-child {
        border-bottom: 0;
    }

    .data-label {
        color: #65758b;
        font-size: .82rem;
    }

    .data-value {
        font-weight: 600;
        text-align: right;
    }

    .activity-table thead th {
        background: #0B2D59;
        color: #fff;
        border-color: #0B2D59;
        font-size: .78rem;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .activity-table td {
        vertical-align: middle;
    }

    .nav-tabs .nav-link {
        color: #52647a;
        font-weight: 600;
    }

    .nav-tabs .nav-link.active {
        color: #0B2D59;
        border-top: 3px solid #D9A23D;
    }

    .empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        padding: 2rem;
        text-align: center;
    }
</style>

<div class="container-fluid py-4 user-detail-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="{{ route('admin.usuarios') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left-circle me-1"></i>
            Volver
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn text-white rounded-3" style="background-color:#D9A23D;">
                <i class="bi bi-pencil-fill me-1"></i>
                Editar
            </a>
        </div>
    </div>

    <div class="user-hero p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="rounded-circle bg-white text-center d-inline-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;color:#0B2D59;">
                        <i class="bi bi-person-fill fs-3"></i>
                    </div>
                    <div>
                        <h1 class="h3 fw-bold mb-1">{{ $usuario->name }}</h1>
                        <p class="mb-0 opacity-75">{{ $usuario->email }}</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-person-gear me-1"></i>
                        {{ $usuario->rol?->nombre ?? 'Sin rol' }}
                    </span>
                    <span class="badge {{ $usuario->activo ? 'bg-success' : 'bg-danger' }}">
                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-building me-1"></i>
                        {{ $usuario->persona?->departamento?->nombre ?? 'Sin departamento' }}
                    </span>
                </div>
            </div>

            <div class="text-md-end">
                <div class="small opacity-75">Último documento registrado</div>
                <div class="fw-semibold">
                    {{ $ultimoDocumento ? $formatDate($ultimoDocumento->fecha, true) : 'Sin actividad documental' }}
                </div>
                <div class="small opacity-75 mt-2">Pendientes asignadas</div>
                <div class="fs-4 fw-bold">{{ $metricas['derivacionesPendientes'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="metric-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small text-muted">Documentos registrados</div>
                        <div class="metric-value fw-bold">{{ $metricas['documentos'] }}</div>
                    </div>
                    <i class="bi bi-file-earmark-text fs-3 text-muted"></i>
                </div>
                <div class="small text-muted mt-2">{{ $metricas['conPdf'] }} con PDF adjunto</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small text-muted">Documentos pendientes</div>
                        <div class="metric-value fw-bold">{{ $metricas['pendientes'] }}</div>
                    </div>
                    <i class="bi bi-hourglass-split fs-3 text-muted"></i>
                </div>
                <div class="small text-muted mt-2">{{ $metricas['atendidosArchivados'] }} atendidos o archivados</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small text-muted">Derivaciones enviadas</div>
                        <div class="metric-value fw-bold">{{ $metricas['derivacionesEnviadas'] }}</div>
                    </div>
                    <i class="bi bi-send-check fs-3 text-muted"></i>
                </div>
                <div class="small text-muted mt-2">Movimientos que generó el usuario</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small text-muted">Derivaciones asignadas</div>
                        <div class="metric-value fw-bold">{{ $metricas['derivacionesAsignadas'] }}</div>
                    </div>
                    <i class="bi bi-inbox-fill fs-3 text-muted"></i>
                </div>
                <div class="small text-muted mt-2">{{ $metricas['derivacionesPendientes'] }} pendientes de recepción</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="section-card p-4 mb-4">
                <h2 class="section-title fw-bold mb-3">
                    <i class="bi bi-person-badge-fill me-1"></i>
                    Información institucional
                </h2>

                <div class="data-row">
                    <span class="data-label">ID usuario</span>
                    <span class="data-value">{{ $usuario->id }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Rol</span>
                    <span class="data-value">{{ $usuario->rol?->nombre ?? 'N/A' }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Estado de cuenta</span>
                    <span class="data-value">
                        <span class="badge {{ $usuario->activo ? 'bg-success' : 'bg-danger' }}">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </span>
                </div>

                @if($usuario->persona)
                    <hr>
                    <div class="data-row">
                        <span class="data-label">Persona vinculada</span>
                        <span class="data-value">{{ $usuario->persona->nombre }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">CI</span>
                        <span class="data-value">{{ $usuario->persona->ci ?? 'N/A' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Cargo</span>
                        <span class="data-value">{{ $usuario->persona->cargos_nombres ?? 'Sin cargo' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Departamento</span>
                        <span class="data-value">{{ $usuario->persona->departamento?->nombre ?? 'Sin departamento' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Tipo</span>
                        <span class="data-value">
                            <span class="badge {{ $usuario->persona->tipo === 'INTERNO' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $usuario->persona->tipo }}
                            </span>
                        </span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Celular</span>
                        <span class="data-value">{{ $usuario->persona->telefono_celular ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="empty-state mt-3">
                        <i class="bi bi-exclamation-triangle-fill d-block fs-3 mb-2"></i>
                        Usuario sin persona vinculada.
                    </div>
                @endif
            </div>

            <div class="section-card p-4">
                <h2 class="section-title fw-bold mb-3">
                    <i class="bi bi-activity me-1"></i>
                    Lectura rápida
                </h2>
                <p class="text-muted small mb-3">
                    Esta ficha resume qué registra, qué deriva y qué tiene pendiente el usuario dentro del flujo documental.
                </p>
                <div class="data-row">
                    <span class="data-label">Última derivación asignada</span>
                    <span class="data-value">{{ $ultimaDerivacionAsignada ? $formatDate($ultimaDerivacionAsignada->fechaEnvio, true) : 'Sin asignaciones' }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Carga pendiente</span>
                    <span class="data-value">{{ $metricas['derivacionesPendientes'] }} derivaciones</span>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="section-card">
                <div class="p-4 pb-0">
                    <h2 class="section-title fw-bold mb-3">
                        <i class="bi bi-kanban-fill me-1"></i>
                        Actividad del usuario
                    </h2>

                    <ul class="nav nav-tabs" id="usuarioDetalleTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-pane" type="button" role="tab">
                                Documentos
                                <span class="badge bg-secondary ms-1">{{ $documentos->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="asignadas-tab" data-bs-toggle="tab" data-bs-target="#asignadas-pane" type="button" role="tab">
                                Asignadas
                                <span class="badge bg-secondary ms-1">{{ $derivacionesAsignadas->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="enviadas-tab" data-bs-toggle="tab" data-bs-target="#enviadas-pane" type="button" role="tab">
                                Enviadas
                                <span class="badge bg-secondary ms-1">{{ $derivacionesEnviadas->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-4" id="usuarioDetalleTabsContent">
                    <div class="tab-pane fade show active" id="docs-pane" role="tabpanel" aria-labelledby="docs-tab" tabindex="0">
                        @if($documentos->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-file-earmark-x d-block fs-3 mb-2"></i>
                                No existen documentos registrados por este usuario.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover activity-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Cite</th>
                                            <th>Documento</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Ubicación</th>
                                            <th class="text-end">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($documentos as $doc)
                                            <tr>
                                                <td class="fw-semibold text-nowrap">{{ $doc->cite }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $doc->asunto }}</div>
                                                    <div class="small text-muted">
                                                        {{ $doc->tipoDocumento->nombre ?? 'Sin tipo' }}
                                                        <span class="mx-1">·</span>
                                                        <span class="badge {{ $urgenciaClass($doc->urgencia?->nombre) }}">
                                                            {{ $doc->urgencia->nombre ?? 'Normal' }}
                                                        </span>
                                                        @if($doc->ruta_pdf)
                                                            <span class="badge bg-light text-dark border ms-1">PDF</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-nowrap">{{ $formatDate($doc->fecha, true) }}</td>
                                                <td>
                                                    <span class="badge {{ $estadoClass($doc->estado?->nombre) }}">
                                                        {{ $doc->estado->nombre ?? 'Sin estado' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $doc->ultimaDerivacion?->departamentoDestino?->nombre
                                                        ?? $doc->seguimientos->sortByDesc('fecha')->first()?->ubicacion
                                                        ?? 'Sin movimiento' }}
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.correspondencia.show', $doc->idDocumento) }}?volver=usuarios"
                                                       class="btn btn-sm btn-outline-primary rounded-3">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="asignadas-pane" role="tabpanel" aria-labelledby="asignadas-tab" tabindex="0">
                        @if($derivacionesAsignadas->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                                No existen derivaciones asignadas a este usuario.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover activity-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Documento</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Enviado por</th>
                                            <th>Estado</th>
                                            <th>Recepción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($derivacionesAsignadas as $derivacion)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $derivacion->documento?->cite ?? 'Sin cite' }}</div>
                                                    <div class="small text-muted">{{ $derivacion->documento?->asunto ?? 'Sin asunto' }}</div>
                                                </td>
                                                <td>{{ $derivacion->departamentoOrigen?->nombre ?? 'N/A' }}</td>
                                                <td>{{ $derivacion->departamentoDestino?->nombre ?? 'N/A' }}</td>
                                                <td>{{ $derivacion->usuarioEnvio?->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($derivacion->fechaRecepcion)
                                                        <span class="badge bg-success">Recibida</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="small">Envío: {{ $formatDate($derivacion->fechaEnvio, true) }}</div>
                                                    <div class="small text-muted">Recepción: {{ $formatDate($derivacion->fechaRecepcion, true) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="enviadas-pane" role="tabpanel" aria-labelledby="enviadas-tab" tabindex="0">
                        @if($derivacionesEnviadas->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-send-x d-block fs-3 mb-2"></i>
                                No existen derivaciones enviadas por este usuario.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover activity-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Documento</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Asignado a</th>
                                            <th>Instrucción</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($derivacionesEnviadas as $derivacion)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $derivacion->documento?->cite ?? 'Sin cite' }}</div>
                                                    <div class="small text-muted">{{ $formatDate($derivacion->fechaEnvio, true) }}</div>
                                                </td>
                                                <td>{{ $derivacion->departamentoOrigen?->nombre ?? 'N/A' }}</td>
                                                <td>{{ $derivacion->departamentoDestino?->nombre ?? 'N/A' }}</td>
                                                <td>{{ $derivacion->usuarioAsignado?->name ?? 'N/A' }}</td>
                                                <td>{{ $derivacion->instruccion ?: 'Sin instrucción' }}</td>
                                                <td>
                                                    @if($derivacion->fechaRecepcion)
                                                        <span class="badge bg-success">Recibida</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
