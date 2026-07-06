@extends('layouts.app')

@section('title', 'Personas')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold text-white mb-2">
                        <i class="bi bi-person-vcard-fill"></i>
                        Gestión de Personas
                    </h1>
                    <p class="text-light mb-0">
                        Administración institucional de personas registradas
                    </p>
                </div>
                <a href="{{ route('admin.personas.create') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-plus-circle-fill"></i> Nueva Persona
                </a>
            </div>

        </div>

    </div>

    {{-- TABS --}}
    <div class="card border-0 shadow-lg rounded-4">

        <ul class="nav nav-tabs card-header rounded-top-4" role="tablist" style="background-color:#0B2D59; border-bottom: 2px solid #ddd;">
            
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-white fw-bold" id="trabajadores-tab" data-bs-toggle="tab" 
                        data-bs-target="#trabajadores" type="button" role="tab">
                    <i class="bi bi-briefcase-fill"></i> Trabajadores ({{ $personas->filter(fn($p) => !is_null($p->idDepartamento) && is_null($p->fecha_deshabilitacion))->count() }})
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link text-white fw-bold" id="remitentes-tab" data-bs-toggle="tab" 
                        data-bs-target="#remitentes" type="button" role="tab">
                    <i class="bi bi-send-fill"></i> Remitentes ({{ $personas->filter(fn($p) => is_null($p->idDepartamento) && is_null($p->fecha_deshabilitacion))->count() }})
                </button>
            </li>

        </ul>

        <div class="tab-content card-body">

            {{-- TAB: TRABAJADORES --}}
            <div class="tab-pane fade show active" id="trabajadores" role="tabpanel">

                <h5 class="mb-4">Personal que trabaja en la institución</h5>

                {{-- FILTROS --}}
                <div class="row mb-4">
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="filtro-ci" placeholder="Filtrar por CI">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="filtro-nombre" placeholder="Filtrar por Nombre">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="filtro-departamento">
                            <option value="">-- Departamento --</option>
                            @foreach($personas->pluck('departamento')->filter()->unique('idDepartamento') as $depto)
                                <option value="{{ $depto->idDepartamento }}">{{ $depto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="filtro-celular" placeholder="Filtrar por Celular">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="filtro-cargo">
                            <option value="">-- Cargo --</option>
                            @foreach($personas->pluck('cargo')->filter()->unique('idCargo') as $cargo)
                                <option value="{{ $cargo->idCargo }}">{{ $cargo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-outline-primary w-100" id="btn-limpiar-filtros">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                    </div>
                </div>

                {{-- TABLA TRABAJADORES --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabla-trabajadores">
                        <thead class="table-light">
                            <tr>
                                <th>CI</th>
                                <th>Nombre</th>
                                <th>Departamento</th>
                                <th>Cargo</th>
                                <th>Celular</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personas->filter(fn($p) => !is_null($p->idDepartamento)) as $persona)
                                <tr class="persona-row" data-ci="{{ $persona->ci }}" data-nombre="{{ $persona->nombre }}" 
                                    data-departamento="{{ $persona->idDepartamento }}" data-celular="{{ $persona->telefono_celular }}"
                                    data-cargo="{{ $persona->idCargo }}" data-estado="{{ $persona->activo ? 'activo' : 'inactivo' }}">
                                    <td>{{ $persona->ci }}</td>
                                    <td class="fw-semibold">{{ $persona->nombre }}</td>
                                    <td>{{ $persona->departamento?->nombre ?? 'N/A' }}</td>
                                    <td>{{ $persona->cargos_nombres ?? 'N/A' }}</td>
                                    <td>{{ $persona->telefono_celular ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $persona->tipo == 'INTERNO' ? 'bg-primary' : 'bg-warning' }}">
                                            {{ $persona->tipo }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(is_null($persona->fecha_deshabilitacion))
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            {{-- EDIT --}}
                                            <a href="{{ route('admin.personas.edit', $persona->idPersona) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            {{-- TOGGLE --}}
                                            <form action="{{ route('admin.personas.toggle', $persona->idPersona) }}"
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        class="btn btn-sm {{ $persona->activo ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                        onclick="return confirm('¿Confirmar cambio de estado?')">
                                                    <i class="bi {{ $persona->activo ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i> No hay trabajadores registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- TAB: REMITENTES --}}
            <div class="tab-pane fade" id="remitentes" role="tabpanel">

                <h5 class="mb-4">Remitentes de correspondencia</h5>

                {{-- TABLA REMITENTES --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>CI</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Cargo</th>
                                <th>Teléfono Fijo</th>
                                <th>Institución</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personas->filter(fn($p) => is_null($p->idDepartamento)) as $persona)
                                <tr>
                                    <td>{{ $persona->ci }}</td>
                                    <td class="fw-semibold">{{ $persona->nombre }}</td>
                                    <td>{{ $persona->correo ?? 'N/A' }}</td>
                                    <td>{{ $persona->cargos_nombres ?? 'N/A' }}</td>
                                    <td>{{ $persona->telefono_fijo ?? 'N/A' }}</td>
                                    <td>{{ $persona->institucion ?? 'N/A' }}</td>
                                    <td>
                                        @if(is_null($persona->fecha_deshabilitacion))
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.personas.edit', $persona->idPersona) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-fill"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i> No hay remitentes registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtros = {
        ci: document.getElementById('filtro-ci'),
        nombre: document.getElementById('filtro-nombre'),
        departamento: document.getElementById('filtro-departamento'),
        celular: document.getElementById('filtro-celular'),
        cargo: document.getElementById('filtro-cargo')
    };

    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    const tabla = document.getElementById('tabla-trabajadores');
    const filas = tabla.querySelectorAll('tbody tr.persona-row');

    function aplicarFiltros() {
        filas.forEach(fila => {
            let mostrar = true;

            if (filtros.ci.value && !fila.dataset.ci.includes(filtros.ci.value)) mostrar = false;
            if (filtros.nombre.value && !fila.dataset.nombre.toLowerCase().includes(filtros.nombre.value.toLowerCase())) mostrar = false;
            if (filtros.departamento.value && fila.dataset.departamento !== filtros.departamento.value) mostrar = false;
            if (filtros.celular.value && !fila.dataset.celular.includes(filtros.celular.value)) mostrar = false;
            if (filtros.cargo.value && fila.dataset.cargo !== filtros.cargo.value) mostrar = false;

            fila.style.display = mostrar ? '' : 'none';
        });
    }

    Object.values(filtros).forEach(filtro => {
        filtro.addEventListener('input', aplicarFiltros);
        filtro.addEventListener('change', aplicarFiltros);
    });

    btnLimpiar.addEventListener('click', function() {
        Object.values(filtros).forEach(filtro => filtro.value = '');
        aplicarFiltros();
    });
});
</script>

@endsection
