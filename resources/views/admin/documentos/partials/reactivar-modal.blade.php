<div class="modal fade" id="modalReactivarDocumento" tabindex="-1" aria-labelledby="modalReactivarDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                <div>
                    <h5 class="modal-title fw-bold" id="modalReactivarDocumentoLabel">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reactivar documento
                    </h5>
                    <small class="text-white-50">Accion administrativa con registro de seguimiento</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" id="formReactivarDocumento">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="alert border-0 rounded-3 mb-3" style="background-color:#fff7e6;color:#775c17;">
                        <div class="d-flex gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div>
                                <div class="fw-bold">El documento volvera a estado Pendiente.</div>
                                <div class="small">
                                    Se asignara nuevamente al ultimo responsable conocido y quedara visible en su bandeja.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#0B2D59;">Documento</label>
                        <div class="border rounded-3 bg-light p-3">
                            <span id="reactivarDocumentoTitulo" class="fw-bold">Documento seleccionado</span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="color:#0B2D59;">Observacion administrativa</label>
                        <textarea name="observacion"
                                  class="form-control rounded-3"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Opcional: motivo de la reactivacion"></textarea>
                        <small class="text-muted">Este texto quedara guardado en auditoria y seguimiento.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning rounded-3 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Confirmar reactivacion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalReactivarDocumento');

    if (!modal) {
        return;
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const form = document.getElementById('formReactivarDocumento');
        const titulo = document.getElementById('reactivarDocumentoTitulo');

        if (!button || !form || !titulo) {
            return;
        }

        form.action = button.getAttribute('data-reactivar-url') || '#';
        titulo.textContent = button.getAttribute('data-documento-titulo') || 'Documento seleccionado';
    });
});
</script>
