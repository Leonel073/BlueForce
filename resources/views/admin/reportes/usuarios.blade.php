@extends('layouts.app')
@section('title', 'Reporte de Usuarios')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <h1 class="fw-bold text-white"><i class="bi bi-people-fill"></i> Reporte de Usuarios</h1>
            <p class="text-light mb-0">Actividad documental por usuario</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.usuarios') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nombre del Usuario</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Administrador..." value="{{ request('nombre') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Correo Electrónico</label>
                        <input type="text" name="correo" class="form-control" placeholder="usuario@armada.mil.bo" value="{{ request('correo') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
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
            <a href="{{ route('admin.reportes.usuarios.pdf', request()->query()) }}" class="btn btn-danger shadow-sm">
                <i class="bi bi-file-pdf-fill"></i> Exportar a PDF
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white">Usuario</th>
                            <th class="text-white">Correo</th>
                            <th class="text-white text-center">Documentos Registrados</th>
                            <th class="text-white text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td><span class="fw-bold">{{ $usuario->name }}</span></td>
                                <td>{{ $usuario->email }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6">{{ $usuario->correspondencias_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if($usuario->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No existen usuarios registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection