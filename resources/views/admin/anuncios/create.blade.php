@extends('layouts.app')

@section('title', 'Nuevo Anuncio')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-megaphone-fill me-2"></i>
                Nuevo Anuncio
            </h2>
            <p class="text-muted mb-0">El anuncio se mostrará a todos los usuarios al ingresar al sistema</p>
        </div>
        <a href="{{ route('admin.anuncios.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-chevron-left"></i> Volver al historial
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
            <strong>Error en el formulario</strong>
            <ul class="mb-0 ms-3 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.anuncios.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-pencil-square me-1"></i>
                        Contenido del Anuncio
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Título <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="titulo"
                                   class="form-control rounded-3 @error('titulo') is-invalid @enderror"
                                   value="{{ old('titulo') }}"
                                   placeholder="Ej: Mantenimiento programado del sistema"
                                   required>
                            @error('titulo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Anuncio <span class="text-danger">*</span>
                            </label>
                            <textarea name="asunto" rows="6"
                                      class="form-control rounded-3 @error('asunto') is-invalid @enderror"
                                      placeholder="Escriba el contenido del anuncio que verán los usuarios..."
                                      required>{{ old('asunto') }}</textarea>
                            @error('asunto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        PDF Adjunto
                        <small class="text-white-50">(Opcional)</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <input type="file" name="archivo_pdf" id="archivo_pdf"
                                   class="form-control rounded-3 @error('archivo_pdf') is-invalid @enderror"
                                   accept="application/pdf,.pdf">
                            @error('archivo_pdf')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Solo PDF, máximo 10 MB.</small>
                        </div>

                        <div id="pdf-preview-container" class="d-none">
                            <hr>
                            <p class="fw-semibold mb-2">
                                <i class="bi bi-eye me-1"></i> Vista previa
                            </p>
                            <iframe id="pdf-preview" class="w-100 rounded-3 border"
                                    style="height:300px;" title="Vista previa PDF"></iframe>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="alert alert-info rounded-3 mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Al publicar, todos los usuarios verán este anuncio como pantallazo al ingresar.
                        </div>
                        <button type="submit"
                                class="btn text-white w-100 rounded-4 py-2"
                                style="background: linear-gradient(135deg,#D9A23D,#BF8A2E); border:none;">
                            <i class="bi bi-send-fill me-1"></i>
                            Publicar Anuncio
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
document.getElementById('archivo_pdf').addEventListener('change', function(e) {
    const container = document.getElementById('pdf-preview-container');
    const iframe = document.getElementById('pdf-preview');
    const file = e.target.files[0];

    if (file && file.type === 'application/pdf') {
        const url = URL.createObjectURL(file);
        iframe.src = url;
        container.classList.remove('d-none');
    } else {
        iframe.src = '';
        container.classList.add('d-none');
    }
});
</script>

@endsection
