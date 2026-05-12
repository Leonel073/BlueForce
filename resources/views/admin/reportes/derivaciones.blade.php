@extends('layouts.app')

@section('title', 'Reporte de Derivaciones')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-arrow-left-right"></i>

                Reporte de Derivaciones

            </h1>

            <p class="text-light mb-0">

                Seguimiento y trazabilidad documental

            </p>

        </div>

    </div>
    {{-- FILTROS --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-body">

        <form method="GET"
              action="{{ route('admin.reportes.derivaciones') }}">

            <div class="row">

                {{-- DOCUMENTO --}}
                <div class="col-md-3 mb-3">

                    <label class="form-label fw-semibold">

                        Documento

                    </label>

                    <input type="text"
                           name="documento"
                           class="form-control rounded-3"
                           placeholder="Cite o asunto"
                           value="{{ request('documento') }}">

                </div>

                {{-- ORIGEN --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">

                        Origen

                    </label>

                    <select name="origen"
                            class="form-select rounded-3">

                        <option value="">

                            Todos

                        </option>

                        @foreach($departamentos as $dep)

                            <option value="{{ $dep->idDepartamento }}"
                                {{ request('origen') == $dep->idDepartamento ? 'selected' : '' }}>

                                {{ $dep->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- DESTINO --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">

                        Destino

                    </label>

                    <select name="destino"
                            class="form-select rounded-3">

                        <option value="">

                            Todos

                        </option>

                        @foreach($departamentos as $dep)

                            <option value="{{ $dep->idDepartamento }}"
                                {{ request('destino') == $dep->idDepartamento ? 'selected' : '' }}>

                                {{ $dep->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- FECHA INICIO --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">

                        Desde

                    </label>

                    <input type="date"
                           name="fecha_inicio"
                           class="form-control rounded-3"
                           value="{{ request('fecha_inicio') }}">

                </div>

                {{-- FECHA FIN --}}
                <div class="col-md-2 mb-3">

                    <label class="form-label fw-semibold">

                        Hasta

                    </label>

                    <input type="date"
                           name="fecha_fin"
                           class="form-control rounded-3"
                           value="{{ request('fecha_fin') }}">

                </div>

                {{-- BOTÓN --}}
                <div class="col-md-1 d-flex align-items-end mb-3">

                    <button type="submit"
                            class="btn text-white w-100"
                            style="background-color:#0B2D59;">

                        <i class="bi bi-search"></i>

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

    {{-- TABLA --}}
    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">
        {{-- BOTÓN EXPORTAR EN LA CABECERA --}}
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">Resultados de la búsqueda</h5>
            <a href="{{ route('admin.reportes.derivaciones.pdf', request()->query()) }}" class="btn btn-danger shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i> Exportar PDF
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark" style="--bs-table-bg: #010303;">
                        <tr>
                            <th class="text-white">Documento</th>
                            <th class="text-white">Origen</th>
                            <th class="text-white">Destino</th>
                            <th class="text-white">Fecha Envío</th>
                            <th class="text-white">Instrucción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($derivaciones as $derivacion)
                            <tr>
                                {{-- DOCUMENTO --}}
                                <td>
                                    <div class="fw-bold">{{ $derivacion->documento->cite ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $derivacion->documento->asunto ?? '' }}</small>
                                </td>
                                {{-- ORIGEN --}}
                                <td>
                                    <span class="badge bg-primary">{{ $derivacion->departamentoOrigen->nombre ?? 'N/A' }}</span>
                                </td>
                                {{-- DESTINO --}}
                                <td>
                                    <span class="badge bg-success">{{ $derivacion->departamentoDestino->nombre ?? 'N/A' }}</span>
                                </td>
                                {{-- FECHA --}}
                                <td>{{ $derivacion->fechaEnvio }}</td>
                                {{-- INSTRUCCIÓN --}}
                                <td>{{ $derivacion->instruccion ?? 'Sin instrucción' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    No existen derivaciones registradas.
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