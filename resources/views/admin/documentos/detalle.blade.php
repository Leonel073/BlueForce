@extends('layouts.app')

@section('title', 'Detalle Documento')

@section('content')

@php

    $volverUrl = request()->query('volver')
        ? route(request()->query('volver'))
        : route('admin.documentos.index');

@endphp

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h1 class="fw-bold text-white">

                        <i class="bi bi-file-earmark-text-fill"></i>

                        Detalle del Documento

                    </h1>

                    <p class="text-light mb-0">

                        Información institucional del documento

                    </p>

                </div>

                <a href="{{ $volverUrl }}"
                   class="btn btn-light rounded-4">

                    <i class="bi bi-arrow-left"></i>

                    Volver

                </a>

            </div>

        </div>

    </div>

    <div class="row">

        {{-- INFORMACIÓN PRINCIPAL --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    Información General

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="text-muted small">

                                Cite

                            </label>

                            <h5 class="fw-bold">

                                {{ $documento->cite }}

                            </h5>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="text-muted small">

                                Fecha

                            </label>

                            <h5>

                                {{ \Carbon\Carbon::parse($documento->fecha)->format('d/m/Y H:i') }}

                            </h5>

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="text-muted small">

                            Asunto

                        </label>

                        <div class="border rounded-4 p-3 bg-light">

                            {{ $documento->asunto }}

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="text-muted small">

                                Tipo Documento

                            </label>

                            <div>

                                <span class="badge bg-primary">

                                    {{ $documento->tipoDocumento->nombre ?? 'N/A' }}

                                </span>

                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="text-muted small">

                                Estado

                            </label>

                            <div>

                                <span class="badge bg-success">

                                    {{ $documento->estado->nombre ?? 'N/A' }}

                                </span>

                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="text-muted small">

                                Urgencia

                            </label>

                            <div>

                                @php

                                    $u =
                                        strtolower(
                                            $documento->urgencia->nombre ?? ''
                                        );

                                @endphp

                                @if(str_contains($u, 'urg') || str_contains($u, 'crit'))

                                    <span class="badge bg-danger rounded-pill px-3 py-2">

                                        {{ $documento->urgencia->nombre ?? 'N/A' }}

                                    </span>

                                @elseif(str_contains($u, 'alta'))

                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:#ea580c;color:#fff;">

                                        {{ $documento->urgencia->nombre ?? 'N/A' }}

                                    </span>

                                @elseif(str_contains($u, 'media') || str_contains($u, 'moder'))

                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">

                                        {{ $documento->urgencia->nombre ?? 'N/A' }}

                                    </span>

                                @else

                                    <span class="badge bg-success rounded-pill px-3 py-2">

                                        {{ $documento->urgencia->nombre ?? 'N/A' }}

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- SEGUIMIENTO --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    Seguimiento del Documento

                </div>

                <div class="card-body">

                    @php

                        $nombresDeptosDerivacion =
                            $documento->derivaciones
                                ->flatMap(function ($d) {
                                    return [
                                        strtolower(
                                            trim(
                                                $d->departamentoOrigen->nombre ?? ''
                                            )
                                        ),
                                        strtolower(
                                            trim(
                                                $d->departamentoDestino->nombre ?? ''
                                            )
                                        ),
                                    ];
                                })
                                ->filter()
                                ->unique();

                        $seguimientosVisibles =
                            $documento->seguimientos->filter(
                                function ($s) use ($nombresDeptosDerivacion) {

                                    $loc =
                                        strtolower(
                                            trim($s->ubicacion ?? '')
                                        );

                                    if ($loc === '') {
                                        return false;
                                    }

                                    if (
                                        str_contains($loc, 'derivado')
                                        || str_contains($loc, 'derivación')
                                    ) {
                                        return false;
                                    }

                                    if (
                                        $nombresDeptosDerivacion->contains(
                                            $loc
                                        )
                                    ) {
                                        return false;
                                    }

                                    return true;

                                }
                            );

                    @endphp

                    @if($seguimientosVisibles->isNotEmpty())

                        @foreach($seguimientosVisibles as $seguimiento)

                            <div class="border-start border-4 border-primary ps-3 mb-4">

                                <h6 class="fw-bold">

                                    {{ $seguimiento->ubicacion }}

                                </h6>

                                <small class="text-muted">

                                    {{ \Carbon\Carbon::parse($seguimiento->fecha)->format('d/m/Y H:i') }}

                                </small>

                            </div>

                        @endforeach

                    @elseif($documento->derivaciones->isEmpty())

                        <div class="text-muted text-center py-2 small">

                            No hay movimientos de seguimiento registrados.

                        </div>

                    @endif

                    @if($documento->derivaciones->isNotEmpty())

                        @if($seguimientosVisibles->isNotEmpty())

                            <hr class="my-4">

                        @endif

                        <h6 class="fw-bold mb-3" style="color:#0B2D59;">

                            <i class="bi bi-arrow-left-right me-2"></i>

                            Derivaciones del documento

                        </h6>

                        @foreach($documento->derivaciones->sortBy('orden') as $der)

                            <div class="border-start border-4 border-warning ps-3 mb-4 pb-1">

                                <div class="small text-muted mb-1">

                                    {{ \Carbon\Carbon::parse($der->fechaEnvio)->format('d/m/Y H:i') }}

                                </div>

                                <div class="fw-semibold">

                                    {{ $der->departamentoOrigen->nombre ?? 'N/A' }}

                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>

                                    {{ $der->departamentoDestino->nombre ?? 'N/A' }}

                                </div>

                                <div class="mt-2 small">

                                    <span class="text-muted">Derivado por:</span>

                                    @if($der->usuarioEnvio)

                                        <strong>{{ $der->usuarioEnvio->name }}</strong>

                                    @else

                                        <span class="badge bg-secondary bg-opacity-50 text-dark">

                                            No registrado

                                        </span>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    @endif

                </div>

            </div>

        </div>

        {{-- PANEL DERECHO --}}
        <div class="col-lg-4">

            {{-- REMITENTE --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    Remitente

                </div>

                <div class="card-body">

                    <h5 class="fw-bold">

                        {{ $documento->remitente->nombre ?? 'N/A' }}

                    </h5>

                    <hr>

                    <p class="mb-2">

                        <strong>CI:</strong>

                        {{ $documento->remitente->ci ?? 'N/A' }}

                    </p>

                    <p class="mb-2">

                        <strong>Correo:</strong>

                        {{ $documento->remitente->correo ?? 'N/A' }}

                    </p>

                    <p class="mb-2">

                        <strong>Celular:</strong>

                        {{ $documento->remitente->telefono_celular ?? 'N/A' }}

                    </p>

                    <p class="mb-2">

                        <strong>Cargo:</strong>

                       {{ $documento->remitente->cargos_nombres ?? 'N/A' }}

                    </p>

                    <p class="mb-0">

                        <strong>Institución:</strong>

                        {{ $documento->remitente->institucion ?? 'N/A' }}

                    </p>

                </div>

            </div>

            {{-- DESTINO ACTUAL --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    <i class="bi bi-building-fill"></i>

                    Destino Actual

                </div>

                <div class="card-body">

                    @php

                        $ultimaDerivacion =
                            $documento->derivaciones
                                ->sortByDesc('orden')
                                ->first();

                        $departamentoActual =
                            $ultimaDerivacion?->departamentoDestino;

                    @endphp

                    @if($departamentoActual)

                        {{-- DEPARTAMENTO --}}
                        <div class="mb-3">

                            <label class="text-muted small">

                                Departamento Destino

                            </label>

                            <h5 class="fw-bold text-primary mb-0">

                                {{ $departamentoActual->nombre }}

                            </h5>

                        </div>

                        <hr>

                        {{-- RESPONSABLE --}}
                        <div>

                            <label class="text-muted small">

                                Responsable

                            </label>

                            <h6 class="fw-semibold mb-1">

                                {{ $departamentoActual->encargado->nombre ?? 'Sin responsable asignado' }}

                            </h6>

                            @if($departamentoActual->encargado)

                                <small class="text-muted">

                                    CI:
                                    {{ $departamentoActual->encargado->ci ?? 'N/A' }}

                                </small>

                            @endif

                        </div>

                    @else

                        <div class="text-muted text-center py-3">

                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                            Documento sin derivación actual

                        </div>

                    @endif

                </div>

            </div>

            {{-- USUARIO --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    Registrado por

                </div>

                <div class="card-body">

                    <h5 class="fw-bold">

                        {{ $documento->usuario->name ?? 'N/A' }}

                    </h5>

                </div>

            </div>

            {{-- ARCHIVO PDF ADJUNTO --}}
            @if($documento->tiene_archivo)
            <div class="card border-0 shadow-sm rounded-4 mt-3">
                <div class="card-header text-white rounded-top-4"
                     style="background-color:#c0392b;">
                    <i class="bi bi-file-pdf-fill me-1"></i>
                    Documento PDF Adjunto
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <i class="bi bi-paperclip me-1 text-danger"></i>
                        <strong>{{ $documento->archivo_pdf }}</strong>
                    </p>
                    <p class="small text-muted mb-3">
                        Tamaño: {{ $documento->tamano_formateado }}
                        @if($documento->fecha_subida)
                            — Subido el {{ $documento->fecha_subida->format('d/m/Y H:i') }}
                        @endif
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('documentos.pdf.descargar', $documento->idDocumento) }}"
                           class="btn btn-danger btn-sm rounded-3">
                            <i class="bi bi-download me-1"></i>
                            Descargar PDF
                        </a>
                        <a href="{{ route('documentos.pdf.previsualizar', $documento->idDocumento) }}"
                           target="_blank"
                           class="btn btn-outline-danger btn-sm rounded-3">
                            <i class="bi bi-eye me-1"></i>
                            Ver PDF
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

@endsection
