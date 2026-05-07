@extends('layouts.app')

@section('title', 'Reportes Administrativos')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-bar-chart-fill"></i>

                Reportes Administrativos

            </h1>

            <p class="text-light mb-0">

                Estadísticas y control documental institucional

            </p>

        </div>

    </div>

    {{-- CARDS --}}
    <div class="row mb-4">

        {{-- TOTAL DOCUMENTOS --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold"
                    style="color:#0B2D59;">

                    {{ $totalDocumentos }}

                </h1>

                <div class="text-muted">

                    Total Documentos

                </div>

            </div>

        </div>

        {{-- USUARIOS --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-primary">

                    {{ $totalUsuarios }}

                </h1>

                <div class="text-muted">

                    Usuarios

                </div>

            </div>

        </div>

        {{-- DERIVACIONES --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-warning">

                    {{ $totalDerivaciones }}

                </h1>

                <div class="text-muted">

                    Derivaciones

                </div>

            </div>

        </div>

        {{-- FINALIZADOS --}}
        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm rounded-4 text-center p-4">

                <h1 class="fw-bold text-success">

                    {{ $documentosFinalizados }}

                </h1>

                <div class="text-muted">

                    Finalizados

                </div>

            </div>

        </div>

    </div>

    {{-- PENDIENTES --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body text-center">

            <h2 class="fw-bold text-danger">

                {{ $documentosPendientes }}

            </h2>

            <div class="text-muted">

                Documentos Pendientes

            </div>

        </div>

    </div>
    {{-- ACCESOS A REPORTES --}}
<div class="row mb-4">

    {{-- REPORTE USUARIOS --}}
    <div class="col-md-4 mb-3">

        <a href="{{ route('admin.reportes.usuarios') }}"
           class="text-decoration-none">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <i class="bi bi-people-fill fs-1"
                       style="color:#0B2D59;"></i>

                    <h4 class="fw-bold mt-3">

                        Reporte Usuarios

                    </h4>

                    <p class="text-muted mb-0">

                        Actividad documental por usuario

                    </p>

                </div>

            </div>

        </a>

    </div>

    {{-- REPORTE DEPARTAMENTOS --}}
    <div class="col-md-4 mb-3">

        <a href="{{ route('admin.reportes.departamentos') }}"
           class="text-decoration-none">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <i class="bi bi-building-fill fs-1"
                       style="color:#D9A23D;"></i>

                    <h4 class="fw-bold mt-3">

                        Reporte Departamentos

                    </h4>

                    <p class="text-muted mb-0">

                        Flujo documental institucional

                    </p>

                </div>

            </div>

        </a>

    </div>

    {{-- REPORTE DERIVACIONES --}}
    <div class="col-md-4 mb-3">

        <a href="{{ route('admin.reportes.derivaciones') }}"
            class="text-decoration-none">

                <div class="card border-0 shadow-sm rounded-4 h-100">

                    <div class="card-body text-center p-4">

                        <i class="bi bi-arrow-left-right fs-1"
                        style="color:#2E608C;"></i>

                        <h4 class="fw-bold mt-3">

                            Reporte Derivaciones

                        </h4>

                        <p class="text-muted mb-0">

                            Trazabilidad documental completa

                        </p>

                    </div>

                </div>

            </a>

        </div>

    </div>

    {{-- ÚLTIMOS DOCUMENTOS --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Últimos Documentos Registrados

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

                        @forelse($ultimosDocumentos as $doc)

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

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center text-muted">

                                    No existen registros.

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