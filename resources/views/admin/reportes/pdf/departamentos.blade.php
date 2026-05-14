<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Flujo Institucional</title>

    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color:#2c2c2c;
            margin:0;
            padding:0;
        }

        .page{
            padding:30px;
        }

        /* =========================
           HEADER
        ==========================*/
        .header{
            width:100%;
            border-bottom:3px solid #0B2D59;
            padding-bottom:15px;
            margin-bottom:25px;
        }

        .header-table{
            width:100%;
        }

        .logo{
            width:90px;
        }

        .institution{
            text-align:center;
        }

        .institution h1{
            margin:0;
            font-size:24px;
            color:#0B2D59;
            font-weight:bold;
        }

        .institution p{
            margin:3px 0;
            color:#666;
            font-size:11px;
        }

        /* =========================
           TITLE
        ==========================*/
        .title{
            text-align:center;
            margin-bottom:25px;
        }

        .title h2{
            margin:0;
            color:#0B2D59;
            font-size:20px;
        }

        .subtitle{
            color:#777;
            font-size:11px;
            margin-top:5px;
        }

        /* =========================
           STATS
        ==========================*/
        .stats{
            width:100%;
            margin-bottom:25px;
        }

        .stat-box{
            width:23%;
            border:1px solid #ddd;
            border-radius:6px;
            padding:10px;
            text-align:center;
            background:#f8f9fa;
        }

        .stat-title{
            font-size:10px;
            color:#666;
            margin-bottom:5px;
        }

        .stat-value{
            font-size:20px;
            font-weight:bold;
            color:#0B2D59;
        }

        /* =========================
           TABLE
        ==========================*/
        table{
            width:100%;
            border-collapse:collapse;
        }

        thead th{
            background:#0B2D59;
            color:white;
            padding:10px;
            font-size:11px;
            text-align:center;
        }

        tbody td{
            border:1px solid #ddd;
            padding:9px;
            text-align:center;
        }

        tbody tr:nth-child(even){
            background:#f4f6f8;
        }

        .text-left{
            text-align:left;
        }

        .total{
            font-weight:bold;
            color:#0B2D59;
        }

        /* =========================
           FOOTER
        ==========================*/
        .footer{
            position:fixed;
            bottom:-10px;
            left:0;
            right:0;
            text-align:center;
            font-size:10px;
            color:#777;
            border-top:1px solid #ddd;
            padding-top:5px;
        }

        .footer strong{
            color:#0B2D59;
        }

    </style>
</head>

<body>

<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="20%">
                    <img src="{{ public_path('images/LogoEmpresa.png') }}" class="logo">
                </td>

                <td width="80%" class="institution">
                    <h1>Sistema de Gestión Documental</h1>
                    <p>Escuela de Posgrado de la Armada Boliviana</p>
                    <p>
                        Fecha de generación:
                        {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- TITLE --}}
    <div class="title">
        <h2>Reporte de Flujo Institucional por Áreas</h2>

        <div class="subtitle">
            Resumen consolidado de movimientos documentales
        </div>
    </div>

    {{-- ESTADISTICAS --}}
    <table class="stats">
        <tr>

            <td class="stat-box">
                <div class="stat-title">TOTAL ÁREAS</div>
                <div class="stat-value">
                    {{ $departamentos->count() }}
                </div>
            </td>

            <td width="2%"></td>

            <td class="stat-box">
                <div class="stat-title">DOC. RECIBIDOS</div>
                <div class="stat-value">
                    {{ $departamentos->sum('recibidos') }}
                </div>
            </td>

            <td width="2%"></td>

            <td class="stat-box">
                <div class="stat-title">DOC. ENVIADOS</div>
                <div class="stat-value">
                    {{ $departamentos->sum('enviados') }}
                </div>
            </td>

            <td width="2%"></td>

            <td class="stat-box">
                <div class="stat-title">MOVIMIENTO TOTAL</div>
                <div class="stat-value">
                    {{ $departamentos->sum(fn($d) => $d->recibidos + $d->enviados) }}
                </div>
            </td>

        </tr>
    </table>

    {{-- TABLA --}}
    <table>

        <thead>
            <tr>
                <th width="40%">Departamento</th>
                <th width="20%">Recibidos</th>
                <th width="20%">Enviados</th>
                <th width="20%">Movimiento Total</th>
            </tr>
        </thead>

        <tbody>

        @forelse($departamentos as $dep)

            <tr>

                <td class="text-left">
                    <strong>{{ $dep->nombre }}</strong>
                </td>

                <td>
                    {{ $dep->recibidos }}
                </td>

                <td>
                    {{ $dep->enviados }}
                </td>

                <td class="total">
                    {{ $dep->recibidos + $dep->enviados }}
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="4">
                    No existen registros disponibles
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

{{-- FOOTER --}}
<div class="footer">
    <strong>SISGED</strong>
    — Reporte institucional generado automáticamente
</div>

</body>
</html>