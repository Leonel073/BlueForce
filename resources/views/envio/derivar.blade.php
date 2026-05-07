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

                Gestión de flujo documental institucional

            </p>

        </div>

    </div>

    {{-- INFORMACIÓN DOCUMENTO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <small class="text-muted">

                        Cite

                    </small>

                    <h5 class="fw-bold">

                        {{ $documento->cite }}

                    </h5>

                </div>

                <div class="col-md-8">

                    <small class="text-muted">

                        Asunto

                    </small>

                    <h5 class="fw-bold">

                        {{ $documento->asunto }}

                    </h5>

                </div>

            </div>

        </div>

    </div>

    {{-- FORMULARIO --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-body">

            <form action="{{ route('envios.derivar', $documento->idDocumento) }}"
                  method="POST">

                @csrf

                {{-- DEPARTAMENTO --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Departamento Destino

                    </label>

                    <select name="idDepartamentoDestino"
                            class="form-select rounded-3"
                            required>

                        <option value="">

                            Seleccionar Departamento

                        </option>

                        @foreach($departamentos as $dep)

                            <option value="{{ $dep->idDepartamento }}">

                                {{ $dep->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- USUARIO --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Usuario Asignado

                    </label>

                    <select name="idUsuarioAsignado"
                            class="form-select rounded-3">

                        <option value="">

                            Sin asignar

                        </option>

                        @foreach($usuarios as $user)

                            <option value="{{ $user->id }}">

                                {{ $user->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- INSTRUCCIÓN --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Instrucción

                    </label>

                    <textarea name="instruccion"
                              rows="5"
                              class="form-control rounded-3"
                              placeholder="Escriba instrucciones para el destinatario..."></textarea>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3">

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

        <div class="card-header text-white rounded-top-4"
             style="background-color:#D9A23D; color:#0B2D59;">

            <h5 class="mb-0">

                Historial de Derivaciones

            </h5>

        </div>

        <div class="card-body">

            @forelse($documento->derivaciones as $derivacion)

                <div class="border rounded-4 p-3 mb-3">

                    <div class="row">

                        <div class="col-md-4">

                            <small class="text-muted">

                                Origen

                            </small>

                            <div class="fw-bold">

                                {{ $derivacion->departamentoOrigen->nombre ?? 'N/A' }}

                            </div>

                        </div>

                        <div class="col-md-4">

                            <small class="text-muted">

                                Destino

                            </small>

                            <div class="fw-bold">

                                {{ $derivacion->departamentoDestino->nombre ?? 'N/A' }}

                            </div>

                        </div>

                        <div class="col-md-4">

                            <small class="text-muted">

                                Usuario

                            </small>

                            <div class="fw-bold">

                                {{ $derivacion->usuarioAsignado->name ?? 'Sin asignar' }}

                            </div>

                        </div>

                    </div>

                    {{-- INSTRUCCIÓN --}}
                    @if($derivacion->instruccion)

                        <div class="mt-3">

                            <small class="text-muted">

                                Instrucción

                            </small>

                            <div>

                                {{ $derivacion->instruccion }}

                            </div>

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