@extends('layouts.app')

@section('title', 'Nueva Persona')

@section('content')

<div class="container-fluid">

    {{-- TÍTULO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-person-plus-fill"></i>
                Nueva Persona
            </h2>
            <p class="text-muted mb-0">Crear nuevo registro de persona en la institución</p>
        </div>
        <a href="{{ route('admin.personas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    {{-- ALERTAS DE ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error en el formulario</strong>
            <ul class="mb-0 ms-3 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="{{ route('admin.personas.store') }}" method="POST" class="needs-validation">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                {{-- DATOS PERSONALES --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-person-fill"></i>
                        Datos Personales
                    </div>
                    <div class="card-body">
                        {{-- CI --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Carnet de Identidad
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror"
                                   value="{{ old('ci') }}" placeholder="Ej: 1234567-8" required>
                            @error('ci')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NOMBRE --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nombre Completo
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" placeholder="Ej: Juan Carlos García" required>
                            @error('nombre')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TIPO --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Tipo de Persona
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="INTERNO" @selected(old('tipo') === 'INTERNO')>Interno</option>
                                    <option value="EXTERNO" @selected(old('tipo') === 'EXTERNO')>Externo</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Departamento
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="idDepartamento" class="form-select @error('idDepartamento') is-invalid @enderror" required>
                                    <option value="">Seleccione departamento</option>
                                    @foreach($departamentos as $depto)
                                        <option value="{{ $depto->idDepartamento }}" @selected(old('idDepartamento') === (string)$depto->idDepartamento)>
                                            {{ $depto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idDepartamento')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- CARGO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Cargo
                                <span class="text-danger">*</span>
                            </label>
                            <select name="idCargo" class="form-select @error('idCargo') is-invalid @enderror" required>
                                <option value="">Seleccione cargo</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->idCargo }}" @selected(old('idCargo') === (string)$cargo->idCargo)>
                                        {{ $cargo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idCargo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- CONTACTO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-telephone-fill"></i>
                        Información de Contacto
                    </div>
                    <div class="card-body">
                        {{-- CELULAR --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Teléfono Celular
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="telefono_celular" 
                                       class="form-control @error('telefono_celular') is-invalid @enderror"
                                       value="{{ old('telefono_celular') }}" placeholder="+591 71234567" required>
                                @error('telefono_celular')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Teléfono Fijo
                                </label>
                                <input type="text" name="telefono_fijo" 
                                       class="form-control @error('telefono_fijo') is-invalid @enderror"
                                       value="{{ old('telefono_fijo') }}" placeholder="(+591) 2-2123456">
                                @error('telefono_fijo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- CORREO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Correo Electrónico
                            </label>
                            <input type="email" name="correo" 
                                   class="form-control @error('correo') is-invalid @enderror"
                                   value="{{ old('correo') }}" placeholder="usuario@ejemplo.com">
                            @error('correo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- INSTITUCIÓN --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Institución
                            </label>
                            <input type="text" name="institucion" 
                                   class="form-control @error('institucion') is-invalid @enderror"
                                   value="{{ old('institucion') }}" placeholder="Ej: Ministerio de Educación">
                            @error('institucion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- RESPONSABILIDAD --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-shield-check"></i>
                        Responsabilidad
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="es_responsable" 
                                   id="es_responsable" value="1" @checked(old('es_responsable'))>
                            <label class="form-check-label" for="es_responsable">
                                <strong>Asignar como Responsable del Departamento</strong>
                            </label>
                            <small class="d-block text-muted mt-2">
                                Si marca esta opción, esta persona será asignada como responsable del departamento seleccionado.
                            </small>
                        </div>

                        {{-- INFO --}}
                        <div class="alert alert-info mt-4 mb-0">
                            <small>
                                <strong>Nota:</strong> Esta información es importante para auditoría y seguimiento de responsabilidades departamentales.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Crear Persona
                    </button>
                    <a href="{{ route('admin.personas.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>

</div>

@endsection
