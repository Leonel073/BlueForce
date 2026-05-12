<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Documentos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; margin-bottom: 25px; border-bottom: 3px solid #0B2D59; padding-bottom: 15px; }
        .logo { width: 90px; }
        .empresa { text-align: center; }
        .empresa h1 { margin: 0; color: #0B2D59; font-size: 24px; }
        .empresa p { margin: 3px 0; font-size: 12px; color: #666; }
        .titulo { margin-top: 25px; margin-bottom: 20px; text-align: center; }
        .titulo h2 { margin: 0; color: #0B2D59; font-size: 20px; }
        .info { margin-bottom: 20px; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        table th { background-color: #0B2D59; color: white; padding: 10px; border: 1px solid #ddd; font-size: 12px; }
        table td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        tbody tr:nth-child(even) { background-color: #f4f4f4; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>

    {{-- HEADER UNIFICADO --}}
    <table class="header">
        <tr>
            <td width="20%" style="border: none; background: transparent;">
                <img src="{{ public_path('images/LogoEmpresa.png') }}" class="logo">
            </td>
            <td width="80%" class="empresa" style="border: none; background: transparent;">
                <h1>Sistema de Gestión Documental</h1>
                <p>Escuela de Posgrado de la Armada Boliviana</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    {{-- TITULO --}}
    <div class="titulo">
        <h2>Reporte General de Correspondencia</h2>
    </div>

    <div class="info">
        Total registros: <strong>{{ $documentos->count() }}</strong>
    </div>

    {{-- TABLA DE DATOS --}}
    <table>
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
                    <td><small>{{ $doc->asunto }}</small></td>
                    <td>{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</td>
                    <td>
                        {{ $doc->remitente->nombre ?? 'N/A' }}<br>
                        <small style="color:#555;">CI: {{ $doc->remitente->ci ?? 'S/R' }}</small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
                    <td><strong>{{ $doc->estado->nombre ?? 'N/A' }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Sistema de Gestión Documental — Reporte generado automáticamente
    </div>

</body>
</html>