@extends('layouts.app')

@section('title', 'Dashboard Personal')

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
    </style>

    {{-- HEADER --}}
    <div class="card glass-card shadow-lg mb-4 dashboard-gradient">
        <div class="card-body p-5 dashboard-gradient">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center dashboard-gradient">
                <div>
                    <h1 class="fw-bold text-white mb-2">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard Personal
                    </h1>
                    <p class="text-light mb-0">
                        Panel de seguimiento de tus documentos
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

    @php
        $documentosPendientes =
            collect($estadosPorTipo)
                ->where('nombre', '!=', 'Finalizado')
                ->sum('cantidad');
    @endphp

    {{-- ESTADÍSTICAS --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glass-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-4">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $totalDocumentos ?? 0 }}</h2>
                        <div class="text-muted">Total Documentos</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glass-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-4">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $documentosPendientes ?? 0 }}</h2>
                        <div class="text-muted">Documentos Pendientes</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glass-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-4">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $derivacionesRealizadas ?? 0 }}</h2>
                        <div class="text-muted">Derivaciones Realizadas</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glass-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger me-4">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $urgentes ?? 0 }}</h2>
                        <div class="text-muted">Documentos Urgentes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <a href="{{ route('documentos.index') }}" class="text-decoration-none quick-link">
                <div class="card glass-card shadow-sm text-center p-4">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="bi bi-file-text-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Documentos</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('envios.bandeja') }}" class="text-decoration-none quick-link">
                <div class="card glass-card shadow-sm text-center p-4">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Mi Bandeja</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('envios.index') }}" class="text-decoration-none quick-link">
                <div class="card glass-card shadow-sm text-center p-4">
                    <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-send-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Enviados</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.reportes.documentos') }}" class="text-decoration-none quick-link">
                <div class="card glass-card shadow-sm text-center p-4">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Reportes Documentos</h5>
                </div>
            </a>
        </div>
    </div>

    {{-- ESTADOS Y TIPOS --}}
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
                    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Documentos por Tipo</h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 300px;">
                        <canvas id="tiposChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TENDENCIA --}}
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

    {{-- ÚLTIMOS DOCUMENTOS --}}
    <div class="card glass-card shadow-lg">
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
                        @forelse($ultimosDocumentos as $doc)
                            <tr>
                                <td class="fw-semibold">{{ $doc->cite }}</td>
                                <td>{{ $doc->asunto }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    No hay documentos recientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- CHART.JS LIBRARY --}}
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
const primaryColor = '#0B2D59';
const secondaryColor = '#2E608C';

// CHART 1: Estados
const estadosCtx = document.getElementById('estadosChart')?.getContext('2d');
if (estadosCtx) {
    new Chart(estadosCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($estadosPorTipo as $estado)
                '{{ $estado["nombre"] }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($estadosPorTipo as $estado)
                    {{ $estado["cantidad"] }},
                    @endforeach
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d', '#fd7e14'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

// CHART 2: Tipos
const tiposCtx = document.getElementById('tiposChart')?.getContext('2d');
if (tiposCtx) {
    new Chart(tiposCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($documentosPorTipo as $tipo)
                '{{ $tipo["nombre"] }}',
                @endforeach
            ],
            datasets: [{
                label: 'Cantidad',
                data: [
                    @foreach($documentosPorTipo as $tipo)
                    {{ $tipo["cantidad"] }},
                    @endforeach
                ],
                backgroundColor: secondaryColor,
                borderColor: primaryColor,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });
}

// CHART 3: Tendencia
const tendenciaCtx = document.getElementById('tendenciaChart')?.getContext('2d');
if (tendenciaCtx) {
    new Chart(tendenciaCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($documentosPorMes as $mes)
                '{{ $mes["mes"] }}',
                @endforeach
            ],
            datasets: [{
                label: 'Documentos',
                data: [
                    @foreach($documentosPorMes as $mes)
                    {{ $mes["cantidad"] }},
                    @endforeach
                ],
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
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
}
</script>

@endsection
