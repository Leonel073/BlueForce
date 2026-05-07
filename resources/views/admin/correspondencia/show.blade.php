@extends('layouts.app')

@section('title', 'Detalle de Correspondencia')

@section('content')

<div class="container-fluid py-4">

    {{-- CABECERA --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body d-flex justify-content-between align-items-center">

            <div>

                <h2 class="text-white fw-bold mb-1">

                    <i class="bi bi-file-earmark-text-fill"></i>

                    Detalle del Documento

                </h2>

                <p class="text-light mb-0">

                    Información completa y seguimiento documental

                </p>

            </div>

            <a href="{{ route('admin.correspondencia') }}"
               class="btn text-white rounded-3"
               style="background-color:#D9A23D; color:#0B2D59;">

                <i class="bi bi-arrow-left-circle-fill"></i>

                Volver

            </a>

        </div>

    </div>


    <div class="row">

        {{-- INFORMACIÓN PRINCIPAL --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    <h5 class="mb-0">

                        Información General

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <strong>Cite:</strong>

                            <p>{{ $documento->cite }}</p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Fecha:</strong>

                            <p>{{ $documento->fecha }}</p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Tipo Documento:</strong>

                            <p>

                                {{ $documento->tipoDocumento->nombre ?? 'Sin tipo' }}

                            </p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Usuario Responsable:</strong>

                            <p>

                                {{ $documento->usuario->name ?? 'Sin usuario' }}

                            </p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Estado:</strong>

                            <br>

                            <span class="badge bg-success">

                                {{ $documento->estado->nombre ?? 'Sin estado' }}

                            </span>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Urgencia:</strong>

                            <br>

                            <span class="badge"
                                  style="background-color:#D9A23D;
                                         color:#0B2D59;">

                                {{ $documento->urgencia->nombre ?? 'Normal' }}

                            </span>

                        </div>

                        <div class="col-12">

                            <strong>Asunto:</strong>

                            <div class="p-3 rounded-3 mt-2"
                                 style="background-color:#F5F7FA;">

                                {{ $documento->asunto }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- SEGUIMIENTO --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#2E608C;">

                    <h5 class="mb-0">

                        Seguimiento del Documento

                    </h5>

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
            {{-- DERIVACIONES --}}
<div class="card border-0 shadow-sm rounded-4 mt-4">

    <div class="card-header text-white rounded-top-4"
         style="background-color:#D9A23D; color:#0B2D59;">

        <h5 class="mb-0">

            Flujo de Derivaciones

        </h5>

    </div>

    <div class="card-body">

        @forelse($documento->derivaciones as $derivacion)

            <div class="border rounded-4 p-3 mb-3">

                <div class="row align-items-center">

                    {{-- ORIGEN --}}
                    <div class="col-md-3">

                        <small class="text-muted">

                            Departamento Origen

                        </small>

                        <h6 class="fw-bold mb-0">

                            {{ $derivacion->departamentoOrigen->nombre ?? 'N/A' }}

                        </h6>

                    </div>

                    {{-- FLECHA --}}
                    <div class="col-md-1 text-center">

                        <i class="bi bi-arrow-right-circle-fill"
                           style="font-size:1.5rem; color:#0B2D59;"></i>

                    </div>

                    {{-- DESTINO --}}
                    <div class="col-md-3">

                        <small class="text-muted">

                            Departamento Destino

                        </small>

                        <h6 class="fw-bold mb-0">

                            {{ $derivacion->departamentoDestino->nombre ?? 'N/A' }}

                        </h6>

                    </div>

                    {{-- USUARIO --}}
                    <div class="col-md-3">

                        <small class="text-muted">

                            Usuario Asignado

                        </small>

                        <h6 class="fw-bold mb-0">

                            {{ $derivacion->usuarioAsignado->name ?? 'Sin asignar' }}

                        </h6>

                    </div>

                    {{-- ORDEN --}}
                    <div class="col-md-2 text-center">

                        <span class="badge rounded-pill px-3 py-2"
                              style="background-color:#2E608C;">

                            Paso {{ $derivacion->orden }}

                        </span>

                    </div>

                </div>

                {{-- INSTRUCCIÓN --}}
                @if($derivacion->instruccion)

                    <div class="mt-3 p-3 rounded-3"
                         style="background-color:#F5F7FA;">

                        <strong>Instrucción:</strong>

                        <br>

                        {{ $derivacion->instruccion }}

                    </div>

                @endif

                {{-- FECHAS --}}
                <div class="row mt-3">

                    <div class="col-md-6">

                        <small class="text-muted">

                            Fecha Envío

                        </small>

                        <div>

                            {{ $derivacion->fechaEnvio }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <small class="text-muted">

                            Fecha Recepción

                        </small>

                        <div>

                            {{ $derivacion->fechaRecepcion ?? 'Pendiente' }}

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="alert alert-warning mb-0">

                No existen derivaciones registradas.

            </div>

        @endforelse

    </div>

</div>

        </div>
        


        {{-- PANEL LATERAL --}}
        <div class="col-lg-4">

            {{-- REMITENTE --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">

                    <h5 class="mb-0">

                        Remitente

                    </h5>

                </div>

                <div class="card-body">

                    <p>

                        <strong>Nombre:</strong><br>

                        {{ $documento->remitente->nombre ?? 'N/A' }}

                    </p>

                    <p>

                        <strong>Correo:</strong><br>

                        {{ $documento->remitente->correo ?? 'N/A' }}

                    </p>

                    <p>

                        <strong>Cargo:</strong><br>

                        {{ $documento->remitente->cargo ?? 'N/A' }}

                    </p>

                    <p>

                        <strong>Institución:</strong><br>

                        {{ $documento->remitente->institucion ?? 'N/A' }}

                    </p>

                </div>

            </div>
            

            {{-- ACCIONES --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header text-white rounded-top-4"
                     style="background-color:#D9A23D; color:#0B2D59;">

                    <h5 class="mb-0">

                        Acciones Administrativas

                    </h5>

                </div>

                <div class="card-body d-grid gap-3">

                    <button class="btn text-white rounded-3"
                            style="background-color:#0B2D59;">

                        <i class="bi bi-pencil-square"></i>

                        Editar Documento

                    </button>

                    <button class="btn btn-success rounded-3">

                        <i class="bi bi-check-circle-fill"></i>

                        Cambiar Estado

                    </button>

                    <button class="btn btn-warning rounded-3 text-dark">

                        <i class="bi bi-arrow-left-right"></i>

                        Derivar Documento

                    </button>

                    <button class="btn btn-danger rounded-3">

                        <i class="bi bi-trash-fill"></i>

                        Desactivar

                    </button>

                </div>
                
            </div>

        </div>

    </div>

</div>

@endsection