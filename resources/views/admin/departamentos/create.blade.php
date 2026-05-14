@extends('layouts.app')

@section('title', 'Nuevo Departamento')

@section('content')

<div class="container py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-building-add"></i>

                Nuevo Departamento

            </h1>

            <p class="text-light mb-0">

                Registro administrativo institucional

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

        <div class="card-body">

            <form action="{{ route('admin.departamentos.store') }}"
                  method="POST">

                @csrf

                {{-- NOMBRE --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Nombre

                    </label>

                    <input type="text"
                           name="nombre"
                           class="form-control"
                           required>

                </div>

                {{-- BUSCADOR --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Buscar Encargado

                    </label>

                    <input type="text"
                           id="buscarPersona"
                           class="form-control"
                           placeholder="Buscar por nombre o CI">

                </div>

                {{-- RESULTADOS --}}
                <div id="resultadoBusqueda"
                     class="list-group mb-3">

                </div>

                {{-- ID OCULTO --}}
                <input type="hidden"
                       name="idPersonaEncargada"
                       id="idPersonaEncargada">

                {{-- PERSONA SELECCIONADA --}}
                <div id="personaSeleccionada"
                     class="alert alert-info d-none">

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3 mt-4">

                    <a href="{{ route('admin.departamentos.index') }}"
                       class="btn btn-secondary rounded-4 px-4">

                        Volver

                    </a>

                    <button type="submit"
                            class="btn text-white rounded-4 px-4"
                            style="background-color:#0B2D59;">

                        Guardar Departamento

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

const input =
    document.getElementById('buscarPersona');

const resultados =
    document.getElementById('resultadoBusqueda');

const idPersona =
    document.getElementById('idPersonaEncargada');

const personaSeleccionada =
    document.getElementById('personaSeleccionada');

input.addEventListener('keyup', function(){

    let valor = this.value.trim();

    if(valor.length < 2)
    {
        resultados.innerHTML = '';
        return;
    }

    fetch(`/admin/departamentos/personas/buscar?q=${valor}`)

    .then(res => res.json())

    .then(data => {

        resultados.innerHTML = '';

        if(data.length === 0)
        {
            resultados.innerHTML = `
                <div class="list-group-item">
                    Sin resultados
                </div>
            `;
            return;
        }

        data.forEach(persona => {

            let item =
                document.createElement('button');

            item.type = 'button';

            /*
            |--------------------------------------------------------------------------
            | COLOR SI YA TIENE DEPARTAMENTO
            |--------------------------------------------------------------------------
            */

            item.className =
                persona.ocupado
                ? 'list-group-item list-group-item-action bg-dark text-white'
                : 'list-group-item list-group-item-action';

            item.innerHTML = `
                <strong>${persona.nombre}</strong><br>
                <small>
                    CI: ${persona.ci}
                </small>

                ${
                    persona.ocupado
                    ? `<div class="mt-1 text-warning fw-bold">
                        Ya pertenece a:
                        ${persona.departamento}
                       </div>`
                    : ''
                }
            `;

            item.addEventListener('click', function(){

                idPersona.value =
                    persona.idPersona;

                personaSeleccionada.classList.remove('d-none');

                personaSeleccionada.innerHTML = `
                    Encargado seleccionado:
                    <strong>${persona.nombre}</strong>
                `;

                input.value =
                    persona.nombre;

                resultados.innerHTML = '';

            });

            resultados.appendChild(item);

        });

    });

});

</script>

@endsection