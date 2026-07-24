<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Integral de Personas</title>
    @include('admin.reportes.pdf.partials.styles')
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
                <a href="{{ route('admin.reportes.personas', request()->query()) }}">Volver al reporte</a>
            </div>
        </div>
    @endif

    {{-- HEADER --}}
    <table class="pdf-header">
        <tr>
            <td width="20%">
                @if(!empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" class="pdf-logo">
                @endif
            </td>
            <td width="80%" class="pdf-brand">
                <h1>Sistema de Gestion Documental</h1>
                <p>Reporte Oficial Integral de Personas y Documentos</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    {{-- TITULO --}}
    <div class="pdf-title">
        <h2>Historial de Remitentes</h2>
        <p>Total de perfiles registrados: <strong>{{ $personas->count() }}</strong></p>
    </div>

    {{-- RESUMEN --}}
    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">TOTAL PERSONAS</div>
                <div class="summary-value">{{ $personas->count() }}</div>
            </td>
            <td>
                <div class="summary-label">CON DOCUMENTOS</div>
                <div class="summary-value">{{ $estadisticas['personas_con_documentos'] ?? $personas->filter(fn($p) => $p->documentos->count() > 0)->count() }}</div>
            </td>
            <td>
                <div class="summary-label">DOCS. ENVIADOS</div>
                <div class="summary-value">{{ $estadisticas['total_documentos_enviados'] ?? $personas->sum('total_documentos') }}</div>
            </td>
            <td>
                <div class="summary-label">DERIVACIONES</div>
                <div class="summary-value">{{ $estadisticas['total_derivaciones'] ?? 0 }}</div>
            </td>
        </tr>
    </table>

    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">INTERNAS</div>
                <div class="summary-value">{{ $estadisticas['personas_internas'] ?? $personas->filter(fn($p) => $p->tipo === 'INTERNO')->count() }}</div>
            </td>
            <td>
                <div class="summary-label">EXTERNAS</div>
                <div class="summary-value">{{ $estadisticas['personas_externas'] ?? $personas->filter(fn($p) => $p->tipo === 'EXTERNO')->count() }}</div>
            </td>
            <td>
                <div class="summary-label">PROMEDIO DOCS/PERSONA</div>
                <div class="summary-value">{{ $estadisticas['promedio_documentos_por_persona'] ?? 0 }}</div>
            </td>
            <td>
                <div class="summary-label">DOCS. CON SEGUIMIENTO</div>
                <div class="summary-value">{{ $estadisticas['documentos_con_derivacion'] ?? 0 }}</div>
            </td>
        </tr>
    </table>

    {{-- TABLA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="25%">Persona</th>
                <th width="25%">Contacto / Institucion</th>
                <th width="50%">Historial de Documentos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personas as $p)
                <tr>
                    <td>
                        <span class="strong-navy" style="font-size:11px;">{{ $p->nombre }}</span><br>
                        <span class="muted">CI: {{ $p->ci ?? 'N/A' }}</span><br><br>
                        <span class="tag {{ $p->tipo === 'INTERNO' ? 'tag-success' : 'tag-gold' }}">{{ $p->tipo }}</span><br>
                        <span class="tag tag-navy">{{ $p->documentos_enviados ?? $p->total_documentos ?? $p->documentos->count() }} docs enviados</span>
                    </td>
                    <td>
                        <span class="muted">
                            Cel: {{ $p->telefono_celular ?? '-' }}<br>
                            Fijo: {{ $p->telefono_fijo ?? '-' }}<br>
                            Email: {{ $p->correo ?? '-' }}
                        </span><br><br>
                        <strong>{{ $p->institucion ?? 'Independiente' }}</strong><br>
                        <span class="muted">{{ $p->cargos_nombres }}</span>
                    </td>
                    <td>
                        @if($p->documentos->count() > 0)
                            @foreach($p->documentos as $doc)
                                <div style="margin-bottom:6px;padding-bottom:4px;border-bottom:1px dashed #DDE3EC;">
                                    <span class="strong-navy">{{ $doc->cite }}</span>
                                    <span class="tag">{{ $doc->tipoDocumento->nombre ?? 'Doc' }}</span><br>
                                    <span class="muted">Asunto: {{ $doc->asunto }}</span><br>
                                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}
                                    | <strong>Est:</strong> {{ $doc->estado->nombre ?? 'N/A' }}
                                </div>
                            @endforeach
                        @else
                            <span class="muted" style="font-style:italic;">Sin documentos en el periodo seleccionado.</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="pdf-footer">
        <strong>SISGED</strong> &mdash; Reporte generado automaticamente
    </div>

</body>
</html>
