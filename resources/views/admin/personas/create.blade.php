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
        <a href="{{ route('admin.personas.index') }}"class="text-decoration-none text-muted mb-3 d-inline-block">
            <i class="bi bi-chevron-left"></i> Volver
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
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tipo de Persona
                                <span class="text-danger">*</span>
                            </label>
                            <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" onchange="actualizarFormulario()">
                                <option value="INTERNO" @selected(old('tipo') === 'INTERNO' || true)>Interno</option>
                                <option value="EXTERNO" @selected(old('tipo') === 'EXTERNO')>Externo</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CARGO (solo para INTERNOS) --}}
                        <div class="mb-3" id="cargoDiv">
                            <label class="form-label fw-semibold">
                                Cargo
                                <span class="text-muted">(Opcional)</span>
                            </label>
                            <div class="input-group">
                                <select name="idCargo" id="cargoSelect" class="form-select @error('idCargo') is-invalid @enderror">
                                    <option value="">Seleccione un cargo</option>
                                    @foreach($cargos as $cargo)
                                        <option value="{{ $cargo->idCargo }}" @selected(old('idCargo') === (string)$cargo->idCargo)>
                                            {{ $cargo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('idCargo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DEPARTAMENTO (solo para INTERNOS) --}}
                        <div class="mb-3" id="departamentoDiv" style="display: none;">
                            <label class="form-label fw-semibold">
                                Departamento
                                <span class="text-muted">(Opcional)</span>
                            </label>
                            <select name="idDepartamento" id="departamentoSelect" class="form-select @error('idDepartamento') is-invalid @enderror">
                                <option value="">No asignar a departamento</option>
                                @foreach(\App\Models\Departamento::orderBy('nombre')->get() as $depto)
                                    <option value="{{ $depto->idDepartamento }}" @selected(old('idDepartamento') === (string)$depto->idDepartamento)>
                                        {{ $depto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="d-block text-muted mt-2">
                                También puedes agregar esta persona al departamento desde la sección de Departamentos.
                            </small>
                            @error('idDepartamento')
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

                        {{-- INSTITUCIÓN (OCULTO - Se asigna automáticamente) --}}
                        <input type="hidden" name="institucion" value="EPAB">
                        
                        {{-- INSTITUCIÓN (solo para EXTERNOS) --}}
                        <div class="mb-3" id="institucionDiv" style="display: none;">
                            <label class="form-label fw-semibold">
                                Institución
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="institucion" id="institucion"
                                   class="form-control @error('institucion') is-invalid @enderror"
                                   value="{{ old('institucion') }}" 
                                   placeholder="Ej: Universidad Mayor de San Andrés, Ministerio de Defensa, Empresa XYZ, Particular">
                            <small class="d-block text-muted mt-2">
                                Indicar la institución a la que pertenece esta persona. 
                                Escriba "Particular" si no pertenece a institución alguna.
                            </small>
                            @error('institucion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-0" id="institucionInfo">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                <strong>Institución:</strong> Se asigna automáticamente como EPAB para personas internas.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- RESUMEN --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-info-circle-fill"></i>
                        Información
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3" id="infoTipo">
                            <strong>Tipo:</strong> <span id="tipoTexto">Interno</span>
                        </p>
                        <p class="text-muted mb-3" id="infoInstitucion">
                            <strong>Institución:</strong> EPAB (Escuela de Postgrado de la Armada Boliviana)
                        </p>
                        <div class="alert alert-info mb-0">
                            <small id="notaInfo">
                                <strong>Nota:</strong> Los campos de Cargo y Departamento son opcionales. 
                                Puedes asignarlos después desde la vista correspondiente.
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

{{-- SCRIPT PARA FORMULARIO DINÁMICO --}}
<script>
function actualizarFormulario() {
    const tipo = document.getElementById('tipo').value;
    const cargoDiv = document.getElementById('cargoDiv');
    const departamentoDiv = document.getElementById('departamentoDiv');
    const institucionDiv = document.getElementById('institucionDiv');
    const institucionInfo = document.getElementById('institucionInfo');
    const tipoTexto = document.getElementById('tipoTexto');
    const notaInfo = document.getElementById('notaInfo');
    const institucion = document.getElementById('institucion');
    
    if (tipo === 'INTERNO') {
        // Mostrar campos para INTERNOS
        cargoDiv.style.display = 'block';
        departamentoDiv.style.display = 'block';
        institucionDiv.style.display = 'none';
        institucionInfo.style.display = 'block';
        tipoTexto.textContent = 'Interno';
        institucion.removeAttribute('required');
        notaInfo.innerHTML = `
            <strong>Nota:</strong> Puedes asignar opcionalmente un Cargo y Departamento. 
            También podrás hacerlo después desde la vista correspondiente.
        `;
    } else {
        // Ocultar para EXTERNOS
        cargoDiv.style.display = 'none';
        departamentoDiv.style.display = 'none';
        institucionDiv.style.display = 'block';
        institucionInfo.style.display = 'none';
        tipoTexto.textContent = 'Externo';
        institucion.setAttribute('required', 'required');
        notaInfo.innerHTML = `
            <strong>Nota:</strong> Debes indicar la institución de procedencia de esta persona externa.
            Si no pertenece a ninguna institución, escribe "Particular".
        `;
        // Limpiar valores
        document.getElementById('cargoSelect').value = '';
        document.getElementById('departamentoSelect').value = '';
    }
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', actualizarFormulario);
</script>

@endsection
