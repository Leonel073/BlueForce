@extends('layouts.app')
@section('title', 'Reporte por Departamentos')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <h1 class="fw-bold text-white"><i class="bi bi-building-fill"></i> Reporte por Departamentos</h1>
            <p class="text-light mb-0">Flujo documental institucional por áreas</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.departamentos') }}">
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Nombre del Departamento</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Recursos Humanos..." value="{{ request('nombre') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Movimientos Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Movimientos Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100" style="background-color:#0B2D59;"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">Resultados de la búsqueda</h5>
            <a href="{{ route('admin.reportes.departamentos.pdf', request()->query()) }}" class="btn btn-danger shadow-sm">
                <i class="bi bi-file-pdf-fill"></i> Exportar a PDF
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white text-start">Departamento</th>
                            <th class="text-white">Documentos Recibidos</th>
                            <th class="text-white">Documentos Enviados</th>
                            <th class="text-white">Movimiento Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departamentos as $dep)
                            <tr>
                                <td class="text-start"><span class="fw-bold">{{ $dep->nombre }}</span></td>
                                <td><span class="badge bg-success fs-6">{{ $dep->recibidos }}</span></td>
                                <td><span class="badge bg-primary fs-6">{{ $dep->enviados }}</span></td>
                                <td>
                                    <span class="badge fs-6" style="background-color:#D9A23D; color:#0B2D59;">
                                        {{ $dep->recibidos + $dep->enviados }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted py-4">No existen departamentos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection