<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Departamentos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; margin-bottom: 25px; border-bottom: 3px solid #0B2D59; padding-bottom: 15px; }
        .logo { width: 90px; }
        .empresa { text-align: center; }
        .empresa h1 { margin: 0; color: #0B2D59; font-size: 24px; }
        .empresa p { margin: 3px 0; font-size: 12px; color: #666; }
        .titulo { margin-top: 25px; margin-bottom: 20px; text-align: center; }
        .titulo h2 { margin: 0; color: #0B2D59; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background-color: #0B2D59; color: white; padding: 10px; border: 1px solid #ddd; font-size: 12px; text-align: center;}
        table td { border: 1px solid #ddd; padding: 8px; vertical-align: middle; text-align: center;}
        .text-left { text-align: left; }
        tbody tr:nth-child(even) { background-color: #f4f4f4; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
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

    <div class="titulo"><h2>Reporte de Flujo Institucional por Áreas</h2></div>

    <table>
        <thead>
            <tr>
                <th class="text-left" width="40%">Departamento</th>
                <th width="20%">Doc. Recibidos</th>
                <th width="20%">Doc. Enviados</th>
                <th width="20%">Total Movimiento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departamentos as $dep)
                <tr>
                    <td class="text-left"><strong>{{ $dep->nombre }}</strong></td>
                    <td>{{ $dep->recibidos }}</td>
                    <td>{{ $dep->enviados }}</td>
                    <td style="font-weight: bold; color: #0B2D59;">
                        {{ $dep->recibidos + $dep->enviados }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">SISGED — Reporte generado automáticamente</div>
</body>
</html>