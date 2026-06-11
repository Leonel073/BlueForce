@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')

<div class="container-fluid py-4">

    {{-- ESTILOS --}}
    <style>

        .dashboard-gradient {
            background: linear-gradient(135deg,#0B2D59,#2E608C);
        }

        .glass-card {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
            background: #fff;
            transition: all .3s ease;
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(0,0,0,.08);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .quick-link {
            transition: all .25s ease;
        }

        .quick-link:hover {
            transform: scale(1.03);
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table thead th {
            border: 0;
            color: #0B2D59;
            font-weight: 700;
        }

        .mini-bar {
            height: 14px;
            border-radius: 20px;
            background: #e9ecef;
            overflow: hidden;
        }

        .mini-bar-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg,#0B2D59,#2E608C);
        }

        .section-title {
            font-weight: 700;
            color: #0B2D59;
        }

    </style>

    {{-- HEADER --}}
    <div class="card glass-card shadow-lg mb-4 dashboard-gradient">

        <div class="card-body p-5 dashboard-gradient">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center dashboard-gradient">

                <div>

                    <h1 class="fw-bold text-white mb-2">

                        <i class="bi bi-speedometer2 me-2"></i>

                        Dashboard Administrativo

                    </h1>

                    <p class="text-light mb-0">

                        Panel general del sistema documental

                    </p>

                </div>

                <div class="mt-4 mt-md-0">

                    <span class="badge bg-light text-dark px-4 py-3 rounded-pill fs-6">

                        <i class="bi bi-calendar-event me-2"></i>

                        {{ now()->format('d/m/Y') }}

                    </span>

                </div>

            </div>

        </div>

    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="row mb-4">

        {{-- DOCUMENTOS --}}
        <div class="col-lg-4 col-md-6 mb-3">

            <div class="card glass-card shadow-sm h-100">

                <div class="card-body p-4 d-flex align-items-center">

                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-4">

                        <i class="bi bi-file-earmark-text-fill"></i>

                    </div>

                    <div>

                        <h2 class="fw-bold mb-1">

                            {{ $totalDocumentos }}

                        </h2>

                        <div class="text-muted">

                            Total Documentos

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- USUARIOS --}}
        <div class="col-lg-4 col-md-6 mb-3">

            <div class="card glass-card shadow-sm h-100">

                <div class="card-body p-4 d-flex align-items-center">

                    <div class="stat-icon bg-success bg-opacity-10 text-success me-4">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <div>

                        <h2 class="fw-bold mb-1">

                            {{ $totalUsuarios }}

                        </h2>

                        <div class="text-muted">

                            Usuarios Registrados

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- DERIVACIONES --}}
        <div class="col-lg-4 col-md-12 mb-3">

            <div class="card glass-card shadow-sm h-100">

                <div class="card-body p-4 d-flex align-items-center">

                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-4">

                        <i class="bi bi-arrow-left-right"></i>

                    </div>

                    <div>

                        <h2 class="fw-bold mb-1">

                            {{ $totalDerivaciones }}

                        </h2>

                        <div class="text-muted">

                            Derivaciones Totales

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <div class="row mb-4">

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.usuarios') }}"
               class="text-decoration-none quick-link">

                <div class="card glass-card shadow-sm text-center p-4">

                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <h5 class="fw-bold mb-0">

                        Usuarios

                    </h5>

                </div>

            </a>

        </div>

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.correspondencia') }}"
               class="text-decoration-none quick-link">

                <div class="card glass-card shadow-sm text-center p-4">

                    <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">

                        <i class="bi bi-folder-fill"></i>

                    </div>

                    <h5 class="fw-bold mb-0">

                        Correspondencia

                    </h5>

                </div>

            </a>

        </div>

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.reportes.index') }}"
               class="text-decoration-none quick-link">

                <div class="card glass-card shadow-sm text-center p-4">

                    <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3">

                        <i class="bi bi-bar-chart-fill"></i>

                    </div>

                    <h5 class="fw-bold mb-0">

                        Reportes

                    </h5>

                </div>

            </a>

        </div>

        <div class="col-md-3 mb-3">

            <a href="{{ route('admin.reportes.derivaciones') }}"
               class="text-decoration-none quick-link">

                <div class="card glass-card shadow-sm text-center p-4">

                    <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto mb-3">

                        <i class="bi bi-arrow-left-right"></i>

                    </div>

                    <h5 class="fw-bold mb-0">

                        Derivaciones

                    </h5>

                </div>

            </a>

        </div>

    </div>

    {{-- FLUJO DE DEPARTAMENTOS CON GRÁFICO --}}
    <div class="card glass-card shadow-lg mb-4">

        <div class="card-header border-0 dashboard-gradient text-white p-4">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">

                    <i class="bi bi-diagram-3-fill me-2"></i>

                    Estadísticas de Departamentos

                </h5>

                <span class="badge bg-light text-dark rounded-pill px-3 py-2">

                    SISTEMA

                </span>

            </div>

        </div>

        <div class="card-body p-4">

            <div style="position: relative; height: 300px;">

                <canvas id="departamentosChart"></canvas>

            </div>

        </div>

    </div>

    {{-- ESTADOS CON GRÁFICOS --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card glass-card shadow-lg">
                <div class="card-header dashboard-gradient text-white p-4 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Estado de Documentos</h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 300px;">
                        <canvas id="estadosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card glass-card shadow-lg">
                <div class="card-header dashboard-gradient text-white p-4 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Tipos de Documentos</h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 300px;">
                        <canvas id="tiposChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LÍNEA TEMPORAL DE DOCUMENTOS --}}
    <div class="card glass-card shadow-lg mb-4">
        <div class="card-header dashboard-gradient text-white p-4 border-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Tendencia de Documentos Últimos 6 Meses</h5>
        </div>
        <div class="card-body p-4">
            <div style="position: relative; height: 300px;">
                <canvas id="tendenciaChart"></canvas>
            </div>
        </div>
    </div>

    {{-- DOCUMENTOS RECIENTES --}}
    <div class="card glass-card shadow-lg mb-4">

        <div class="card-header dashboard-gradient text-white p-4 border-0">

            <h5 class="mb-0 fw-bold">

                <i class="bi bi-clock-history me-2"></i>

                Últimos Documentos

            </h5>

        </div>

        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table align-middle table-hover">

                    <thead>

                        <tr>

                            <th>Cite</th>

                            <th>Asunto</th>

                            <th>Fecha</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($documentosRecientes as $doc)

                            <tr>

                                <td class="fw-semibold">

                                    {{ $doc->cite }}

                                </td>

                                <td>

                                    {{ $doc->asunto }}

                                </td>

                                <td>

                                    <span class="badge bg-light text-dark">

                                        {{ $doc->fecha }}

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- DERIVACIONES RECIENTES --}}
    <div class="card glass-card shadow-lg">

        <div class="card-header text-white p-4 border-0"
             style="background: linear-gradient(135deg,#2E608C,#0B2D59);">

            <h5 class="mb-0 fw-bold">

                <i class="bi bi-arrow-repeat me-2"></i>

                Últimas Derivaciones

            </h5>

        </div>

        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table align-middle table-hover">

                    <thead>

                        <tr>

                            <th>Documento</th>

                            <th>Origen</th>

                            <th>Destino</th>

                            <th>Realizada por</th>

                            <th>Fecha</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($derivacionesRecientes as $d)

                            <tr>

                                <td class="fw-semibold">

                                    {{ $d->documento->cite ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->departamentoOrigen->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $d->departamentoDestino->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    @if($d->usuarioEnvio)

                                        <span class="text-muted small">

                                            <i class="bi bi-person-badge me-1"></i>

                                            {{ $d->usuarioEnvio->name }}

                                        </span>

                                    @else

                                        <span class="badge bg-secondary bg-opacity-25 text-secondary">

                                            No registrado

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <span class="badge bg-light text-dark">

                                        {{ $d->fechaEnvio }}

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

{{-- CHART.JS LIBRARY --}}
<script src="{{ asset('js/chart.umd.min.js') }}"></script>

<script>
// GLOBAL COLORS
const primaryColor = '#0B2D59';
const secondaryColor = '#2E608C';
const successColor = '#28a745';
const warningColor = '#ffc107';
const dangerColor = '#dc3545';
const infoColor = '#17a2b8';

// CHART 1: Estados de Documentos (Pie Chart)
const estadosCtx = document.getElementById('estadosChart')?.getContext('2d');
if (estadosCtx) {
    fetch('{{ route("admin.api.estadisticas.dashboard") }}')
        .then(res => res.json())
        .then(data => {
            new Chart(estadosCtx, {
                type: 'doughnut',
                data: {
                    labels: data.estados.map(e => e.nombre),
                    datasets: [{
                        data: data.estados.map(e => e.cantidad),
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d', '#fd7e14'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });
}

// CHART 2: Tipos de Documentos (Bar Chart)
const tiposCtx = document.getElementById('tiposChart')?.getContext('2d');
if (tiposCtx) {
    fetch('{{ route("admin.api.estadisticas.dashboard") }}')
        .then(res => res.json())
        .then(data => {
            new Chart(tiposCtx, {
                type: 'bar',
                data: {
                    labels: data.tipos.map(t => t.nombre),
                    datasets: [{
                        label: 'Cantidad',
                        data: data.tipos.map(t => t.cantidad),
                        backgroundColor: secondaryColor,
                        borderColor: primaryColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });
        });
}

// CHART 3: Tendencia de Documentos (Line Chart)
const tendenciaCtx = document.getElementById('tendenciaChart')?.getContext('2d');
if (tendenciaCtx) {
    fetch('{{ route("admin.api.estadisticas.dashboard") }}')
        .then(res => res.json())
        .then(data => {
            new Chart(tendenciaCtx, {
                type: 'line',
                data: {
                    labels: data.meses.map(m => m.mes),
                    datasets: [{
                        label: 'Documentos',
                        data: data.meses.map(m => m.cantidad),
                        borderColor: primaryColor,
                        backgroundColor: primaryColor + '20',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 5,
                        pointBackgroundColor: primaryColor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
}


const departamentosCanvas =
    document.getElementById('departamentosChart');

if (departamentosCanvas) {

    const ctx =
        departamentosCanvas.getContext('2d');

    fetch('{{ route("admin.api.estadisticas.departamentos") }}')

        .then(response => response.json())

        .then(data => {

            if (!Array.isArray(data)) {

                console.error('API departamentos: respuesta inválida', data);

                return;

            }

            new Chart(ctx, {

                type: 'bar',

                data: {

                    labels: data.map(d => d.nombre),

                    datasets: [

                        {
                            label: 'Documentos',

                            data: data.map(d => d.documentos),

                            backgroundColor: '#0B2D59'
                        },

                        {
                            label: 'Derivaciones',

                            data: data.map(d => d.derivaciones),

                            backgroundColor: '#2E608C'
                        }

                    ]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    indexAxis: 'y',

                    scales: {

                        x: {

                            beginAtZero: true
                        }
                    }
                }
            });

        })

        .catch(error => {

            console.error('ERROR:', error);

        });

}


</script>

@endsection