@extends('layouts.app')

@section('title', 'Anuncios')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="fw-bold text-white mb-2">
                        <i class="bi bi-megaphone-fill me-2"></i>
                        Gestión de Anuncios
                    </h1>
                    <p class="text-light mb-0">
                        Historial de anuncios enviados a todos los usuarios del sistema
                    </p>
                </div>
                <a href="{{ route('admin.anuncios.create') }}"
                   class="btn text-white rounded-4 px-4 shadow-sm mt-3 mt-md-0"
                   style="background: linear-gradient(135deg,#D9A23D,#BF8A2E); border:none;">
                    <i class="bi bi-plus-circle-fill me-1"></i>
                    Nuevo Anuncio
                </a>
            </div>
        </div>
    </div>

    {{-- TABLA HISTORIAL --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header border-0 rounded-top-4 text-white p-4"
             style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2"></i>
                Historial de Anuncios
            </h5>
        </div>
        <div class="card-body p-4">
            @if($anuncios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Fecha</th>
                                <th>Creado por</th>
                                <th class="text-center">Vistas</th>
                                <th class="text-center">PDF</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anuncios as $anuncio)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $anuncio->titulo }}</span>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($anuncio->asunto, 60) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $anuncio->fechaCreacion->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                    <td>{{ $anuncio->creador->name ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill"
                                              style="background-color:#D9A23D; color:#0B2D59;">
                                            {{ $anuncio->vistas_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($anuncio->tienePdf())
                                            <i class="bi {{ $anuncio->archivoIcono() }} fs-5"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($anuncio->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.anuncios.show', $anuncio->idAnuncio) }}"
                                               class="btn btn-sm btn-outline-primary rounded-3"
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.anuncios.toggle', $anuncio->idAnuncio) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-warning rounded-3"
                                                        title="{{ $anuncio->activo ? 'Desactivar' : 'Activar' }}">
                                                    <i class="bi bi-{{ $anuncio->activo ? 'pause' : 'play' }}-fill"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.anuncios.destroy', $anuncio->idAnuncio) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar este anuncio permanentemente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-3"
                                                        title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $anuncios->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-megaphone text-muted" style="font-size:3rem;"></i>
                    <p class="text-muted mt-3 mb-4">No hay anuncios publicados aún.</p>
                    <a href="{{ route('admin.anuncios.create') }}"
                       class="btn text-white rounded-4 px-4"
                       style="background: linear-gradient(135deg,#D9A23D,#BF8A2E); border:none;">
                        <i class="bi bi-plus-circle-fill me-1"></i>
                        Publicar primer anuncio
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
