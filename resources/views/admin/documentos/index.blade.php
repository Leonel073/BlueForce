@php
use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('title', 'Gestión Documental')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold text-white mb-0">
                        <i class="bi bi-folder2-open"></i>
                        Gestión Documental
                    </h1>
                    <p class="text-light mb-0">Centro de control administrativo</p>
                </div>
            </div>

        </div>

    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLA PRINCIPAL --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">
            <div class="d-flex justify-content-between align-items-center">
                <span>Documentos del Sistema</span>
                <span class="badge bg-light text-dark">{{ $documentos->total() }} total</span>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-3">Código</th>
                            <th>Referencia</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Remitente</th>
                            <th>Destino Actual</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Última Actualización</th>
                            <th class="text-center pe-3">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($documentos as $doc)

                            <tr>

                                <td class="ps-3">
                                    <span class="badge bg-secondary">{{ $doc->cite }}</span>
                                </td>

                                <td>
                                    <small class="text-muted">{{ $doc->idDocumento }}</small>
                                </td>

                                <td>
                                    <strong>{{ Str::limit($doc->asunto, 50) }}</strong>
                                </td>

                                <td>
                                    <small>
                                        @php
                                            $fecha = is_object($doc->fecha) ? $doc->fecha : \Carbon\Carbon::parse($doc->fecha);
                                        @endphp
                                        {{ $fecha->format('d/m/Y') }}
                                    </small>
                                </td>

                                <td>
                                    <small>{{ $doc->remitente->nombre ?? 'N/A' }}</small>
                                </td>

                                <td>
                                    @php
                                        $ultimaDer = $doc->ultimaDerivacion;
                                        $responsable = $ultimaDer?->departamentoDestino?->nombre ?? 'Sin asignar';
                                    @endphp
                                    <small>{{ $responsable }}</small>
                                </td>

                                <td>
                                    <span class="badge bg-primary">{{ $doc->estado->nombre ?? 'N/A' }}</span>
                                </td>

                                <td>
                                    @php
                                        $u = strtolower($doc->urgencia->nombre ?? '');
                                    @endphp

                                    @if(str_contains($u, 'urg') || str_contains($u, 'crit'))
                                        <span class="badge rounded-pill px-2 py-1 bg-danger">
                                            {{ $doc->urgencia->nombre ?? 'N/A' }}
                                        </span>
                                    @elseif(str_contains($u, 'alta'))
                                        <span class="badge rounded-pill px-2 py-1" style="background:#ea580c;color:#fff;">
                                            {{ $doc->urgencia->nombre ?? 'N/A' }}
                                        </span>
                                    @elseif(str_contains($u, 'media') || str_contains($u, 'moder'))
                                        <span class="badge rounded-pill px-2 py-1 bg-warning text-dark">
                                            {{ $doc->urgencia->nombre ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2 py-1 bg-success">
                                            {{ $doc->urgencia->nombre ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <small class="text-muted">
                                        @if($doc->updated_at)
                                            @php
                                                $dt = is_object($doc->updated_at) ? $doc->updated_at : \Carbon\Carbon::parse($doc->updated_at);
                                            @endphp
                                            {{ $dt->format('d/m/Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </small>
                                </td>

                                <td class="text-center pe-3">

                                    <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">

                                        {{-- VER --}}
                                        <a href="{{ route('admin.documentos.detalle', $doc->idDocumento) }}"
                                           class="btn btn-sm btn-doc btn-doc-view"
                                           title="Ver documento"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- DERIVACIONES --}}
                                        <button type="button"
                                           class="btn btn-sm btn-doc btn-doc-derive"
                                           title="Ver historial"
                                           data-bs-toggle="tooltip"
                                           onclick="loadDerivaciones({{ $doc->idDocumento }})">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.documentos.edit', $doc->idDocumento) }}"
                                           class="btn btn-sm btn-doc btn-doc-edit"
                                           title="Editar"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- PDF --}}
                                        @if($doc->tiene_archivo)
                                            <a href="{{ route('documentos.pdf.descargar', $doc->idDocumento) }}"
                                               class="btn btn-sm btn-doc"
                                               style="background: #c0392b; color: white;"
                                               title="PDF"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="text-center text-muted py-5">

                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No existen documentos en el sistema.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if($documentos->hasPages())
        <div class="card-footer bg-light rounded-bottom-4 border-top">
            <div class="d-flex justify-content-center">
                {{ $documentos->links() }}
            </div>
        </div>
        @endif

    </div>

</div>

{{-- MODAL PARA DERIVACIONES --}}
<div class="modal fade" id="derivacionesModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-gradient text-white rounded-top-4" style="background: linear-gradient(135deg, #0B2D59, #2E608C); border: none;">
        <h5 class="modal-title fw-bold" id="derivacionesModalTitle">Historial de Derivaciones</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="derivacionesModalBody">
        <div class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    function loadDerivaciones(docId) {
        const modal = new bootstrap.Modal(document.getElementById('derivacionesModal'));
        const body = document.getElementById('derivacionesModalBody');
        
        body.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
        
        // Aquí iría la llamada AJAX para cargar derivaciones
        setTimeout(() => {
            body.innerHTML = `<p class="text-muted text-center">Funcionalidad de derivaciones en desarrollo</p>`;
        }, 500);
        
        modal.show();
    }
</script>

@endsection
