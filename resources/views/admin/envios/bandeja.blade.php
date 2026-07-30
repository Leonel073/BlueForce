@extends('layouts.app')

@section('title', 'Mi Bandeja')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>

                    <h2 class="fw-bold text-white mb-2">

                        <i class="bi bi-inboxes-fill me-2"></i>

                        Mi Bandeja Documental

                    </h2>

                    <p class="text-light mb-0 opacity-75">

                        Gestión y seguimiento institucional de documentos

                    </p>

                </div>

                <div>

                    <div class="bg-white bg-opacity-10 rounded-4 px-4 py-3 text-center">

                        <div class="text-white small">

                            TOTAL DOCUMENTOS

                        </div>

                        <div class="fs-3 fw-bold text-warning">

                            {{ $documentos->total() }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ALERTAS --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.bandeja') }}"
                  class="row g-3 align-items-end">

                <div class="col-lg-4 col-md-6">

                    <label class="form-label small text-muted mb-1">Buscar</label>

                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           class="form-control rounded-3"
                           placeholder="Cite o asunto">

                </div>

                <div class="col-lg-3 col-md-6">

                    <label class="form-label small text-muted mb-1">Departamento (derivación)</label>

                    <select name="idDepartamentoDestino"
                            class="form-select rounded-3">

                        <option value="">Todos</option>

                        @foreach($departamentos as $dep)

                            <option value="{{ $dep->idDepartamento }}"
                                @selected(request('idDepartamentoDestino') == $dep->idDepartamento)>

                                {{ $dep->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-2 col-md-6">

                    <label class="form-label small text-muted mb-1">Estado</label>

                    <select name="idEstado"
                            class="form-select rounded-3">

                        <option value="">Todos</option>

                        @foreach($estados as $est)

                            <option value="{{ $est->idEstado }}"
                                @selected(request('idEstado') == $est->idEstado)>

                                {{ $est->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-2 col-md-6">

                    <label class="form-label small text-muted mb-1">Urgencia</label>

                    <select name="idUrgencia"
                            class="form-select rounded-3">

                        <option value="">Todas</option>

                        @foreach($urgencias as $urg)

                            <option value="{{ $urg->idUrgencia }}"
                                @selected(request('idUrgencia') == $urg->idUrgencia)>

                                {{ $urg->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label small text-muted mb-1">Vista</label>
                    <select name="solo_mis" class="form-select rounded-3">
                        <option value="">Global</option>
                        <option value="1" @selected(request('solo_mis') === '1')>Asignados a mi</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-6 d-flex gap-2">

                    <button type="submit"
                            class="btn text-white rounded-3 flex-grow-1"
                            style="background-color:#0B2D59;">

                        <i class="bi bi-funnel-fill"></i>

                    </button>

                    <a href="{{ route('admin.bandeja') }}"
                       class="btn btn-outline-secondary rounded-3"
                       title="Limpiar">

                        <i class="bi bi-x-lg"></i>

                    </a>

                </div>

            </form>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

        {{-- HEADER TABLA --}}
        <div class="card-header border-0 py-3 px-4 text-white"
             style="background: linear-gradient(135deg,#0B2D59,#16477D);">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div class="fw-semibold">

                    <i class="bi bi-folder2-open me-2"></i>

                    Documentos en Flujo

                </div>

                <small class="opacity-75">

                    Sistema institucional documental

                </small>

            </div>

        </div>

        {{-- BODY --}}
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead style="background-color:#F4F7FB;">

                        <tr>

                            <th class="px-4 py-3">CITE</th>

                            <th class="py-3">DOCUMENTO</th>

                            <th class="py-3">DESTINO</th>

                            <th class="py-3 text-center">URGENCIA</th>

                            <th class="py-3 text-center">ESTADO</th>

                            <th class="py-3 text-center">FECHA</th>

                            <th class="py-3 text-center">ACCIONES</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($documentos as $doc)

                           @php

    $ultimaDerivacion =
        $doc->derivaciones
            ->sortByDesc('orden')
            ->first();

    $urgencia =
        strtolower(
            $doc->urgencia->nombre ?? ''
        );

    $estadoDocumento =
        strtoupper(
            $doc->estado->nombre ?? ''
        );

    // Documento bloqueado si está finalizado, archivado o cerrado
    $bloqueadoPorEstado =
        in_array(
            $estadoDocumento,
            [
                'FINALIZADO',
                'ARCHIVADO',
                'CERRADO'
            ]
        );

    // Bloquear si documento está en estado final O si no es responsable actual
    // Admin siempre tiene acceso excepto si el documento está finalizado/archivado
    $esAdmin = Auth::user()->idRol == 1;
    $esResponsableActual = 
        $esAdmin || 
        ($ultimaDerivacion && $ultimaDerivacion->idUsuarioAsignado == Auth::id());

    // Bloquear botones si documento está finalizado/archivado O si no es responsable actual
    $bloqueado = $bloqueadoPorEstado || !$esResponsableActual;

@endphp

                            <tr>

                                {{-- CITE --}}
                                <td class="px-4">

                                    <div class="fw-bold text-primary">

                                        {{ $doc->cite }}

                                    </div>

                                </td>

                                {{-- DOCUMENTO --}}
                                <td style="min-width:320px;">

                                    <div class="fw-semibold text-dark mb-1">

                                        {{ $doc->asunto }}

                                    </div>

                                    <div class="small text-muted">

                                        <i class="bi bi-person-fill"></i>

                                        {{ $doc->remitente->nombre ?? 'N/A' }}

                                    </div>

                                </td>

                                {{-- DESTINO --}}
                                <td>

                                    @if($ultimaDerivacion)

                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background-color:#D9ECFF; color:#0B2D59;">

                                            <i class="bi bi-building me-1"></i>

                                            {{ $ultimaDerivacion->departamentoDestino->nombre ?? 'N/A' }}

                                        </span>

                                    @else

                                        <span class="badge bg-secondary rounded-pill px-3 py-2">

                                            Sin derivación

                                        </span>

                                    @endif

                                </td>

                                {{-- URGENCIA --}}
                                <td class="text-center">

                                    @if(str_contains($urgencia, 'urg') || str_contains($urgencia, 'crit'))

                                        <span class="badge bg-danger rounded-pill px-3 py-2">

                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>

                                            {{ $doc->urgencia->nombre }}

                                        </span>

                                    @elseif(str_contains($urgencia, 'alta'))

                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background:#ea580c;color:#fff;">

                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre }}

                                        </span>

                                    @elseif(str_contains($urgencia, 'media') || str_contains($urgencia, 'moder'))

                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">

                                            <i class="bi bi-exclamation-circle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre }}

                                        </span>

                                    @else

                                        <span class="badge bg-success rounded-pill px-3 py-2">

                                            <i class="bi bi-check-circle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'Normal' }}

                                        </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td class="text-center">

                                    <span class="badge bg-dark rounded-pill px-3 py-2">

                                        {{ $doc->estado->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                {{-- FECHA --}}
                                <td class="text-center">

                                    <div class="small fw-semibold text-muted">

                                        {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}

                                    </div>

                                    <div class="small text-muted">

                                        {{ \Carbon\Carbon::parse($doc->fecha)->format('H:i') }}

                                    </div>

                                </td>

                                {{-- ACCIONES --}}
                                <td>

                                    <div class="d-flex justify-content-center flex-wrap gap-2">

                                        {{-- VER --}}
                                        <a href="{{ route('admin.correspondencia.show', $doc->idDocumento) }}?volver=bandeja"
                                           class="btn btn-sm btn-doc btn-doc-view"
                                           title="Ver detalle">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                        {{-- DERIVAR --}}
                                        @if(!$bloqueado)

                                            <a href="{{ route('admin.bandeja.derivar.form', $doc->idDocumento) }}?volver=bandeja"
                                               class="btn btn-sm btn-doc btn-doc-derive"
                                               title="Derivar documento">

                                                <i class="bi bi-arrow-left-right"></i>

                                            </a>

                                        @else

                                            <button type="button"
                                                    class="btn btn-sm btn-doc btn-doc-archive"
                                                    disabled
                                                    title="Documento archivado o finalizado">

                                                <i class="bi bi-lock-fill"></i>

                                            </button>

                                        @endif

                                        {{-- FINALIZAR --}}
                                        @if(!$bloqueado)

                                            <button type="button"
                                                    class="btn btn-sm btn-doc btn-doc-finalize"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalFinalizar{{ $doc->idDocumento }}"
                                                    title="Finalizar documento">

                                                <i class="bi bi-patch-check-fill"></i>

                                            </button>

                                        @else

                                        <span class="badge rounded-pill px-3 py-2
                                                    {{
                                                        $estadoDocumento == 'ARCHIVADO'
                                                            ? 'bg-secondary'
                                                            : 'bg-success'
                                                    }}">

                                                    <i class="bi bi-lock-fill me-1"></i>

                                                    {{ $doc->estado->nombre }}

                                                </span>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                            {{-- MODAL --}}
                            @if(($doc->estado->nombre ?? '') !== 'Finalizado')

                                <div class="modal fade"
                                     id="modalFinalizar{{ $doc->idDocumento }}"
                                     tabindex="-1"
                                     aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                                            {{-- HEADER --}}
                                            <div class="modal-header border-0 text-white"
                                                 style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

                                                <h5 class="modal-title fw-bold">

                                                    <i class="bi bi-check-circle-fill me-2"></i>

                                                    Finalizar Documento

                                                </h5>

                                                <button type="button"
                                                        class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>

                                            </div>

                                            {{-- BODY --}}
                                            <div class="modal-body text-center py-5">

                                                <i class="bi bi-folder-check text-success"
                                                   style="font-size:70px;"></i>

                                                <h4 class="fw-bold mt-3">

                                                    ¿Desea finalizar este documento?

                                                </h4>

                                                <p class="text-muted mt-3 mb-0">

                                                    El documento será marcado como concluido
                                                    dentro del flujo documental.

                                                </p>

                                            </div>

                                            {{-- FOOTER --}}
                                            <div class="modal-footer border-0 justify-content-center pb-4">

                                                <button type="button"
                                                        class="btn btn-light rounded-3 px-4"
                                                        data-bs-dismiss="modal">

                                                    Cancelar

                                                </button>

                                                <form action="{{ route('envios.finalizar', $doc->idDocumento) }}"
                                                      method="POST">

                                                    @csrf
                                                    @method('PUT')

                                                    <button type="submit"
                                                            class="btn btn-doc btn-doc-finalize btn-doc-lg rounded-3">

                                                        <i class="bi bi-check-circle-fill"></i>

                                                        Finalizar

                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endif

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>

                                        <div class="fw-semibold fs-5">

                                            No existen documentos en bandeja

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-center mt-3">

                {{ $documentos->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
