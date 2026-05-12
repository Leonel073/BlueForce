    @extends('layouts.app')

    @section('title', 'Reporte General de Documentos')

    @section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#6f42c1);">
            <div class="card-body">
                <h1 class="fw-bold text-white"><i class="bi bi-file-earmark-text-fill"></i> Reporte General de Documentos</h1>
                <p class="text-light mb-0">Listado integral de correspondencia con datos de origen y destino</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.documentos') }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscar Documento</label>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cite o asunto..." value="{{ request('q') }}">
                        </div>
                    <div class="col-md-2">
        <label class="form-label small fw-bold">Tipo</label>
        <select name="idTipo" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach($tipos as $t)
                {{-- CORRECCIÓN: Cambiamos $t->idTipo por $t->idTipoDocumento --}}
                <option value="{{ $t->idTipoDocumento }}" {{ request('idTipo') == $t->idTipoDocumento ? 'selected' : '' }}>
                    {{ $t->nombre }}
                </option>
            @endforeach
        </select>
    </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estado</label>
                            <select name="idEstado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($estados as $e)
                                    <option value="{{ $e->idEstado }}" {{ request('idEstado') == $e->idEstado ? 'selected' : '' }}>{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ request('fecha_fin') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark btn-sm w-100" style="background-color:#0B2D59;"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-lg rounded-4">
        {{-- BOTÓN EXPORTAR EN LA CABECERA --}}
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">Resultados de la búsqueda</h5>
            <a href="{{ route('admin.reportes.documentos.pdf', request()->query()) }}" class="btn btn-danger shadow-sm">
                <i class="bi bi-file-pdf-fill"></i> Descargar Reporte PDF
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white">Documento / Cite</th>
                            <th class="text-white">Remitente (CI)</th>
                            <th class="text-white">Fecha</th>
                            <th class="text-white">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td>
                                    <strong>{{ $doc->cite }}</strong><br>
                                    <small class="text-muted">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    {{ $doc->remitente->nombre ?? 'N/A' }}<br>
                                    <span class="badge bg-light text-dark border">CI: {{ $doc->remitente->ci ?? 'S/R' }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: #0B2D59;">{{ $doc->estado->nombre ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4">No se encontraron documentos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    @endsection