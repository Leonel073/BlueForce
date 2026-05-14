@extends('layouts.app')

@section('title', 'Reporte de Derivaciones')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo Empresa" style="width: 80px; margin-right: 20px;">
            <div>
                <h1 class="fw-bold text-white mb-1"><i class="bi bi-arrow-left-right"></i> Reporte de Derivaciones</h1>
                <p class="text-light mb-0">Seguimiento y trazabilidad documental</p>
            </div>
        </div>
    </div>
    {{-- FILTROS --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-body">

        <form method="GET" action="{{ route('admin.reportes.derivaciones') }}" id="filtros-derivaciones">

            <div class="row">

                {{-- DOCUMENTO --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">Documento</label>

                    <input type="text" name="documento" class="form-control rounded-3"
                           placeholder="Cite o asunto" value="{{ request('documento') }}">

                </div>

                {{-- REMITENTE --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">Remitente</label>

                    <select name="idRemitente" class="form-select rounded-3">

                        <option value="">Todos</option>

                        @foreach($personas as $p)

                            <option value="{{ $p->idPersona }}"
                                {{ request('idRemitente') == $p->idPersona ? 'selected' : '' }}>

                                {{ substr($p->nombre, 0, 20) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- ORIGEN --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">Origen</label>

                    <select name="origen" class="form-select rounded-3">

                        <option value="">Todos</option>

                        @foreach($departamentos as $dep)

                            <option value="{{ $dep->idDepartamento }}"
                                {{ request('origen') == $dep->idDepartamento ? 'selected' : '' }}>

                                {{ $dep->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- DESTINO --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">Destino</label>

                    <select name="destino" class="form-select rounded-3">

                        <option value="">Todos</option>

                        @foreach($departamentos as $dep)

                            <option value="{{ $dep->idDepartamento }}"
                                {{ request('destino') == $dep->idDepartamento ? 'selected' : '' }}>

                                {{ $dep->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- FECHA INICIO --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">Desde</label>

                    <input type="date" name="fecha_inicio" class="form-control rounded-3"
                           value="{{ request('fecha_inicio') }}">

                </div>

                {{-- FECHA FIN --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">Hasta</label>

                    <input type="date" name="fecha_fin" class="form-control rounded-3"
                           value="{{ request('fecha_fin') }}">

                </div>

            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFiltrosDerivaciones()">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                    <button type="button" class="btn btn-success" onclick="mostrarEstadisticasDerivaciones()">
                        <i class="bi bi-bar-chart"></i> Estadísticas
                    </button>
                    <a href="{{ route('admin.reportes.derivaciones.pdf', request()->query()) }}" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Exportar PDF
                    </a>
                    <button type="button" class="btn btn-secondary" onclick="cerrar()">
                        <i class="bi bi-x-lg"></i> Salida
                    </button>
                </div>
            </div>

        </form>

    </div>

</div>

{{-- ESTADÍSTICAS --}}
@if($estadisticas['total_derivaciones'] > 0)
<div class="card border-0 shadow-sm rounded-4 mb-4" id="estadisticas-derivaciones" style="display: none;">
    <div class="card-body">
        <h5 class="fw-bold mb-3">📊 Estadísticas de Derivaciones</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Derivaciones</h6>
                        <h2 class="fw-bold">{{ $estadisticas['total_derivaciones'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Derivaciones Completadas</h6>
                        <h2 class="fw-bold text-success">{{ $estadisticas['derivaciones_completadas'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Departamentos</h6>
                        <h2 class="fw-bold text-info">{{ $estadisticas['departamentos_involucrados'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Promedio por Depto</h6>
                        <h2 class="fw-bold text-primary">{{ $estadisticas['promedio_derivaciones_por_depto'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-3 fw-bold text-secondary">Detalle de Derivaciones ({{ $derivaciones->count() }})</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white">Documento</th>
                            <th class="text-white">Remitente</th>
                            <th class="text-white">Usuario Derivación</th>
                            <th class="text-white">Origen</th>
                            <th class="text-white">Destino</th>
                            <th class="text-white">Fecha Envío</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($derivaciones as $derivacion)
                            <tr>
                                {{-- DOCUMENTO --}}
                                <td>
                                    <div class="fw-bold">{{ $derivacion->documento->cite ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ substr($derivacion->documento->asunto ?? '', 0, 30) }}...</small>
                                </td>
                                {{-- REMITENTE --}}
                                <td>
                                    <span class="badge bg-info">{{ $derivacion->persona_remitente ?? 'N/A' }}</span>
                                </td>
                                {{-- USUARIO DERIVACIÓN --}}
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $derivacion->usuario_derivacion ?? 'N/A' }}</span>
                                </td>
                                {{-- ORIGEN --}}
                                <td>
                                    <span class="badge bg-primary">{{ $derivacion->departamentoOrigen->nombre ?? 'N/A' }}</span>
                                </td>
                                {{-- DESTINO --}}
                                <td>
                                    <span class="badge bg-success">{{ $derivacion->departamentoDestino->nombre ?? 'N/A' }}</span>
                                </td>
                                {{-- FECHA --}}
                                <td>{{ \Carbon\Carbon::parse($derivacion->fechaEnvio)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    No existen derivaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function resetFiltrosDerivaciones() {
    document.getElementById('filtros-derivaciones').reset();
    document.getElementById('filtros-derivaciones').submit();
}

function mostrarEstadisticasDerivaciones() {
    const seccion = document.getElementById('estadisticas-derivaciones');
    if (seccion) {
        seccion.style.display = seccion.style.display === 'none' ? 'block' : 'none';
    }
}

function cerrar() {
    if (confirm('¿Deseas cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>

@endsection