@extends('layouts.app')
@section('title', 'Reporte por Departamentos')
@section('content')

@include('admin.reportes.partials.styles')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>

<div class="report-container">

    {{-- HERO --}}
    <div class="report-hero">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo">
            <div>
                <h1><i class="bi bi-building-fill"></i> Reporte por Departamentos</h1>
                <p>Flujo documental institucional por areas - Estadisticas detalladas con analisis grafico</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="report-filter-area">
        <form method="GET" action="{{ route('admin.reportes.departamentos') }}" id="filtros-departamentos">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Nombre del Departamento</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Recursos Humanos..." value="{{ request('nombre') }}">
                </div>
                <div class="col-md-3">
                    <label>Movimientos Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label>Movimientos Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn-bf-primary btn-sm"><i class="bi bi-search"></i> Buscar</button>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="resetFiltrosDepartamentos()"><i class="bi bi-arrow-clockwise"></i> Limpiar</button>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="mostrarEstadisticasDepartamentos()"><i class="bi bi-bar-chart"></i> Estadisticas</button>
                    <a href="{{ route('admin.reportes.departamentos.pdf', request()->query()) }}" class="btn-bf-danger btn-sm"><i class="bi bi-file-pdf-fill"></i> PDF Gral</a>
                    <button type="button" class="btn-bf-secondary btn-sm" onclick="cerrar()"><i class="bi bi-x-lg"></i> Salida</button>
                </div>
            </div>
        </form>
    </div>

    {{-- ESTADISTICAS GENERALES --}}
    @if($estadisticas['total_departamentos'] > 0)
    <div id="estadisticas-departamentos" style="display: none;">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-navy">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_departamentos'] }}</div>
                    <div class="stat-label">Total Departamentos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-file-earmark"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_documentos'] }}</div>
                    <div class="stat-label">Total Documentos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_personal'] }}</div>
                    <div class="stat-label">Total Personal</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-gold">
                    <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
                    <div class="stat-value">{{ $estadisticas['promedio_documentos_por_depto'] }}</div>
                    <div class="stat-label">Promedio Docs/Depto</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_en_curso'] }}</div>
                    <div class="stat-label">En Curso</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_finalizados'] }}</div>
                    <div class="stat-label">Finalizados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-gray" style="--stat-accent: var(--bf-muted); --stat-icon-bg: rgba(94,116,145,0.1);">
                    <div class="stat-icon"><i class="bi bi-archive"></i></div>
                    <div class="stat-value">{{ $estadisticas['total_archivados'] }}</div>
                    <div class="stat-label">Archivados</div>
                </div>
            </div>
        </div>

        {{-- GRAFICAS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header">
                        <h5 class="report-card-title"><i class="bi bi-pie-chart"></i> Estado de Documentos</h5>
                    </div>
                    <div class="report-card-body">
                        <canvas id="chartEstados" height="180"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header">
                        <h5 class="report-card-title"><i class="bi bi-bar-chart"></i> Top 5 Departamentos</h5>
                    </div>
                    <div class="report-card-body">
                        <canvas id="chartTop5" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA PRINCIPAL --}}
    <div class="report-card">
        <div class="report-card-header">
            <h5 class="report-card-title"><i class="bi bi-list-ul"></i> Detalle por Departamento ({{ $departamentos->count() }})</h5>
        </div>
        <div class="report-card-body p-0">
            <div class="report-accordion accordion" id="departamentosAccordion">
                @forelse($departamentos as $dep)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#depto{{ $dep->idDepartamento }}">
                            <i class="bi bi-building me-2"></i>
                            <strong>{{ $dep->nombre }}</strong>
                            <span class="ms-3 badge-bf badge-bf-success">{{ $dep->recibidos }}</span>
                            <span class="ms-2 badge-bf badge-bf-navy">{{ $dep->enviados }}</span>
                            <span class="ms-2 badge-bf badge-bf-blue">{{ $dep->total_personas }}</span>
                            <span class="ms-2 badge-bf badge-bf-gold">{{ $dep->documentos_originarios }}</span>
                        </button>
                    </h2>
                    <div id="depto{{ $dep->idDepartamento }}" class="accordion-collapse collapse">
                        <div class="accordion-body pt-0">

                            {{-- ENCARGADO --}}
                            <div style="background:var(--bf-blue-pale);border-left:4px solid var(--bf-blue);padding:10px 14px;border-radius:0 8px 8px 0;margin-bottom:1rem;">
                                <strong>Encargado del Departamento:</strong>
                                <span class="badge-bf badge-bf-navy">{{ $dep->encargado_nombre }}</span>
                            </div>

                            <div class="row g-3">
                                {{-- INFO GENERAL --}}
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle"></i> Informacion General</h6>
                                    <div class="report-card" style="margin-bottom:0;">
                                        <div class="report-card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><strong>Documentos Originarios:</strong> <span class="badge-bf badge-bf-navy">{{ $dep->documentos_originarios }}</span></li>
                                                <li class="mb-2"><strong>En Curso:</strong> <span class="badge-bf badge-bf-blue">{{ $dep->documentos_en_curso }}</span></li>
                                                <li class="mb-2"><strong>Finalizados:</strong> <span class="badge-bf badge-bf-success">{{ $dep->documentos_derivados_finalizados }}</span></li>
                                                <li class="mb-2"><strong>Archivados:</strong> <span class="badge-bf badge-bf-gray">{{ $dep->documentos_archivados }}</span></li>
                                                <li><strong>Tasa de completitud:</strong> <span class="badge-bf badge-bf-gold">{{ $dep->estadisticas['tasa_completitud'] }}%</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- PERSONAL --}}
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2"><i class="bi bi-people"></i> Personal del Departamento</h6>
                                    <div class="report-card" style="margin-bottom:0;">
                                        <div class="report-card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><strong>Total Personas:</strong> <span class="badge-bf badge-bf-navy">{{ $dep->total_personas }}</span></li>
                                                <li class="mb-2"><strong>Internas:</strong> <span class="badge-bf badge-bf-success">{{ $dep->personas_internas }}</span></li>
                                                <li class="mb-2"><strong>Activas:</strong> <span class="badge-bf badge-bf-blue">{{ $dep->personas_activas }}</span></li>
                                            </ul>
                                            @if(count($dep->personas_lista) > 0)
                                            <hr class="report-divider">
                                            <strong class="d-block mb-2">Lista de Personal:</strong>
                                            <div style="max-height:150px;overflow-y:auto;">
                                                <ul class="list-unstyled small">
                                                    @foreach($dep->personas_lista as $persona)
                                                    <li class="mb-1"><i class="bi bi-person-fill" style="color:var(--bf-blue);"></i> {{ $persona }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @else
                                            <p class="text-muted small mt-2">No hay personal asignado</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- GRAFICAS INDIVIDUALES --}}
                            <h6 class="fw-bold mt-4 mb-2">Estadisticas Detalladas</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="report-card" style="margin-bottom:0;">
                                        <div class="report-card-body">
                                            <canvas id="chartDepto{{ $dep->idDepartamento }}" height="120"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="report-card" style="margin-bottom:0;">
                                        <div class="report-card-body">
                                            <canvas id="chartFlujoDep{{ $dep->idDepartamento }}" height="120"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- BARRA DE PROGRESO --}}
                            <h6 class="fw-bold mt-3 mb-2">Flujo de Documentos</h6>
                            @php
                                $total = $dep->recibidos + $dep->enviados;
                                $recPct = $total > 0 ? ($dep->recibidos / $total) * 100 : 0;
                                $envPct = $total > 0 ? ($dep->enviados / $total) * 100 : 0;
                            @endphp
                            <div class="progress" style="height:28px;border-radius:8px;">
                                <div class="progress-bar" role="progressbar" style="width:{{ $recPct }}%;background:var(--bf-success);font-weight:bold;font-size:11px;"
                                    aria-valuenow="{{ $dep->recibidos }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                                     {{ $dep->recibidos }}
                                </div>
                                <div class="progress-bar" role="progressbar" style="width:{{ $envPct }}%;background:var(--bf-navy);font-weight:bold;font-size:11px;"
                                    aria-valuenow="{{ $dep->enviados }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                                     {{ $dep->enviados }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @empty
                <div class="p-3"><span class="badge-bf badge-bf-gray">No existen departamentos registrados.</span></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@php
    $top5Deps = $departamentos->sortByDesc(function($d) { return $d->documentos_originarios; })->take(5);
    $top5Labels = json_encode($top5Deps->pluck('nombre')->toArray());
    $top5Data = json_encode($top5Deps->pluck('documentos_originarios')->toArray());
    $estadosData = json_encode([$estadisticas['total_en_curso'], $estadisticas['total_finalizados'], $estadisticas['total_archivados']]);
@endphp

<script>
    const BF_COLORS = {
        navy: '#0B2D59',
        blue: '#2E608C',
        gold: '#D9A23D',
        success: '#0D9E6E',
        danger: '#DC2626',
        info: '#2563EB',
        muted: '#5E7491',
        navyLight: '#1a3f6e',
        bluePale: '#E8EFF6'
    };

    const chartEstadosData = {
        labels: ['En Curso', 'Finalizados', 'Archivados'],
        datasets: [{
            label: 'Cantidad de Documentos',
            data: {!! $estadosData !!},
            backgroundColor: [BF_COLORS.info, BF_COLORS.success, BF_COLORS.muted],
            borderColor: [BF_COLORS.navy, BF_COLORS.navy, BF_COLORS.navy],
            borderWidth: 2
        }]
    };

    const chartTop5Data = {
        labels: {!! $top5Labels !!},
        datasets: [{
            label: 'Documentos',
            data: {!! $top5Data !!},
            backgroundColor: BF_COLORS.navy,
            borderColor: BF_COLORS.blue,
            borderWidth: 1,
            borderRadius: 6
        }]
    };

    const ctxEstados = document.getElementById('chartEstados');
    if (ctxEstados) {
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: chartEstadosData,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { weight: '600' }, color: BF_COLORS.navy } }
                }
            }
        });
    }

    const ctxTop5 = document.getElementById('chartTop5');
    if (ctxTop5) {
        new Chart(ctxTop5, {
            type: 'bar',
            data: chartTop5Data,
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: BF_COLORS.navy }, grid: { color: BF_COLORS.bluePale } },
                    y: { ticks: { color: BF_COLORS.navy }, grid: { display: false } }
                }
            }
        });
    }

    @foreach($departamentos as $dep)
    @php
        $depEstadosData = json_encode([$dep->documentos_en_curso, $dep->documentos_derivados_finalizados, $dep->documentos_archivados]);
        $depFlujosData = json_encode([$dep->recibidos, $dep->enviados]);
    @endphp
    const ctx{{ $dep->idDepartamento }} = document.getElementById('chartDepto{{ $dep->idDepartamento }}');
    if (ctx{{ $dep->idDepartamento }}) {
        new Chart(ctx{{ $dep->idDepartamento }}, {
            type: 'pie',
            data: {
                labels: ['En Curso', 'Finalizados', 'Archivados'],
                datasets: [{
                    data: {!! $depEstadosData !!},
                    backgroundColor: [BF_COLORS.info, BF_COLORS.success, BF_COLORS.muted],
                    borderColor: BF_COLORS.navy,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { weight: '600' }, color: BF_COLORS.navy } },
                    title: { display: true, text: 'Estado de Documentos', color: BF_COLORS.navy, font: { weight: '700' } }
                }
            }
        });
    }

    const ctxFlujo{{ $dep->idDepartamento }} = document.getElementById('chartFlujoDep{{ $dep->idDepartamento }}');
    if (ctxFlujo{{ $dep->idDepartamento }}) {
        new Chart(ctxFlujo{{ $dep->idDepartamento }}, {
            type: 'bar',
            data: {
                labels: ['Recibidos', 'Enviados'],
                datasets: [{
                    label: 'Derivaciones',
                    data: {!! $depFlujosData !!},
                    backgroundColor: [BF_COLORS.success, BF_COLORS.navy],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { weight: '600' }, color: BF_COLORS.navy } },
                    title: { display: true, text: 'Flujo de Documentos', color: BF_COLORS.navy, font: { weight: '700' } }
                },
                scales: {
                    y: { ticks: { color: BF_COLORS.navy }, grid: { color: BF_COLORS.bluePale } },
                    x: { ticks: { color: BF_COLORS.navy }, grid: { display: false } }
                }
            }
        });
    }
    @endforeach

    function resetFiltrosDepartamentos() {
        document.getElementById('filtros-departamentos').reset();
        document.getElementById('filtros-departamentos').submit();
    }
    function mostrarEstadisticasDepartamentos() {
        const s = document.getElementById('estadisticas-departamentos');
        if (s) s.style.display = s.style.display === 'none' ? 'block' : 'none';
    }
    function cerrar() {
        if (confirm('Desea cerrar este reporte?')) {
            window.location.href = '{{ route("admin.reportes.index") }}';
        }
    }
</script>
@endsection
