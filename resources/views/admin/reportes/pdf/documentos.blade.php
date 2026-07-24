<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Control Documental</title>
    @include('admin.reportes.pdf.partials.styles')
    <style>
        .filter-context {
            border: 1px solid #DDE3EC;
            background: #F8FAFC;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 9px;
        }
        .trace-line {
            color: #5E7491;
            font-size: 8px;
            margin-top: 2px;
        }
        .doc-subject {
            color: #5E7491;
            font-size: 8px;
            line-height: 1.35;
        }
        .derivation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .derivation-table th {
            background: #E8EFF6;
            color: #0B2D59;
            border: 1px solid #DDE3EC;
            padding: 4px;
            font-size: 7px;
            text-transform: uppercase;
        }
        .derivation-table td {
            border: 1px solid #DDE3EC;
            padding: 4px;
            font-size: 7px;
            vertical-align: top;
        }
        .derivation-title {
            color: #0B2D59;
            font-weight: bold;
            margin-top: 5px;
            font-size: 8px;
        }
    </style>
</head>
<body>

    @if(!empty($showPrintToolbar))
        <div class="print-toolbar">
            <div>
                <strong>Reporte listo para imprimir</strong>
                <span>Use el boton para abrir la impresion del navegador.</span>
            </div>
            <div class="print-toolbar-actions">
                <button type="button" onclick="window.print()">Imprimir</button>
                <a href="{{ route('admin.reportes.documentos', request()->query()) }}">Volver</a>
            </div>
        </div>
    @endif

    <table class="pdf-header">
        <tr>
            <td width="20%">
                @if(!empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" class="pdf-logo">
                @endif
            </td>
            <td width="80%" class="pdf-brand">
                <h1>Sistema de Gestion Documental</h1>
                <p>Reporte de control documental y trazabilidad resumida</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    <div class="pdf-title">
        <h2>Reporte de Documentos</h2>
        <p>Estado, urgencia, remitente, archivo digital y ultimo movimiento por documento.</p>
    </div>

    @if(request()->hasAny(['q', 'idTipo', 'idEstado', 'idUrgencia', 'idRemitente', 'fecha_inicio', 'fecha_fin']))
        <div class="filter-context">
            <strong>Filtros aplicados:</strong>
            {{ request('q') ? 'Busqueda: ' . request('q') . ' | ' : '' }}
            {{ request('idTipo') ? 'Tipo ID: ' . request('idTipo') . ' | ' : '' }}
            {{ request('idEstado') ? 'Estado ID: ' . request('idEstado') . ' | ' : '' }}
            {{ request('idUrgencia') ? 'Urgencia ID: ' . request('idUrgencia') . ' | ' : '' }}
            {{ request('idRemitente') ? 'Remitente ID: ' . request('idRemitente') . ' | ' : '' }}
            {{ request('fecha_inicio') ? 'Desde: ' . request('fecha_inicio') . ' | ' : '' }}
            {{ request('fecha_fin') ? 'Hasta: ' . request('fecha_fin') : '' }}
        </div>
    @endif

    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">Documentos</div>
                <div class="summary-value">{{ $estadisticas['total_documentos'] }}</div>
            </td>
            <td>
                <div class="summary-label">Derivaciones</div>
                <div class="summary-value">{{ $estadisticas['derivaciones_total'] }}</div>
            </td>
            <td>
                <div class="summary-label">Urgentes</div>
                <div class="summary-value">{{ $estadisticas['documentos_urgentes'] }}</div>
            </td>
            <td>
                <div class="summary-label">Con PDF</div>
                <div class="summary-value">{{ $estadisticas['con_pdf'] }}</div>
            </td>
            <td>
                <div class="summary-label">Sin movimiento</div>
                <div class="summary-value">{{ $estadisticas['documentos_sin_derivacion'] }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="21%">Documento</th>
                <th width="19%">Origen</th>
                <th width="16%">Clasificacion</th>
                <th width="16%">Control</th>
                <th width="28%">Trazabilidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documentos as $doc)
                @php
                    $estadoNombre = $doc->estado->nombre ?? 'N/A';
                    $urgenciaNombre = $doc->urgencia->nombre ?? 'Sin urgencia';
                    $urgenciaClass = strtolower($urgenciaNombre) === 'urgente' ? 'tag-danger' : 'tag-navy';
                @endphp
                <tr>
                    <td>
                        <strong class="strong-navy">{{ $doc->cite ?? 'S/C' }}</strong>
                        <div class="doc-subject">{{ $doc->asunto ?? 'Sin asunto' }}</div>
                    </td>
                    <td>
                        <strong>{{ $doc->remitente->nombre ?? 'N/A' }}</strong><br>
                        <span class="muted">CI: {{ $doc->remitente->ci ?? 'S/R' }}</span><br>
                        <span class="tag">{{ $doc->remitente->departamento->nombre ?? 'Externo/Independiente' }}</span>
                    </td>
                    <td>
                        <span class="tag tag-navy">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</span><br>
                        <span class="tag {{ $urgenciaClass }}">{{ $urgenciaNombre }}</span>
                    </td>
                    <td>
                        <span class="tag tag-gold">{{ $estadoNombre }}</span><br>
                        <span class="muted">{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</span><br>
                        <span class="muted">{{ $doc->dias_registro ?? 0 }} dias registrado</span><br>
                        <span class="tag">{{ $doc->tiene_archivo ? 'Con PDF' : 'Sin PDF' }}</span>
                    </td>
                    <td>
                        <strong>{{ $doc->ubicacion_actual_reporte }}</strong>
                        <div class="trace-line">{{ $doc->ultimo_movimiento_reporte }}</div>
                        <span class="tag tag-navy">{{ $doc->total_derivaciones }} movimientos</span>
                        <span class="tag">{{ $doc->estado_fisico_reporte }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div class="derivation-title">Registro de derivaciones: {{ $doc->total_derivaciones }} registros</div>
                        @if($doc->derivaciones_reporte->count() > 0)
                            <table class="derivation-table">
                                <thead>
                                    <tr>
                                        <th width="8%">Orden</th>
                                        <th width="25%">Flujo</th>
                                        <th width="22%">Fechas</th>
                                        <th width="23%">Responsables</th>
                                        <th width="22%">Instruccion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doc->derivaciones_reporte as $derivacion)
                                        <tr>
                                            <td>#{{ $derivacion->orden ?? $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $derivacion->departamentoOrigen->nombre ?? 'Origen no identificado' }}</strong><br>
                                                <span class="muted">a {{ $derivacion->departamentoDestino->nombre ?? 'Destino no identificado' }}</span>
                                            </td>
                                            <td>
                                                Envio: {{ $derivacion->fechaEnvio ? \Carbon\Carbon::parse($derivacion->fechaEnvio)->format('d/m/Y H:i') : 'S/F' }}<br>
                                                Recepcion: {{ $derivacion->fechaRecepcion ? \Carbon\Carbon::parse($derivacion->fechaRecepcion)->format('d/m/Y H:i') : 'Pendiente' }}
                                            </td>
                                            <td>
                                                Envia: {{ $derivacion->usuarioEnvio->name ?? 'No registrado' }}<br>
                                                Asignado: {{ $derivacion->usuarioAsignado->name ?? 'No asignado' }}
                                            </td>
                                            <td>{{ $derivacion->instruccion ?? 'Sin instruccion registrada' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <span class="muted">Este documento aun no tiene derivaciones registradas.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No se encontraron documentos para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pdf-footer">
        <strong>SISGED</strong> - Reporte generado automaticamente
    </div>

</body>
</html>
