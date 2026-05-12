@extends('layouts.app')

@section('title', 'Editar Departamento')

@section('content')

<div class="container py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-building-gear"></i>

                Editar Departamento

            </h1>

            <p class="text-light mb-0">

                Administración institucional

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

    {{-- FORMULARIO --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Información del Departamento

        </div>

        <div class="card-body">

            <form action="{{ route('admin.departamentos.update', $departamento->idDepartamento) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Nombre

                    </label>

                    <input type="text"
                           name="nombre"
                           class="form-control"
                           value="{{ old('nombre', $departamento->nombre) }}"
                           required>

                </div>

                

{{-- BUSCADOR PERSONA --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-header text-white rounded-top-4"
         style="background-color:#0B2D59;">

        Buscar Persona Encargada

    </div>

    <div class="card-body">

        <label class="form-label fw-semibold">

            Buscar por nombre o carnet

        </label>

        <input type="text"
               id="buscar_persona"
               class="form-control"
               placeholder="Ej: Carlos o 1234567"
               autocomplete="off">

        {{-- RESULTADOS --}}
        <div id="resultadoBusqueda"
             class="list-group mt-2"
             style="max-height:250px; overflow-y:auto;">

        </div>

    </div>

</div>

{{-- SELECT PERSONA --}}
<div class="mb-4">

    <label class="form-label fw-semibold">

        Persona Encargada

    </label>

    <select name="idPersonaEncargada"
            id="selectPersona"
            class="form-select">

        <option value="">

            -- Sin encargado --

        </option>

        @foreach($personas as $persona)

            <option value="{{ $persona->idPersona }}"
                {{ $departamento->idPersonaEncargada == $persona->idPersona ? 'selected' : '' }}>

                {{ $persona->nombre }} - {{ $persona->ci }}

            </option>

        @endforeach

    </select>

</div>

                {{-- ESTADO --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Estado

                    </label>

                    <select name="activo"
                            class="form-select"
                            required>

                        <option value="1"
                            {{ $departamento->activo ? 'selected' : '' }}>

                            Activo

                        </option>

                        <option value="0"
                            {{ !$departamento->activo ? 'selected' : '' }}>

                            Inactivo

                        </option>

                    </select>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3">

                    <a href="{{ route('admin.departamentos.index') }}"
                       class="btn btn-secondary rounded-4 px-4">

                        Volver

                    </a>

                    <button type="submit"
                            class="btn text-white rounded-4 px-4"
                            style="background-color:#0B2D59;">

                        Guardar Cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
<script>

const inputBusqueda =
    document.getElementById('buscar_persona');

const resultadoBusqueda =
    document.getElementById('resultadoBusqueda');

const selectPersona =
    document.getElementById('selectPersona');

inputBusqueda.addEventListener('keyup', function () {

    let valor = this.value.trim();

    if(valor.length < 2)
    {
        resultadoBusqueda.innerHTML = '';
        return;
    }

    fetch(`/admin/departamentos/personas/buscar?q=${valor}`)

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

            /*
            |--------------------------------------------------------------------------
            | PERSONA OCUPADA
            |--------------------------------------------------------------------------
            */

            if(persona.ocupado
                && persona.idPersona != "{{ $departamento->idPersonaEncargada }}")
            {
                item.classList.add(
                    'bg-dark',
                    'text-white'
                );
            }

            item.innerHTML = `
                <div>

                    <strong>
                        ${persona.nombre}
                    </strong>

                    <br>

                    <small>
                        CI: ${persona.ci}
                    </small>

                    ${
                        persona.ocupado
                        && persona.idPersona != "{{ $departamento->idPersonaEncargada }}"
                        ? `
                            <br>

                            <span class="badge bg-warning text-dark mt-1">

                                Ya pertenece a:
                                ${persona.departamento}

                            </span>
                        `
                        : ''
                    }

                </div>
            `;

            /*
            |--------------------------------------------------------------------------
            | NO SELECCIONAR SI ESTÁ OCUPADO
            |--------------------------------------------------------------------------
            */

            if(!(persona.ocupado
                && persona.idPersona != "{{ $departamento->idPersonaEncargada }}"))
            {
                item.addEventListener('click', function () {

                    selectPersona.value =
                        persona.idPersona;

                    inputBusqueda.value =
                        persona.nombre;

                    resultadoBusqueda.innerHTML = '';

                });
            }

            resultadoBusqueda.appendChild(item);

        });

    });

});

</script>
@endsection