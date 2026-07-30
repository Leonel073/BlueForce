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
                Modificacion institucional de datos personales
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
            Informacion Personal
        </div>

        <div class="card-body">
            <form action="{{ route('admin.personas.update', $persona->idPersona) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- NOMBRE --}}
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ old('nombre', $persona->nombre) }}" required>
                    </div>

                    {{-- CI --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Carnet de Identidad</label>
                        <input type="text" name="ci" inputmode="numeric" pattern="[0-9]+" class="form-control"
                               value="{{ old('ci', $persona->ci) }}" required>
                    </div>

                    {{-- COMPLEMENTO CI --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">
                            Complemento
                            <span class="text-muted">(Opcional)</span>
                        </label>
                        <input type="text" name="complemento_ci" maxlength="10" class="form-control text-uppercase"
                               value="{{ old('complemento_ci', $persona->complemento_ci) }}" placeholder="Ej: LP">
                    </div>
                </div>

                <div class="row">
                    {{-- CELULAR --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Celular</label>
                        <input type="text" name="telefono_celular" class="form-control"
                               value="{{ old('telefono_celular', $persona->telefono_celular) }}">
                    </div>

                    {{-- FIJO --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Telefono Fijo</label>
                        <input type="text" name="telefono_fijo" class="form-control"
                               value="{{ old('telefono_fijo', $persona->telefono_fijo) }}">
                    </div>
                </div>

                <div class="row">
                    {{-- CORREO --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Correo</label>
                        <input type="email" name="correo" class="form-control"
                               value="{{ old('correo', $persona->correo) }}">
                    </div>

                    {{-- TIPO --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select name="tipo" class="form-select" required id="tipo">
                            <option value="INTERNO" {{ $persona->tipo == 'INTERNO' ? 'selected' : '' }}>INTERNO</option>
                            <option value="EXTERNO" {{ $persona->tipo == 'EXTERNO' ? 'selected' : '' }}>EXTERNO</option>
                        </select>
                    </div>
                </div>

                {{-- CARGOS (solo para INTERNOS) --}}
                <div class="mb-3" id="cargo_section"
                     style="display: {{ $persona->tipo === 'INTERNO' ? 'block' : 'none' }};">
                    <label class="form-label fw-semibold">
                        Cargos
                        <span class="text-muted">(Puede agregar varios)</span>
                    </label>
                    <div id="cargosContainer">
                        <div class="input-group mb-2">
                            <input type="text" id="cargoInput" class="form-control"
                                   placeholder="Escriba un cargo y presione Enter o el boton +"
                                   autocomplete="off">
                            <button type="button" class="btn btn-outline-success" id="btnAddCargo" title="Agregar cargo">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <div id="cargosTags" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            Escriba el nombre del cargo y presione Enter o haga clic en +.
                            Puede agregar varios cargos. Solo disponible para personas INTERNAS.
                        </small>
                        <input type="hidden" name="cargos_nombres" id="cargosNombres"
                               value="{{ old('cargos_nombres', $persona->cargos_nombres) }}">
                    </div>
                </div>

                {{-- INSTITUCION --}}
                <div class="mb-3" id="institucion_section">
                    <label class="form-label fw-semibold">Institucion</label>
                    <input type="text" name="institucion" id="institucion" class="form-control"
                           value="{{ old('institucion', $persona->institucion) }}"
                           placeholder="Ej: EPAB (para internos), Universidad Mayor de San Andres (para externos)">
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        <span id="institucionHelp">Para personas internas se asigna como EPAB. Para externas, indique la institucion de procedencia o escriba "Particular".</span>
                    </small>
                </div>

                {{-- DEPARTAMENTO --}}
                <div class="mb-3" id="departamento_section"
                     style="display: {{ $persona->tipo === 'INTERNO' ? 'block' : 'none' }};">
                    <label class="form-label fw-semibold">Departamento</label>
                    <select name="idDepartamento" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->idDepartamento }}"
                                {{ $persona->idDepartamento == $dep->idDepartamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
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

<style>
.tag-cargo {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #0B2D59;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}
.tag-cargo .btn-remove {
    background: rgba(255,255,255,0.25);
    border: none;
    color: white;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.7rem;
    padding: 0;
    line-height: 1;
}
.tag-cargo .btn-remove:hover {
    background: rgba(255,0,0,0.6);
}
#cargoInput {
    min-width: 0;
}
.autocomplete-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0 0 8px 8px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.autocomplete-list .autocomplete-item {
    padding: 8px 14px;
    cursor: pointer;
    font-size: 0.9rem;
}
.autocomplete-list .autocomplete-item:hover {
    background: #f0f4f8;
}
</style>

<script>
const cargosExistentes = @json($cargos->pluck('nombre')->toArray());
let cargosSeleccionados = [];

document.addEventListener('DOMContentLoaded', function() {
    // Parsear cargos actuales de la persona desde el atributo computado
    const oldVal = document.getElementById('cargosNombres').value;
    if (oldVal) {
        oldVal.split(',').forEach(c => {
            const trimmed = c.trim();
            if (trimmed && !cargosSeleccionados.includes(trimmed)) {
                cargosSeleccionados.push(trimmed);
            }
        });
    }
    actualizarTags();
});

function addCargoTag(nombre) {
    nombre = nombre.trim();
    if (!nombre || cargosSeleccionados.includes(nombre)) return;
    cargosSeleccionados.push(nombre);
    actualizarTags();
    actualizarInputHidden();
}

function removeCargoTag(nombre) {
    cargosSeleccionados = cargosSeleccionados.filter(c => c !== nombre);
    actualizarTags();
    actualizarInputHidden();
}

function actualizarTags() {
    const container = document.getElementById('cargosTags');
    container.innerHTML = '';
    cargosSeleccionados.forEach(nombre => {
        const tag = document.createElement('span');
        tag.className = 'tag-cargo';
        tag.innerHTML = nombre + ' <button type="button" class="btn-remove" onclick="removeCargoTag(\'' + nombre.replace(/'/g, "\\'") + '\')">&times;</button>';
        container.appendChild(tag);
    });
}

function actualizarInputHidden() {
    document.getElementById('cargosNombres').value = cargosSeleccionados.join(', ');
}

// Autocomplete
const cargoInput = document.getElementById('cargoInput');
let autocompleteList = null;

cargoInput.addEventListener('input', function() {
    const val = this.value.trim().toLowerCase();
    if (autocompleteList) autocompleteList.remove();
    if (val.length < 2) return;

    const matches = cargosExistentes.filter(c => c.toLowerCase().includes(val) && !cargosSeleccionados.includes(c));
    if (matches.length === 0) return;

    autocompleteList = document.createElement('div');
    autocompleteList.className = 'autocomplete-list';
    matches.slice(0, 8).forEach(nombre => {
        const item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.textContent = nombre;
        item.addEventListener('click', function() {
            addCargoTag(nombre);
            cargoInput.value = '';
            if (autocompleteList) autocompleteList.remove();
        });
        autocompleteList.appendChild(item);
    });

    cargoInput.parentElement.style.position = 'relative';
    cargoInput.parentElement.appendChild(autocompleteList);
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#cargosContainer')) {
        if (autocompleteList) autocompleteList.remove();
    }
});

cargoInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = this.value.trim();
        if (val) {
            const match = cargosExistentes.find(c => c.toLowerCase() === val.toLowerCase());
            addCargoTag(match || val);
            this.value = '';
            if (autocompleteList) autocompleteList.remove();
        }
    }
});

document.getElementById('btnAddCargo').addEventListener('click', function() {
    const val = cargoInput.value.trim();
    if (val) {
        const match = cargosExistentes.find(c => c.toLowerCase() === val.toLowerCase());
        addCargoTag(match || val);
        cargoInput.value = '';
        if (autocompleteList) autocompleteList.remove();
    }
});

// Toggle fields based on tipo
const tipoSelect = document.getElementById('tipo');
const cargoSection = document.getElementById('cargo_section');
const institucionSection = document.getElementById('institucion_section');
const departamentoSection = document.getElementById('departamento_section');
const institucionInput = document.getElementById('institucion');
const institucionHelp = document.getElementById('institucionHelp');

function toggleFields() {
    if (tipoSelect.value === 'INTERNO') {
        cargoSection.style.display = 'block';
        departamentoSection.style.display = 'block';
        institucionInput.readOnly = true;
        institucionInput.value = 'EPAB';
        institucionHelp.textContent = 'Se asigna automaticamente como EPAB para personas internas.';
    } else {
        cargoSection.style.display = 'none';
        departamentoSection.style.display = 'none';
        institucionInput.readOnly = false;
        if (!institucionInput.value || institucionInput.value === 'EPAB') {
            institucionInput.value = '';
        }
        institucionHelp.textContent = 'Indicar la institucion de procedencia de esta persona externa. Escriba "Particular" si no pertenece a institucion alguna.';
    }
}

tipoSelect.addEventListener('change', toggleFields);
document.addEventListener('DOMContentLoaded', toggleFields);
</script>

@endsection
