@extends('layouts.app')

@section('title', 'Personas')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-person-vcard-fill"></i>

                Gestión de Personas

            </h1>

            <p class="text-light mb-0">

                Administración institucional de personas registradas

            </p>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Personas Registradas

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>CI</th>

                            <th>Nombre</th>

                            <th>Celular</th>

                            <th>Correo</th>

                            <th>Cargo</th>

                            <th>Departamento</th>

                            <th>Tipo</th>

                            <th>Estado</th>

                            <th class="text-center">

                                Acciones

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($personas as $persona)

                            <tr>

                                <td>

                                    {{ $persona->ci }}

                                </td>

                                <td class="fw-semibold">

                                    {{ $persona->nombre }}

                                </td>

                                <td>

                                    {{ $persona->telefono_celular ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $persona->correo ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $persona->cargo ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $persona->departamento->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    <span class="badge bg-primary">

                                        {{ $persona->tipo }}

                                    </span>

                                </td>

                                <td>

                                    @if($persona->activo)

                                        <span class="badge bg-success">

                                            Activo

                                        </span>

                                    @else

                                        <span class="badge bg-danger">

                                            Inactivo

                                        </span>

                                    @endif

                                </td>

                                <td class="text-center">

                                    <div class="btn-group">

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.personas.edit', $persona->idPersona) }}"
                                           class="btn btn-sm btn-warning">

                                            <i class="bi bi-pencil-fill"></i>

                                        </a>

                                        {{-- TOGGLE --}}
                                        <form action="{{ route('admin.personas.toggle', $persona->idPersona) }}"
                                              method="POST">

                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                    class="btn btn-sm {{ $persona->activo ? 'btn-danger' : 'btn-success' }}">

                                                <i class="bi {{ $persona->activo ? 'bi-x-circle-fill' : 'bi-check-circle-fill' }}"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center text-muted py-5">

                                    No existen personas registradas.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection