@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')

<div class="container py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold text-white mb-1">
                    <i class="bi bi-person-plus-fill"></i>
                    Nuevo Usuario
                </h1>
                <p class="text-light mb-0">
                    Solo se pueden crear cuentas para personas internas sin usuario asignado.
                </p>
            </div>
            <a href="{{ route('admin.usuarios') }}"
               class="btn btn-light rounded-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Errores en el formulario:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">

        {{-- FORMULARIO --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">
                    <i class="bi bi-person-fill"></i>
                    Datos del Nuevo Usuario
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('admin.usuarios.store') }}"
                          method="POST">
                        @csrf

                        {{-- PERSONA INTERNA --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Persona <span class="text-danger">*</span>
                            </label>

                            @if($personas->isEmpty())
                                <div class="alert alert-warning rounded-3 mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    No existen personas internas disponibles para crear usuarios.
                                    Todas las personas internas ya tienen una cuenta asignada,
                                    o no existen personas con tipo = <strong>INTERNO</strong>.
                                </div>
                            @else
                                {{-- CAMPO DE BÚSQUEDA --}}
                                <div class="input-group mb-3 rounded-3">
                                    <input type="text"
                                           id="searchInput"
                                           class="form-control rounded-start-3"
                                           placeholder="Buscar por CI o nombre..."
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>

                                {{-- RESULTADOS DE BÚSQUEDA --}}
                                <div id="searchResults"
                                     class="border rounded-3 bg-white position-relative mb-3"
                                     style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                                </div>

                                {{-- SELECT OCULTO PERO REQUERIDO --}}
                                <select name="idPersona"
                                        id="idPersona"
                                        class="form-select rounded-3 @error('idPersona') is-invalid @enderror"
                                        required
                                        style="display: none;">
                                    <option value="">— Seleccione una persona interna —</option>
                                    @foreach($personas as $persona)
                                        <option value="{{ $persona->idPersona }}"
                                                data-nombre="{{ $persona->nombre }}"
                                                data-ci="{{ $persona->ci ?? 'N/A' }}"
                                                data-correo="{{ $persona->correo ?? '' }}"
                                                data-cargo="{{ $persona->cargos_nombres ?? 'Sin cargo' }}"
                                                data-departamento="{{ $persona->departamento?->nombre ?? 'Sin departamento' }}">
                                            {{ $persona->nombre }}
                                            — CI: {{ $persona->ci ?? 'N/A' }}
                                            @if($persona->departamento)
                                                — {{ $persona->departamento->nombre }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                {{-- PERSONA SELECCIONADA --}}
                                <div id="personaSeleccionada" class="alert alert-success rounded-3 d-none">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong id="sel-nombre">—</strong><br>
                                            <small class="text-muted">CI: <span id="sel-ci">—</span></small><br>
                                            <small class="text-muted">Departamento: <span id="sel-departamento">—</span></small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarSeleccion()">
                                            <i class="bi bi-x"></i> Cambiar
                                        </button>
                                    </div>
                                </div>

                                @error('idPersona')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Solo se muestran personas internas activas sin cuenta asignada.
                                </small>
                            @endif
                        </div>

                        <hr class="my-4">

                        {{-- NOMBRE DEL USUARIO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nombre de usuario <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   class="form-control rounded-3 @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Nombre completo"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- EMAIL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Correo electrónico institucional <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   class="form-control rounded-3 @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="usuario@institucion.gob.bo"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ROL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Rol <span class="text-danger">*</span>
                            </label>
                            <select name="idRol"
                                    class="form-select rounded-3 @error('idRol') is-invalid @enderror"
                                    required>
                                <option value="">— Seleccione un rol —</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->idRol }}"
                                            @selected(old('idRol') == $rol->idRol)>
                                        {{ $rol->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idRol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- CONTRASEÑA --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control rounded-start-3 @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres"
                                       required>
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="togglePass('password')">
                                    <i class="bi bi-eye" id="icon-password"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Debe contener: mayúscula, minúscula y carácter especial (@$!%*?&).
                                Mínimo 8 caracteres.
                            </small>
                        </div>

                        {{-- CONFIRMAR CONTRASEÑA --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Confirmar contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control rounded-start-3"
                                       placeholder="Repita la contraseña"
                                       required>
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="togglePass('password_confirmation')">
                                    <i class="bi bi-eye" id="icon-password_confirmation"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ACTIVO --}}
                        <div class="form-check mb-4">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="activo"
                                   id="activo"
                                   value="1"
                                   checked>
                            <label class="form-check-label fw-semibold" for="activo">
                                Usuario activo al crear
                            </label>
                        </div>

                        {{-- BOTONES --}}
                        <div class="d-flex gap-3">
                            <button type="submit"
                                    class="btn text-white px-4 rounded-3"
                                    style="background-color:#0B2D59;"
                                    @if($personas->isEmpty()) disabled @endif>
                                <i class="bi bi-person-check-fill me-1"></i>
                                Crear Usuario
                            </button>
                            <a href="{{ route('admin.usuarios') }}"
                               class="btn btn-outline-secondary rounded-3">
                                Cancelar
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- PANEL INFORMATIVO --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header text-white rounded-top-4"
                     style="background-color:#2E608C;">
                    <i class="bi bi-info-circle-fill"></i>
                    Reglas del sistema
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span>Solo las personas clasificadas como <strong>Interno</strong> pueden tener cuenta.</span>
                        </li>
                        <li class="mb-3 d-flex gap-2">
                            <i class="bi bi-x-circle-fill text-danger mt-1 flex-shrink-0"></i>
                            <span>Las personas <strong>externas</strong> no pueden tener cuenta de usuario bajo ninguna circunstancia.</span>
                        </li>
                        <li class="mb-3 d-flex gap-2">
                            <i class="bi bi-shield-fill-check text-primary mt-1 flex-shrink-0"></i>
                            <span>Cada persona solo puede tener <strong>una cuenta</strong>. No se permiten duplicados.</span>
                        </li>
                        <li class="mb-3 d-flex gap-2">
                            <i class="bi bi-envelope-fill text-warning mt-1 flex-shrink-0"></i>
                            <span>El acceso al sistema es por <strong>correo + contraseña</strong>.</span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-lock-fill text-secondary mt-1 flex-shrink-0"></i>
                            <span>La contraseña debe tener mínimo <strong>8 caracteres</strong>, mayúscula, minúscula y símbolo.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>
@php
$personasJson = $personas->map(function ($p) {
    return [
        'idPersona' => $p->idPersona,
        'nombre' => $p->nombre,
        'ci' => $p->ci ?? '',
        'correo' => $p->correo ?? '',
        'cargo' => $p->cargos_nombres ?? 'Sin cargo',
        'departamento' => optional($p->departamento)->nombre ?? 'Sin departamento',
    ];
})->values()->toArray();
@endphp
<script>
let currentPersonas = [];

document.addEventListener('DOMContentLoaded', function () {

    currentPersonas = @json($personasJson);

    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const query = e.target.value.trim();
            const resultsDiv = document.getElementById('searchResults');

            if (!resultsDiv) return;

            if (query.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }

            const filtered = currentPersonas.filter(p =>
                (p.nombre || '').toLowerCase().includes(query.toLowerCase()) ||
                (p.ci || '').includes(query)
            );

            if (filtered.length === 0) {
                resultsDiv.innerHTML =
                    '<div class="p-3 text-muted">No se encontraron resultados</div>';
                resultsDiv.style.display = 'block';
                return;
            }

            resultsDiv.innerHTML = filtered.map(p => `
                <div class="p-3 border-bottom search-result"
                     onclick="seleccionarPersona(
                        ${p.idPersona},
                        '${String(p.nombre).replace(/'/g, "\\'")}',
                        '${String(p.ci).replace(/'/g, "\\'")}',
                        '${String(p.correo).replace(/'/g, "\\'")}',
                        '${String(p.cargo).replace(/'/g, "\\'")}',
                        '${String(p.departamento).replace(/'/g, "\\'")}'
                     )"
                     style="cursor:pointer; transition:background-color 0.2s;">

                    <div class="d-flex justify-content-between">
                        <strong>${p.nombre}</strong>
                        <span class="badge bg-primary">${p.ci}</span>
                    </div>

                    <small class="text-muted">
                        ${p.cargo} • ${p.departamento}
                    </small>
                </div>
            `).join('');

            document.querySelectorAll('.search-result').forEach(el => {
                el.addEventListener('mouseenter', function () {
                    this.style.backgroundColor = '#f0f0f0';
                });

                el.addEventListener('mouseleave', function () {
                    this.style.backgroundColor = 'transparent';
                });
            });

            resultsDiv.style.display = 'block';
        });
    }

    if (clearSearch) {
        clearSearch.addEventListener('click', function () {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').style.display = 'none';
            limpiarSeleccion();
        });
    }
});

function seleccionarPersona(idPersona, nombre, ci, correo, cargo, departamento) {

    document.getElementById('idPersona').value = idPersona;
    document.getElementById('searchInput').value = nombre;
    document.getElementById('searchResults').style.display = 'none';

    document.getElementById('sel-nombre').textContent = nombre;
    document.getElementById('sel-ci').textContent = ci;
    document.getElementById('sel-departamento').textContent = departamento;

    document.getElementById('personaSeleccionada')
        .classList.remove('d-none');

    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');

    if (nameInput && !nameInput.value) {
        nameInput.value = nombre;
    }

    if (emailInput && !emailInput.value) {
        emailInput.value = correo || '';
    }
}

function limpiarSeleccion() {

    document.getElementById('idPersona').value = '';
    document.getElementById('searchInput').value = '';

    const results = document.getElementById('searchResults');
    if (results) {
        results.style.display = 'none';
    }

    document.getElementById('personaSeleccionada')
        .classList.add('d-none');
}

document.addEventListener('click', function (e) {

    const searchDiv = document.getElementById('searchResults');

    if (
        !e.target.closest('#searchInput') &&
        !e.target.closest('#searchResults')
    ) {
        if (searchDiv) {
            searchDiv.style.display = 'none';
        }
    }
});

function togglePass(fieldId) {

    const input = document.getElementById(fieldId);
    const icon = document.getElementById('icon-' + fieldId);

    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

@endsection
