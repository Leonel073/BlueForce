@php
    $modalId = $modalId ?? 'anuncioModal';
    $redirectUrl = $redirectUrl ?? url()->current();
    $detalleUrl = $detalleUrl ?? null;
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Title" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <div class="modal-header border-0 text-white p-4"
                 style="background:linear-gradient(135deg,#071e3d,#0B2D59 48%,#2E608C); border-bottom:5px solid #D9A23D !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-white text-primary"
                         style="width:54px;height:54px;border-radius:14px;color:#0B2D59 !important;">
                        <i class="bi bi-megaphone-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="badge rounded-pill mb-2" style="background:#D9A23D;color:#172033;">Comunicado institucional</span>
                        <h5 class="modal-title fw-bold mb-1" id="{{ $modalId }}Title">{{ $anuncioPendiente->titulo }}</h5>
                        <small class="text-white-50">
                            Publicado el {{ $anuncioPendiente->fechaCreacion->format('d/m/Y H:i') }}
                            @if($anuncioPendiente->creador)
                                por {{ $anuncioPendiente->creador->name }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="{{ $anuncioPendiente->tienePdf() ? 'col-lg-5' : 'col-12' }}">
                        <div class="p-4 h-100" style="background:#F8FAFC;">
                            <div class="mb-3">
                                <span class="badge rounded-pill" style="background:#E8EFF6;color:#0B2D59;border:1px solid #DDE3EC;">
                                    <i class="bi bi-info-circle me-1"></i> Debe marcarse como visto
                                </span>
                            </div>
                            <div style="white-space:pre-wrap;line-height:1.75;font-size:1rem;color:#1A2942;">{{ $anuncioPendiente->asunto }}</div>
                        </div>
                    </div>

                    @if($anuncioPendiente->tienePdf())
                        <div class="col-lg-7">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                                    <strong style="color:#0B2D59;">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                        {{ $anuncioPendiente->archivo_pdf }}
                                    </strong>
                                    <a href="{{ route('anuncios.pdf.descargar', $anuncioPendiente->idAnuncio) }}"
                                       class="btn btn-sm btn-outline-danger rounded-3">
                                        <i class="bi bi-download me-1"></i> Descargar
                                    </a>
                                </div>
                                <iframe src="{{ route('anuncios.pdf.previsualizar', $anuncioPendiente->idAnuncio) }}"
                                        class="w-100 rounded-3 border"
                                        style="height:430px;"
                                        title="PDF del anuncio"></iframe>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer border-0 p-4 justify-content-between" style="background:#fff;">
                <div class="text-muted small">
                    <i class="bi bi-shield-check me-1"></i>
                    El sistema registra la confirmacion por cada cuenta.
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($detalleUrl)
                        <a href="{{ $detalleUrl }}" class="btn btn-outline-primary rounded-3">
                            <i class="bi bi-eye me-1"></i> Ver detalle
                        </a>
                    @endif
                    <form action="{{ route('anuncios.marcar-visto', $anuncioPendiente->idAnuncio) }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect" value="{{ $redirectUrl }}">
                        <button type="submit" class="btn text-white rounded-3 px-4"
                                style="background:linear-gradient(135deg,#D9A23D,#b07d1a);border:none;">
                            <i class="bi bi-check-circle-fill me-1"></i> Marcar como visto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('{{ $modalId }}');
    if (modalElement) {
        new bootstrap.Modal(modalElement).show();
    }
});
</script>
