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
                <a href="{{ route('admin.personas.create') }}"
                   class="btn text-white rounded-4 px-4 shadow-sm"
                   style="background: linear-gradient(135deg,#D9A23D,#BF8A2E); border:none;">

                    <i class="bi bi-plus-circle-fill me-1"></i>

                    Nueva Persona

                </a>
            </div>

        </div>

    </div>

    {{-- TABS --}}
    <div class="card border-0 shadow-lg rounded-4">

        <ul class="nav nav-tabs card-header border-0 rounded-top-4 px-2 pt-2 gap-1"
            role="tablist"
            style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

            <li class="nav-item" role="presentation">

                <button class="nav-link active fw-semibold rounded-top-3 text-white"
                        id="trabajadores-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#trabajadores"
                        type="button"
                        role="tab"
                        style="border: none;">

                    <i class="bi bi-briefcase-fill me-1"></i>

                    Trabajadores ({{ $personas->filter(fn($p) => !is_null($p->idDepartamento) && is_null($p->fecha_deshabilitacion))->count() }})

                </button>

            </li>

            <li class="nav-item" role="presentation">

                <button class="nav-link fw-semibold rounded-top-3 text-white-50"
                        id="remitentes-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#remitentes"
                        type="button"
                        role="tab"
                        style="border: none;">

                    <i class="bi bi-send-fill me-1"></i>

                    Remitentes ({{ $personas->filter(fn($p) => is_null($p->idDepartamento) && is_null($p->fecha_deshabilitacion))->count() }})

                </button>

            </li>

        </ul>

        <div class="tab-content card-body">

            {{-- TAB: TRABAJADORES --}}
            <div class="tab-pane fade show active" id="trabajadores" role="tabpanel">

                <h5 class="mb-4">Personal que trabaja en la institución</h5>

                {{-- FILTROS --}}
                <div class="rounded-4 border bg-light p-3 mb-4">

                    <div class="row g-2 g-md-3 align-items-end">

                        <div class="col-md-2">

                            <label class="form-label small text-muted mb-0">CI</label>

                            <input type="text"
                                   class="form-control form-control-sm rounded-3"
                                   id="filtro-ci"
                                   placeholder="Filtrar por CI">

                        </div>

                        <div class="col-md-2">

                            <label class="form-label small text-muted mb-0">Nombre</label>

                            <input type="text"
                                   class="form-control form-control-sm rounded-3"
                                   id="filtro-nombre"
                                   placeholder="Nombre">

                        </div>

                        <div class="col-md-2">

                            <label class="form-label small text-muted mb-0">Departamento</label>

                            <select class="form-select form-select-sm rounded-3"
                                    id="filtro-departamento">

                                <option value="">Todos</option>

                                @foreach($personas->pluck('departamento')->filter()->unique('idDepartamento') as $depto)

                                    <option value="{{ $depto->idDepartamento }}">{{ $depto->nombre }}</option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2">

                            <label class="form-label small text-muted mb-0">Celular</label>

                            <input type="text"
                                   class="form-control form-control-sm rounded-3"
                                   id="filtro-celular"
                                   placeholder="Celular">

                        </div>

                        <div class="col-md-2">

                            <label class="form-label small text-muted mb-0">Cargo</label>

                            <select class="form-select form-select-sm rounded-3"
                                    id="filtro-cargo">

                                <option value="">Todos</option>

                                @foreach($personas->pluck('cargo')->filter()->unique('idCargo') as $cargo)

                                    <option value="{{ $cargo->idCargo }}">{{ $cargo->nombre }}</option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2">

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-3 w-100"
                                    id="btn-limpiar-filtros">

                                <i class="bi bi-arrow-clockwise me-1"></i>

                                Limpiar

                            </button>

                        </div>

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
                                    <td>{{ $persona->cargo?->nombre ?? 'N/A' }}</td>
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

                                        <div class="d-flex justify-content-center gap-2 flex-wrap">

                                            <a href="{{ route('admin.personas.edit', $persona->idPersona) }}"
                                               class="btn btn-sm btn-doc btn-doc-edit"
                                               title="Editar">

                                                <i class="bi bi-pencil-fill"></i>

                                            </a>

                                            <form action="{{ route('admin.personas.toggle', $persona->idPersona) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Confirmar cambio de estado?');">

                                                @csrf
                                                @method('PUT')

                                                <button type="submit"
                                                        class="btn btn-sm btn-doc {{ $persona->activo ? 'btn-doc-archive' : 'btn-doc-restore' }}"
                                                        title="{{ $persona->activo ? 'Desactivar' : 'Activar' }}">

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
                                    <td>{{ $persona->cargo?->nombre ?? 'N/A' }}</td>
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
                                           class="btn btn-sm btn-doc btn-doc-edit"
                                           title="Editar">

                                            <i class="bi bi-pencil-fill"></i>

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
            <div class="d-flex justify-content-center mt-4">
    {{ $personas->links() }}
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