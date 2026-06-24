<!-- Acciones de Transición de Estado -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient text-white">
        <h6 class="mb-0">
            <i class="bi bi-lightning me-2"></i> Acciones Disponibles
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-2">
            @php
                $transiciones = $documento->obtenerTransicionesPermitidas();
            @endphp

            @if(count($transiciones) > 0)
                @foreach($transiciones as $transicion)
                    <div class="col-12">
                        <button class="btn btn-{{ $transicion['clase'] }} w-100" 
                                type="button" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalTransicion{{ $transicion['metodo'] }}">
                            <i class="bi {{ $transicion['metodo'] === 'RECIBIR' ? 'bi-check-circle' : ($transicion['metodo'] === 'ATENDER' ? 'bi-hand-thumbs-up' : 'bi-archive') }} me-2"></i>
                            {{ $transicion['nombre'] }}
                        </button>
                    </div>

                    <!-- Modal para Transición -->
                    <div class="modal fade" id="modalTransicion{{ $transicion['metodo'] }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h6 class="modal-title">
                                        @if($transicion['metodo'] === 'RECIBIR')
                                            <i class="bi bi-check-circle text-success me-2"></i> Recibir Documento
                                        @elseif($transicion['metodo'] === 'ATENDER')
                                            <i class="bi bi-hand-thumbs-up text-primary me-2"></i> Atender Documento
                                        @else
                                            <i class="bi bi-archive text-secondary me-2"></i> Archivar Documento
                                        @endif
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="form{{ $transicion['metodo'] }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Observación (Opcional)</label>
                                            <textarea class="form-control" name="observacion" rows="3" placeholder="Agregue una observación sobre esta acción..."></textarea>
                                            <small class="text-muted">Máximo 500 caracteres</small>
                                        </div>

                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle me-2"></i>
                                            @if($transicion['metodo'] === 'RECIBIR')
                                                Confirmar que ha recibido el documento. El estado cambiará a <strong>Recibido</strong>.
                                            @elseif($transicion['metodo'] === 'ATENDER')
                                                Marcar el documento como atendido. El estado cambiará a <strong>Atendido</strong>.
                                            @else
                                                Archivar el documento definitivamente. El estado cambiará a <strong>Archivado</strong>.
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-{{ $transicion['clase'] }}">
                                            Confirmar {{ $transicion['nombre'] }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.getElementById('form{{ $transicion['metodo'] }}').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const observacion = this.querySelector('textarea[name="observacion"]').value;
                            const url = '/api/v1/documentos/{{ $documento->idDocumento }}/{{ strtolower($transicion['metodo'] === 'RECIBIR' ? 'recibir' : ($transicion['metodo'] === 'ATENDER' ? 'atender' : 'archivar')) }}';
                            
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer ' + (localStorage.getItem('token') || ''),
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                },
                                body: JSON.stringify({
                                    observacion: observacion || null
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Acción realizada correctamente');
                                    location.reload();
                                } else {
                                    alert('Error: ' + (data.message || 'Error desconocido'));
                                }
                            })
                            .catch(error => {
                                alert('Error: ' + error.message);
                            });
                        });
                    </script>
                @endforeach
            @else
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No hay acciones disponibles para este documento en su estado actual.
                    <br>
                    <small class="text-muted">Estado actual: <strong>{{ $documento->estado?->nombre }}</strong></small>
                </div>
            @endif
        </div>
    </div>
</div>
