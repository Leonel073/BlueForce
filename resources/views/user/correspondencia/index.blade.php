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

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> 
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- MENSAJE DE ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> 
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!--  HEADER -->
    <div class=" text-mi-azul d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold">Gestión de Documentos</h3>
            <small class="text-muted">Administra todos los documentos del sistema</small>
        </div>

        <a href="{{ route('documentos.crear') }}" class="btn btn-mi-amarillo shadow text-mi-azul">
            Subir Documento
        </a>
    </div>

    <!--  BUSCADOR -->
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder=" Buscar documentos...">

            <select name="estado" class="form-select w-auto">
                <option value="">Estado</option>
                <option value="Recibido">Recibido</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Finalizado">Finalizado</option>
            </select>

            <button class="btn btn-outline-primary">Filtros</button>
        </div>
    </div>

    <!-- tarjetas -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold">0</h2>
                <small>Total Documentos</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold text-success">0</h2>
                <small>Aprobados</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold text-warning">0</h2>
                <small>Vigentes</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h2 class="fw-bold text-danger">0</h2>
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
                        <th>Código de Ruta</th>
                        <th>Asunto</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Urgencia</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($documentos as $doc)
                        <tr>
                            <td><strong>{{ $doc->cite }}</strong></td>
                            <td>{{ $doc->asunto }}</td>
                            <td>{{ $doc->tipoDocumento->nombre ?? '-' }}</td>
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
                            <td>
                                <a href="#" class="btn btn-sm btn-mi-azul">Ver</a>
                                <a href="#" class="btn btn-sm btn-mi-amarillo">Editar</a>
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
