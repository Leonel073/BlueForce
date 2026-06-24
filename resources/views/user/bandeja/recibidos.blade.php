@extends('layouts.app')

@section('title', 'Bandeja - Recibidos')

@section('content')

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 fw-bold text-dark">Documentos Recibidos</h2>
                <a href="{{ route('envios.bandeja') }}" class="btn btn-sm btn-secondary">← Volver</a>
            </div>
        </div>
    </div>

    {{-- TABLA DE DOCUMENTOS RECIBIDOS --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Correspondencia en estado Recibido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Asunto</th>
                                    <th>Fecha Recepción</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentos ?? [] as $doc)
                                    <tr>
                                        <td><code>#{{ $doc->idCorrespondencia }}</code></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $doc->tipoDocumento->nombre ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $doc->asunto ?? 'Sin asunto' }}</td>
                                        <td>{{ $doc->fecha ? $doc->fecha->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>{{ $doc->usuario->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('documentos.detalle', $doc->idCorrespondencia) }}" 
                                                   class="btn btn-outline-primary" title="Ver detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-outline-warning" data-bs-toggle="modal" 
                                                        data-bs-target="#modalAtender{{ $doc->idCorrespondencia }}" 
                                                        title="Marcar como atendido">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- MODAL ATENDER --}}
                                    <div class="modal fade" id="modalAtender{{ $doc->idCorrespondencia }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Marcar como Atendido</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('recibidas.atender', $doc->idCorrespondencia) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Observación</label>
                                                            <textarea class="form-control" name="observacion" rows="3" placeholder="Describe la acción realizada..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-warning">Confirmar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No hay documentos recibidos</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
