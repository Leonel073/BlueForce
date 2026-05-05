@extends('layouts.app')

@section('title', 'Gestión de Documentos')

@section('content')

<style>
.bg-mi-fondo {
    background-color: #0b295b;
    color: white;
}

.btn-mi-amarillo {
    background-color: #d3af37;
    color: #0b295b;
    border: none;
}
.btn-mi-azul {
    background-color: #0b295b;
    color: white;
    border: none;
}

.btn-mi-verde:hover {
    background-color: #146c43;
}

.text-mi-azul {
    color:  #0b295b;
}
</style>


<div class="container-fluid">

    <!--  HEADER -->
    <div class=" text-mi-azul d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold">Gestión de Documentos</h3>
            <small class="text-muted">Administra todos los documentos del sistema</small>
        </div>

        <a href="{{ route('documento.create') }}" class="btn btn-mi-amarillo shadow text-mi-azul">
            Subir Documento
        </a>
    </div>

    <!--  BUSCADOR -->
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder=" Buscar documentos...">

            <select name="estado" class="form-select w-auto">
                <option value="">Estado</option>
                @foreach($estados as $e)
                    <option value="{{ $e->idEstado }}">
                        {{ $e->nombre }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-outline-primary">Filtros</button>
        </div>
    </div>

    <!-- tarjetas -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold">{{ $docs->count() }}</h2>
                <small>Total Documentos</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold text-success">
                    {{ $docs->where('estado.nombre', 'Aprobado')->count() }}
                </h2>
                <small>Aprobados</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold text-warning">
                    {{ $docs->where('estado.nombre', 'Vigente')->count() }}
                </h2>
                <small>Vigentes</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold text-danger">
                    {{ $docs->where('estado.nombre', 'Pendiente')->count() }}
                </h2>
                <small>En Revisión</small>
            </div>
        </div>

    </div>

    <!-- 📄 TABLA -->
    <div class="card shadow">

        <div class="card-header bg-mi-fondo text-white fw-bold">
            Biblioteca de Documentos
        </div>

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>CITE</th>
                        <th>Asunto</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Urgencia</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($docs as $doc)
                        <tr>

                            <td><strong>{{ $doc->cite }}</strong></td>

                            <td>{{ $doc->asunto }}</td>

                            <td>{{ $doc->tipo->nombre ?? '-' }}</td>

                            <td>
                                <span class="badge bg-{{ 
                                    ($doc->estado->nombre ?? '') == 'Pendiente' ? 'warning' : 
                                    (($doc->estado->nombre ?? '') == 'Finalizado' ? 'success' : 'secondary') 
                                }}">
                                    {{ $doc->estado->nombre ?? '-' }}
                                </span>
                            </td>

                            <td>{{ $doc->urgencia->nombre ?? '-' }}</td>

                            <td>{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
<!-- editar esto sigue teniendo errores-->
                            <td>
                                <a href="{{ route('documento.show', $doc->idDocumento) }}" 
                                   class="btn btn-sm btn-mi-azul">Ver</a>

                                <a href="{{ route('documento.edit', $doc->idDocumento) }}" 
                                   class="btn btn-sm btn-mi-amarillo">Editar</a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay documentos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>


@endsection