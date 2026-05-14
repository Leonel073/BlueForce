@extends('layouts.app')

@section('title', 'Detalle Documento')

@section('content')

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

                <a href="{{ route('admin.correspondencia') }}"
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

                                <span class="badge bg-danger">

                                    {{ $documento->urgencia->nombre ?? 'N/A' }}

                                </span>

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

                    @forelse($documento->seguimientos as $seguimiento)

                        <div class="border-start border-4 border-primary ps-3 mb-4">

                            <h6 class="fw-bold">

                                {{ $seguimiento->ubicacion }}

                            </h6>

                            <small class="text-muted">

                                {{ \Carbon\Carbon::parse($seguimiento->fecha)->format('d/m/Y H:i') }}

                            </small>

                        </div>

                    @empty

                        <div class="text-muted text-center py-4">

                            No existe seguimiento registrado.

                        </div>

                    @endforelse

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

                       {{ $documento->remitente->cargo->nombre ?? 'N/A' }}

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

        </div>

    </div>

</div>

@endsection