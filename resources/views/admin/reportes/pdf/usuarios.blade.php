<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    @include('admin.reportes.pdf.partials.styles')
    <style>
        .filter-context {
            background: #fff;
            border: 1px solid #DDE3EC;
            border-left: 5px solid #D9A23D;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 9px;
            color: #1A2942;
        }

        .user-card {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: avoid;
            border: 1px solid #DDE3EC;
        }

        .user-card td {
            border: 1px solid #DDE3EC;
            vertical-align: top;
            padding: 7px;
        }

        .user-heading td {
            background: #E8EFF6;
            border-color: #C8D7E6;
        }

        .user-name {
            color: #0B2D59;
            font-size: 12px;
            font-weight: bold;
        }

        .section-label {
            color: #0B2D59;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 4px;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .activity-table th {
            background: #0B2D59;
            color: #fff;
            border: 1px solid #153f70;
            padding: 5px;
            font-size: 8px;
            text-align: left;
        }

        .activity-table td {
            border: 1px solid #DDE3EC;
            padding: 5px;
            font-size: 8px;
        }

        .empty-activity {
            border: 1px dashed #CBD5E1;
            background: #F8FAFC;
            color: #5E7491;
            padding: 8px;
            font-style: italic;
        }
    </style>
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
                <a href="{{ route('admin.reportes.usuarios', request()->query()) }}">Volver al reporte</a>
            </div>
        </div>
    @endif

    <table class="pdf-header">
        <tr>
            <td width="16%">
                @if(!empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" class="pdf-logo">
                @endif
            </td>
            <td width="84%" class="pdf-brand">
                <h1>Sistema de Gestion Documental</h1>
                <p>Reporte de Actividad y Control de Usuarios</p>
                <p>Generado: {{ now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    <div class="pdf-title">
        <h2>Auditoria de Usuarios</h2>
        <p>Cuentas del sistema, perfil institucional y documentos gestionados.</p>
    </div>

    @if(request()->hasAny(['nombre', 'correo', 'estado', 'cite', 'fecha_inicio', 'fecha_fin']))
        <div class="filter-context">
            <strong>Filtros aplicados:</strong>
            {{ request('nombre') ? 'Usuario: ' . request('nombre') . ' | ' : '' }}
            {{ request('correo') ? 'Correo: ' . request('correo') . ' | ' : '' }}
            {{ request('estado') !== null && request('estado') !== '' ? 'Estado: ' . (request('estado') == '1' ? 'Activo' : 'Inactivo') . ' | ' : '' }}
            {{ request('cite') ? 'Cite: ' . request('cite') . ' | ' : '' }}
            {{ request('fecha_inicio') ? 'Desde: ' . request('fecha_inicio') . ' | ' : '' }}
            {{ request('fecha_fin') ? 'Hasta: ' . request('fecha_fin') : '' }}
        </div>
    @endif

    <table class="pdf-summary">
        <tr>
            <td>
                <div class="summary-label">USUARIOS</div>
                <div class="summary-value">{{ $estadisticas['total_usuarios'] }}</div>
            </td>
            <td>
                <div class="summary-label">ACTIVOS</div>
                <div class="summary-value" style="color:#0D9E6E;">{{ $estadisticas['usuarios_activos'] }}</div>
            </td>
            <td>
                <div class="summary-label">INACTIVOS</div>
                <div class="summary-value" style="color:#DC2626;">{{ $estadisticas['usuarios_inactivos'] }}</div>
            </td>
            <td>
                <div class="summary-label">CON ACTIVIDAD</div>
                <div class="summary-value">{{ $estadisticas['usuarios_con_actividad'] }}</div>
            </td>
            <td>
                <div class="summary-label">DOCS. VISIBLES</div>
                <div class="summary-value">{{ $estadisticas['documentos_en_reporte'] }}</div>
            </td>
            <td>
                <div class="summary-label">PROMEDIO HIST.</div>
                <div class="summary-value">{{ $estadisticas['promedio_documentos_por_usuario'] }}</div>
            </td>
        </tr>
    </table>

    @forelse($usuarios as $usuario)
        @php
            $persona = $usuario->persona;
            $departamento = $persona?->departamento?->nombre ?? 'Sin departamento';
            $cargo = $persona?->cargos_nombres ?? 'Sin cargo';
            $rol = $usuario->rol?->nombre ?? ($usuario->idRol == 1 ? 'Administrador' : 'Usuario');
        @endphp

        <table class="user-card">
            <tr class="user-heading">
                <td colspan="3">
                    <span class="user-name">{{ $usuario->name }}</span>
                    <span class="tag tag-navy">{{ $rol }}</span>
                    <span class="tag {{ $usuario->activo ? 'tag-success' : 'tag-danger' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span>
                    <span class="tag">Visibles: {{ $usuario->documentos_en_reporte }} / Historicos: {{ $usuario->total_documentos }}</span>
                </td>
            </tr>
            <tr>
                <td width="28%">
                    <div class="section-label">Cuenta</div>
                    <strong>Correo:</strong><br>
                    <span class="muted">{{ $usuario->email }}</span><br><br>
                    <strong>Estado:</strong> {{ $usuario->activo ? 'Activo' : 'Inactivo' }}<br>
                    <strong>Rol:</strong> {{ $rol }}
                </td>
                <td width="30%">
                    <div class="section-label">Perfil institucional</div>
                    <strong>Persona vinculada:</strong><br>
                    <span class="muted">{{ $persona?->nombre ?? 'Sin persona vinculada' }}</span><br><br>
                    <strong>Departamento:</strong> {{ $departamento }}<br>
                    <strong>Cargo:</strong> {{ $cargo }}
                </td>
                <td width="42%">
                    <div class="section-label">Actividad documental reciente</div>
                    @if($usuario->ultimos_cambios->count() > 0)
                        <table class="activity-table">
                            <thead>
                                <tr>
                                    <th width="19%">Fecha</th>
                                    <th width="18%">Cite</th>
                                    <th width="33%">Asunto</th>
                                    <th width="15%">Tipo</th>
                                    <th width="15%">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuario->ultimos_cambios->take(5) as $cambio)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($cambio['fecha'])->format('d/m/Y H:i') }}</td>
                                        <td><span class="strong-navy">{{ $cambio['cite'] }}</span></td>
                                        <td>{{ \Illuminate\Support\Str::limit($cambio['asunto'], 58) }}</td>
                                        <td>{{ $cambio['tipo'] }}</td>
                                        <td>{{ $cambio['estado'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-activity">
                            Sin actividad documental visible con los filtros aplicados.
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    @empty
        <table class="data-table">
            <tr>
                <td class="text-center">No existen usuarios con registros para los filtros seleccionados.</td>
            </tr>
        </table>
    @endforelse

    <div class="pdf-footer">
        <strong>SISGED</strong> - Reporte generado automaticamente
    </div>
</body>
</html>
