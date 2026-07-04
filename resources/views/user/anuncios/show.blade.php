@extends('layouts.app')

@section('title', $anuncio->titulo)

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-megaphone-fill me-2"></i>
                {{ $anuncio->titulo }}
            </h2>
            <p class="text-muted mb-0">
                Publicado el {{ $anuncio->fechaCreacion->format('d/m/Y H:i') }}
            </p>
        </div>
        <a href="{{ route('user.anuncios.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-chevron-left"></i> Volver a anuncios
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 mb-4">
                <div class="card-header text-white rounded-top-4 p-4 border-0"
                     style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                    <div class="d-flex gap-2">
                        @if($visto)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Marcado como visto
                            </span>
                        @else
                            <span class="badge"
                                  style="background-color:#D9A23D; color:#0B2D59;">
                                <i class="bi bi-circle-fill me-1"></i>Sin leer
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="white-space: pre-wrap; line-height: 1.7; font-size: 1.05rem;">
                        {{ $anuncio->asunto }}
                    </div>
                </div>
            </div>

            @if($anuncio->tienePdf())
                <div class="card border-0 shadow-lg rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4 border-0"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        {{ $anuncio->archivo_pdf }}
                    </div>
                    <div class="card-body p-0">
                        <iframe src="{{ route('anuncios.pdf.previsualizar', $anuncio->idAnuncio) }}"
                                class="w-100 rounded-bottom-4"
                                style="height:500px; border:none;"
                                title="PDF del anuncio"></iframe>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <a href="{{ route('anuncios.pdf.descargar', $anuncio->idAnuncio) }}"
                           class="btn btn-outline-primary rounded-3">
                            <i class="bi bi-download me-1"></i> Descargar PDF
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    @if(!$visto)
                        <form action="{{ route('anuncios.marcar-visto', $anuncio->idAnuncio) }}" method="POST">
                            @csrf
                            <input type="hidden" name="redirect" value="{{ route('user.anuncios.show', $anuncio->idAnuncio) }}">
                            <button type="submit"
                                    class="btn text-white w-100 rounded-4 py-2 mb-3"
                                    style="background: linear-gradient(135deg,#D9A23D,#BF8A2E); border:none;">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Marcar como visto
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success rounded-3 mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Ya marcaste este anuncio como visto.
                        </div>
                    @endif

                    <a href="{{ route('user.anuncios.index') }}"
                       class="btn btn-outline-secondary w-100 rounded-4">
                        <i class="bi bi-list me-1"></i>
                        Ver todos los anuncios
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
