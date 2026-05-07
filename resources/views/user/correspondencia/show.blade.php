@extends('layouts.app')

@section('title', 'Detalle Documento')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-file-earmark-text-fill"></i>

                Detalle del Documento

            </h1>

        </div>

    </div>

    {{-- DATOS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Cite

                    </label>

                    <div>

                        {{ $documento->cite }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Fecha

                    </label>

                    <div>

                        {{ $documento->fecha }}

                    </div>

                </div>

                <div class="col-md-12 mb-3">

                    <label class="fw-bold">

                        Asunto

                    </label>

                    <div>

                        {{ $documento->asunto }}

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- DERIVACIONES --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Historial de Derivaciones

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Origen</th>

                            <th>Destino</th>

                            <th>Usuario</th>

                            <th>Fecha</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($derivaciones as $d)

                            <tr>

                                <td>

                                    {{ $d->orden }}

                                </td>

                                <td>

                                    {{ $d->departamentoOrigen->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->departamentoDestino->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->usuarioAsignado->name ?? 'Sin asignar' }}

                                </td>

                                <td>

                                    {{ $d->fechaEnvio }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center text-muted">

                                    No existen derivaciones.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection