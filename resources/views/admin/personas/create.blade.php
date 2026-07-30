@extends('layouts.app')

@section('title', 'Nueva Persona')

@section('content')

<div class="container-fluid">

    {{-- TITULO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-person-plus-fill"></i>
                Nueva Persona
            </h2>
            <p class="text-muted mb-0">Crear nuevo registro de persona en la institucion</p>
        </div>
        <a href="{{ route('admin.personas.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
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
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">
                                    Carnet de Identidad
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="ci" inputmode="numeric" pattern="[0-9]+"
                                       class="form-control @error('ci') is-invalid @enderror"
                                       value="{{ old('ci') }}" placeholder="Ej: 12345678" required>
                                @error('ci')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    Complemento
                                    <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" name="complemento_ci" maxlength="10"
                                       class="form-control text-uppercase @error('complemento_ci') is-invalid @enderror"
                                       value="{{ old('complemento_ci') }}" placeholder="Ej: LP">
                                @error('complemento_ci')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- NOMBRE --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nombre Completo
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" placeholder="Ej: Juan Carlos Garcia" required>
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

                        {{-- CARGOS (solo para INTERNOS) --}}
                        <div class="mb-3" id="cargoDiv">
                            <label class="form-label fw-semibold">
                                Cargos
                                <span class="text-muted">(Opcional - puede agregar varios)</span>
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
                                    Puede agregar varios cargos. Ej: "Tecnico en Humanidades", "Asistente Administrativo"
                                </small>
                                <input type="hidden" name="cargos_nombres" id="cargosNombres" value="{{ old('cargos_nombres', '') }}">
                            </div>
                            @error('cargos_nombres')
                                <div class="text-danger small mt-1">{{ $message }}</div>
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
                                Tambien puedes agregar esta persona al departamento desde la seccion de Departamentos.
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
                        Informacion de Contacto
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Telefono Celular
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
                                    Telefono Fijo
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
                                Correo Electronico
                            </label>
                            <input type="email" name="correo"
                                   class="form-control @error('correo') is-invalid @enderror"
                                   value="{{ old('correo') }}" placeholder="usuario@ejemplo.com">
                            @error('correo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="institucion" value="EPAB">

                        {{-- INSTITUCION (solo para EXTERNOS) --}}
                        <div class="mb-3" id="institucionDiv" style="display: none;">
                            <label class="form-label fw-semibold">
                                Institucion
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="institucion" id="institucion"
                                   class="form-control @error('institucion') is-invalid @enderror"
                                   value="{{ old('institucion') }}"
                                   placeholder="Ej: Universidad Mayor de San Andres, Ministerio de Defensa, Empresa XYZ, Particular">
                            <small class="d-block text-muted mt-2">
                                Indicar la institucion a la que pertenece esta persona.
                                Escriba "Particular" si no pertenece a institucion alguna.
                            </small>
                            @error('institucion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-0" id="institucionInfo">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                <strong>Institucion:</strong> Se asigna automaticamente como EPAB para personas internas.
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
                        Informacion
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3" id="infoTipo">
                            <strong>Tipo:</strong> <span id="tipoTexto">Interno</span>
                        </p>
                        <p class="text-muted mb-3" id="infoInstitucion">
                            <strong>Institucion:</strong> EPAB (Escuela de Postgrado de la Armada Boliviana)
                        </p>
                        <div class="alert alert-info mb-0">
                            <small id="notaInfo">
                                <strong>Nota:</strong> Los campos de Cargo y Departamento son opcionales.
                                Puedes asignarlos despues desde la vista correspondiente.
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
.autocomplete-list .autocomplete-item .text-muted {
    font-size: 0.78rem;
}
</style>

<script>
const cargosExistentes = @json($cargos->pluck('nombre')->toArray());
let cargosSeleccionados = [];

document.addEventListener('DOMContentLoaded', function() {
    // Parsear valores old
    const oldVal = document.getElementById('cargosNombres').value;
    if (oldVal) {
        oldVal.split(',').forEach(c => {
            const trimmed = c.trim();
            if (trimmed) addCargoTag(trimmed);
        });
    }
    actualizarFormulario();
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
            // Buscar coincidencia exacta o agregar como nuevo
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
        cargoDiv.style.display = 'block';
        departamentoDiv.style.display = 'block';
        institucionDiv.style.display = 'none';
        institucionInfo.style.display = 'block';
        tipoTexto.textContent = 'Interno';
        institucion.removeAttribute('required');
        notaInfo.innerHTML = '<strong>Nota:</strong> Puedes asignar opcionalmente uno o mas Cargos y un Departamento. Tambien podras hacerlo despues desde la vista correspondiente.';
    } else {
        cargoDiv.style.display = 'none';
        departamentoDiv.style.display = 'none';
        institucionDiv.style.display = 'block';
        institucionInfo.style.display = 'none';
        tipoTexto.textContent = 'Externo';
        institucion.setAttribute('required', 'required');
        notaInfo.innerHTML = '<strong>Nota:</strong> Debes indicar la institucion de procedencia de esta persona externa. Si no pertenece a ninguna institucion, escribe "Particular".';
        document.getElementById('departamentoSelect').value = '';
    }
}
</script>

@endsection
