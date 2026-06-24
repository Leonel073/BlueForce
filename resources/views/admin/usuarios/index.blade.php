@extends('layouts.app')

@section('title', 'Usuarios Registrados')

@section('content')

<div class="container-fluid py-4">

    {{-- ENCABEZADO --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <h1 class="fw-bold text-white mb-1">

                    <i class="bi bi-people-fill"></i>

                    Usuarios Registrados

                </h1>

                <p class="text-light mb-0">

                    Administración y seguimiento de usuarios del sistema

                </p>

            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">

                <span class="badge rounded-pill px-4 py-3"
                      style="background-color: #D9A23D;
                             color: #0B2D59;">
                    Total Usuarios:
                    {{ $usuarios->total() }}
                </span>

                <a href="{{ route('admin.usuarios.create') }}"
                   class="btn btn-light fw-semibold rounded-3">
                    <i class="bi bi-person-plus-fill me-1"></i>
                    Nuevo Usuario
                </a>

            </div>

        </div>

    </div>

    {{-- FILTROS Y BUSCADOR --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.usuarios') }}">

                <div class="row g-3 align-items-center">

                    {{-- BUSCADOR --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            Buscar Usuario

                        </label>

                        <input type="text"
                               name="buscar"
                               class="form-control rounded-3"
                               placeholder="Nombre o correo..."
                               value="{{ request('buscar') }}">

                    </div>

                    {{-- FILTRO ROL --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Rol

                        </label>

                        <select name="rol"
                                class="form-select rounded-3">

                            <option value="">
                                Todos los roles
                            </option>

                            <option value="1"
                                {{ request('rol') == 1 ? 'selected' : '' }}>

                                Administrador

                            </option>

                            <option value="2"
                                {{ request('rol') == 2 ? 'selected' : '' }}>

                                Usuario

                            </option>

                        </select>

                    </div>

                    {{-- FILTRO ESTADO --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Estado

                        </label>

                        <select name="estado"
                                class="form-select rounded-3">

                            <option value="">
                                Todos
                            </option>

                            <option value="1"
                                {{ request('estado') == '1' ? 'selected' : '' }}>

                                Activos

                            </option>

                            <option value="0"
                                {{ request('estado') == '0' ? 'selected' : '' }}>

                                Inactivos

                            </option>

                        </select>

                    </div>

                    {{-- BOTÓN --}}
                    <div class="col-md-2 d-grid">

                        <label class="form-label invisible">
                            Buscar
                        </label>

                        <button type="submit"
                                class="btn rounded-3 fw-semibold text-white"
                                style="background-color:#0B2D59;">

                            <i class="bi bi-search"></i>

                            Buscar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead style="background-color:#0B2D59;">

                        <tr>

                            <th class="text-dark">#</th>

                            <th class="text-dark">Usuario</th>

                            <th class="text-dark">Correo</th>

                            <th class="text-dark">Persona Vinculada</th>

                            <th class="text-dark">Rol</th>

                            <th class="text-dark text-center">Estado</th>

                            <th class="text-dark text-center">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($usuarios as $usuario)

                        <tr>

                            {{-- ID --}}
                            <td>

                                {{ $usuario->id }}

                            </td>

                            {{-- NOMBRE --}}
                            <td>

                                <div class="fw-bold">

                                    {{ $usuario->name }}

                                </div>

                            </td>

                            {{-- EMAIL --}}
                            <td>

                                {{ $usuario->email }}

                            </td>

                            {{-- PERSONA VINCULADA --}}
                            <td>
                                @if($usuario->persona)
                                    <div class="small fw-semibold">{{ $usuario->persona->nombre }}</div>
                                    <div class="small text-muted">CI: {{ $usuario->persona->ci ?? 'N/A' }}</div>
                                    @if($usuario->persona->departamento)
                                        <div class="small text-muted">
                                            <i class="bi bi-building me-1"></i>{{ $usuario->persona->departamento->nombre }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Sin persona</span>
                                @endif
                            </td>

                            {{-- ROL --}}
                            <td>

                                @if($usuario->idRol == 1)

                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background-color:#D9A23D;
                                                 color:#0B2D59;">

                                        Administrador

                                    </span>

                                @else

                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background-color:#2E608C;">

                                        Usuario

                                    </span>

                                @endif

                            </td>

                            {{-- ESTADO --}}
                            <td class="text-center">

                                @if($usuario->activo)

                                    <span class="badge bg-success px-3 py-2">

                                        Activo

                                    </span>

                                @else

                                    <span class="badge bg-danger px-3 py-2">

                                        Inactivo

                                    </span>

                                @endif

                            </td>

                            {{-- ACCIONES --}}
                            <td>

                                <div class="d-flex flex-wrap gap-2 justify-content-center">

                                    {{-- VER --}}
                                    <a href="{{ route('admin.usuarios.show', $usuario->id) }}"
                                       class="btn btn-sm rounded-3 fw-semibold"
                                       style="background-color:#D9B13B;
                                              color:#0B2D59;">

                                        <i class="bi bi-eye-fill"></i>

                                        Ver

                                    </a>

                                    {{-- EDITAR --}}
                                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                                       class="btn btn-sm text-white rounded-3"
                                       style="background-color:#2E608C;">

                                        <i class="bi bi-pencil-fill"></i>

                                        Editar

                                    </a>

                                    {{-- ACTIVAR / DESACTIVAR --}}
                                    <form action="{{ route('admin.usuarios.toggle', $usuario->id) }}"
                                          method="POST">

                                        @csrf
                                        @method('PUT')

                                        @if($usuario->activo)

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger rounded-3">

                                                <i class="bi bi-person-x-fill"></i>

                                                Desactivar

                                            </button>

                                        @else

                                            <button type="submit"
                                                    class="btn btn-sm btn-success rounded-3">

                                                <i class="bi bi-person-check-fill"></i>

                                                Activar

                                            </button>

                                        @endif

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="7"
                                class="text-center py-5 text-muted">

                                <i class="bi bi-exclamation-circle fs-3"></i>

                                <br>

                                No se encontraron usuarios registrados.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-4 d-flex justify-content-center">

                {{ $usuarios->links() }}

            </div>

        </div>

    </div>

</div>

@endsection