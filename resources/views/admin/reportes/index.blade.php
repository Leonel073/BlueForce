@extends('layouts.app')

@section('title', 'Reportes Administrativos')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo Empresa" style="width: 80px; margin-right: 20px;">
            <div>
                <h1 class="fw-bold text-white mb-1">
                    <i class="bi bi-bar-chart-fill"></i> Reportes Administrativos
                </h1>
                <p class="text-light mb-0">Estadísticas y control documental institucional</p>
            </div>
        </div>
    </div>

    {{-- CARDS ESTADÍSTICAS --}}
    <div class="row mb-4">
        {{-- TOTAL DOCUMENTOS --}}
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <h1 class="fw-bold" style="color:#0B2D59;">{{ $totalDocumentos }}</h1>
                <div class="text-muted">Total Documentos</div>
            </div>
        </div>

        {{-- USUARIOS --}}
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <h1 class="fw-bold text-primary">{{ $totalUsuarios }}</h1>
                <div class="text-muted">Usuarios</div>
            </div>
        </div>

        {{-- DERIVACIONES --}}
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <h1 class="fw-bold text-warning">{{ $totalDerivaciones }}</h1>
                <div class="text-muted">Derivaciones</div>
            </div>
        </div>

        {{-- FINALIZADOS --}}
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <h1 class="fw-bold text-success">{{ $documentosFinalizados }}</h1>
                <div class="text-muted">Finalizados</div>
            </div>
        </div>
    </div>

    {{-- PENDIENTES --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body text-center">
            <h2 class="fw-bold text-danger">{{ $documentosPendientes }}</h2>
            <div class="text-muted">Documentos Pendientes</div>
        </div>
    </div>

    {{-- ACCESOS A REPORTES (CUADRÍCULA CORREGIDA) --}}
    <div class="row mb-4">

        {{-- REPORTE PERSONAS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.personas') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-person-badge-fill fs-1" style="color:#198754;"></i>
                        <h5 class="fw-bold mt-3 text-dark">Reporte Personas</h5>
                        <p class="text-muted mb-0 small">Directorio con CI y roles</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- REPORTE USUARIOS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.usuarios') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-people-fill fs-1" style="color:#0B2D59;"></i>
                        <h5 class="fw-bold mt-3 text-dark">Reporte Usuarios</h5>
                        <p class="text-muted mb-0 small">Actividad documental</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- REPORTE DEPARTAMENTOS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.departamentos') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-building-fill fs-1" style="color:#D9A23D;"></i>
                        <h5 class="fw-bold mt-3 text-dark">Reporte Deptos.</h5>
                        <p class="text-muted mb-0 small">Flujo institucional</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- REPORTE GENERAL DE DOCUMENTOS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.documentos') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-file-earmark-text-fill fs-1" style="color:#2E608C;"></i>
                        <h5 class="fw-bold mt-3 text-dark">Documentos</h5>
                        <p class="text-muted mb-0 small">Inventario por Tipos y Estados</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- REPORTE DERIVACIONES --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.derivaciones') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-arrow-left-right fs-1" style="color:#2E608C;"></i>
                        <h5 class="fw-bold mt-3 text-dark">Derivaciones</h5>
                        <p class="text-muted mb-0 small">Trazabilidad documental</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>

@endsection
