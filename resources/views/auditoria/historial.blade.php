@extends('layouts.app')

@section('title', 'Historial - ' . $modelo)

@section('content')
<div class="container-fluid p-4">
    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('admin.auditoria.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
                <i class="bi bi-chevron-left"></i> Volver
            </a>
            <h1 class="h3 fw-bold" style="color: #0B2D59;">
                <i class="bi bi-clock-history"></i> Historial de {{ $modelo }}
            </h1>
            <p class="text-muted mb-0">ID del Registro: {{ $idRegistro }}</p>
        </div>
    </div>

    <!-- TIMELINE -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <div class="timeline">
                @foreach($auditorias as $auditoria)
                    <div class="timeline-item mb-4" style="padding-left: 40px; position: relative;">
                        <!-- Punto en la línea de tiempo -->
                        <div style="position: absolute; left: 0; top: 0; width: 24px; height: 24px; border-radius: 50%; background: #0B2D59; display: flex; align-items: center; justify-content: center;">
                            @if($auditoria->accion === 'CREATE')
                                <i class="bi bi-plus-circle text-white" style="font-size: 14px;"></i>
                            @elseif($auditoria->accion === 'UPDATE')
                                <i class="bi bi-pencil-square text-white" style="font-size: 14px;"></i>
                            @else
                                <i class="bi bi-trash text-white" style="font-size: 14px;"></i>
                            @endif
                        </div>

                        <!-- Contenido -->
                        <div class="card border-0 shadow-sm rounded-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex gap-2 align-items-center mb-2">
                                            {!! $auditoria->accion_badge !!}
                                            <span class="text-muted small">
                                                {{ $auditoria->fecha->format('d/m/Y H:i:s') }}
                                            </span>
                                        </div>
                                        <p class="mb-2">
                                            <strong>Usuario:</strong>
                                            @if($auditoria->usuario)
                                                {{ $auditoria->usuario->name }}
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </p>
                                        <p class="mb-0">
                                            <strong>Descripción:</strong> {{ $auditoria->cambios_resumo }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('admin.auditoria.show', $auditoria->idAuditoria) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 11px;
    top: 30px;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #0B2D59, rgba(11, 45, 89, 0.2));
}
</style>
@endsection
