@extends('layouts.app')

@section('title', 'Reporte de Personas y Documentos')

@section('content')

@include('admin.reportes.partials.styles')

<div class="report-container">

    {{-- HERO --}}
    <div class="report-hero">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
            <div>
                <h1><i class="bi bi-person-lines-fill"></i> Reporte Integral de Personas</h1>
                <p>Historial completo de remitentes, CI y documentos emitidos</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.personas') }}" id="filtros-form">
            <div class="row g-3">
                <div class="col-md-2">
                    <label>Nombre de Persona</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Perez" value="{{ request('nombre') }}">
                </div>
                <div class="col-md-2">
                    <label>Carnet (CI)</label>
                    <input type="text" name="ci" class="form-control" placeholder="Ej: 7380150" value="{{ request('ci') }}">
                </div>
                <div class="col-md-2">
                    <label>Tipo Perfil</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="INTERNO" {{ request('tipo') == 'INTERNO' ? 'selected' : '' }}>Interno</option>
                        <option value="EXTERNO" {{ request('tipo') == 'EXTERNO' ? 'selected' : '' }}>Externo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Departamento</label>
                    <select name="idDepartamento" class="form-select">
                        <option value="">Todos</option>
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto->idDepartamento }}" {{ request('idDepartamento') == $depto->idDepartamento ? 'selected' : '' }}>
                                {{ $depto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label>Estado</label>
                    <select name="activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="solo_con_tramites" value="1"
                            {{ request('solo_con_tramites') == '1' ? 'checked' : '' }} id="soloTramites">
                        <label class="form-check-label small" for="soloTramites">Solo con Tramites</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>Doc. Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label>Doc. Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
            </div>
            <div class="filter-actions mt-3">
                <button type="submit" class="btn-bf-primary"><i class="bi bi-search"></i> Buscar</button>
                <button type="button" class="btn-bf-secondary" onclick="resetFiltros()"><i class="bi bi-arrow-clockwise"></i> Limpiar</button>
                <button type="button" id="btn-estadisticas-personas" class="btn-bf-gold" onclick="mostrarEstadisticas()" aria-expanded="false" aria-controls="estadisticas-section">
                    <i class="bi bi-bar-chart"></i> Ver estadisticas
                </button>
                <a href="{{ route('admin.reportes.personas.pdf', request()->query()) }}" class="btn-bf-danger"><i class="bi bi-file-pdf-fill"></i> Exportar a PDF</a>
                <button type="button" class="btn-bf-secondary" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
            </div>
        </form>
    </div>

    {{-- ESTADISTICAS --}}
    @if($estadisticas['total_personas'] > 0)
    <div id="estadisticas-section" style="display: none;">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_personas'] }}</div>
                    <div class="stat-label">Total Personas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                    <div class="stat-value">{{ $estadisticas['personas_internas'] }}</div>
                    <div class="stat-label">Internas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-gold">
                    <div class="stat-icon"><i class="bi bi-person"></i></div>
                    <div class="stat-value">{{ $estadisticas['personas_externas'] }}</div>
                    <div class="stat-label">Externas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-send-check"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_documentos_enviados'] }}</div>
                    <div class="stat-label">Docs. Enviados</div>
                </div>
            </div>
        </div>

        @php
            $personaMayorActividad = $personas->sortByDesc('documentos_enviados')->first();
        @endphp
        <div class="report-card">
            <div class="report-card-header justify-content-between flex-wrap">
                <div>
                    <h5 class="report-card-title"><i class="bi bi-clipboard-data"></i> Lectura de actividad</h5>
                    <small class="text-muted">Resumen del rol de las personas como remitentes dentro del periodo filtrado.</small>
                </div>
                <span class="badge-bf badge-bf-navy">Promedio: {{ $estadisticas['promedio_documentos_por_persona'] }} docs/persona</span>
            </div>
            <div class="report-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <strong style="color:var(--bf-navy);">Con documentos enviados</strong><br>
                        <span class="badge-bf badge-bf-blue mt-2">{{ $estadisticas['personas_con_documentos'] }} personas</span>
                    </div>
                    <div class="col-md-3">
                        <strong style="color:var(--bf-navy);">Mayor actividad</strong><br>
                        <span class="text-muted">{{ $personaMayorActividad?->nombre ?? 'N/A' }}</span><br>
                        <span class="badge-bf badge-bf-gold mt-2">{{ $personaMayorActividad?->documentos_enviados ?? 0 }} enviados</span>
                    </div>
                    <div class="col-md-3">
                        <strong style="color:var(--bf-navy);">Docs. con seguimiento</strong><br>
                        <span class="badge-bf badge-bf-success mt-2">{{ $estadisticas['documentos_con_derivacion'] }} documentos</span>
                    </div>
                    <div class="col-md-3">
                        <strong style="color:var(--bf-navy);">Movimientos derivados</strong><br>
                        <span class="badge-bf badge-bf-gray mt-2">{{ $estadisticas['total_derivaciones'] }} derivaciones</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA --}}
    <div class="report-table-wrapper">
        <table class="report-table">
            <thead>
                <tr>
                    <th width="25%">Datos de la Persona</th>
                    <th width="20%">Contacto e Institucion</th>
                    <th width="55%">Historial de Documentos Emitidos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($personas as $p)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="persona-avatar">{{ strtoupper(substr($p->nombre, 0, 1)) }}</div>
                                <div>
                                    <strong style="font-size:0.95rem;">{{ $p->nombre }}</strong><br>
                                    <span class="badge-bf badge-bf-gray">CI: {{ $p->ci ?? 'Sin registro' }}</span>
                                </div>
                            </div>
                            @if($p->tipo == 'INTERNO')
                                <span class="badge-bf badge-bf-success">INTERNO</span>
                            @else
                                <span class="badge-bf badge-bf-gold">EXTERNO</span>
                            @endif
                            <div class="mt-2">
                                <span class="badge-bf badge-bf-blue">
                                    <i class="bi bi-send-check"></i> {{ $p->documentos_enviados }} documentos enviados
                                </span>
                            </div>
                        </td>
                        <td>
                            <small class="d-block"><i class="bi bi-envelope me-1"></i>{{ $p->correo ?? 'N/A' }}</small>
                            <small class="d-block"><i class="bi bi-phone me-1"></i>Cel: {{ $p->telefono_celular ?? 'N/A' }}</small>
                            <small class="d-block"><i class="bi bi-telephone me-1"></i>Fijo: {{ $p->telefono_fijo ?? 'N/A' }}</small>
                            <hr class="report-divider">
                            <strong class="d-block">{{ $p->institucion ?? 'Independiente' }}</strong>
                            <small class="text-muted">{{ $p->cargos_nombres }}</small>
                        </td>
                        <td>
                            @if($p->documentos->count() > 0)
                                <ul class="list-unstyled mb-0">
                                    @foreach($p->documentos as $doc)
                                        <li style="border-bottom:1px dashed var(--bf-border);padding-bottom:6px;margin-bottom:6px;">
                                            <strong>{{ $doc->cite }}</strong>
                                            <span class="badge-bf badge-bf-navy">{{ $doc->tipoDocumento->nombre ?? 'Doc' }}</span><br>
                                            <small class="text-muted">{{ $doc->asunto }}</small><br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}
                                                | <strong>{{ $doc->estado->nombre ?? 'N/A' }}</strong>
                                            </small>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted fst-italic"><i class="bi bi-info-circle"></i> Sin documentos en el periodo seleccionado.</span>
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

<script>
function resetFiltros() {
    window.location.href = '{{ route("admin.reportes.personas") }}';
}
function mostrarEstadisticas() {
    const s = document.getElementById('estadisticas-section');
    const btn = document.getElementById('btn-estadisticas-personas');
    if (!s || !btn) return;

    const oculto = s.style.display === 'none' || s.style.display === '';
    s.style.display = oculto ? 'block' : 'none';
    btn.setAttribute('aria-expanded', oculto ? 'true' : 'false');
    btn.innerHTML = oculto
        ? '<i class="bi bi-eye-slash"></i> Ocultar estadisticas'
        : '<i class="bi bi-bar-chart"></i> Ver estadisticas';

    if (oculto) {
        setTimeout(() => s.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
    }
}
function cerrar() {
    if (confirm('Desea cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection
