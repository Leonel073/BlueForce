<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Flujo Institucional</title>
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
        <h2>Reporte de Flujo Institucional por Areas</h2>
        <p>Resumen consolidado de movimientos documentales</p>
    </div>

    {{-- RESUMEN --}}
    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">TOTAL AREAS</div>
                <div class="summary-value">{{ $departamentos->count() }}</div>
            </td>
            <td>
                <div class="summary-label">DOC. RECIBIDOS</div>
                <div class="summary-value">{{ $departamentos->sum('recibidos') }}</div>
            </td>
            <td>
                <div class="summary-label">DOC. ENVIADOS</div>
                <div class="summary-value">{{ $departamentos->sum('enviados') }}</div>
            </td>
            <td>
                <div class="summary-label">MOVIMIENTO TOTAL</div>
                <div class="summary-value">{{ $departamentos->sum(fn($d) => $d->recibidos + $d->enviados) }}</div>
            </td>
        </tr>
    </table>

    {{-- TABLA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="40%">Departamento</th>
                <th width="20%" class="text-center">Recibidos</th>
                <th width="20%" class="text-center">Enviados</th>
                <th width="20%" class="text-center">Movimiento Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departamentos as $dep)
                <tr>
                    <td class="text-left"><strong>{{ $dep->nombre }}</strong></td>
                    <td class="text-center">{{ $dep->recibidos }}</td>
                    <td class="text-center">{{ $dep->enviados }}</td>
                    <td class="text-center strong-navy">{{ $dep->recibidos + $dep->enviados }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No existen registros disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="pdf-footer">
        <strong>SISGED</strong> &mdash; Reporte institucional generado automaticamente
    </div>

</body>
</html>
