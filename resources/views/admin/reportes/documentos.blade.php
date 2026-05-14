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
                <form method="GET" action="{{ route('admin.reportes.documentos') }}" id="filtros-documentos">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Buscar Documento</label>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cite o asunto..." value="{{ request('q') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tipo</label>
                            <select name="idTipo" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($tipos as $t)
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
                            <label class="form-label small fw-bold">Remitente</label>
                            <select name="idRemitente" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($personas as $p)
                                    <option value="{{ $p->idPersona }}" {{ request('idRemitente') == $p->idPersona ? 'selected' : '' }}>
                                        {{ substr($p->nombre, 0, 20) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small fw-bold">Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small fw-bold">Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ request('fecha_fin') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark btn-sm w-100" style="background-color:#0B2D59;"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="resetFiltrosDocumentos()">
                                <i class="bi bi-arrow-clockwise"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="mostrarEstadisticasDocumentos()">
                                <i class="bi bi-bar-chart"></i> Ver Estadísticas
                            </button>
                            <a href="{{ route('admin.reportes.documentos.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-file-pdf-fill"></i> Exportar PDF
                            </a>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="cerrar()">
                                <i class="bi bi-x-lg"></i> Salida
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ESTADÍSTICAS PRE-VISUALIZACIÓN --}}
        @if($estadisticas['total_documentos'] > 0)
        <div class="card border-0 shadow-sm rounded-4 mb-4" id="estadisticas-documentos" style="display: none;">
            <div class="card-body">
                <h5 class="fw-bold mb-3">📊 Estadísticas del Reporte</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Total Documentos</h6>
                                <h2 class="fw-bold">{{ $estadisticas['total_documentos'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Pendientes</h6>
                                <h2 class="fw-bold text-warning">{{ $estadisticas['documentos_pendientes'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Finalizados</h6>
                                <h2 class="fw-bold text-success">{{ $estadisticas['documentos_finalizados'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Urgentes</h6>
                                <h2 class="fw-bold text-danger">{{ $estadisticas['documentos_urgentes'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-lg rounded-4">
        {{-- BOTÓN EXPORTAR EN LA CABECERA --}}
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">Resultados de la búsqueda ({{ $documentos->count() }})</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white">Documento / Cite</th>
                            <th class="text-white">Remitente (CI)</th>
                            <th class="text-white">Tipo</th>
                            <th class="text-white">Fecha</th>
                            <th class="text-white">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td>
                                    <strong>{{ $doc->cite }}</strong><br>
                                    <small class="text-muted">{{ substr($doc->asunto, 0, 40) }}...</small>
                                </td>
                                <td>
                                    {{ $doc->remitente->nombre ?? 'N/A' }}<br>
                                    <span class="badge bg-light text-dark border">{{ $doc->remitente->ci ?? 'S/R' }}</span>
                                </td>
                                <td>
                                    <small class="badge bg-secondary">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: #0B2D59;">{{ $doc->estado->nombre ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">No se encontraron documentos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <script>
    function resetFiltrosDocumentos() {
        document.getElementById('filtros-documentos').reset();
        document.getElementById('filtros-documentos').submit();
    }

    function mostrarEstadisticasDocumentos() {
        const seccion = document.getElementById('estadisticas-documentos');
        if (seccion) {
            seccion.style.display = seccion.style.display === 'none' ? 'block' : 'none';
        }
    }

    function cerrar() {
        if (confirm('¿Deseas cerrar este reporte?')) {
            window.location.href = '{{ route("admin.index") }}';
        }
    }
    </script>
    @endsection