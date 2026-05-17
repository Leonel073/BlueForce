@extends('layouts.app')

@section('title', 'Estadísticas de Auditoría')

@section('content')
<div class="container-fluid p-4">
    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('auditoria.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
                <i class="bi bi-chevron-left"></i> Volver a auditoría
            </a>
            <h1 class="h3 fw-bold" style="color: #0B2D59;">
                <i class="bi bi-graph-up"></i> Estadísticas de Auditoría
            </h1>
        </div>
        <div class="col-auto">
            <form method="GET" action="{{ route('auditoria.estadisticas') }}" class="d-flex gap-2">
                <select name="dias" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="7" {{ $dias == 7 ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="30" {{ $dias == 30 ? 'selected' : '' }}>Últimos 30 días</option>
                    <option value="90" {{ $dias == 90 ? 'selected' : '' }}>Últimos 90 días</option>
                </select>
            </form>
        </div>
    </div>

    <!-- MÉTRICAS PRINCIPALES -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="bi bi-list-check text-primary" style="font-size: 32px;"></i>
                    </div>
                    <h4 style="color: #0B2D59;">{{ $resumen['total'] }}</h4>
                    <p class="text-muted mb-0 small">Total de Operaciones</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(25, 135, 84, 0.05));">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="bi bi-person-check text-success" style="font-size: 32px;"></i>
                    </div>
                    <h4 style="color: #198754;">{{ $resumen['usuarios_activos'] }}</h4>
                    <p class="text-muted mb-0 small">Usuarios Activos</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="bi bi-percent text-warning" style="font-size: 32px;"></i>
                    </div>
                    <h4 style="color: #FFC107;">
                        {{ $resumen['total'] > 0 ? round(($resumen['actualizaciones'] / $resumen['total']) * 100, 1) : 0 }}%
                    </h4>
                    <p class="text-muted mb-0 small">Tasa de Actualización</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 32px;"></i>
                    </div>
                    <h4 style="color: #DC3545;">{{ $resumen['eliminaciones'] }}</h4>
                    <p class="text-muted mb-0 small">Eliminaciones</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- DISTRIBUCIÓN POR ACCIÓN -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-pie-chart"></i> Distribución por Acción
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartAcciones" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- RESUMEN RÁPIDO -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-diagram-3"></i> Resumen de Operaciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <div>
                                    <p class="mb-1 small text-muted">Creaciones</p>
                                    <h5 class="mb-0" style="color: #198754;">{{ $resumen['creaciones'] }}</h5>
                                </div>
                                <div style="width: 60px; height: 60px; background: rgba(25, 135, 84, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-plus-circle text-success" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <div>
                                    <p class="mb-1 small text-muted">Actualizaciones</p>
                                    <h5 class="mb-0" style="color: #FFC107;">{{ $resumen['actualizaciones'] }}</h5>
                                </div>
                                <div style="width: 60px; height: 60px; background: rgba(255, 193, 7, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-pencil-square text-warning" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <div>
                                    <p class="mb-1 small text-muted">Eliminaciones</p>
                                    <h5 class="mb-0" style="color: #DC3545;">{{ $resumen['eliminaciones'] }}</h5>
                                </div>
                                <div style="width: 60px; height: 60px; background: rgba(220, 53, 69, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-trash text-danger" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP USUARIOS -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-people"></i> Usuarios Más Activos
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($actividadPorUsuario->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="color: #0B2D59;" class="small fw-semibold">Usuario</th>
                                        <th style="color: #0B2D59;" class="small fw-semibold text-end">Operaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($actividadPorUsuario->take(10) as $actividad)
                                        <tr>
                                            <td class="small">
                                                @if($actividad->usuario)
                                                    {{ $actividad->usuario->name }}
                                                @else
                                                    Sistema
                                                @endif
                                            </td>
                                            <td class="small text-end">
                                                <span class="badge" style="background: #0B2D59;">
                                                    {{ $actividad->total }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            No hay datos disponibles
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- MODELOS AUDITADOS -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-boxes"></i> Modelos Más Modificados
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($actividadPorModelo->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="color: #0B2D59;" class="small fw-semibold">Modelo</th>
                                        <th style="color: #0B2D59;" class="small fw-semibold">Acción</th>
                                        <th style="color: #0B2D59;" class="small fw-semibold text-end">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($actividadPorModelo->take(10) as $actividad)
                                        <tr>
                                            <td class="small">{{ $actividad->modelo }}</td>
                                            <td class="small">
                                                @if($actividad->accion === 'CREATE')
                                                    <span class="badge bg-success"><i class="bi bi-plus-circle"></i> CREATE</span>
                                                @elseif($actividad->accion === 'UPDATE')
                                                    <span class="badge bg-warning"><i class="bi bi-pencil-square"></i> UPDATE</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="bi bi-trash"></i> DELETE</span>
                                                @endif
                                            </td>
                                            <td class="small text-end">
                                                <span class="badge bg-light text-dark">{{ $actividad->total }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            No hay datos disponibles
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    // Gráfico de distribución por acción
    const chartAccionesCtx = document.getElementById('chartAcciones').getContext('2d');
    new Chart(chartAccionesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Creaciones', 'Actualizaciones', 'Eliminaciones'],
            datasets: [{
                data: [
                    {{ $resumen['creaciones'] }},
                    {{ $resumen['actualizaciones'] }},
                    {{ $resumen['eliminaciones'] }}
                ],
                backgroundColor: [
                    '#198754',
                    '#FFC107',
                    '#DC3545'
                ],
                borderColor: [
                    '#fff',
                    '#fff',
                    '#fff'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
