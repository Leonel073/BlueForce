@extends('layouts.app')

@section('title', 'Nuevo Anuncio')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background:linear-gradient(135deg,#071e3d,#0B2D59 48%,#2E608C);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-white">
                    <span class="badge rounded-pill mb-2" style="background:#D9A23D;color:#172033;">Comunicado institucional</span>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-megaphone-fill me-2"></i>
                        Nuevo Anuncio
                    </h2>
                    <p class="text-light mb-0">Se mostrara a usuarios y administradores al ingresar al sistema.</p>
                </div>
                <a href="{{ route('admin.anuncios.index') }}" class="btn btn-light rounded-3">
                    <i class="bi bi-chevron-left me-1"></i> Volver al historial
                </a>
            </div>
        </div>
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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4 p-3"
                         style="background:linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-pencil-square me-1"></i>
                        Contenido del anuncio
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Titulo <span class="text-danger">*</span>
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
                                Mensaje <span class="text-danger">*</span>
                            </label>
                            <textarea name="asunto" rows="8"
                                      class="form-control rounded-3 @error('asunto') is-invalid @enderror"
                                      placeholder="Escriba el comunicado que veran usuarios y administradores..."
                                      required>{{ old('asunto') }}</textarea>
                            @error('asunto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Use un texto claro, accionable y con fechas concretas si aplica.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4 p-3"
                         style="background:linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-paperclip me-1"></i>
                        Archivo adjunto
                        <small class="text-white-50">(opcional)</small>
                    </div>
                    <div class="card-body p-4">
                        <input type="file" name="archivo_pdf" id="archivo_pdf"
                               class="form-control rounded-3 @error('archivo_pdf') is-invalid @enderror"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        @error('archivo_pdf')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">PDF, Word o Excel. Maximo 10 MB.</small>

                        <div id="pdf-preview-container" class="d-none mt-3">
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
                            Cada cuenta vera el anuncio hasta marcarlo como visto. El registro de lectura queda guardado por usuario.
                        </div>
                        <button type="submit"
                                class="btn text-white w-100 rounded-3 py-2"
                                style="background:linear-gradient(135deg,#D9A23D,#b07d1a);border:none;">
                            <i class="bi bi-send-fill me-1"></i>
                            Publicar anuncio
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
