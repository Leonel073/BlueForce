@extends('layouts.app')

@section('title', 'Reportes Administrativos')

@section('content')

@include('admin.reportes.partials.styles')

<div class="report-container">

    {{-- HERO --}}
    <div class="report-hero">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
            <div>
                <h1><i class="bi bi-bar-chart-fill"></i> Reportes Administrativos</h1>
                <p>Estadisticas y control documental institucional</p>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-navy">
                <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="stat-value">{{ $totalDocumentos }}</div>
                <div class="stat-label">Total Documentos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-value">{{ $totalUsuarios }}</div>
                <div class="stat-label">Usuarios</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-gold">
                <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div class="stat-value">{{ $totalDerivaciones }}</div>
                <div class="stat-label">Derivaciones</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value">{{ $documentosFinalizados }}</div>
                <div class="stat-label">Finalizados</div>
            </div>
        </div>
    </div>

    {{-- PENDIENTES --}}
    <div class="stat-card stat-danger mb-4" style="padding:1.2rem;">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <div class="stat-icon" style="width:44px;height:44px;font-size:1.2rem;"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value" style="font-size:1.8rem;">{{ $documentosPendientes }}</div>
                <div class="stat-label">Documentos Pendientes</div>
            </div>
        </div>
    </div>

    {{-- MENU DE REPORTES --}}
    <div class="row g-3">
        {{-- PERSONAS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.personas') }}" class="menu-card">
                <div class="report-card">
                    <div class="icon-wrapper icon-personas"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="menu-title">Reporte Personas</div>
                    <p class="menu-desc">Directorio con CI y roles</p>
                </div>
            </a>
        </div>

        {{-- USUARIOS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.usuarios') }}" class="menu-card">
                <div class="report-card">
                    <div class="icon-wrapper icon-usuarios"><i class="bi bi-people-fill"></i></div>
                    <div class="menu-title">Reporte Usuarios</div>
                    <p class="menu-desc">Actividad documental</p>
                </div>
            </a>
        </div>

        {{-- DEPARTAMENTOS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.departamentos') }}" class="menu-card">
                <div class="report-card">
                    <div class="icon-wrapper icon-deptos"><i class="bi bi-building-fill"></i></div>
                    <div class="menu-title">Reporte Deptos.</div>
                    <p class="menu-desc">Flujo institucional</p>
                </div>
            </a>
        </div>

        {{-- DOCUMENTOS --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.documentos') }}" class="menu-card">
                <div class="report-card">
                    <div class="icon-wrapper icon-docs"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div class="menu-title">Documentos</div>
                    <p class="menu-desc">Inventario por Tipos y Estados</p>
                </div>
            </a>
        </div>

        {{-- DERIVACIONES --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.reportes.derivaciones') }}" class="menu-card">
                <div class="report-card">
                    <div class="icon-wrapper icon-deriv"><i class="bi bi-arrow-left-right"></i></div>
                    <div class="menu-title">Derivaciones</div>
                    <p class="menu-desc">Trazabilidad documental</p>
                </div>
            </a>
        </div>
    </div>

</div>

@endsection
