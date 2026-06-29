<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Documentos</title>
    @include('admin.reportes.pdf.partials.styles')
</head>
<body>

    {{-- HEADER --}}
    <table class="pdf-header">
        <tr>
            <td width="20%">
                <img src="{{ public_path('images/LogoEmpresa.png') }}" class="pdf-logo">
            </td>
            <td width="80%" class="pdf-brand">
                <h1>Sistema de Gestion Documental</h1>
                <p>Escuela de Posgrado de la Armada Boliviana</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    {{-- TITULO --}}
    <div class="pdf-title">
        <h2>Reporte General de Correspondencia</h2>
        <p>Total registros: <strong>{{ $documentos->count() }}</strong></p>
    </div>

    {{-- TABLA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th>Cite</th>
                <th>Asunto</th>
                <th>Tipo</th>
                <th>Remitente / CI</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentos as $doc)
                <tr>
                    <td><strong>{{ $doc->cite }}</strong></td>
                    <td><span class="muted">{{ $doc->asunto }}</span></td>
                    <td><span class="tag">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</span></td>
                    <td>
                        {{ $doc->remitente->nombre ?? 'N/A' }}<br>
                        <span class="muted">CI: {{ $doc->remitente->ci ?? 'S/R' }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
                    <td><span class="tag tag-navy">{{ $doc->estado->nombre ?? 'N/A' }}</span></td>
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
