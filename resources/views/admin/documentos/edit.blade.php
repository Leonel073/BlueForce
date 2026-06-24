@extends('layouts.app')

@section('title', 'Editar Documento')

@section('content')

@php

    $documentoBloqueado =
        strtoupper($documento->estado->nombre ?? '') == 'ARCHIVADO'
        || strtoupper($documento->estado->nombre ?? '') == 'FINALIZADO';

@endphp

<div class="container py-4">

    {{-- ALERTA DOCUMENTO ARCHIVADO --}}
    @if($documentoBloqueado)

        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">

            <div class="d-flex align-items-start">

                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>

                <div>

                    <h5 class="fw-bold mb-1">

                        Documento archivado o finalizado

                    </h5>

                    <p class="mb-0">

                        Este documento se encuentra archivado o finalizado.
                        Para modificar su contenido primero debe cambiar
                        su estado documental.

                    </p>

                </div>

            </div>

        </div>

    @endif

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-pencil-square"></i>

                Editar Documento

            </h1>

            <p class="text-light mb-0">

                Corrección administrativa documental

            </p>

        </div>

    </div>

    {{-- ERRORES --}}
    @if($errors->any())

        <div class="alert alert-warning rounded-4">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- FORM --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Información del Documento

        </div>

        <div class="card-body">

            <form action="{{ route('admin.documentos.update', $documento->idDocumento) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- CITE --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Cite

                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $documento->cite }}"
                           disabled>

                </div>

                {{-- ASUNTO --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Asunto

                    </label>

                    <textarea name="asunto"
                              rows="4"
                              class="form-control"
                              required
                              {{ $documentoBloqueado ? 'disabled' : '' }}>{{ old('asunto', $documento->asunto) }}</textarea>

                </div>

                <div class="row">

                    {{-- REMITENTE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Remitente

                        </label>

                        <select name="idRemitente"
                                class="form-select"
                                required
                                {{ $documentoBloqueado ? 'disabled' : '' }}>

                            @foreach($personas as $persona)

                                <option value="{{ $persona->idPersona }}"
                                    {{ $documento->idRemitente == $persona->idPersona ? 'selected' : '' }}>

                                    {{ $persona->nombre }} - CI: {{ $persona->ci }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- TIPO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Tipo Documento

                        </label>

                        <select name="idTipoDocumento"
                                class="form-select"
                                required
                                {{ $documentoBloqueado ? 'disabled' : '' }}>

                            @foreach($tiposDocumento as $tipo)

                                <option value="{{ $tipo->idTipoDocumento }}"
                                    {{ $documento->idTipoDocumento == $tipo->idTipoDocumento ? 'selected' : '' }}>

                                    {{ $tipo->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="row">

                    {{-- URGENCIA --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Nivel Urgencia

                        </label>

                        <select name="idUrgencia"
                                class="form-select"
                                required
                                {{ $documentoBloqueado ? 'disabled' : '' }}>

                            @foreach($nivelesUrgencia as $urgencia)

                                <option value="{{ $urgencia->idUrgencia }}"
                                    {{ $documento->idUrgencia == $urgencia->idUrgencia ? 'selected' : '' }}>

                                    {{ $urgencia->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Estado

                        </label>

                        <select name="idEstado"
                                class="form-select"
                                required>

                            @foreach($estados as $estado)

                                <option value="{{ $estado->idEstado }}"
                                    {{ $documento->idEstado == $estado->idEstado ? 'selected' : '' }}>

                                    {{ $estado->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-3 mt-4">

                    <a href="{{ route('admin.documentos.index') }}"
                       class="btn btn-secondary rounded-4 px-4">

                        Volver

                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-4 px-4">

                        {{
                            $documentoBloqueado
                                ? 'Cambiar Estado'
                                : 'Guardar Cambios'
                        }}

                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- GESTIÓN DE PDF --}}
    <div class="card border-0 shadow-lg rounded-4 mt-4">
        <div class="card-header text-white rounded-top-4"
             style="background-color:#c0392b;">
            <i class="bi bi-file-pdf-fill me-1"></i>
            Archivo PDF Adjunto
        </div>
        <div class="card-body">

            @if($documento->tiene_archivo)

                {{-- PDF ACTUAL --}}
                <div class="alert alert-light border rounded-3 mb-4">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <i class="bi bi-file-pdf-fill text-danger fs-2"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $documento->archivo_pdf }}</div>
                            <small class="text-muted">
                                {{ $documento->tamano_formateado }}
                                @if($documento->fecha_subida)
                                    — Subido: {{ $documento->fecha_subida->format('d/m/Y H:i') }}
                                @endif
                                @if($documento->usuarioPdf)
                                    — Por: {{ $documento->usuarioPdf->name }}
                                @endif
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('documentos.pdf.descargar', $documento->idDocumento) }}"
                               class="btn btn-danger btn-sm rounded-3">
                                <i class="bi bi-download"></i> Descargar
                            </a>
                            <a href="{{ route('documentos.pdf.previsualizar', $documento->idDocumento) }}"
                               target="_blank"
                               class="btn btn-outline-danger btn-sm rounded-3">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ELIMINAR PDF --}}
                <form action="{{ route('admin.documentos.pdf.eliminar', $documento->idDocumento) }}"
                      method="POST" class="mb-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-outline-danger btn-sm rounded-3"
                            onclick="return confirm('¿Eliminar el PDF adjunto? Esta acción no se puede deshacer.')">
                        <i class="bi bi-trash3-fill me-1"></i>
                        Eliminar PDF actual
                    </button>
                </form>

                <hr>
                <p class="fw-semibold text-muted mb-3">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Reemplazar PDF actual:
                </p>

            @else
                <div class="alert alert-info rounded-3 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Este documento no tiene un archivo PDF adjunto.
                </div>
            @endif

            {{-- SUBIR / REEMPLAZAR PDF --}}
            <form action="{{ route('admin.documentos.pdf.subir', $documento->idDocumento) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                @if(session('success'))
                    <div class="alert alert-success rounded-3">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ $documento->tiene_archivo ? 'Nuevo archivo PDF (reemplaza el actual)' : 'Archivo PDF' }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="file"
                           name="archivo_pdf"
                           class="form-control rounded-3 @error('archivo_pdf') is-invalid @enderror"
                           accept=".pdf,application/pdf"
                           required>
                    @error('archivo_pdf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Solo PDF. Máximo 10 MB.</small>
                </div>

                <button type="submit" class="btn btn-danger rounded-3">
                    <i class="bi bi-upload me-1"></i>
                    {{ $documento->tiene_archivo ? 'Reemplazar PDF' : 'Subir PDF' }}
                </button>

            </form>

        </div>
    </div>

</div>

@endsection