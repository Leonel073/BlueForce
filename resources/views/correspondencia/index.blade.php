@extends('layouts.app')

@section('title', 'Mi Correspondencia')

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

                        Mi Correspondencia

                    </h1>

                    <p class="text-light mb-0">

                        Gestión documental personal

                    </p>

                </div>

                <div>

                  <a href="{{ route('documentos.crear') }}"
   class="btn text-white rounded-4 px-4 shadow-sm"
   style="background: linear-gradient(135deg,#D9A23D,#BF8A2E); border:none;">

    <i class="bi bi-file-earmark-plus-fill me-2"></i>

    Registrar Documento

</a>

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

                Documentos Registrados

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

                                {{-- URGENCIA --}}
                                <td>

                                    @if(
                                        optional($doc->urgencia)->nombre == 'Urgente'
                                    )

                                        <span class="badge bg-danger rounded-pill">

                                            Urgente

                                        </span>

                                    @else

                                        <span class="badge bg-secondary rounded-pill">

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

                                    <div class="btn-group">

                                        {{-- VER --}}
                                       <a href="{{ route('correspondencia.show', $doc->idDocumento) }}"
                                           class="btn btn-sm text-white"
                                           style="background-color:#0B2D59;"
                                           title="Ver Documento">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                      

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
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

        </div>

    </div>

</div>

@endsection