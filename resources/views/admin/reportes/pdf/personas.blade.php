<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Integral de Personas</title>
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
        table th { background-color: #0B2D59; color: white; padding: 10px; border: 1px solid #ddd; font-size: 12px; text-align: left;}
        table td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        tbody tr:nth-child(even) { background-color: #f4f4f4; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; }
        
        /* Clases específicas para Personas */
        .doc-list { margin: 0; padding-left: 15px; }
        .doc-item { margin-bottom: 8px; border-bottom: 1px dashed #ccc; padding-bottom: 4px; font-size: 10px;}
        .doc-cite { color: #0B2D59; font-weight: bold; }
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
                <p>Reporte Oficial Integral de Personas y Documentos</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    {{-- TITULO --}}
    <div class="titulo">
        <h2>Historial de Remitentes</h2>
    </div>

    <div class="info">
        Total de perfiles registrados: <strong>{{ $personas->count() }}</strong>
    </div>

    {{-- TABLA DE DATOS --}}
    <table>
        <thead>
            <tr>
                <th width="25%">Persona</th>
                <th width="25%">Contacto / Institución</th>
                <th width="50%">Historial de Documentos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personas as $p)
                <tr>
                    {{-- PERSONA --}}
                    <td>
                        <strong style="font-size: 13px;">{{ $p->nombre }}</strong><br>
                        <span style="color:#555;">CI: {{ $p->ci ?? 'N/A' }}</span><br><br>
                        <strong>{{ $p->tipo }}</strong>
                    </td>
                    
                    {{-- CONTACTO --}}
                    <td>
                        <small>
                            Tel: {{ $p->telefono ?? '-' }}<br>
                            Email: {{ $p->correo ?? '-' }}
                        </small>
                        <br><br>
                        <strong>{{ $p->institucion ?? 'Independiente' }}</strong><br>
                        <small>{{ $p->cargo ?? '-' }}</small>
                    </td>
                    
                    {{-- DOCUMENTOS --}}
                    <td>
                        @if($p->documentos->count() > 0)
                            <ul class="doc-list">
                                @foreach($p->documentos as $doc)
                                    <li class="doc-item">
                                        <span class="doc-cite">{{ $doc->cite }}</span> ({{ $doc->tipoDocumento->nombre ?? 'Doc' }})<br>
                                        <span style="color: #666;">Asunto: {{ $doc->asunto }}</span><br>
                                        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }} | <strong>Est:</strong> {{ $doc->estado->nombre ?? 'N/A' }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <i style="color: #888; font-size: 11px;">Sin documentos en el periodo seleccionado.</i>
                        @endif
                    </td>
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