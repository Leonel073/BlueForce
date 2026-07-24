@extends('layouts.app')

@section('title', 'Reporte por Departamentos')

@section('content')
@include('admin.reportes.partials.styles')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>

<div class="report-container">
    <div class="report-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
                <div>
                    <span class="report-hero-badge">Flujo institucional</span>
                    <h1 class="mt-2"><i class="bi bi-building-fill"></i> Reporte por Departamentos</h1>
                    <p>Carga documental, personal asignado y balance de movimientos por area.</p>
                </div>
            </div>
            <div class="report-hero-badge">
                {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.departamentos') }}" id="filtros-departamentos">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Departamento</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre del departamento" value="{{ request('nombre') }}">
                </div>
                <div class="col-md-3">
                    <label>Movimientos desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label>Movimientos hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn-bf-primary btn-sm"><i class="bi bi-search"></i> Buscar</button>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="resetFiltrosDepartamentos()" title="Quitar filtros aplicados">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                    <button type="button" id="btn-graficas-departamentos" class="btn-bf-gold btn-sm" onclick="mostrarEstadisticasDepartamentos()" aria-expanded="false" aria-controls="estadisticas-departamentos">
                        <i class="bi bi-bar-chart"></i> Ver graficas
                    </button>
                    <a href="{{ route('admin.reportes.departamentos.pdf', request()->query()) }}" class="btn-bf-danger btn-sm"><i class="bi bi-file-pdf-fill"></i> PDF</a>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
                </div>
            </div>
        </form>
    </div>

    @if(request()->hasAny(['nombre', 'fecha_inicio', 'fecha_fin']))
        <div class="report-card">
            <div class="report-card-body py-3">
                <strong style="color:var(--bf-navy);">Contexto del reporte:</strong>
                <span class="text-muted">
                    {{ request('nombre') ? 'Departamento: ' . request('nombre') . ' | ' : '' }}
                    {{ request('fecha_inicio') ? 'Desde: ' . request('fecha_inicio') . ' | ' : '' }}
                    {{ request('fecha_fin') ? 'Hasta: ' . request('fecha_fin') : '' }}
                </span>
            </div>
        </div>
    @endif

    @php
        $departamentoMayorMovimiento = $departamentos->sortByDesc('movimiento_total')->first();
        $departamentoMayorCarga = $departamentos->sortByDesc('documentos_originarios')->first();
    @endphp

    @if($estadisticas['total_departamentos'] > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_departamentos'] }}</div>
                    <div class="stat-label">Departamentos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                    <div class="stat-value">{{ $estadisticas['movimiento_total'] }}</div>
                    <div class="stat-label">Movimientos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_personal'] }}</div>
                    <div class="stat-label">Personal asignado</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-gold">
                    <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_documentos'] }}</div>
                    <div class="stat-label">Docs. originarios</div>
                </div>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-header justify-content-between flex-wrap">
                <div>
                    <h5 class="report-card-title"><i class="bi bi-clipboard-data"></i> Lectura ejecutiva</h5>
                    <small class="text-muted">Resumen para interpretar carga, flujo y cobertura institucional.</small>
                </div>
                <span class="badge-bf badge-bf-navy">Promedio: {{ $estadisticas['promedio_documentos_por_depto'] }} docs/depto</span>
            </div>
            <div class="report-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 rounded-3" style="background:var(--bf-blue-pale);border:1px solid var(--bf-border);">
                            <strong class="d-block" style="color:var(--bf-navy);">Mayor movimiento</strong>
                            <span class="text-muted">{{ $departamentoMayorMovimiento?->nombre ?? 'N/A' }}</span><br>
                            <span class="badge-bf badge-bf-blue mt-2">{{ $departamentoMayorMovimiento?->movimiento_total ?? 0 }} movimientos</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-3" style="background:var(--bf-gold-pale);border:1px solid rgba(217,162,61,0.35);">
                            <strong class="d-block" style="color:var(--bf-navy);">Mayor carga documental</strong>
                            <span class="text-muted">{{ $departamentoMayorCarga?->nombre ?? 'N/A' }}</span><br>
                            <span class="badge-bf badge-bf-gold mt-2">{{ $departamentoMayorCarga?->documentos_originarios ?? 0 }} documentos</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-3" style="background:#fff;border:1px solid var(--bf-border);">
                            <strong class="d-block" style="color:var(--bf-navy);">Recibidos / Enviados</strong>
                            <span class="text-muted">Balance institucional del periodo.</span><br>
                            <span class="badge-bf badge-bf-success mt-2">Rec. {{ $estadisticas['total_recibidos'] }}</span>
                            <span class="badge-bf badge-bf-navy mt-2">Env. {{ $estadisticas['total_enviados'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-3" style="background:#fff;border:1px solid var(--bf-border);">
                            <strong class="d-block" style="color:var(--bf-navy);">Cobertura de personal</strong>
                            <span class="text-muted">{{ $estadisticas['departamentos_con_personal'] }} con personal registrado.</span><br>
                            <span class="badge-bf badge-bf-gray mt-2">{{ $estadisticas['departamentos_sin_movimiento'] }} sin movimiento</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="estadisticas-departamentos" style="display: none;">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <h5 class="report-card-title"><i class="bi bi-arrow-left-right"></i> Flujo institucional</h5>
                        </div>
                        <div class="report-card-body" style="height:260px;">
                            <canvas id="chartFlujoGlobal"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <h5 class="report-card-title"><i class="bi bi-bar-chart"></i> Top 5 por movimiento</h5>
                        </div>
                        <div class="report-card-body" style="height:260px;">
                            <canvas id="chartTop5"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="report-card">
        <div class="report-card-header justify-content-between flex-wrap">
            <div>
                <h5 class="report-card-title"><i class="bi bi-list-ul"></i> Detalle por departamento</h5>
                <small class="text-muted">{{ $departamentos->count() }} departamentos incluidos en el reporte.</small>
            </div>
            <span class="badge-bf badge-bf-blue">{{ $estadisticas['movimiento_total'] }} movimientos evaluados</span>
        </div>
        <div class="report-card-body">
            <div class="report-accordion accordion" id="departamentosAccordion">
                @forelse($departamentos as $dep)
                    @php
                        $totalFlujo = $dep->movimiento_total;
                        $recPct = $totalFlujo > 0 ? ($dep->recibidos / $totalFlujo) * 100 : 0;
                        $envPct = $totalFlujo > 0 ? ($dep->enviados / $totalFlujo) * 100 : 0;
                        $balanceLabel = $dep->balance_flujo > 0 ? 'Recibe mas' : ($dep->balance_flujo < 0 ? 'Envia mas' : 'Equilibrado');
                        $balanceClass = $dep->balance_flujo > 0 ? 'badge-bf-success' : ($dep->balance_flujo < 0 ? 'badge-bf-gold' : 'badge-bf-gray');
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#depto{{ $dep->idDepartamento }}">
                                <i class="bi bi-building me-2"></i>
                                <span class="flex-grow-1">
                                    <strong>{{ $dep->nombre }}</strong>
                                    <span class="d-block small text-muted">Encargado: {{ $dep->encargado_nombre }}</span>
                                </span>
                                <span class="badge-bf badge-bf-blue me-2">Mov. {{ $dep->movimiento_total }}</span>
                                <span class="badge-bf badge-bf-success me-2">Rec. {{ $dep->recibidos }}</span>
                                <span class="badge-bf badge-bf-navy me-2">Env. {{ $dep->enviados }}</span>
                                <span class="badge-bf {{ $balanceClass }}">{{ $balanceLabel }}</span>
                            </button>
                        </h2>
                        <div id="depto{{ $dep->idDepartamento }}" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <h6 class="fw-bold mb-3" style="color:var(--bf-navy);">Flujo documental</h6>
                                                <p class="mb-2"><strong>Recibidos:</strong> <span class="badge-bf badge-bf-success">{{ $dep->recibidos }}</span></p>
                                                <p class="mb-2"><strong>Enviados:</strong> <span class="badge-bf badge-bf-navy">{{ $dep->enviados }}</span></p>
                                                <p class="mb-2"><strong>Total movimientos:</strong> <span class="badge-bf badge-bf-blue">{{ $dep->movimiento_total }}</span></p>
                                                <p class="mb-0"><strong>Balance:</strong> <span class="badge-bf {{ $balanceClass }}">{{ $balanceLabel }} ({{ $dep->balance_flujo }})</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <h6 class="fw-bold mb-3" style="color:var(--bf-navy);">Carga originaria</h6>
                                                <p class="mb-2"><strong>Originarios:</strong> <span class="badge-bf badge-bf-navy">{{ $dep->documentos_originarios }}</span></p>
                                                <p class="mb-2"><strong>En curso:</strong> <span class="badge-bf badge-bf-blue">{{ $dep->documentos_en_curso }}</span></p>
                                                <p class="mb-2"><strong>Finalizados:</strong> <span class="badge-bf badge-bf-success">{{ $dep->documentos_derivados_finalizados }}</span></p>
                                                <p class="mb-2"><strong>Archivados:</strong> <span class="badge-bf badge-bf-gray">{{ $dep->documentos_archivados }}</span></p>
                                                <p class="mb-0"><strong>Completitud:</strong> <span class="badge-bf badge-bf-gold">{{ $dep->estadisticas['tasa_completitud'] }}%</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <h6 class="fw-bold mb-3" style="color:var(--bf-navy);">Personal y responsable</h6>
                                                <p class="mb-2"><strong>Encargado:</strong><br><span class="text-muted">{{ $dep->encargado_nombre }}</span></p>
                                                <p class="mb-2"><strong>Total personal:</strong> <span class="badge-bf badge-bf-navy">{{ $dep->total_personas }}</span></p>
                                                <p class="mb-2"><strong>Internos:</strong> <span class="badge-bf badge-bf-success">{{ $dep->personas_internas }}</span></p>
                                                <p class="mb-0"><strong>Activos:</strong> <span class="badge-bf badge-bf-blue">{{ $dep->personas_activas }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(count($dep->personas_lista) > 0)
                                    <div class="mb-3">
                                        <strong class="d-block mb-2" style="color:var(--bf-navy);">Personal registrado</strong>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($dep->personas_lista as $persona)
                                                <span class="badge-bf badge-bf-gray"><i class="bi bi-person-fill"></i> {{ $persona }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <h6 class="fw-bold mt-3 mb-2" style="color:var(--bf-navy);">Balance de flujo</h6>
                                <div class="progress" style="height:28px;border-radius:8px;">
                                    <div class="progress-bar" role="progressbar" style="width:{{ $recPct }}%;background:var(--bf-success);font-weight:bold;font-size:11px;" aria-valuenow="{{ $dep->recibidos }}" aria-valuemin="0" aria-valuemax="{{ $totalFlujo }}">
                                        {{ $dep->recibidos }}
                                    </div>
                                    <div class="progress-bar" role="progressbar" style="width:{{ $envPct }}%;background:var(--bf-navy);font-weight:bold;font-size:11px;" aria-valuenow="{{ $dep->enviados }}" aria-valuemin="0" aria-valuemax="{{ $totalFlujo }}">
                                        {{ $dep->enviados }}
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-md-6">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <canvas id="chartDepto{{ $dep->idDepartamento }}" height="120"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="report-card mb-0">
                                            <div class="report-card-body">
                                                <canvas id="chartFlujoDep{{ $dep->idDepartamento }}" height="120"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-building fs-2 d-block mb-2"></i>
                        No existen departamentos registrados para los filtros seleccionados.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@php
    $top5Deps = $departamentos->sortByDesc(function($d) { return $d->movimiento_total; })->take(5);
    $top5Labels = json_encode($top5Deps->pluck('nombre')->toArray());
    $top5Data = json_encode($top5Deps->pluck('movimiento_total')->toArray());
    $estadosData = json_encode([$estadisticas['total_en_curso'], $estadisticas['total_finalizados'], $estadisticas['total_archivados']]);
    $flujoGlobalData = json_encode([$estadisticas['total_recibidos'], $estadisticas['total_enviados']]);
@endphp

<script>
const BF_COLORS = {
    navy: '#0B2D59',
    blue: '#2E608C',
    gold: '#D9A23D',
    success: '#0D9E6E',
    info: '#2563EB',
    muted: '#5E7491',
    pale: '#E8EFF6'
};

const departamentosCharts = [];

const chartFlujoGlobalData = {
    labels: ['Recibidos', 'Enviados'],
    datasets: [{
        label: 'Movimientos',
        data: {!! $flujoGlobalData !!},
        backgroundColor: [BF_COLORS.success, BF_COLORS.navy],
        borderColor: BF_COLORS.navy,
        borderWidth: 2
    }]
};

const chartTop5Data = {
    labels: {!! $top5Labels !!},
    datasets: [{
        label: 'Movimientos',
        data: {!! $top5Data !!},
        backgroundColor: [BF_COLORS.navy, BF_COLORS.blue, BF_COLORS.gold, BF_COLORS.success, BF_COLORS.muted],
        borderColor: BF_COLORS.navy,
        borderWidth: 1,
        borderRadius: 6
    }]
};

const ctxFlujoGlobal = document.getElementById('chartFlujoGlobal');
if (ctxFlujoGlobal) {
    departamentosCharts.push(new Chart(ctxFlujoGlobal, {
        type: 'doughnut',
        data: chartFlujoGlobalData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: BF_COLORS.navy } },
                title: { display: true, text: 'Recibidos frente a enviados', color: BF_COLORS.navy }
            }
        }
    }));
}

const ctxTop5 = document.getElementById('chartTop5');
if (ctxTop5) {
    departamentosCharts.push(new Chart(ctxTop5, {
        type: 'bar',
        data: chartTop5Data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: BF_COLORS.navy }, grid: { color: BF_COLORS.pale } },
                y: { ticks: { color: BF_COLORS.navy }, grid: { display: false } }
            }
        }
    }));
}

@foreach($departamentos as $dep)
@php
    $depEstadosData = json_encode([$dep->documentos_en_curso, $dep->documentos_derivados_finalizados, $dep->documentos_archivados]);
    $depFlujosData = json_encode([$dep->recibidos, $dep->enviados]);
@endphp
const ctx{{ $dep->idDepartamento }} = document.getElementById('chartDepto{{ $dep->idDepartamento }}');
if (ctx{{ $dep->idDepartamento }}) {
    departamentosCharts.push(new Chart(ctx{{ $dep->idDepartamento }}, {
        type: 'pie',
        data: {
            labels: ['En curso', 'Finalizados', 'Archivados'],
            datasets: [{ data: {!! $depEstadosData !!}, backgroundColor: [BF_COLORS.info, BF_COLORS.success, BF_COLORS.muted], borderColor: BF_COLORS.navy, borderWidth: 2 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Estado de documentos', color: BF_COLORS.navy } } }
    }));
}

const ctxFlujo{{ $dep->idDepartamento }} = document.getElementById('chartFlujoDep{{ $dep->idDepartamento }}');
if (ctxFlujo{{ $dep->idDepartamento }}) {
    departamentosCharts.push(new Chart(ctxFlujo{{ $dep->idDepartamento }}, {
        type: 'bar',
        data: {
            labels: ['Recibidos', 'Enviados'],
            datasets: [{ label: 'Derivaciones', data: {!! $depFlujosData !!}, backgroundColor: [BF_COLORS.success, BF_COLORS.navy], borderRadius: 6 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' }, title: { display: true, text: 'Flujo de documentos', color: BF_COLORS.navy } },
            scales: {
                y: { ticks: { color: BF_COLORS.navy }, grid: { color: BF_COLORS.pale } },
                x: { ticks: { color: BF_COLORS.navy }, grid: { display: false } }
            }
        }
    }));
}
@endforeach

document.querySelectorAll('#departamentosAccordion .accordion-collapse').forEach((panel) => {
    panel.addEventListener('shown.bs.collapse', () => {
        panel.querySelectorAll('canvas').forEach((canvas) => {
            const chart = Chart.getChart(canvas);
            if (chart) chart.resize();
        });
    });
});

function resetFiltrosDepartamentos() {
    window.location.href = '{{ route("admin.reportes.departamentos") }}';
}
function mostrarEstadisticasDepartamentos() {
    const s = document.getElementById('estadisticas-departamentos');
    const btn = document.getElementById('btn-graficas-departamentos');
    if (!s || !btn) return;

    const oculto = s.style.display === 'none' || s.style.display === '';
    s.style.display = oculto ? 'block' : 'none';
    btn.setAttribute('aria-expanded', oculto ? 'true' : 'false');
    btn.innerHTML = oculto
        ? '<i class="bi bi-eye-slash"></i> Ocultar graficas'
        : '<i class="bi bi-bar-chart"></i> Ver graficas';

    if (oculto) {
        setTimeout(() => {
            departamentosCharts.forEach(chart => chart.resize());
            s.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 80);
    }
}
function cerrar() {
    if (confirm('Desea cerrar este reporte?')) {
        window.location.href = '{{ route("admin.reportes.index") }}';
    }
}
</script>
@endsection
