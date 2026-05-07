{{-- resources/views/admin/correspondencia/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Administración de Correspondencia')

@section('content')

<div class="container-fluid py-4">

    {{-- ENCABEZADO --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <h1 class="fw-bold text-white mb-1">

                    <i class="bi bi-folder-fill"></i>

                    Administración de Correspondencia

                </h1>

                <p class="text-light mb-0">

                    Control global documental del sistema

                </p>

            </div>

            <div>

                <span class="badge rounded-pill px-4 py-3"
                      style="background-color:#D9A23D; color:#0B2D59;">

                    Total: {{ $totalDocumentos }}

                </span>

            </div>

        </div>

    </div>


    {{-- ESTADÍSTICAS --}}
    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body text-center">

                    <h2 class="fw-bold"
                        style="color:#0B2D59;">

                        {{ $totalDocumentos }}

                    </h2>

                    <p class="text-muted mb-0">

                        Total Documentos

                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body text-center">

                    <h2 class="fw-bold text-danger">

                        {{ $totalUrgentes }}

                    </h2>

                    <p class="text-muted mb-0">

                        Urgentes

                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body text-center">

                    <h2 class="fw-bold text-warning">

                        {{ $totalRevision }}

                    </h2>

                    <p class="text-muted mb-0">

                        En Revisión

                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.correspondencia') }}">

                <div class="row g-3">

                    {{-- BUSCADOR --}}
                    <div class="col-md-3">

                        <input type="text"
                               name="buscar"
                               class="form-control rounded-3"
                               placeholder="Buscar cite o asunto..."
                               value="{{ request('buscar') }}">

                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-2">

                        <select name="estado"
                                class="form-select rounded-3">

                            <option value="">

                                Estado

                            </option>

                            @foreach($estados as $estado)

                                <option value="{{ $estado->idEstado }}"
                                    {{ request('estado') == $estado->idEstado ? 'selected' : '' }}>

                                    {{ $estado->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- URGENCIA --}}
                    <div class="col-md-2">

                        <select name="urgencia"
                                class="form-select rounded-3">

                            <option value="">

                                Urgencia

                            </option>

                            @foreach($urgencias as $urgencia)

                                <option value="{{ $urgencia->idUrgencia }}"
                                    {{ request('urgencia') == $urgencia->idUrgencia ? 'selected' : '' }}>

                                    {{ $urgencia->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- USUARIO --}}
                    <div class="col-md-2">

                        <select name="usuario"
                                class="form-select rounded-3">

                            <option value="">

                                Usuario

                            </option>

                            @foreach($usuarios as $usuario)

                                <option value="{{ $usuario->id }}"
                                    {{ request('usuario') == $usuario->id ? 'selected' : '' }}>

                                    {{ $usuario->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- FECHA INICIO --}}
                    <div class="col-md-1">

                        <input type="date"
                               name="fecha_inicio"
                               class="form-control rounded-3"
                               value="{{ request('fecha_inicio') }}">

                    </div>

                    {{-- FECHA FIN --}}
                    <div class="col-md-1">

                        <input type="date"
                               name="fecha_fin"
                               class="form-control rounded-3"
                               value="{{ request('fecha_fin') }}">

                    </div>

                    {{-- BOTÓN --}}
                    <div class="col-md-1 d-grid">

                        <button type="submit"
                                class="btn text-white rounded-3"
                                style="background-color:#0B2D59;">

                            <i class="bi bi-search"></i>

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

                            <th class="text-dark">Cite</th>

                            <th class="text-dark">Asunto</th>

                            <th class="text-dark">Usuario</th>

                            <th class="text-dark">Estado</th>

                            <th class="text-dark">Urgencia</th>

                            <th class="text-dark">Fecha</th>

                            <th class="text-dark">Seguimiento</th>

                            <th class="text-dark text-center">
                                Acciones
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($documentos as $doc)

                        <tr>

                            {{-- CITE --}}
                            <td>

                                <span class="fw-bold">

                                    {{ $doc->cite }}

                                </span>

                            </td>

                            {{-- ASUNTO --}}
                            <td>

                                {{ $doc->asunto }}

                            </td>

                            {{-- USUARIO --}}
                            <td>

                                {{ $doc->usuario->name ?? 'Sin usuario' }}

                            </td>

                            {{-- ESTADO --}}
                            <td>

                                <span class="badge bg-success">

                                    {{ $doc->estado->nombre ?? 'Sin estado' }}

                                </span>

                            </td>

                            {{-- URGENCIA --}}
                            <td>

                                <span class="badge"
                                      style="background-color:#D9A23D;
                                             color:#0B2D59;">

                                    {{ $doc->urgencia->nombre ?? 'Normal' }}

                                </span>

                            </td>

                            {{-- FECHA --}}
                            <td>

                                {{ $doc->fecha }}

                            </td>

                            {{-- SEGUIMIENTO --}}
                            <td>

                                @forelse($doc->seguimientos as $seg)

                                    <div class="mb-1">

                                        <span class="badge bg-primary">

                                            {{ $seg->ubicacion }}

                                        </span>

                                    </div>

                                @empty

                                    <span class="text-muted">

                                        Sin seguimiento

                                    </span>

                                @endforelse

                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center">

                              <a href="{{ route('admin.correspondencia.show', $doc->idDocumento) }}"
                                   class="btn btn-sm text-white rounded-3"
                                   style="background-color:#0B2D59;">

                                    <i class="bi bi-eye-fill"></i>

                                    Ver

                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="8"
                                class="text-center text-muted py-5">

                                No existen documentos registrados.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- PAGINACIÓN --}}
    <div class="mt-4">

        {{ $documentos->links() }}

    </div>

</div>

@endsection