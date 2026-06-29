<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Derivaciones</title>
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
                <p>Reporte Institucional de Derivaciones</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    {{-- TITULO --}}
    <div class="pdf-title">
        <h2>Reporte General de Derivaciones</h2>
        <p>Total registros: <strong>{{ $derivaciones->count() }}</strong></p>
    </div>

    {{-- TABLA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($derivaciones as $d)
                <tr>
                    <td>
                        <strong>{{ $d->documento->cite ?? 'N/A' }}</strong><br>
                        <span class="muted">{{ $d->documento->asunto ?? '' }}</span>
                    </td>
                    <td>
                        <span class="tag">{{ $d->departamentoOrigen->nombre ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="tag tag-success">{{ $d->departamentoDestino->nombre ?? 'N/A' }}</span>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($d->fechaEnvio)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding:20px;">
                        No existen derivaciones registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="pdf-footer">
        <strong>SISGED</strong> &mdash; Reporte generado automaticamente
    </div>

</body>
</html>
