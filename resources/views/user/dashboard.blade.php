@extends('layouts.app')

@section('title', 'Dashboard Personal')

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- HEADER CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            {{-- Total Documentos --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4" style="border-color: #0B2D59;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Documentos</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $totalDocumentos ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl text-blue-600"><i class="bi bi-file-earmark-text"></i></div>
                </div>
            </div>

            {{-- Documentos Pendientes --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Documentos Pendientes</p>
                        <h3 class="text-3xl font-bold text-yellow-600">{{ collect($estadosPorTipo)->where('nombre', '!=', 'Finalizado')->sum('cantidad') ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl text-yellow-600"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>

            {{-- Derivaciones Realizadas --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4" style="border-color: #6f42c1;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Derivaciones</p>
                        <h3 class="text-3xl font-bold" style="color: #6f42c1;">{{ $derivacionesRealizadas ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl" style="color: #6f42c1;"><i class="bi bi-arrow-left-right"></i></div>
                </div>
            </div>

            {{-- Documentos Urgentes --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Documentos Urgentes</p>
                        <h3 class="text-3xl font-bold text-red-600">{{ $urgentes ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl text-red-600"><i class="bi bi-exclamation-circle"></i></div>
                </div>
            </div>
        </div>

        {{-- CHARTS ROW 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Estados por Tipo (Pie Chart) --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Estado de Documentos</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="estadosChart"></canvas>
                </div>
            </div>

            {{-- Documentos por Mes (Line Chart) --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Documentos Últimos 6 Meses</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="documentosPorMesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- CHARTS ROW 2 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Tipos de Documentos --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Documentos por Tipo</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="documentosPorTipoChart"></canvas>
                </div>
            </div>

            {{-- Últimos Documentos --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🕐 Últimos 5 Documentos</h3>
                <div class="space-y-2">
                    @forelse($ultimosDocumentos as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded border-l-2" style="border-color: #0B2D59;">
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ $doc->cite }}</p>
                            <p class="text-xs text-gray-600">{{ substr($doc->asunto, 0, 40) }}...</p>
                        </div>
                        <span class="text-xs badge px-2 py-1 rounded" style="background-color: #0B2D59; color: white;">
                            {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m') }}
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No hay documentos recientes</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHART.JS LIBRARY --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// GLOBAL COLORS
const primaryColor = '#0B2D59';
const secondaryColor = '#2E608C';
const successColor = '#28a745';
const warningColor = '#ffc107';
const dangerColor = '#dc3545';
const infoColor = '#17a2b8';

// CHART 1: Estados por Tipo (Pie Chart)
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
                backgroundColor: [
                    '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d', '#fd7e14'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// CHART 2: Documentos por Mes (Line Chart)
const mesCtx = document.getElementById('documentosPorMesChart')?.getContext('2d');
if (mesCtx) {
    new Chart(mesCtx, {
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
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// CHART 3: Documentos por Tipo (Bar Chart)
const tipoCtx = document.getElementById('documentosPorTipoChart')?.getContext('2d');
if (tipoCtx) {
    new Chart(tipoCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($documentosPorTipo as $tipo)
                '{{ substr($tipo["nombre"], 0, 15) }}',
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
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>

@endsection