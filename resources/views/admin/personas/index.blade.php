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
                        Administración de personas internas y externas del sistema
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

    {{-- BÚSQUEDA GLOBAL --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.personas.index') }}" class="d-flex gap-2">
                <div class="flex-grow-1">
                    <input type="text" 
                           name="q" 
                           class="form-control rounded-3" 
                           placeholder="Buscar por nombre, CI o correo..."
                           value="{{ $search }}"
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary rounded-3">
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('admin.personas.index') }}" class="btn btn-outline-secondary rounded-3">
                        <i class="bi bi-x-circle me-1"></i>
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- TABS CON CONTADORES --}}
    <div class="card border-0 shadow-lg rounded-4">

        <ul class="nav nav-tabs card-header border-0 rounded-top-4 px-3 pt-3 gap-2"
            role="tablist"
            style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

            {{-- TAB: PERSONAS INTERNAS --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold rounded-top-3 text-white {{ $tab === 'internas' ? 'active' : '' }}"
                        id="internas-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#internas"
                        type="button"
                        role="tab"
                        style="border: none;">
                    <i class="bi bi-briefcase-fill me-1"></i>
                    Personas Internas
                    <span class="badge bg-info ms-2">{{ $totalInternas }}</span>
                </button>
            </li>

            {{-- TAB: PERSONAS EXTERNAS --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold rounded-top-3 {{ $tab === 'externas' ? 'active text-white' : 'text-white-50' }}"
                        id="externas-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#externas"
                        type="button"
                        role="tab"
                        style="border: none;">
                    <i class="bi bi-building me-1"></i>
                    Personas Externas
                    <span class="badge bg-warning text-dark ms-2">{{ $totalExternas }}</span>
                </button>
            </li>

        </ul>

        <div class="tab-content card-body">

            {{-- TAB CONTENT: INTERNAS --}}
            <div class="tab-pane fade {{ $tab === 'internas' ? 'show active' : '' }}" id="internas" role="tabpanel">

                {{-- INFORMACIÓN --}}
                <div class="alert alert-info rounded-3 mb-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Personas Internas:</strong> Empleados y personal que trabaja en la institución.
                </div>

                @if($personasInternas->count() > 0)

                    {{-- TABLA INTERNAS --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Correo</th>
                                    <th>Institución</th>
                                    <th>Estado</th>
                                    <th class="text-center" style="width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personasInternas as $persona)
                                    <tr>
                                        <td>
                                            <strong>{{ $persona->nombre }}</strong>
                                        </td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $persona->ci ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            @if($persona->correo)
                                                <a href="mailto:{{ $persona->correo }}" class="text-decoration-none">
                                                    {{ $persona->correo }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($persona->institucion)
                                                {{ $persona->institucion }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_null($persona->fecha_deshabilitacion))
                                                <span class="badge bg-success">
                                                    <i class="bi bi-circle-fill"></i>
                                                    Activo
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-circle-fill"></i>
                                                    Deshabilitado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.personas.edit', $persona->idPersona) }}"
                                                   class="btn btn-outline-primary"
                                                   title="Editar"
                                                   data-bs-toggle="tooltip">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <form action="{{ route('admin.personas.toggle', $persona->idPersona) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Confirmar cambio de estado?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="btn {{ is_null($persona->fecha_deshabilitacion) ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                            title="{{ is_null($persona->fecha_deshabilitacion) ? 'Desactivar' : 'Activar' }}"
                                                            data-bs-toggle="tooltip">
                                                        <i class="bi {{ is_null($persona->fecha_deshabilitacion) ? 'bi-lock' : 'bi-unlock' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINACIÓN INTERNAS --}}
                    <nav class="d-flex justify-content-center mt-4" aria-label="Paginación">
                        {{ $personasInternas->links('pagination::bootstrap-4') }}
                    </nav>

                @else

                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">
                            @if($search)
                                No se encontraron personas internas que coincidan con "<strong>{{ $search }}</strong>"
                            @else
                                No hay personas internas registradas
                            @endif
                        </p>
                    </div>

                @endif

            </div>

            {{-- TAB CONTENT: EXTERNAS --}}
            <div class="tab-pane fade {{ $tab === 'externas' ? 'show active' : '' }}" id="externas" role="tabpanel">

                {{-- INFORMACIÓN --}}
                <div class="alert alert-warning rounded-3 mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Personas Externas:</strong> Remitentes, ciudadanos o entidades externas a la institución.
                </div>

                @if($personasExternas->count() > 0)

                    {{-- TABLA EXTERNAS --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Institución</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th class="text-center" style="width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personasExternas as $persona)
                                    <tr>
                                        <td>
                                            <strong>{{ $persona->nombre }}</strong>
                                            <br>
                                            <span class="badge bg-warning text-dark">EXTERNO</span>
                                        </td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $persona->ci ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            @if($persona->institucion)
                                                {{ $persona->institucion }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($persona->correo)
                                                <a href="mailto:{{ $persona->correo }}" class="text-decoration-none">
                                                    {{ $persona->correo }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_null($persona->fecha_deshabilitacion))
                                                <span class="badge bg-success">
                                                    <i class="bi bi-circle-fill"></i>
                                                    Activo
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-circle-fill"></i>
                                                    Deshabilitado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.personas.edit', $persona->idPersona) }}"
                                                   class="btn btn-outline-primary"
                                                   title="Editar"
                                                   data-bs-toggle="tooltip">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <form action="{{ route('admin.personas.toggle', $persona->idPersona) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Confirmar cambio de estado?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="btn {{ is_null($persona->fecha_deshabilitacion) ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                            title="{{ is_null($persona->fecha_deshabilitacion) ? 'Desactivar' : 'Activar' }}"
                                                            data-bs-toggle="tooltip">
                                                        <i class="bi {{ is_null($persona->fecha_deshabilitacion) ? 'bi-lock' : 'bi-unlock' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINACIÓN EXTERNAS --}}
                    <nav class="d-flex justify-content-center mt-4" aria-label="Paginación">
                        {{ $personasExternas->links('pagination::bootstrap-4') }}
                    </nav>

                @else

                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">
                            @if($search)
                                No se encontraron personas externas que coincidan con "<strong>{{ $search }}</strong>"
                            @else
                                No hay personas externas registradas
                            @endif
                        </p>
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const tab = new URLSearchParams(window.location.search).get('tab');
        if (tab) {
            const tabElement = document.querySelector(`#${tab}-tab`);
            if (tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        }
    });
</script>

<style>
    @media (max-width: 768px) {
        .table {
            font-size: 0.85rem;
        }
        
        .btn-group-sm {
            gap: 0.25rem;
        }
        
        .btn-group-sm > .btn {
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    .badge {
        font-weight: 500;
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }

    .table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }

    .table tbody tr:hover {
        background-color: rgba(11, 45, 89, 0.05);
    }

    .table a {
        color: #0B2D59;
        font-weight: 500;
    }

    .table a:hover {
        text-decoration: underline !important;
    }
</style>

@endsection



