@extends('layouts.app')

@section('title', 'Reporte de Usuarios')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-people-fill"></i>

                Reporte de Usuarios

            </h1>

            <p class="text-light mb-0">

                Actividad documental por usuario

            </p>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead style="background-color:#0B2D59;">

                        <tr>

                            <th class="text-white">

                                Usuario

                            </th>

                            <th class="text-white">

                                Correo

                            </th>

                            <th class="text-white">

                                Documentos Registrados

                            </th>

                            <th class="text-white">

                                Estado

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($usuarios as $usuario)

                            <tr>

                                <td>

                                    <span class="fw-bold">

                                        {{ $usuario->name }}

                                    </span>

                                </td>

                                <td>

                                    {{ $usuario->email }}

                                </td>

                                <td>

                                    <span class="badge bg-primary">

                                        {{ $usuario->correspondencias_count }}

                                    </span>

                                </td>

                                <td>

                                    @if($usuario->activo)

                                        <span class="badge bg-success">

                                            Activo

                                        </span>

                                    @else

                                        <span class="badge bg-danger">

                                            Inactivo

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="text-center text-muted py-5">

                                    No existen usuarios registrados.

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