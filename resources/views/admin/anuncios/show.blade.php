@extends('layouts.app')

@section('title', 'Detalle del Anuncio')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-megaphone-fill me-2"></i>
                Detalle del Anuncio
            </h2>
            <p class="text-muted mb-0">Publicado el {{ $anuncio->fechaCreacion->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.anuncios.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-chevron-left"></i> Volver al historial
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 mb-4">
                <div class="card-header text-white rounded-top-4 p-4 border-0"
                     style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                    <h4 class="fw-bold mb-1">{{ $anuncio->titulo }}</h4>
                    <div class="d-flex gap-2 mt-2">
                        @if($anuncio->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-eye me-1"></i>{{ $anuncio->vistas_count }} vistas
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-0" style="white-space: pre-wrap; line-height: 1.7;">{{ $anuncio->asunto }}</div>
                </div>
            </div>

            @if($anuncio->tienePdf())
                <div class="card border-0 shadow-lg rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4 border-0"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        Documento Adjunto: {{ $anuncio->archivo_pdf }}
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
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header text-white rounded-top-4"
                     style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                    <i class="bi bi-info-circle me-1"></i> Información
                </div>
                <div class="card-body p-4">
                    <dl class="mb-0">
                        <dt class="text-muted small">Creado por</dt>
                        <dd class="fw-semibold">{{ $anuncio->creador->name ?? '—' }}</dd>

                        <dt class="text-muted small mt-3">Fecha de publicación</dt>
                        <dd>{{ $anuncio->fechaCreacion->format('d/m/Y H:i') }}</dd>

                        <dt class="text-muted small mt-3">Usuarios que lo vieron</dt>
                        <dd>
                            <span class="badge rounded-pill"
                                  style="background-color:#D9A23D; color:#0B2D59; font-size:1rem;">
                                {{ $anuncio->vistas_count }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            @if($anuncio->vistas->count() > 0)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header text-white rounded-top-4"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-people me-1"></i> Usuarios que marcaron como visto
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" style="max-height:400px; overflow-y:auto;">
                            @foreach($anuncio->vistas->sortByDesc('fechaVisto') as $vista)
                                <div class="list-group-item px-4 py-3">
                                    <div class="fw-semibold">
                                        {{ $vista->usuario->persona->nombre ?? $vista->usuario->name }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $vista->fechaVisto->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
