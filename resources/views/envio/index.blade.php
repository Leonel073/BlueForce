@extends('layouts.app')

@section('title', 'Bandeja General')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-inboxes-fill"></i>

                Bandeja General Documental

            </h1>

            <p class="text-light mb-0">

                Control general del flujo documental institucional

            </p>

        </div>

    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="row mb-4">

        {{-- TOTAL --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-primary">

                    {{ $totalDocumentos }}

                </h1>

                <div class="text-muted">

                    Total Derivaciones

                </div>

            </div>

        </div>

        {{-- EN TRÁNSITO --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-warning">

                    {{ $enTransito }}

                </h1>

                <div class="text-muted">

                    En Tránsito

                </div>

            </div>

        </div>

        {{-- RECIBIDOS --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-success">

                    {{ $recibidos }}

                </h1>

                <div class="text-muted">

                    Recibidos

                </div>

            </div>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Flujo General de Documentos

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Cite</th>

                            <th>Asunto</th>

                            <th>Remitente</th>

                            <th>Origen</th>

                            <th>Destino</th>

                            <th>Urgencia</th>

                            <th>Estado</th>

                            <th>Envío</th>

                            <th>Recepción</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($derivaciones as $d)

                            <tr>

                                {{-- CITE --}}
                                <td>

                                    {{ $d->documento->cite }}

                                </td>

                                {{-- ASUNTO --}}
                                <td>

                                    {{ $d->documento->asunto }}

                                </td>

                                {{-- REMITENTE --}}
                                <td>

                                    {{ $d->documento->remitente->nombre ?? 'N/A' }}

                                </td>

                                {{-- ORIGEN --}}
                                <td>

                                    {{ $d->departamentoOrigen->nombre ?? 'N/A' }}

                                </td>

                                {{-- DESTINO --}}
                                <td>

                                    {{ $d->departamentoDestino->nombre ?? 'N/A' }}

                                </td>

                                {{-- URGENCIA --}}
                                <td>

                                    <span class="badge bg-danger">

                                        {{ $d->documento->urgencia->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                {{-- ESTADO --}}
                                <td>

                                    <span class="badge bg-primary">

                                        {{ $d->documento->estado->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                {{-- FECHA ENVÍO --}}
                                <td>

                                    {{ $d->fechaEnvio }}

                                </td>

                                {{-- FECHA RECEPCIÓN --}}
                                <td>

                                    @if($d->fechaRecepcion)

                                        <span class="badge bg-success">

                                            Recibido

                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">

                                            En tránsito

                                        </span>

                                    @endif

                                </td>

                                {{-- ACCIONES --}}
                                <td>

                                    <a href="{{ route('admin.correspondencia.show', $d->documento->idDocumento) }}"
                                       class="btn btn-sm text-white"
                                       style="background-color:#0B2D59;">

                                        <i class="bi bi-eye-fill"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10"
                                    class="text-center text-muted">

                                    No existen derivaciones registradas.

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