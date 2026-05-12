@extends('layouts.app')

@section('title', 'Derivar Documento')

@section('content')

<div class="container py-4">

    {{-- ENCABEZADO --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white mb-1">

                <i class="bi bi-arrow-left-right"></i>

                Derivar Documento

            </h1>

            <p class="text-light mb-0">

                Flujo documental entre departamentos

            </p>

        </div>

    </div>

    {{-- INFORMACIÓN DOCUMENTO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-3">

                    <small class="text-muted">
                        Cite
                    </small>

                    <h5 class="fw-bold">
                        {{ $documento->cite }}
                    </h5>

                </div>

                <div class="col-md-6">

                    <small class="text-muted">
                        Asunto
                    </small>

                    <h5 class="fw-bold">
                        {{ $documento->asunto }}
                    </h5>

                </div>

                <div class="col-md-3">

                    <small class="text-muted">
                        Estado
                    </small>

                    <div>
                        <span class="badge bg-primary">
                            {{ $documento->estado->nombre ?? 'Sin estado' }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- FORMULARIO --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Nueva Derivación

        </div>

        <div class="card-body">

            <form action="{{ route('envios.derivar', $documento->idDocumento) }}"
                  method="POST">

                @csrf

                {{-- DEPARTAMENTO DESTINO --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Departamento Destino

                    </label>
                    @php

    $ultimaDerivacion =
        $documento->derivaciones
            ->sortByDesc('orden')
            ->first();

    $departamentoActual =
        $ultimaDerivacion?->idDepartamentoDestino;

@endphp

                    <select name="idDepartamentoDestino"
                            class="form-select rounded-3"
                            required>

                        <option value="">

                            Seleccionar Departamento

                        </option>

                    @foreach($departamentos as $dep)

                        <option value="{{ $dep->idDepartamento }}"
                            {{ $departamentoActual == $dep->idDepartamento ? 'disabled' : '' }}>

                            {{ $dep->nombre }}

                            @if($departamentoActual == $dep->idDepartamento)
                                (Departamento Actual)
                            @endif

                        </option>

                    @endforeach

                    </select>

                </div>

                {{-- INSTRUCCIÓN --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Instrucción / Observación

                    </label>

                    <textarea name="instruccion"
                              rows="5"
                              class="form-control rounded-3"
                              placeholder="Escriba instrucciones para el departamento destino..."></textarea>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3 flex-wrap">

                    <a href="{{ route('envios.bandeja') }}"
                       class="btn btn-secondary rounded-3">

                        <i class="bi bi-arrow-left-circle-fill"></i>

                        Volver

                    </a>

                    <button type="submit"
                            class="btn text-white rounded-3"
                            style="background-color:#0B2D59;">

                        <i class="bi bi-send-fill"></i>

                        Derivar Documento

                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- HISTORIAL --}}
    <div class="card border-0 shadow-sm rounded-4 mt-4">

        <div class="card-header rounded-top-4"
             style="background-color:#D9A23D;">

            <h5 class="mb-0 text-dark fw-bold">

                Historial de Derivaciones

            </h5>

        </div>

        <div class="card-body">

            @forelse($documento->derivaciones->sortByDesc('orden') as $derivacion)

                <div class="border rounded-4 p-4 mb-3 bg-light">

                    <div class="row align-items-center">

                        {{-- ORIGEN --}}
                        <div class="col-md-4">

                            <small class="text-muted">

                                Departamento Origen

                            </small>

                            <div class="fw-bold">

                                {{ $derivacion->departamentoOrigen->nombre ?? 'N/A' }}

                            </div>

                        </div>

                        {{-- DESTINO --}}
                        <div class="col-md-4">

                            <small class="text-muted">

                                Departamento Destino

                            </small>

                            <div class="fw-bold text-primary">

                                {{ $derivacion->departamentoDestino->nombre ?? 'N/A' }}

                            </div>

                        </div>

                        {{-- FECHA --}}
                        <div class="col-md-4">

                            <small class="text-muted">

                                Fecha Envío

                            </small>

                            <div class="fw-bold">

                                {{ \Carbon\Carbon::parse($derivacion->fechaEnvio)->format('d/m/Y H:i') }}

                            </div>

                        </div>

                    </div>

                    {{-- INSTRUCCIÓN --}}
                    @if($derivacion->instruccion)

                        <hr>

                        <small class="text-muted">

                            Instrucción

                        </small>

                        <div class="mt-1">

                            {{ $derivacion->instruccion }}

                        </div>

                    @endif

                </div>

            @empty

                <div class="alert alert-warning mb-0">

                    No existen derivaciones registradas.

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection