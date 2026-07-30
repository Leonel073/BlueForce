@extends('layouts.app')

@section('title', 'Anuncios')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <h1 class="fw-bold text-white mb-2">
                <i class="bi bi-megaphone-fill me-2"></i>
                Anuncios
            </h1>
            <p class="text-light mb-0">
                Comunicados activos de la institución
            </p>
        </div>
    </div>

    @if($anuncios->count() > 0)
        <div class="row">
            @foreach($anuncios as $anuncio)
                @php $visto = $anuncio->vistas->isNotEmpty(); @endphp
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-lg rounded-4 h-100 {{ !$visto ? 'border-start border-4' : '' }}"
                         style="{{ !$visto ? 'border-color:#D9A23D !important;' : '' }}">
                        <div class="card-header border-0 rounded-top-4 text-white p-4"
                             style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="fw-bold mb-0">{{ $anuncio->titulo }}</h5>
                                @if($visto)
                                    <span class="badge bg-success ms-2">
                                        <i class="bi bi-check-circle me-1"></i>Visto
                                    </span>
                                @else
                                    <span class="badge ms-2"
                                          style="background-color:#D9A23D; color:#0B2D59;">
                                        <i class="bi bi-circle-fill me-1"></i>Nuevo
                                    </span>
                                @endif
                            </div>
                            <small class="text-white-50 mt-2 d-block">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $anuncio->fechaCreacion->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <p class="text-muted mb-3 flex-grow-1">{{ Str::limit($anuncio->asunto, 150) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($anuncio->tienePdf())
                                        <span class="badge bg-light text-dark">
                                            <i class="bi {{ $anuncio->archivoIcono() }} me-1"></i>{{ $anuncio->archivoTipoLabel() }} adjunto
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('user.anuncios.show', $anuncio->idAnuncio) }}"
                                   class="btn btn-sm text-white rounded-3"
                                   style="background: linear-gradient(135deg,#0B2D59,#2E608C); border:none;">
                                    <i class="bi bi-eye me-1"></i>Ver anuncio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $anuncios->links() }}
        </div>
    @else
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-megaphone text-muted" style="font-size:3rem;"></i>
                <p class="text-muted mt-3 mb-0">No hay anuncios activos en este momento.</p>
            </div>
        </div>
    @endif

</div>

@endsection
