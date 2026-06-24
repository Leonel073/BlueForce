<!-- Historial de Transiciones de Estado -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient text-white">
        <h6 class="mb-0">
            <i class="bi bi-clock-history me-2"></i> Historial de Transiciones de Estado
        </h6>
    </div>
    <div class="card-body">
        @if($documento->obtenerHistorialTransiciones()->count() > 0)
            <div class="timeline">
                @foreach($documento->obtenerHistorialTransiciones() as $transicion)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $transicion->accion === 'CREAR' ? 'info' : ($transicion->accion === 'DERIVAR' ? 'warning' : ($transicion->accion === 'RECIBIR' ? 'success' : ($transicion->accion === 'ATENDER' ? 'primary' : 'secondary'))) }}">
                            @switch($transicion->accion)
                                @case('CREAR')
                                    <i class="bi bi-plus-circle"></i>
                                    @break
                                @case('DERIVAR')
                                    <i class="bi bi-arrow-right-circle"></i>
                                    @break
                                @case('RECIBIR')
                                    <i class="bi bi-check-circle"></i>
                                    @break
                                @case('ATENDER')
                                    <i class="bi bi-hand-thumbs-up"></i>
                                    @break
                                @case('ARCHIVAR')
                                    <i class="bi bi-archive"></i>
                                    @break
                            @endswitch
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">
                                <span class="badge bg-{{ $transicion->accion === 'CREAR' ? 'info' : ($transicion->accion === 'DERIVAR' ? 'warning' : ($transicion->accion === 'RECIBIR' ? 'success' : ($transicion->accion === 'ATENDER' ? 'primary' : 'secondary'))) }}">
                                    {{ $transicion->accion_legible }}
                                </span>
                            </h6>
                            <p class="mb-1">
                                <strong>Transición:</strong> {{ $transicion->resumen }}
                            </p>
                            @if($transicion->usuario)
                                <p class="mb-1">
                                    <strong>Usuario:</strong> {{ $transicion->usuario->name }}
                                </p>
                            @endif
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-calendar"></i> {{ $transicion->fecha_formateada }}
                            </p>
                            @if($transicion->observacion)
                                <p class="mb-0 mt-2 p-2 bg-light border-start border-4 border-{{ $transicion->accion === 'CREAR' ? 'info' : ($transicion->accion === 'DERIVAR' ? 'warning' : ($transicion->accion === 'RECIBIR' ? 'success' : ($transicion->accion === 'ATENDER' ? 'primary' : 'secondary'))) }}">
                                    <small>{{ $transicion->observacion }}</small>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i> No hay transiciones registradas aún.
            </div>
        @endif
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    z-index: 1;
}

.timeline-content {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #dee2e6;
}
</style>
