@extends('layouts.app')

@section('title', 'Bandeja de Recibidas')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-white mb-1">
                        <i class="bi bi-inbox-fill me-2"></i>
                        Bandeja de Documentos
                    </h2>
                    <p class="text-light mb-0 opacity-75">
                        Flujo: Pendiente → Recibido → Atendido → Archivado
                    </p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-4 px-4 py-3 text-center">
                    <div class="text-white small">TOTAL</div>
                    <div class="fs-3 fw-bold text-warning">{{ $documentos->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS POR ESTADO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('recibidas.index') }}"
                  class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-semibold text-muted mb-0">Filtrar por estado:</label>
                <select name="estado" class="form-select rounded-3" style="max-width:200px;"
                        onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach($estados as $est)
                        <option value="{{ $est->idEstado }}"
                                @selected(request('estado') == $est->idEstado)>
                            {{ $est->nombre }}
                        </option>
                    @endforeach
                </select>
                @if(request('estado'))
                    <a href="{{ route('recibidas.index') }}"
                       class="btn btn-outline-secondary rounded-3 btn-sm">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header border-0 py-3 px-4 text-white"
             style="background: linear-gradient(135deg,#0B2D59,#16477D);">
            <div class="fw-semibold">
                <i class="bi bi-folder2-open me-2"></i>
                Documentos en proceso
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#F4F7FB;">
                        <tr>
                            <th class="px-4 py-3">CITE</th>
                            <th class="py-3">DOCUMENTO</th>
                            <th class="py-3">REMITENTE</th>
                            <th class="py-3 text-center">ESTADO</th>
                            <th class="py-3 text-center">FECHA</th>
                            <th class="py-3 text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        @php
                            $estadoNombre = strtolower($doc->estado->nombre ?? '');
                            $esPendiente  = $estadoNombre === 'pendiente';
                            $esRecibido   = $estadoNombre === 'recibido';
                            $esAtendido   = $estadoNombre === 'atendido';
                            $esArchivado  = $estadoNombre === 'archivado';

                            $colorEstado = match($estadoNombre) {
                                'pendiente' => 'bg-warning text-dark',
                                'recibido'  => 'bg-primary',
                                'atendido'  => 'bg-success',
                                'archivado' => 'bg-secondary',
                                default     => 'bg-dark',
                            };
                        @endphp
                        <tr>
                            {{-- CITE --}}
                            <td class="px-4">
                                <div class="fw-bold text-primary">{{ $doc->cite }}</div>
                            </td>

                            {{-- DOCUMENTO --}}
                            <td style="min-width:260px;">
                                <div class="fw-semibold text-dark mb-1">
                                    {{ \Str::limit($doc->asunto, 60) }}
                                </div>
                                <small class="text-muted">
                                    {{ $doc->tipoDocumento->nombre ?? 'N/A' }}
                                </small>
                            </td>

                            {{-- REMITENTE --}}
                            <td>
                                <div class="small">{{ $doc->remitente->nombre ?? 'N/A' }}</div>
                            </td>

                            {{-- ESTADO --}}
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-2 {{ $colorEstado }}">
                                    {{ $doc->estado->nombre ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- FECHA --}}
                            <td class="text-center">
                                <div class="small fw-semibold">
                                    {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}
                                </div>
                                <div class="small text-muted">
                                    {{ \Carbon\Carbon::parse($doc->fecha)->format('H:i') }}
                                </div>
                            </td>

                            {{-- ACCIONES --}}
                            <td class="px-3">
                                <div class="d-flex justify-content-center flex-wrap gap-1">

                                    {{-- VER --}}
                                    <a href="{{ route('correspondencia.show', $doc->idDocumento) }}?volver=correspondencia"
                                       class="btn btn-sm btn-doc btn-doc-view"
                                       title="Ver detalle">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    {{-- RECIBIR — solo si está Pendiente --}}
                                    @if($esPendiente)
                                        <form action="{{ route('recibidas.recibir', $doc->idDocumento) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-primary rounded-3"
                                                    onclick="return confirm('¿Marcar como Recibido?')"
                                                    title="Recibir documento">
                                                <i class="bi bi-box-arrow-in-down me-1"></i>
                                                Recibir
                                            </button>
                                        </form>
                                    @endif

                                    {{-- ATENDER — solo si está Recibido --}}
                                    @if($esRecibido)
                                        <form action="{{ route('recibidas.atender', $doc->idDocumento) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-success rounded-3"
                                                    onclick="return confirm('¿Marcar como Atendido?')"
                                                    title="Atender documento">
                                                <i class="bi bi-check2-circle me-1"></i>
                                                Atender
                                            </button>
                                        </form>
                                    @endif

                                    {{-- ARCHIVAR — solo si está Atendido --}}
                                    @if($esAtendido)
                                        <form action="{{ route('recibidas.archivar', $doc->idDocumento) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-secondary rounded-3"
                                                    onclick="return confirm('¿Archivar este documento? Esta acción no se puede deshacer.')"
                                                    title="Archivar documento">
                                                <i class="bi bi-archive-fill me-1"></i>
                                                Archivar
                                            </button>
                                        </form>
                                    @endif

                                    {{-- ARCHIVADO — indicador visual --}}
                                    @if($esArchivado)
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            Archivado
                                        </span>
                                    @endif

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <div class="fw-semibold fs-5">No hay documentos en bandeja</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center p-3">
                {{ $documentos->links() }}
            </div>
        </div>
    </div>

</div>

@endsection
