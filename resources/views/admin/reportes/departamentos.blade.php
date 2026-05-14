@extends('layouts.app')
@section('title', 'Reporte por Departamentos')
@section('content')
<!-- CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <h1 class="fw-bold text-white"><i class="bi bi-building-fill"></i> Reporte por Departamentos</h1>
            <p class="text-light mb-0">Flujo documental institucional por áreas - Estadísticas detalladas con análisis gráfico</p>
        </div>
    </div>

    {{-- FILTROS MEJORADOS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.departamentos') }}" id="filtros-departamentos">
                <div class="row g-2">
                    <div class="col-md-3">
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
                    <div class="col-md-3 d-flex align-items-end gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Buscar</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="resetFiltrosDepartamentos()">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="mostrarEstadisticasDepartamentos()">
                            <i class="bi bi-bar-chart"></i> Estadísticas
                        </button>
                        <a href="{{ route('admin.reportes.departamentos.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-pdf-fill"></i> PDF Gral
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="cerrar()">
                            <i class="bi bi-x-lg"></i> Salida
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ESTADÍSTICAS GENERALES CON GRÁFICAS --}}
    @if($estadisticas['total_departamentos'] > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="estadisticas-departamentos" style="display: none;">
        <div class="card-body">
            <h5 class="fw-bold mb-4">📊 Estadísticas Generales del Sistema</h5>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Departamentos</h6>
                            <h2 class="fw-bold text-primary">{{ $estadisticas['total_departamentos'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Documentos</h6>
                            <h2 class="fw-bold text-success">{{ $estadisticas['total_documentos'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Personal</h6>
                            <h2 class="fw-bold text-info">{{ $estadisticas['total_personal'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Promedio Docs/Depto</h6>
                            <h2 class="fw-bold text-warning">{{ $estadisticas['promedio_documentos_por_depto'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">En Curso</h6>
                            <h3 class="fw-bold text-info">{{ $estadisticas['total_en_curso'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Finalizados</h6>
                            <h3 class="fw-bold text-success">{{ $estadisticas['total_finalizados'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Archivados</h6>
                            <h3 class="fw-bold text-secondary">{{ $estadisticas['total_archivados'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRÁFICAS GENERALES --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Estado de Documentos</h6>
                            <canvas id="chartEstados" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Top 5 Departamentos por Documentos</h6>
                            <canvas id="chartTop5" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLA PRINCIPAL CON DETALLES POR DEPARTAMENTO --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-3 fw-bold text-secondary"> Detalle por Departamento ({{ $departamentos->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="departamentosAccordion">
                @forelse($departamentos as $dep)
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#depto{{ $dep->idDepartamento }}">
                            <i class="bi bi-building me-2 text-primary"></i>
                            <strong class="text-dark">{{ $dep->nombre }}</strong>
                            <span class="ms-3 badge bg-success"> {{ $dep->recibidos }}</span>
                            <span class="ms-2 badge bg-primary"> {{ $dep->enviados }}</span>
                            <span class="ms-2 badge bg-info"> {{ $dep->total_personas }}</span>
                            <span class="ms-2 badge bg-warning text-dark"> {{ $dep->documentos_originarios }}</span>
                        </button>
                    </h2>
                    <div id="depto{{ $dep->idDepartamento }}" class="accordion-collapse collapse" data-bs-parent="#departamentosAccordion">
                        <div class="accordion-body pt-0">
                            
                            {{-- ENCARGADO --}}
                            <div class="alert alert-info mb-3" style="background-color: #e7f3ff; border-color: #b3d9ff;">
                                <strong> Encargado del Departamento:</strong> 
                                <span class="badge bg-primary">{{ $dep->encargado_nombre }}</span>
                            </div>

                            <div class="row">
                                {{-- INFORMACIÓN GENERAL --}}
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3"> Información General</h6>
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><strong>Documentos Originarios:</strong> <span class="badge bg-primary">{{ $dep->documentos_originarios }}</span></li>
                                                <li class="mb-2"><strong>En Curso:</strong> <span class="badge bg-info">{{ $dep->documentos_en_curso }}</span></li>
                                                <li class="mb-2"><strong>Finalizados:</strong> <span class="badge bg-success">{{ $dep->documentos_derivados_finalizados }}</span></li>
                                                <li class="mb-2"><strong>Archivados:</strong> <span class="badge bg-secondary">{{ $dep->documentos_archivados }}</span></li>
                                                <li><strong>Tasa de documentos completados:</strong> <span class="badge bg-warning text-dark">{{ $dep->estadisticas['tasa_completitud'] }}%</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- PERSONAL DEL DEPARTAMENTO --}}
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3"> Personal del Departamento</h6>
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><strong>Total Personas:</strong> <span class="badge bg-secondary">{{ $dep->total_personas }}</span></li>
                                                <li class="mb-2"><strong>Personas Internas:</strong> <span class="badge bg-success">{{ $dep->personas_internas }}</span></li>
                                                <li class="mb-2"><strong>Personas Activas:</strong> <span class="badge bg-primary">{{ $dep->personas_activas }}</span></li>
                                            </ul>
                                            
                                            @if(count($dep->personas_lista) > 0)
                                            <hr class="my-2">
                                            <strong class="d-block mb-2"> Lista de Personal:</strong>
                                            <div style="max-height: 150px; overflow-y: auto;">
                                                <ul class="list-unstyled small">
                                                    @foreach($dep->personas_lista as $persona)
                                                    <li class="mb-1"><i class="bi bi-person-fill text-primary"></i> {{ $persona }}</li>
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

                            {{-- ESTADÍSTICAS CON GRÁFICAS INDIVIDUALES --}}
                            <h6 class="fw-bold mt-4 mb-3">Estadísticas Detalladas</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <canvas id="chartDepto{{ $dep->idDepartamento }}" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <canvas id="chartFlujoDep{{ $dep->idDepartamento }}" height="100"></canvas>
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
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $recPct }}%; font-weight: bold; font-size: 12px;"
                                    aria-valuenow="{{ $dep->recibidos }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                                     {{ $dep->recibidos }}
                                </div>
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $envPct }}%; font-weight: bold; font-size: 12px;"
                                    aria-valuenow="{{ $dep->enviados }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                                     {{ $dep->enviados }}
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-info">No existen departamentos registrados.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@php
    $top5Deps = $departamentos->sortByDesc(function($d) { return $d->documentos_originarios; })->take(5);
    $top5Labels = json_encode($top5Deps->pluck('nombre')->toArray());
    $top5Data = json_encode($top5Deps->pluck('documentos_originarios')->toArray());
    $estadosData = json_encode([$estadisticas['total_en_curso'], $estadisticas['total_finalizados'], $estadisticas['total_archivados']]);
@endphp

<script>
    // Datos para gráficas generales
    const chartEstadosData = {
        labels: ['En Curso', 'Finalizados', 'Archivados'],
        datasets: [{
            label: 'Cantidad de Documentos',
            data: {!! $estadosData !!},
            backgroundColor: ['#0dcaf0', '#198754', '#6c757d'],
            borderColor: ['#0dcaf0', '#198754', '#6c757d'],
            borderWidth: 1
        }]
    };

    const chartTop5Data = {
        labels: {!! $top5Labels !!},
        datasets: [{
            label: 'Documentos',
            data: {!! $top5Data !!},
            backgroundColor: '#0B2D59',
            borderColor: '#2E608C',
            borderWidth: 1
        }]
    };

    // Renderizar gráficas generales
    const ctxEstados = document.getElementById('chartEstados');
    if (ctxEstados) {
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: chartEstadosData,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
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
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Gráficas por departamento
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
                    backgroundColor: ['#0dcaf0', '#198754', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Estado de Documentos' }
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
                    backgroundColor: ['#198754', '#0B2D59']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Flujo de Documentos' }
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
        const seccion = document.getElementById('estadisticas-departamentos');
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