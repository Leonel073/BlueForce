@extends('layouts.app')

@section('title', 'Correspondencia - Administrador')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h1 class="fw-bold text-white mb-1">

                        <i class="bi bi-folder-fill"></i>

                        Correspondencia General

                    </h1>

                    <p class="text-light mb-0">

                        Vista administrativa - TODOS los documentos

                    </p>

                </div>

            </div>

        </div>

    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="row mb-4">

        {{-- TOTAL --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-primary">

                    {{ $totalDocumentos }}

                </h1>

                <div class="text-muted">

                    Total Documentos

                </div>

            </div>

        </div>

        {{-- PENDIENTES --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-warning">

                    {{ $pendientes }}

                </h1>

                <div class="text-muted">

                    Pendientes

                </div>

            </div>

        </div>

        {{-- FINALIZADOS --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-success">

                    {{ $finalizados }}

                </h1>

                <div class="text-muted">

                    Finalizados

                </div>

            </div>

        </div>

        {{-- URGENTES --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-danger">

                    {{ $urgentes }}

                </h1>

                <div class="text-muted">

                    Urgentes

                </div>

            </div>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4 d-flex justify-content-between align-items-center"
             style="background-color:#0B2D59;">

            <span>

                <i class="bi bi-files"></i>

                Correspondencia Registrada

            </span>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Cite</th>

                            <th>Asunto</th>

                            <th>Remitente</th>

                            <th>Departamento</th>

                            <th>Usuario</th>

                            <th>Urgencia</th>

                            <th>Estado</th>

                            <th>Fecha</th>

                            <th class="text-center">

                                Acciones

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($documentos as $doc)

                            <tr>

                                {{-- CITE --}}
                                <td>

                                    <span class="fw-semibold">

                                        {{ $doc->cite }}

                                    </span>

                                </td>

                                {{-- ASUNTO --}}
                                <td>

                                    {{ $doc->asunto }}

                                </td>

                                {{-- REMITENTE --}}
                                <td>

                                    {{ $doc->remitente->nombre ?? 'N/A' }}

                                </td>

                                {{-- DEPARTAMENTO DESTINO --}}
                                <td>

                                    {{

                                        optional(
                                            $doc->derivaciones->last()
                                        )->departamentoDestino->nombre

                                        ?? 'Sin destino'

                                    }}

                                </td>

                                {{-- USUARIO REGISTRADOR --}}
                                <td>

                                    <small>{{ $doc->usuario->name ?? 'N/A' }}</small>

                                </td>

                                {{-- URGENCIA --}}
                                <td>

                                    @php

                                        $u =
                                            strtolower(
                                                $doc->urgencia->nombre ?? ''
                                            );

                                    @endphp

                                    @if(str_contains($u, 'urg') || str_contains($u, 'crit'))

                                        <span class="badge rounded-pill px-3 py-2 bg-danger">

                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @elseif(str_contains($u, 'alta'))

                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background:#ea580c;color:#fff;">

                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @elseif(str_contains($u, 'media') || str_contains($u, 'moder'))

                                        <span class="badge rounded-pill px-3 py-2 bg-warning text-dark">

                                            <i class="bi bi-exclamation-circle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @else

                                        <span class="badge rounded-pill px-3 py-2 bg-success">

                                            <i class="bi bi-check-circle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td>

                                    @php

                                        $estado =
                                            $doc->estado->nombre ?? 'N/A';

                                    @endphp

                                    @if($estado == 'Finalizado')

                                        <span class="badge bg-success rounded-pill">

                                            {{ $estado }}

                                        </span>

                                    @elseif($estado == 'Pendiente')

                                        <span class="badge bg-warning text-dark rounded-pill">

                                            {{ $estado }}

                                        </span>

                                    @elseif($estado == 'Derivado')

                                        <span class="badge bg-primary rounded-pill">

                                            {{ $estado }}

                                        </span>

                                    @else

                                        <span class="badge bg-secondary rounded-pill">

                                            {{ $estado }}

                                        </span>

                                    @endif

                                </td>

                                {{-- FECHA --}}
                                <td>

                                    {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y H:i') }}

                                </td>

                                {{-- ACCIONES --}}
                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="{{ route('correspondencia.show', $doc->idDocumento) }}?volver=admin"
                                           class="btn btn-sm btn-doc btn-doc-view"
                                           title="Ver documento">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>

                                        No existen documentos registrados.

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-center mt-3">

                {{ $documentos->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
