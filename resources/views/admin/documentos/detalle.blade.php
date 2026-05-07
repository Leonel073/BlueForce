@extends('layouts.app')

@section('title', 'Detalle del Documento')

@section('content')

<div class="container py-4">

    {{-- CABECERA --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body text-white">

            <h2 class="fw-bold">
                <i class="bi bi-file-earmark-text-fill"></i>
                {{ $documento->cite }}
            </h2>

            <p class="mb-0">
                {{ $documento->asunto }}
            </p>

        </div>

    </div>

    <div class="row">

        {{-- INFORMACIÓN --}}
        <div class="col-md-5">

            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header text-white"
                     style="background-color:#0B2D59;">

                    Información del Documento

                </div>

                <div class="card-body">

                    <p>
                        <strong>Tipo:</strong>
                        {{ $documento->tipoDocumento->nombre ?? 'N/A' }}
                    </p>

                    <p>
                        <strong>Estado:</strong>

                        <span class="badge bg-success">
                            {{ $documento->estado->nombre ?? 'N/A' }}
                        </span>
                    </p>

                    <p>
                        <strong>Urgencia:</strong>

                        <span class="badge"
                              style="background:#D9A23D;color:#0B2D59;">

                            {{ $documento->urgencia->nombre ?? 'Normal' }}

                        </span>
                    </p>

                    <p>
                        <strong>Fecha:</strong>
                        {{ $documento->fecha }}
                    </p>

                    <p>
                        <strong>Creado por:</strong>
                        {{ $documento->usuario->name ?? 'N/A' }}
                    </p>

                </div>

            </div>

            {{-- REMITENTE --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header text-white"
                     style="background-color:#2E608C;">

                    Remitente

                </div>

                <div class="card-body">

                    <p>
                        <strong>Nombre:</strong>
                        {{ $documento->remitente->nombre ?? 'N/A' }}
                    </p>

                    <p>
                        <strong>Correo:</strong>
                        {{ $documento->remitente->correo ?? 'N/A' }}
                    </p>

                    <p>
                        <strong>Institución:</strong>
                        {{ $documento->remitente->institucion ?? 'N/A' }}
                    </p>

                </div>

            </div>

        </div>

        {{-- SEGUIMIENTO --}}
        <div class="col-md-7">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header text-white"
                     style="background-color:#0B2D59;">

                    Historial de Seguimiento

                </div>

                <div class="card-body">

                    @forelse($documento->seguimientos as $seg)

                        <div class="border-start border-4 ps-3 mb-4"
                             style="border-color:#D9A23D !important;">

                            <h6 class="fw-bold mb-1">

                                {{ $seg->ubicacion }}

                            </h6>

                            <small class="text-muted">

                                {{ $seg->fecha }}

                            </small>

                        </div>

                    @empty

                        <div class="alert alert-warning">

                            No existe seguimiento registrado.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection