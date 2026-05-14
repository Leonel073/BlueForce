@extends('layouts.app')

@section('title', 'Reporte de Personas y Documentos')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#198754);">
        <div class="card-body">
            <h1 class="fw-bold text-white"><i class="bi bi-person-lines-fill"></i> Reporte Integral de Personas</h1>
            <p class="text-light mb-0">Historial completo de remitentes, CI y documentos emitidos</p>
        </div>
    </div>

    {{-- FILTROS AVANZADOS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.personas') }}" id="filtros-form">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Nombre de Persona</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" value="{{ request('nombre') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Carnet (CI)</label>
                        <input type="text" name="ci" class="form-control" placeholder="Ej: 7380150" value="{{ request('ci') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Tipo Perfil</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="INTERNO" {{ request('tipo') == 'INTERNO' ? 'selected' : '' }}>Interno</option>
                            <option value="EXTERNO" {{ request('tipo') == 'EXTERNO' ? 'selected' : '' }}>Externo</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Departamento</label>
                        <select name="idDepartamento" class="form-select">
                            <option value="">Todos</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->idDepartamento }}" {{ request('idDepartamento') == $depto->idDepartamento ? 'selected' : '' }}>
                                    {{ $depto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="solo_con_tramites" value="1" 
                                {{ request('solo_con_tramites') == '1' ? 'checked' : '' }} id="soloTramites">
                            <label class="form-check-label small" for="soloTramites">
                                Solo con Trámites
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Doc. Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Doc. Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-secondary shadow-sm" onclick="resetFiltros()">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-success shadow-sm" onclick="mostrarEstadisticas()">
                            <i class="bi bi-bar-chart"></i> Ver Estadísticas
                        </button>
                        <a href="{{ route('admin.reportes.personas.pdf', request()->query()) }}" class="btn btn-danger shadow-sm">
                            <i class="bi bi-file-pdf-fill"></i> Exportar a PDF
                        </a>
                        <button type="button" class="btn btn-secondary shadow-sm" onclick="cerrar()">
                            <i class="bi bi-x-lg"></i> Salida
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ESTADÍSTICAS PRE-VISUALIZACIÓN --}}
    @if($estadisticas['total_personas'] > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="estadisticas-section" style="display: none;">
        <div class="card-body">
            <h5 class="fw-bold mb-3">📊 Estadísticas del Reporte</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Personas</h6>
                            <h2 class="fw-bold">{{ $estadisticas['total_personas'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Internas</h6>
                            <h2 class="fw-bold text-success">{{ $estadisticas['personas_internas'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Externas</h6>
                            <h2 class="fw-bold text-warning">{{ $estadisticas['personas_externas'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Documentos</h6>
                            <h2 class="fw-bold text-primary">{{ $estadisticas['total_documentos'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white" width="25%">Datos de la Persona</th>
                            <th class="text-white" width="20%">Contacto e Institución</th>
                            <th class="text-white" width="55%">Historial de Documentos Emitidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personas as $p)
                            <tr>
                                {{-- COLUMNA PERSONA --}}
                                <td>
                                    <span class="fw-bold fs-6">{{ $p->nombre }}</span><br>
                                    <span class="badge bg-secondary mb-1">CI: {{ $p->ci ?? 'Sin registro' }}</span><br>
                                    @if($p->tipo == 'INTERNO')
                                        <span class="badge bg-success">INTERNO</span>
                                    @else
                                        <span class="badge bg-warning text-dark">EXTERNO</span>
                                    @endif
                                </td>
                                
                                {{-- COLUMNA CONTACTO --}}
                               <td>
                                    <small>
                                        <i class="bi bi-envelope"></i>
                                        {{ $p->correo ?? 'N/A' }}
                                    </small><br>

                                    <small>
                                        <i class="bi bi-telephone"></i>
                                        {{ $p->telefono ?? 'N/A' }}
                                    </small><br>

                                    <hr class="my-1">

                                    <small class="fw-bold">
                                        {{ $p->institucion ?? 'Independiente' }}
                                    </small><br>

                                    <small class="text-muted">
                                        {{ $p->cargo->nombre ?? 'Sin cargo' }}
                                    </small>
                                </td>
                                
                                {{-- COLUMNA DOCUMENTOS --}}
                                <td>
                                    @if($p->documentos->count() > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach($p->documentos as $doc)
                                                <li class="border-bottom border-light pb-2 mb-2">
                                                    <span class="fw-bold text-dark">{{ $doc->cite }}</span> 
                                                    <span class="badge bg-light text-dark border">{{ $doc->tipoDocumento->nombre ?? 'Documento' }}</span><br>
                                                    <small class="text-secondary">{{ $doc->asunto }}</small><br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }} | 
                                                        Estado: <strong class="text-info">{{ $doc->estado->nombre ?? 'N/A' }}</strong>
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted fst-italic"><i class="bi bi-info-circle"></i> Esta persona no tiene documentos registrados en este rango de fechas.</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4">No se encontraron registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function resetFiltros() {
    document.getElementById('filtros-form').reset();
    document.getElementById('filtros-form').submit();
}

function mostrarEstadisticas() {
    const seccion = document.getElementById('estadisticas-section');
    seccion.style.display = seccion.style.display === 'none' ? 'block' : 'none';
}

function cerrar() {
    if (confirm('¿Deseas cerrar este reporte?')) {
        window.location.href = '{{ route("admin.index") }}';
    }
}
</script>
@endsection