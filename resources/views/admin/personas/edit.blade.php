@extends('layouts.app')

@section('title', 'Editar Persona')

@section('content')

<div class="container py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-pencil-square"></i>

                Editar Persona

            </h1>

            <p class="text-light mb-0">

                Modificación institucional de datos personales

            </p>

        </div>

    </div>

    {{-- VALIDACIONES --}}
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

            Información Personal

        </div>

        <div class="card-body">

            <form action="{{ route('admin.personas.update', $persona->idPersona) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    {{-- NOMBRE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Nombre Completo

                        </label>

                        <input type="text"
                               name="nombre"
                               class="form-control"
                               value="{{ old('nombre', $persona->nombre) }}"
                               required>

                    </div>

                    {{-- CI --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Carnet de Identidad

                        </label>

                        <input type="text"
                               name="ci"
                               class="form-control"
                               value="{{ old('ci', $persona->ci) }}"
                               required>

                    </div>

                </div>

                <div class="row">

                    {{-- CELULAR --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Celular

                        </label>

                        <input type="text"
                               name="telefono_celular"
                               class="form-control"
                               value="{{ old('telefono_celular', $persona->telefono_celular) }}">

                    </div>

                    {{-- FIJO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Teléfono Fijo

                        </label>

                        <input type="text"
                               name="telefono_fijo"
                               class="form-control"
                               value="{{ old('telefono_fijo', $persona->telefono_fijo) }}">

                    </div>

                </div>

                <div class="row">

                    {{-- CORREO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Correo

                        </label>

                        <input type="email"
                               name="correo"
                               class="form-control"
                               value="{{ old('correo', $persona->correo) }}">

                    </div>

                    {{-- CARGO (Solo para internos) --}}
                    <div class="col-md-6 mb-3" id="cargo_section"
                         style="display: {{ $persona->tipo === 'INTERNO' ? 'block' : 'none' }};">

                        <label class="form-label fw-semibold">

                            Cargo

                        </label>

                        <select name="idCargo"
                               id="idCargo"
                               class="form-select">

                            <option value="">

                                -- Seleccione cargo --

                            </option>

                            @foreach($cargos as $cargo)

                                <option value="{{ $cargo->idCargo }}"
                                    {{ $persona->idCargo == $cargo->idCargo ? 'selected' : '' }}>

                                    {{ $cargo->nombre }}

                                </option>

                            @endforeach

                        </select>

                        <small class="text-muted d-block mt-2">

                            <i class="bi bi-info-circle me-1"></i>

                            Solo disponible para personas INTERNAS

                        </small>

                    </div>

                </div>

                {{-- INSTITUCIÓN --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Institución

                    </label>

                    <input type="text"
                           name="institucion"
                           class="form-control"
                           value="{{ old('institucion', $persona->institucion) }}">

                </div>

                <div class="row">

                    {{-- TIPO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Tipo

                        </label>

                        <select name="tipo"
                                class="form-select"
                                required>

                            <option value="INTERNO"
                                {{ $persona->tipo == 'INTERNO' ? 'selected' : '' }}>

                                INTERNO

                            </option>

                            <option value="EXTERNO"
                                {{ $persona->tipo == 'EXTERNO' ? 'selected' : '' }}>

                                EXTERNO

                            </option>

                        </select>

                    </div>

                    {{-- DEPARTAMENTO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Departamento

                        </label>

                        <select name="idDepartamento"
                                class="form-select">

                            <option value="">

                                Seleccione

                            </option>

                            @foreach($departamentos as $dep)

                                <option value="{{ $dep->idDepartamento }}"
                                    {{ $persona->idDepartamento == $dep->idDepartamento ? 'selected' : '' }}>

                                    {{ $dep->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3 mt-4">

                    <a href="{{ route('admin.personas.index') }}"
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

@endsection

<script>
    /*
    |--------------------------------------------------------------------------
    | MOSTRAR/OCULTAR CAMPO DE CARGO SEGÚN TIPO
    |--------------------------------------------------------------------------
    */

    const tipoSelect = document.querySelector('select[name="tipo"]');
    const cargoSection = document.getElementById('cargo_section');
    const idCargoSelect = document.getElementById('idCargo');

    function toggleCargoField() {
        if (tipoSelect.value === 'INTERNO') {
            cargoSection.style.display = 'block';
        } else {
            cargoSection.style.display = 'none';
            idCargoSelect.value = ''; // Limpia el cargo si es externo
        }
    }

    tipoSelect.addEventListener('change', toggleCargoField);
</script>
