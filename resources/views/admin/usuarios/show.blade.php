@extends('layouts.app')

@section('title', 'Actividades del Usuario')

@section('content')
<style>

.table tbody tr:hover{
    background-color: #f4f8fc;
    transition: 0.2s;
}

</style>
<a href="{{ route('admin.usuarios') }}"
   class="btn text-white rounded-3"
   style="background-color:#0B2D59;">

    <i class="bi bi-arrow-left-circle-fill"></i>

    Volver

</a>

<div class="container py-4">

    {{-- CABECERA --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body">

            <h2 class="text-white fw-bold mb-1">

                <i class="bi bi-person-circle"></i>

                {{ $usuario->name }}

            </h2>

            <p class="text-light mb-0">

                {{ $usuario->email }}

            </p>
            
        </div>
        
    </div>

    {{-- INFORMACIÓN --}}
    <div class="row">

        <div class="col-md-4">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body">

                    <h5 class="fw-bold mb-3"
                        style="color:#0B2D59;">

                        Información General

                    </h5>

                    <p>
                        <strong>ID:</strong>
                        {{ $usuario->id }}
                    </p>

                    <p>
                        <strong>Nombre:</strong>
                        {{ $usuario->name }}
                    </p>

                    <p>
                        <strong>Email:</strong>
                        {{ $usuario->email }}
                    </p>

                    <p>
                        <strong>Rol:</strong>
                        {{ $usuario->rol?->nombre ?? 'N/A' }}
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        @if($usuario->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>

                    {{-- PERSONA VINCULADA --}}
                    @if($usuario->persona)
                        <hr>
                        <h6 class="fw-bold" style="color:#0B2D59;">
                            <i class="bi bi-person-badge-fill me-1"></i>
                            Persona vinculada
                        </h6>
                        <p class="mb-1">
                            <strong>Nombre:</strong> {{ $usuario->persona->nombre }}
                        </p>
                        <p class="mb-1">
                            <strong>CI:</strong> {{ $usuario->persona->ci ?? 'N/A' }}
                        </p>
                        <p class="mb-1">
                            <strong>Cargo:</strong> {{ $usuario->persona->cargos_nombres ?? 'Sin cargo' }}
                        </p>
                        <p class="mb-1">
                            <strong>Departamento:</strong> {{ $usuario->persona->departamento?->nombre ?? 'Sin departamento' }}
                        </p>
                        <p class="mb-0">
                            <strong>Tipo:</strong>
                            <span class="badge {{ $usuario->persona->tipo === 'INTERNO' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $usuario->persona->tipo }}
                            </span>
                        </p>
                    @else
                        <hr>
                        <div class="alert alert-warning mb-0 p-2 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Usuario sin persona vinculada.
                        </div>
                    @endif

                    <hr>
                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                        class="btn text-white"
                        style="background-color:#D9A23D;">
                            <i class="bi bi-pencil-fill"></i>
                            Editar Usuario
                    </a>
                </div>

            </div>

        </div>

        <div class="col-md-8">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body">

                    <h5 class="fw-bold mb-3"
                        style="color:#0B2D59;">

                        Actividades del Usuario

                    </h5>

                    <div class="alert alert-info">

                       <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead style="background-color:#0B2D59;" >
                                    <tr>
                                        <th class="text-black">Cite</th>
                                        <th class="text-black">Asunto</th>
                                        <th class="text-black">Tipo</th>
                                        <th class="text-black">Fecha</th>
                                        <th class="text-black">Estado</th>
                                        <th class="text-black">Urgencia</th>
                                        <th class="text-black">Seguimiento</th>
                                        
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($usuario->correspondencias as $doc)

                                    <tr>

                                        <td>{{ $doc->cite }}</td>

                                        <td>{{ $doc->asunto }}</td>

                                        <td>
                                            {{ $doc->tipoDocumento->nombre ?? 'Sin tipo' }}
                                        </td>

                                        <td>{{ $doc->fecha }}</td>

                                        <td>
                                            <span class="badge bg-success">
                                                {{ $doc->estado->nombre ?? 'Sin estado' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge"
                                                style="background-color:#D9A23D; color:#0B2D59;">

                                                {{ $doc->urgencia->nombre ?? 'Normal' }}

                                            </span>
                                        </td>

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

                            
                         
                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No existen correspondencias registradas.
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div><!--fin alert info-->
                        

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection