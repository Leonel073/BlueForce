@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-speedometer2"></i>

                Dashboard Administrativo

            </h1>

            <p class="text-light mb-0">

                Panel general del sistema documental

            </p>

        </div>

    </div>

    {{-- CARDS --}}
    <div class="row mb-4">

        {{-- DOCUMENTOS --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <i class="bi bi-file-earmark-text-fill fs-1 text-primary"></i>

                <h1 class="fw-bold mt-3">

                    {{ $totalDocumentos }}

                </h1>

                <div class="text-muted">

                    Documentos

                </div>

            </div>

        </div>

        {{-- USUARIOS --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <i class="bi bi-people-fill fs-1 text-success"></i>

                <h1 class="fw-bold mt-3">

                    {{ $totalUsuarios }}

                </h1>

                <div class="text-muted">

                    Usuarios

                </div>

            </div>

        </div>

        {{-- DERIVACIONES --}}
        <div class="col-md-4 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <i class="bi bi-arrow-left-right fs-1 text-warning"></i>

                <h1 class="fw-bold mt-3">

                    {{ $totalDerivaciones }}

                </h1>

                <div class="text-muted">

                    Derivaciones

                </div>

            </div>

        </div>

    </div>

    {{-- ACCESOS --}}
    <div class="row mb-4">

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.usuarios') }}"
               class="text-decoration-none">

                <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                    <i class="bi bi-people-fill fs-1 text-primary"></i>

                    <h5 class="fw-bold mt-3">

                        Usuarios

                    </h5>

                </div>

            </a>

        </div>

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.correspondencia') }}"
               class="text-decoration-none">

                <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                    <i class="bi bi-folder-fill fs-1 text-warning"></i>

                    <h5 class="fw-bold mt-3">

                        Correspondencia

                    </h5>

                </div>

            </a>

        </div>

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.reportes.index') }}"
               class="text-decoration-none">

                <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                    <i class="bi bi-bar-chart-fill fs-1 text-success"></i>

                    <h5 class="fw-bold mt-3">

                        Reportes

                    </h5>

                </div>

            </a>

        </div>

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.reportes.derivaciones') }}"
               class="text-decoration-none">

                <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                    <i class="bi bi-arrow-left-right fs-1 text-danger"></i>

                    <h5 class="fw-bold mt-3">

                        Derivaciones

                    </h5>

                </div>

            </a>

        </div>

    </div>

    {{-- DOCUMENTOS RECIENTES --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Últimos Documentos

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Cite</th>

                            <th>Asunto</th>

                            <th>Fecha</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($documentosRecientes as $doc)

                            <tr>

                                <td>

                                    {{ $doc->cite }}

                                </td>

                                <td>

                                    {{ $doc->asunto }}

                                </td>

                                <td>

                                    {{ $doc->fecha }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- DERIVACIONES RECIENTES --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#2E608C;">

            Últimas Derivaciones

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Documento</th>

                            <th>Origen</th>

                            <th>Destino</th>

                            <th>Fecha</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($derivacionesRecientes as $d)

                            <tr>

                                <td>

                                    {{ $d->documento->cite ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->departamentoOrigen->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->departamentoDestino->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->fechaEnvio }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection