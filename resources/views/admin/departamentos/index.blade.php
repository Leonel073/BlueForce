@extends('layouts.app')

@section('title', 'Departamentos')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h1 class="fw-bold text-white mb-1">

                        <i class="bi bi-building-fill"></i>

                        Gestión de Departamentos

                    </h1>

                    <p class="text-light mb-0">

                        Administración institucional de departamentos

                    </p>

                </div>

                <div>

                    <a href="{{ route('admin.departamentos.create') }}"
                       class="btn btn-light rounded-4 px-4">

                        <i class="bi bi-plus-circle-fill"></i>

                        Nuevo Departamento

                    </a>

                </div>

            </div>

        </div>

    </div>

    {{-- ALERTAS --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show rounded-4">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif
    {{-- FILTROS --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-body">

        <form method="GET"
              action="{{ route('admin.departamentos.index') }}">

            <div class="row g-3 align-items-end">

                {{-- BUSCAR --}}
                <div class="col-md-4">

                    <label class="form-label fw-semibold">

                        Buscar Departamento

                    </label>

                    <input type="text"
                           name="buscar"
                           class="form-control"
                           placeholder="Ej: Sistemas"
                           value="{{ request('buscar') }}">

                </div>

                {{-- ESTADO --}}
                <div class="col-md-3">

                    <label class="form-label fw-semibold">

                        Estado

                    </label>

                    <select name="estado"
                            class="form-select">

                        <option value="">

                            Todos

                        </option>

                        <option value="1"
                            {{ request('estado') === '1' ? 'selected' : '' }}>

                            Activos

                        </option>

                        <option value="0"
                            {{ request('estado') === '0' ? 'selected' : '' }}>

                            Inactivos

                        </option>

                    </select>

                </div>

                {{-- ORDEN --}}
                <div class="col-md-3">

                    <label class="form-label fw-semibold">

                        Ordenar

                    </label>

                    <select name="orden"
                            class="form-select">

                        <option value="">

                            Más recientes

                        </option>

                        <option value="az"
                            {{ request('orden') == 'az' ? 'selected' : '' }}>

                            Nombre A-Z

                        </option>

                        <option value="za"
                            {{ request('orden') == 'za' ? 'selected' : '' }}>

                            Nombre Z-A

                        </option>

                    </select>

                </div>

                {{-- BOTONES --}}
                <div class="col-md-2">

                    <div class="d-grid gap-2">

                        <button type="submit"
                                class="btn text-white rounded-4"
                                style="background-color:#0B2D59;">

                            <i class="bi bi-search"></i>

                            Filtrar

                        </button>

                        <a href="{{ route('admin.departamentos.index') }}"
                           class="btn btn-secondary rounded-4">

                            Limpiar

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>
    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            <i class="bi bi-diagram-3-fill"></i>

            Departamentos Registrados

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>ID</th>

                            <th>Departamento</th>

                           
                            <th>Encargado</th>

                            <th>Estado</th>

                            <th class="text-center">

                                Acciones

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($departamentos as $departamento)

                            <tr>

                                {{-- ID --}}
                                <td>

                                    <span class="fw-bold">

                                        #{{ $departamento->idDepartamento }}

                                    </span>

                                </td>

                                {{-- NOMBRE --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $departamento->nombre }}

                                    </div>

                                </td>

                             

                                {{-- ENCARGADO --}}
                                <td>

                                    @if($departamento->encargado)

                                        <div class="fw-semibold">

                                            {{ $departamento->encargado->nombre }}

                                        </div>

                                        <small class="text-muted">

                                            CI:
                                            {{ $departamento->encargado->ci }}

                                        </small>

                                    @else

                                        <span class="badge bg-warning text-dark rounded-pill">

                                            Sin asignar

                                        </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td>

                                    @if($departamento->activo)

                                        <span class="badge bg-success rounded-pill">

                                            Activo

                                        </span>

                                    @else

                                        <span class="badge bg-danger rounded-pill">

                                            Inactivo

                                        </span>

                                    @endif

                                </td>

                                {{-- ACCIONES --}}
                                <td class="text-center">

                                    <div class="btn-group">

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.departamentos.edit', $departamento->idDepartamento) }}"
                                           class="btn btn-sm btn-warning text-dark"
                                           title="Editar">

                                            <i class="bi bi-pencil-fill"></i>

                                        </a>

                                        {{-- ACTIVAR / DESACTIVAR --}}
                                        <form action="{{ route('admin.departamentos.toggle', $departamento->idDepartamento) }}"
                                              method="POST">

                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                    class="btn btn-sm {{ $departamento->activo ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $departamento->activo ? 'Desactivar' : 'Activar' }}">

                                                <i class="bi {{ $departamento->activo ? 'bi-x-circle-fill' : 'bi-check-circle-fill' }}"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-building fs-1 d-block mb-3"></i>

                                        No existen departamentos registrados.

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-center mt-3">

                {{ $departamentos->links() }}

            </div>

        </div>

    </div>

</div>

@endsection