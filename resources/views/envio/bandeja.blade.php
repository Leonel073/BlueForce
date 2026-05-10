@extends('layouts.app')

@section('title', 'Mi Bandeja')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h2 class="fw-bold text-white mb-1">

                        <i class="bi bi-inboxes-fill"></i>

                        Mi Bandeja Documental

                    </h2>

                    <p class="text-light mb-0">

                        Gestión y seguimiento institucional de documentos

                    </p>

                </div>

                <div class="mt-3 mt-md-0">

                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">

                        {{ $documentos->count() }} documentos

                    </span>

                </div>

            </div>

        </div>

    </div>

    {{-- ALERTAS --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm">

            <i class="bi bi-exclamation-triangle-fill"></i>

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4 d-flex justify-content-between align-items-center"
             style="background-color:#0B2D59;">

            <div>

                <i class="bi bi-folder2-open"></i>

                Documentos en Flujo

            </div>

            <small class="text-light">

                Sistema institucional documental

            </small>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>CITE</th>

                            <th>ASUNTO</th>

                            <th>REMITENTE</th>

                            <th>DESTINO ACTUAL</th>

                            <th>URGENCIA</th>

                            <th>ESTADO</th>

                            <th>FECHA</th>

                            <th class="text-center">ACCIONES</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($documentos as $doc)

                            @php

                                $ultimaDerivacion =
                                    $doc->derivaciones
                                        ->sortByDesc('orden')
                                        ->first();

                            @endphp

                            <tr>

                                {{-- CITE --}}
                                <td>

                                    <span class="fw-bold text-primary">

                                        {{ $doc->cite }}

                                    </span>

                                </td>

                                {{-- ASUNTO --}}
                                <td style="min-width:260px;">

                                    <div class="fw-semibold">

                                        {{ $doc->asunto }}

                                    </div>

                                </td>

                                {{-- REMITENTE --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $doc->remitente->nombre ?? 'N/A' }}

                                    </div>

                                </td>

                                {{-- DESTINO --}}
                                <td>

                                    @if($ultimaDerivacion)

                                        <span class="badge rounded-pill bg-info text-dark px-3 py-2">

                                            <i class="bi bi-building"></i>

                                            {{ $ultimaDerivacion->departamentoDestino->nombre ?? 'N/A' }}

                                        </span>

                                    @else

                                        <span class="badge rounded-pill bg-secondary px-3 py-2">

                                            Sin derivación

                                        </span>

                                    @endif

                                </td>

                                {{-- URGENCIA --}}
                                <td>

                                    @php

                                        $urgencia =
                                            strtolower(
                                                $doc->urgencia->nombre ?? ''
                                            );

                                    @endphp

                                    @if(str_contains($urgencia, 'alta'))

                                        <span class="badge bg-danger rounded-pill px-3 py-2">

                                            <i class="bi bi-exclamation-triangle-fill"></i>

                                            {{ $doc->urgencia->nombre }}

                                        </span>

                                    @elseif(str_contains($urgencia, 'media'))

                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">

                                            <i class="bi bi-exclamation-circle-fill"></i>

                                            {{ $doc->urgencia->nombre }}

                                        </span>

                                    @else

                                        <span class="badge bg-success rounded-pill px-3 py-2">

                                            <i class="bi bi-check-circle-fill"></i>

                                            {{ $doc->urgencia->nombre ?? 'Normal' }}

                                        </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td>

                                    <span class="badge bg-dark rounded-pill px-3 py-2">

                                        {{ $doc->estado->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                {{-- FECHA --}}
                                <td>

                                    <small class="text-muted fw-semibold">

                                        {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y H:i') }}

                                    </small>

                                </td>

                                {{-- ACCIONES --}}
                                <td>

                                    <div class="d-flex flex-wrap gap-2 justify-content-center">

                                        {{-- VER --}}
                                        <a href="{{ route('admin.correspondencia.show', $doc->idDocumento) }}"
                                           class="btn btn-sm rounded-3 border-0 shadow-sm text-white"
                                           style="background-color:#2E608C;"
                                           title="Ver detalle">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                        {{-- DERIVAR --}}
                                        @if(($doc->estado->nombre ?? '') !== 'Finalizado')

                                            <a href="{{ route('envios.derivar.form', $doc->idDocumento) }}"
                                               class="btn btn-sm rounded-3 border-0 shadow-sm text-dark"
                                               style="background-color:#D9A23D;"
                                               title="Derivar documento">

                                                <i class="bi bi-arrow-left-right"></i>

                                            </a>

                                        @endif

                                        {{-- FINALIZAR --}}
                                        @if(($doc->estado->nombre ?? '') !== 'Finalizado')

                                            <button type="button"
                                                    class="btn btn-sm text-white rounded-3 border-0 shadow-sm"
                                                    style="
                                                        background: linear-gradient(135deg,#0B2D59,#16477D);
                                                    "
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalFinalizar{{ $doc->idDocumento }}"
                                                    title="Finalizar documento">

                                                <i class="bi bi-patch-check-fill"></i>

                                            </button>

                                        @else

                                            <span class="badge bg-success rounded-pill px-3 py-2">

                                                <i class="bi bi-check2-all"></i>

                                                Finalizado

                                            </span>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            {{-- MODAL FINALIZAR --}}
                            @if(($doc->estado->nombre ?? '') !== 'Finalizado')

                                <div class="modal fade"
                                     id="modalFinalizar{{ $doc->idDocumento }}"
                                     tabindex="-1"
                                     aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content border-0 rounded-4 shadow-lg">

                                            {{-- HEADER --}}
                                            <div class="modal-header text-white border-0"
                                                 style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

                                                <h5 class="modal-title fw-bold">

                                                    <i class="bi bi-check-circle-fill"></i>

                                                    Finalizar Documento

                                                </h5>

                                                <button type="button"
                                                        class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>

                                            </div>

                                            {{-- BODY --}}
                                            <div class="modal-body text-center py-4">

                                                <div class="mb-3">

                                                    <i class="bi bi-folder-check display-1 text-success"></i>

                                                </div>

                                                <h4 class="fw-bold">

                                                    ¿Desea finalizar este documento?

                                                </h4>

                                                <p class="text-muted mt-3 mb-0">

                                                    El documento será marcado como
                                                    concluido dentro del flujo documental.

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
                                                            class="btn text-white rounded-3 px-4"
                                                            style="background-color:#0B2D59;">

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

                                <td colspan="8"
                                    class="text-center py-5 text-muted">

                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>

                                    <div class="fw-semibold">

                                        No existen documentos en bandeja

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection