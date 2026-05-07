@extends('layouts.app')

@section('title', 'Correspondencia Enviada')

@section('content')

<div class="container-fluid py-4">

    {{-- ENCABEZADO --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <h1 class="fw-bold text-white mb-1">

                    <i class="bi bi-send-fill"></i>

                    Correspondencia Enviada

                </h1>

                <p class="text-light mb-0">

                    Gestión y control documental enviado

                </p>

            </div>

            <div>

                <span class="badge rounded-pill px-4 py-3"
                      style="background-color:#D9A23D;
                             color:#0B2D59;">

                    Total:
                    {{ $documentos->count() }}

                </span>

            </div>

        </div>

    </div>


    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead style="background-color:#0B2D59;">

                        <tr>

                            <th class="text-dark">

                                Cite

                            </th>

                            <th class="text-dark">

                                Asunto

                            </th>

                            <th class="text-dark">

                                Tipo

                            </th>

                            <th class="text-dark">

                                Fecha

                            </th>

                            <th class="text-dark">

                                Estado

                            </th>

                            <th class="text-dark">

                                Urgencia

                            </th>

                            <th class="text-dark">

                                Última Derivación

                            </th>

                            <th class="text-dark text-center">

                                Acciones

                            </th>

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

                                    <span class="fw-bold">

                                        {{ $doc->cite }}

                                    </span>

                                </td>

                                {{-- ASUNTO --}}
                                <td>

                                    {{ $doc->asunto }}

                                </td>

                                {{-- TIPO --}}
                                <td>

                                    {{ $doc->tipoDocumento->nombre ?? 'Sin tipo' }}

                                </td>

                                {{-- FECHA --}}
                                <td>

                                    {{ $doc->fecha }}

                                </td>

                                {{-- ESTADO --}}
                                <td>

                                    <span class="badge bg-success">

                                        {{ $doc->estado->nombre ?? 'Sin estado' }}

                                    </span>

                                </td>

                                {{-- URGENCIA --}}
                                <td>

                                    <span class="badge"
                                          style="background-color:#D9A23D;
                                                 color:#0B2D59;">

                                        {{ $doc->urgencia->nombre ?? 'Normal' }}

                                    </span>

                                </td>

                                {{-- DERIVACIÓN --}}
                                <td>

                                    @if($ultimaDerivacion)

                                        <span class="badge bg-primary">

                                            {{ $ultimaDerivacion->departamentoDestino->nombre ?? 'N/A' }}

                                        </span>

                                    @else

                                        <span class="text-muted">

                                            Sin derivación

                                        </span>

                                    @endif

                                </td>

                                {{-- ACCIONES --}}
                                <td class="text-center">

                                    <div class="d-flex gap-2 justify-content-center">

                                        {{-- VER --}}
                                        <a href="{{ route('admin.correspondencia.show', $doc->idDocumento) }}"
                                           class="btn btn-sm text-white"
                                           style="background-color:#0B2D59;">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                        {{-- DERIVAR --}}
                                        <a href="#"
                                           class="btn btn-sm btn-warning">

                                            <i class="bi bi-arrow-left-right"></i>

                                        </a>

                                        {{-- HISTORIAL --}}
                                        <a href="#"
                                           class="btn btn-sm btn-secondary">

                                            <i class="bi bi-clock-history"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center text-muted py-5">

                                    No existen documentos enviados.

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