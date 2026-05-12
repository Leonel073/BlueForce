@extends('layouts.app')

@section('title', 'Bandeja General')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h1 class="fw-bold text-white mb-1">

                        <i class="bi bi-inboxes-fill"></i>

                        Bandeja General Documental

                    </h1>

                    <p class="text-light mb-0">

                        Control y seguimiento institucional de documentos

                    </p>

                </div>

                <div>

                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">

                        {{ $totalDocumentos }} registros

                    </span>

                </div>

            </div>

        </div>

    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="row mb-4">

        {{-- TOTAL --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">

                <div class="mb-2">

                    <i class="bi bi-files fs-1 text-primary"></i>

                </div>

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

            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">

                <div class="mb-2">

                    <i class="bi bi-arrow-left-right fs-1 text-warning"></i>

                </div>

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

            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">

                <div class="mb-2">

                    <i class="bi bi-check-circle-fill fs-1 text-success"></i>

                </div>

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

            <i class="bi bi-folder2-open"></i>

            Flujo General de Documentos

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>CITE</th>

                            <th>ASUNTO</th>

                            <th>REMITENTE</th>

                            <th>ORIGEN</th>

                            <th>DESTINO</th>

                            <th>URGENCIA</th>

                            <th>ESTADO</th>

                            <th>ENVÍO</th>

                            <th>RECEPCIÓN</th>

                            <th class="text-center">

                                ACCIONES

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($derivaciones as $d)

                            <tr>

                                {{-- CITE --}}
                                <td>

                                    <span class="fw-bold text-primary">

                                        {{ $d->documento->cite }}

                                    </span>

                                </td>

                                {{-- ASUNTO --}}
                                <td style="min-width:250px;">

                                    {{ $d->documento->asunto }}

                                </td>

                                {{-- REMITENTE --}}
                                <td>

                                    {{ $d->documento->remitente->nombre ?? 'N/A' }}

                                </td>

                                {{-- ORIGEN --}}
                                <td>

                                    <span class="badge bg-secondary">

                                        {{ $d->departamentoOrigen->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                {{-- DESTINO --}}
                                <td>

                                    <span class="badge bg-info text-dark">

                                        {{ $d->departamentoDestino->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                {{-- URGENCIA --}}
                                <td>

                                    @php
                                        $urgencia = strtolower(
                                            $d->documento->urgencia->nombre ?? ''
                                        );
                                    @endphp

                                    @if(str_contains($urgencia, 'alta'))

                                        <span class="badge bg-danger">

                                            {{ $d->documento->urgencia->nombre }}

                                        </span>

                                    @elseif(str_contains($urgencia, 'media'))

                                        <span class="badge bg-warning text-dark">

                                            {{ $d->documento->urgencia->nombre }}

                                        </span>

                                    @else

                                        <span class="badge bg-success">

                                            {{ $d->documento->urgencia->nombre ?? 'Normal' }}

                                        </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td>

                                    @php
                                        $estado =
                                            $d->documento->estado->nombre ?? '';
                                    @endphp

                                    @if($estado == 'Finalizado')

                                        <span class="badge bg-success">

                                            {{ $estado }}

                                        </span>

                                    @elseif($estado == 'Derivado')

                                        <span class="badge bg-primary">

                                            {{ $estado }}

                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">

                                            {{ $estado }}

                                        </span>

                                    @endif

                                </td>

                                {{-- FECHA ENVÍO --}}
                                <td>

                                    {{ \Carbon\Carbon::parse($d->fechaEnvio)->format('d/m/Y H:i') }}

                                </td>

                                {{-- RECEPCIÓN --}}
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
                                <td class="text-center">

                                    <a href="{{ route('correspondencia.show', $d->documento->idDocumento) }}"
                                       class="btn btn-sm text-white rounded-3"
                                       style="background-color:#0B2D59;"
                                       title="Ver Documento">

                                        <i class="bi bi-eye-fill"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10"
                                    class="text-center py-5 text-muted">

                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>

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