@extends('layouts.app')

@section('title', 'Reporte por Departamentos')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-building-fill"></i>

                Reporte por Departamentos

            </h1>

            <p class="text-light mb-0">

                Flujo documental institucional por áreas

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

                            <th class="text-dark">

                                Departamento

                            </th>

                            <th class="text-dark">

                                Documentos Recibidos

                            </th>

                            <th class="text-dark">

                                Documentos Enviados

                            </th>

                            <th class="text-dark">

                                Movimiento Total

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($departamentos as $dep)

                            <tr>

                                {{-- NOMBRE --}}
                                <td>

                                    <span class="fw-bold">

                                        {{ $dep->nombre }}

                                    </span>

                                </td>

                                {{-- RECIBIDOS --}}
                                <td>

                                    <span class="badge bg-success">

                                        {{ $dep->recibidos }}

                                    </span>

                                </td>

                                {{-- ENVIADOS --}}
                                <td>

                                    <span class="badge bg-primary">

                                        {{ $dep->enviados }}

                                    </span>

                                </td>

                                {{-- TOTAL --}}
                                <td>

                                    <span class="badge"
                                          style="background-color:#D9A23D;
                                                 color:#0B2D59;">

                                        {{ $dep->recibidos + $dep->enviados }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="text-center text-muted py-5">

                                    No existen departamentos registrados.

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