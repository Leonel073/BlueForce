<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>

        Reporte de Derivaciones

    </title>

    <style>

        body {

            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;

        }

        .header {

            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #0B2D59;
            padding-bottom: 15px;

        }

        .logo {

            width: 90px;

        }

        .empresa {

            text-align: center;

        }

        .empresa h1 {

            margin: 0;
            color: #0B2D59;
            font-size: 24px;

        }

        .empresa p {

            margin: 3px 0;
            font-size: 12px;
            color: #666;

        }

        .titulo {

            margin-top: 25px;
            margin-bottom: 20px;
            text-align: center;

        }

        .titulo h2 {

            margin: 0;
            color: #0B2D59;
            font-size: 20px;

        }

        .info {

            margin-bottom: 20px;
            font-size: 11px;
            color: #555;

        }

        table {

            width: 100%;
            border-collapse: collapse;

        }

        table th {

            background-color: #0B2D59;
            color: white;
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 12px;

        }

        table td {

            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;

        }

        tbody tr:nth-child(even) {

            background-color: #f4f4f4;

        }

        .footer {

            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;

            text-align: center;
            font-size: 10px;
            color: #777;

        }

        .badge {

            padding: 3px 6px;
            border-radius: 4px;
            color: white;
            font-size: 10px;

        }

        .origen {

            background-color: #2E608C;

        }

        .destino {

            background-color: #28a745;

        }

    </style>

</head>

<body>

    {{-- HEADER --}}
    <table class="header">

        <tr>

            <td width="20%">

                <img
                    src="{{ public_path('images/LogoEmpresa.png') }}"
                    class="logo">

            </td>

            <td width="80%" class="empresa">

                <h1>

                    Sistema de Gestión Documental

                </h1>

                <p>

                    Reporte Institucional de Derivaciones

                </p>

                <p>

                    Generado:
                    {{ now()->format('d/m/Y H:i') }}

                </p>

            </td>

        </tr>

    </table>

    {{-- TITULO --}}
    <div class="titulo">

        <h2>

            Reporte General de Derivaciones

        </h2>

    </div>

    {{-- INFO --}}
    <div class="info">

        Total registros:
        <strong>

            {{ $derivaciones->count() }}

        </strong>

    </div>

    {{-- TABLA --}}
    <table>

        <thead>

            <tr>

                <th>

                    Documento

                </th>

                <th>

                    Origen

                </th>

                <th>

                    Destino

                </th>

                <th>

                    Usuario

                </th>

                <th>

                    Fecha

                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($derivaciones as $d)

                <tr>

                    {{-- DOCUMENTO --}}
                    <td>

                        <strong>

                            {{ $d->documento->cite ?? 'N/A' }}

                        </strong>

                        <br>

                        <small>

                            {{ $d->documento->asunto ?? '' }}

                        </small>

                    </td>

                    {{-- ORIGEN --}}
                    <td>

                        <span>

                            {{ $d->departamentoOrigen->nombre ?? 'N/A' }}

                        </span>

                    </td>

                    {{-- DESTINO --}}
                    <td>

                        <span>

                            {{ $d->departamentoDestino->nombre ?? 'N/A' }}

                        </span>

                    </td>

                    {{-- USUARIO --}}
                    <td>

                        {{ $d->usuarioAsignado->name ?? 'Sin asignar' }}

                    </td>

                    {{-- FECHA --}}
                    <td>

                        {{ \Carbon\Carbon::parse($d->fechaEnvio)->format('d/m/Y H:i') }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5"
                        style="text-align:center;
                               padding:20px;">

                        No existen derivaciones registradas.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    {{-- FOOTER --}}
    <div class="footer">

        Sistema de Gestión Documental —
        Reporte generado automáticamente

    </div>

</body>

</html>