<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
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
        <h2>Reporte de Actividad de Usuarios</h2>
        <p>Registro detallado de actividad documental por usuario</p>
    </div>

    {{-- RESUMEN --}}
    @php
        $activos = $usuarios->filter(fn($u) => $u->activo)->count();
        $inactivos = $usuarios->filter(fn($u) => !$u->activo)->count();
        $totalDocs = $usuarios->sum('correspondencias_count');
    @endphp
    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">TOTAL USUARIOS</div>
                <div class="summary-value">{{ $usuarios->count() }}</div>
            </td>
            <td>
                <div class="summary-label">ACTIVOS</div>
                <div class="summary-value" style="color:#0D9E6E;">{{ $activos }}</div>
            </td>
            <td>
                <div class="summary-label">INACTIVOS</div>
                <div class="summary-value" style="color:#DC2626;">{{ $inactivos }}</div>
            </td>
            <td>
                <div class="summary-label">TOTAL DOCUMENTOS</div>
                <div class="summary-value">{{ $totalDocs }}</div>
            </td>
        </tr>
    </table>

    {{-- TABLA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo Electronico</th>
                <th class="text-center">Doc. Registrados</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td><strong>{{ $usuario->name }}</strong></td>
                    <td>{{ $usuario->email }}</td>
                    <td class="text-center">{{ $usuario->correspondencias_count }}</td>
                    <td class="text-center">
                        <span class="tag {{ $usuario->activo ? 'tag-success' : 'tag-danger' }}">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
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
