<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Flujo Institucional</title>
    @include('admin.reportes.pdf.partials.styles')
    <style>
        .filter-context {
            background: #fff;
            border: 1px solid #DDE3EC;
            border-left: 5px solid #D9A23D;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 9px;
            color: #1A2942;
        }

        .executive-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .executive-grid td {
            width: 25%;
            border: 1px solid #DDE3EC;
            background: #F8FAFC;
            padding: 8px;
            vertical-align: top;
        }

        .exec-title {
            color: #0B2D59;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .dept-card {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: avoid;
            border: 1px solid #DDE3EC;
        }

        .dept-card td {
            border: 1px solid #DDE3EC;
            padding: 7px;
            vertical-align: top;
        }

        .dept-heading td {
            background: #E8EFF6;
            border-color: #C8D7E6;
        }

        .dept-name {
            color: #0B2D59;
            font-size: 12px;
            font-weight: bold;
        }

        .section-label {
            color: #0B2D59;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 4px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table td {
            border: none;
            border-bottom: 1px solid #EEF2F7;
            padding: 3px 0;
            font-size: 8px;
        }

        .bar-wrap {
            width: 100%;
            height: 12px;
            background: #EEF2F7;
            border: 1px solid #DDE3EC;
            margin-top: 5px;
        }

        .bar-rec,
        .bar-env {
            height: 12px;
            float: left;
        }

        .bar-rec { background: #0D9E6E; }
        .bar-env { background: #0B2D59; }

        .people-list {
            color: #5E7491;
            font-size: 8px;
            line-height: 1.45;
        }
    </style>
</head>
<body>
    @if(!empty($showPrintToolbar))
        <div class="print-toolbar">
            <div>
                <strong>Vista imprimible del reporte</strong>
                <span>Use el boton imprimir para generar el PDF desde el navegador.</span>
            </div>
            <div class="print-toolbar-actions">
                <button type="button" onclick="window.print()">Imprimir</button>
                <a href="{{ route('admin.reportes.departamentos', request()->query()) }}">Volver al reporte</a>
            </div>
        </div>
    @endif

    <table class="pdf-header">
        <tr>
            <td width="16%">
                @if(!empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" class="pdf-logo">
                @endif
            </td>
            <td width="84%" class="pdf-brand">
                <h1>Sistema de Gestion Documental</h1>
                <p>Reporte de Flujo Institucional por Departamentos</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    <div class="pdf-title">
        <h2>Reporte por Departamentos</h2>
        <p>Carga documental, personal asignado y balance de movimientos por area.</p>
    </div>

    @if(request()->hasAny(['nombre', 'fecha_inicio', 'fecha_fin']))
        <div class="filter-context">
            <strong>Filtros aplicados:</strong>
            {{ request('nombre') ? 'Departamento: ' . request('nombre') . ' | ' : '' }}
            {{ request('fecha_inicio') ? 'Desde: ' . request('fecha_inicio') . ' | ' : '' }}
            {{ request('fecha_fin') ? 'Hasta: ' . request('fecha_fin') : '' }}
        </div>
    @endif

    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">DEPARTAMENTOS</div>
                <div class="summary-value">{{ $estadisticas['total_departamentos'] }}</div>
            </td>
            <td>
                <div class="summary-label">MOVIMIENTOS</div>
                <div class="summary-value">{{ $estadisticas['movimiento_total'] }}</div>
            </td>
            <td>
                <div class="summary-label">RECIBIDOS</div>
                <div class="summary-value" style="color:#0D9E6E;">{{ $estadisticas['total_recibidos'] }}</div>
            </td>
            <td>
                <div class="summary-label">ENVIADOS</div>
                <div class="summary-value">{{ $estadisticas['total_enviados'] }}</div>
            </td>
            <td>
                <div class="summary-label">PERSONAL</div>
                <div class="summary-value">{{ $estadisticas['total_personal'] }}</div>
            </td>
            <td>
                <div class="summary-label">DOCS. ORIG.</div>
                <div class="summary-value">{{ $estadisticas['total_documentos'] }}</div>
            </td>
        </tr>
    </table>

    @php
        $departamentoMayorMovimiento = $departamentos->sortByDesc('movimiento_total')->first();
        $departamentoMayorCarga = $departamentos->sortByDesc('documentos_originarios')->first();
    @endphp

    <table class="executive-grid">
        <tr>
            <td>
                <div class="exec-title">Mayor movimiento</div>
                <strong>{{ $departamentoMayorMovimiento?->nombre ?? 'N/A' }}</strong><br>
                <span class="tag tag-navy">{{ $departamentoMayorMovimiento?->movimiento_total ?? 0 }} movimientos</span>
            </td>
            <td>
                <div class="exec-title">Mayor carga documental</div>
                <strong>{{ $departamentoMayorCarga?->nombre ?? 'N/A' }}</strong><br>
                <span class="tag tag-gold">{{ $departamentoMayorCarga?->documentos_originarios ?? 0 }} documentos</span>
            </td>
            <td>
                <div class="exec-title">Cobertura de personal</div>
                <strong>{{ $estadisticas['departamentos_con_personal'] }}</strong> con personal<br>
                <span class="muted">{{ $estadisticas['departamentos_sin_movimiento'] }} sin movimiento</span>
            </td>
            <td>
                <div class="exec-title">Estado documental</div>
                En curso: <strong>{{ $estadisticas['total_en_curso'] }}</strong><br>
                Finalizados: <strong>{{ $estadisticas['total_finalizados'] }}</strong><br>
                Archivados: <strong>{{ $estadisticas['total_archivados'] }}</strong>
            </td>
        </tr>
    </table>

    @forelse($departamentos as $dep)
        @php
            $totalFlujo = $dep->movimiento_total;
            $recPct = $totalFlujo > 0 ? round(($dep->recibidos / $totalFlujo) * 100, 2) : 0;
            $envPct = $totalFlujo > 0 ? round(($dep->enviados / $totalFlujo) * 100, 2) : 0;
            $balanceLabel = $dep->balance_flujo > 0 ? 'Recibe mas' : ($dep->balance_flujo < 0 ? 'Envia mas' : 'Equilibrado');
            $balanceClass = $dep->balance_flujo > 0 ? 'tag-success' : ($dep->balance_flujo < 0 ? 'tag-gold' : 'tag-navy');
        @endphp

        <table class="dept-card">
            <tr class="dept-heading">
                <td colspan="3">
                    <span class="dept-name">{{ $dep->nombre }}</span>
                    <span class="tag tag-navy">Mov. {{ $dep->movimiento_total }}</span>
                    <span class="tag tag-success">Rec. {{ $dep->recibidos }}</span>
                    <span class="tag">Env. {{ $dep->enviados }}</span>
                    <span class="tag {{ $balanceClass }}">{{ $balanceLabel }} ({{ $dep->balance_flujo }})</span>
                </td>
            </tr>
            <tr>
                <td width="32%">
                    <div class="section-label">Flujo documental</div>
                    <table class="mini-table">
                        <tr><td>Recibidos</td><td class="text-center"><strong>{{ $dep->recibidos }}</strong></td></tr>
                        <tr><td>Enviados</td><td class="text-center"><strong>{{ $dep->enviados }}</strong></td></tr>
                        <tr><td>Total movimientos</td><td class="text-center"><strong>{{ $dep->movimiento_total }}</strong></td></tr>
                        <tr><td>Documentos destinatarios</td><td class="text-center"><strong>{{ $dep->documentos_destinatarios }}</strong></td></tr>
                    </table>
                    <div class="bar-wrap">
                        <div class="bar-rec" style="width: {{ $recPct }}%;"></div>
                        <div class="bar-env" style="width: {{ $envPct }}%;"></div>
                    </div>
                    <span class="muted">Recibidos {{ $recPct }}% / Enviados {{ $envPct }}%</span>
                </td>
                <td width="32%">
                    <div class="section-label">Carga originaria</div>
                    <table class="mini-table">
                        <tr><td>Documentos originarios</td><td class="text-center"><strong>{{ $dep->documentos_originarios }}</strong></td></tr>
                        <tr><td>En curso</td><td class="text-center"><strong>{{ $dep->documentos_en_curso }}</strong></td></tr>
                        <tr><td>Finalizados</td><td class="text-center"><strong>{{ $dep->documentos_derivados_finalizados }}</strong></td></tr>
                        <tr><td>Archivados</td><td class="text-center"><strong>{{ $dep->documentos_archivados }}</strong></td></tr>
                        <tr><td>Completitud</td><td class="text-center"><strong>{{ $dep->estadisticas['tasa_completitud'] }}%</strong></td></tr>
                    </table>
                </td>
                <td width="36%">
                    <div class="section-label">Personal y responsable</div>
                    <strong>Encargado:</strong> {{ $dep->encargado_nombre }}<br>
                    <strong>Total personal:</strong> {{ $dep->total_personas }} |
                    <strong>Internos:</strong> {{ $dep->personas_internas }} |
                    <strong>Activos:</strong> {{ $dep->personas_activas }}
                    <br><br>
                    <strong>Personal registrado:</strong><br>
                    <div class="people-list">
                        @if(count($dep->personas_lista) > 0)
                            {{ implode(', ', array_slice($dep->personas_lista, 0, 12)) }}
                            @if(count($dep->personas_lista) > 12)
                                ... (+{{ count($dep->personas_lista) - 12 }} mas)
                            @endif
                        @else
                            Sin personal asignado.
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    @empty
        <table class="data-table">
            <tr>
                <td class="text-center">No existen departamentos registrados para los filtros seleccionados.</td>
            </tr>
        </table>
    @endforelse

    <div class="pdf-footer">
        <strong>SISGED</strong> - Reporte institucional generado automaticamente
    </div>
</body>
</html>
