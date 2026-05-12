@extends('layouts.app')

@section('title', 'Editar Documento')

@section('content')

@php

    $documentoBloqueado =
        strtoupper($documento->estado->nombre ?? '') == 'ARCHIVADO'
        || strtoupper($documento->estado->nombre ?? '') == 'FINALIZADO';

@endphp

<div class="container py-4">

    {{-- ALERTA DOCUMENTO ARCHIVADO --}}
    @if($documentoBloqueado)

        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">

            <div class="d-flex align-items-start">

                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>

                <div>

                    <h5 class="fw-bold mb-1">

                        Documento archivado o finalizado

                    </h5>

                    <p class="mb-0">

                        Este documento se encuentra archivado o finalizado.
                        Para modificar su contenido primero debe cambiar
                        su estado documental.

                    </p>

                </div>

            </div>

        </div>

    @endif

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-pencil-square"></i>

                Editar Documento

            </h1>

            <p class="text-light mb-0">

                Corrección administrativa documental

            </p>

        </div>

    </div>

    {{-- ERRORES --}}
    @if($errors->any())

        <div class="alert alert-warning rounded-4">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- FORM --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Información del Documento

        </div>

        <div class="card-body">

            <form action="{{ route('admin.documentos.update', $documento->idDocumento) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- CITE --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Cite

                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $documento->cite }}"
                           disabled>

                </div>

                {{-- ASUNTO --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Asunto

                    </label>

                    <textarea name="asunto"
                              rows="4"
                              class="form-control"
                              required
                              {{ $documentoBloqueado ? 'disabled' : '' }}>{{ old('asunto', $documento->asunto) }}</textarea>

                </div>

                {{-- BUSCADOR REMITENTE --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">

                    <div class="card-header text-white rounded-top-4"
                         style="background-color:#0B2D59;">

                        Buscar Remitente

                    </div>

                    <div class="card-body">

                        <label class="form-label fw-semibold">

                            Buscar por nombre o carnet

                        </label>

                        <input type="text"
                               id="buscar_remitente"
                               class="form-control"
                               placeholder="Ej: Juan o 1234567"
                               autocomplete="off"
                               {{ $documentoBloqueado ? 'disabled' : '' }}>

                        {{-- RESULTADOS --}}
                        <div id="resultadoBusqueda"
                             class="list-group mt-2"
                             style="max-height:250px; overflow-y:auto;">

                        </div>

                    </div>

                </div>

                <div class="row">

                    {{-- REMITENTE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Remitente

                        </label>

                        <select name="idRemitente"
                                class="form-select"
                                required
                                {{ $documentoBloqueado ? 'disabled' : '' }}>

                            @foreach($personas as $persona)

                                <option value="{{ $persona->idPersona }}"
                                    {{ $documento->idRemitente == $persona->idPersona ? 'selected' : '' }}>

                                    {{ $persona->nombre }} - CI: {{ $persona->ci }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- TIPO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Tipo Documento

                        </label>

                        <select name="idTipoDocumento"
                                class="form-select"
                                required
                                {{ $documentoBloqueado ? 'disabled' : '' }}>

                            @foreach($tiposDocumento as $tipo)

                                <option value="{{ $tipo->idTipoDocumento }}"
                                    {{ $documento->idTipoDocumento == $tipo->idTipoDocumento ? 'selected' : '' }}>

                                    {{ $tipo->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="row">

                    {{-- URGENCIA --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Nivel Urgencia

                        </label>

                        <select name="idUrgencia"
                                class="form-select"
                                required
                                {{ $documentoBloqueado ? 'disabled' : '' }}>

                            @foreach($nivelesUrgencia as $urgencia)

                                <option value="{{ $urgencia->idUrgencia }}"
                                    {{ $documento->idUrgencia == $urgencia->idUrgencia ? 'selected' : '' }}>

                                    {{ $urgencia->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Estado

                        </label>

                        <select name="idEstado"
                                class="form-select"
                                required>

                            @foreach($estados as $estado)

                                <option value="{{ $estado->idEstado }}"
                                    {{ $documento->idEstado == $estado->idEstado ? 'selected' : '' }}>

                                    {{ $estado->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3 mt-4">

                    <a href="{{ route('admin.documentos.index') }}"
                       class="btn btn-secondary rounded-4 px-4">

                        Volver

                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-4 px-4">

                        {{
                            $documentoBloqueado
                                ? 'Cambiar Estado'
                                : 'Guardar Cambios'
                        }}

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@if(!$documentoBloqueado)

<script>

const inputBusqueda =
    document.getElementById('buscar_remitente');

const resultadoBusqueda =
    document.getElementById('resultadoBusqueda');

const selectRemitente =
    document.querySelector('select[name="idRemitente"]');

inputBusqueda.addEventListener('keyup', function () {

    let valor = this.value.trim();

    if(valor.length < 2)
    {
        resultadoBusqueda.innerHTML = '';
        return;
    }

    fetch(`/admin/personas/buscar?q=${valor}`)

    .then(response => response.json())

    .then(data => {

        resultadoBusqueda.innerHTML = '';

        if(data.length === 0)
        {
            resultadoBusqueda.innerHTML = `
                <div class="list-group-item">
                    Sin coincidencias
                </div>
            `;
            return;
        }

        data.forEach(persona => {

            let item = document.createElement('button');

            item.type = 'button';

            item.className =
                'list-group-item list-group-item-action';

            item.innerHTML = `
                <strong>${persona.nombre}</strong><br>
                <small>
                    CI: ${persona.ci}
                </small>
            `;

            item.addEventListener('click', function () {

                selectRemitente.value =
                    persona.idPersona;

                resultadoBusqueda.innerHTML = '';

                inputBusqueda.value =
                    persona.nombre;
            });

            resultadoBusqueda.appendChild(item);

        });

    });

});

</script>

@endif

@endsection