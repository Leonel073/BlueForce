@extends('layouts.app')

@section('title', 'Registro de Documentos')

@section('content')

<div class="container-fluid">
    <h2 class="mb-4">Registro de Documentos</h2>

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> 
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> 
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ERRORES DE VALIDACIÓN --}}
    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Errores de validación:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="{{ route('documentos.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="row">
            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-lg-8">
                {{-- SECCIÓN 1: DATOS DEL DOCUMENTO --}}
                <div class="card shadow-sm mb-4" style="border-left: 4px solid #ffc107;">
                    <div class="card-header" style="background: linear-gradient(90deg, #0d1b2a, #1b263b); color: white;">
                        <i class="bi bi-file-earmark-text"></i> <strong>DATOS DEL DOCUMENTO</strong>
                    </div>
                    <div class="card-body">
                        {{-- CÓDIGO DE RUTA (GENERADO AUTOMÁTICAMENTE) --}}
                        <div class="mb-3">
                            <label for="codigo_ruta" class="form-label">
                                <i class="bi bi-barcode"></i> Código de Ruta <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="codigo_ruta" 
                                   name="codigo_ruta" 
                                   readonly
                                   value="{{ old('codigo_ruta') }}">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Se genera automáticamente con formato: [Tipo][Urgencia]-AAAA-MM-DD-[#]
                            </small>
                        </div>

                        {{-- ASUNTO --}}
                        <div class="mb-3">
                            <label for="asunto" class="form-label">
                                <i class="bi bi-chat-left-text"></i> Asunto <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('asunto') is-invalid @enderror" 
                                      id="asunto" 
                                      name="asunto" 
                                      rows="3"
                                      placeholder="Describa el contenido del documento"
                                      required>{{ old('asunto') }}</textarea>
                            @error('asunto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TIPO DE DOCUMENTO Y NIVEL DE URGENCIA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_documento" class="form-label">
                                    <i class="bi bi-files"></i> Tipo de Documento <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo_documento') is-invalid @enderror" 
                                        id="tipo_documento" 
                                        name="tipo_documento" 
                                        required>
                                    <option value="">-- Seleccione --</option>
                                    @foreach($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->idTipoDocumento }}" 
                                                @selected(old('tipo_documento') == $tipo->idTipoDocumento)>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nivel_urgencia" class="form-label">
                                    <i class="bi bi-exclamation-lg"></i> Nivel de Urgencia <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('nivel_urgencia') is-invalid @enderror" 
                                        id="nivel_urgencia" 
                                        name="nivel_urgencia" 
                                        required>
                                    <option value="">-- Seleccione --</option>
                                    @foreach($nivelesUrgencia as $urgencia)
                                        <option value="{{ $urgencia->idUrgencia }}" 
                                                @selected(old('nivel_urgencia') == $urgencia->idUrgencia)>
                                            {{ $urgencia->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nivel_urgencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: REMITENTE --}}
                <div class="card shadow-sm mb-4" style="border-left: 4px solid #ffc107;">
                    <div class="card-header" style="background: linear-gradient(90deg, #0d1b2a, #1b263b); color: white;">
                        <i class="bi bi-person"></i> <strong>REMITENTE (PERSONA)</strong>
                    </div>
                    <div class="card-body">
                        {{-- NOMBRE --}}
                        <div class="mb-3">
                            <label for="nombre_remitente" class="form-label">
                                <i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre_remitente') is-invalid @enderror" 
                                   id="nombre_remitente" 
                                   name="nombre_remitente" 
                                   placeholder="Nombre completo del remitente"
                                   value="{{ old('nombre_remitente') }}"
                                   required>
                            @error('nombre_remitente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CORREO Y CARGO --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="correo_remitente" class="form-label">
                                    <i class="bi bi-envelope"></i> Correo (Opcional)
                                </label>
                                <input type="email" 
                                       class="form-control @error('correo_remitente') is-invalid @enderror" 
                                       id="correo_remitente" 
                                       name="correo_remitente" 
                                       placeholder="ejemplo@correo.com"
                                       value="{{ old('correo_remitente') }}">
                                @error('correo_remitente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cargo_remitente" class="form-label">
                                    <i class="bi bi-briefcase"></i> Cargo (Opcional)
                                </label>
                                <input type="text" 
                                       class="form-control @error('cargo_remitente') is-invalid @enderror" 
                                       id="cargo_remitente" 
                                       name="cargo_remitente" 
                                       placeholder="Ej: Director, Jefe, etc."
                                       value="{{ old('cargo_remitente') }}">
                                @error('cargo_remitente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- INSTITUCIÓN Y TIPO --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="institucion_remitente" class="form-label">
                                    <i class="bi bi-building"></i> Institución (Opcional)
                                </label>
                                <input type="text" 
                                       class="form-control @error('institucion_remitente') is-invalid @enderror" 
                                       id="institucion_remitente" 
                                       name="institucion_remitente" 
                                       placeholder="Nombre de la institución"
                                       value="{{ old('institucion_remitente') }}">
                                @error('institucion_remitente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_remitente" class="form-label">
                                    <i class="bi bi-diagram-3"></i> Tipo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo_remitente') is-invalid @enderror" 
                                        id="tipo_remitente" 
                                        name="tipo_remitente" 
                                        required>
                                    <option value="">-- Seleccione --</option>
                                    <option value="INTERNO" @selected(old('tipo_remitente') == 'INTERNO')>
                                        INTERNO
                                    </option>
                                    <option value="EXTERNO" @selected(old('tipo_remitente') == 'EXTERNO')>
                                        EXTERNO
                                    </option>
                                </select>
                                @error('tipo_remitente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-lg-4">
                {{-- SECCIÓN 3: DESTINATARIOS --}}
                <div class="card shadow-sm" style="border-left: 4px solid #ffc107;">
                    <div class="card-header" style="background: linear-gradient(90deg, #0d1b2a, #1b263b); color: white;">
                        <i class="bi bi-map-pin"></i> <strong>DEPARTAMENTO DESTINO</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> Seleccione el departamento donde se envía este documento
                        </p>

                        <div class="mb-3">
                            <label for="departamento" class="form-label">
                                Departamento <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('departamento') is-invalid @enderror" 
                                    id="departamento" 
                                    name="departamento" 
                                    required>
                                <option value="">-- Seleccione un departamento --</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->idDepartamento }}" 
                                            @selected(old('departamento') == $depto->idDepartamento)>
                                        <i class="bi bi-folder"></i> {{ $depto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- INFORMACIÓN DEL DEPARTAMENTO --}}
                        <div class="alert alert-info small" role="alert">
                            <i class="bi bi-info-circle"></i>
                            Este documento será enviado al departamento seleccionado para que se lleve a cabo su seguimiento.
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-success w-100 mb-2" style="background: linear-gradient(90deg, #0d1b2a, #1b263b);">
                        <i class="bi bi-check-circle"></i> Guardar Documento
                    </button>
                   <a href="{{ Auth::user()->idRol == 1 ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-outline-secondary w-100">
    <i class="bi bi-arrow-left"></i> Cancelar
</a>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
    // GENERADOR DE CÓDIGO DE RUTA AUTOMÁTICO
    const tiposDocumentoMap = {};
    const nivelesUrgenciaMap = {};
    
    // Mapear tipos de documento a su primera letra
    @foreach($tiposDocumento as $tipo)
        tiposDocumentoMap[{{ $tipo->idTipoDocumento }}] = '{{ strtoupper(substr($tipo->nombre, 0, 1)) }}';
    @endforeach
    
    // Mapear niveles de urgencia a su primera letra
    @foreach($nivelesUrgencia as $urgencia)
        nivelesUrgenciaMap[{{ $urgencia->idUrgencia }}] = '{{ strtoupper(substr($urgencia->nombre, 0, 1)) }}';
    @endforeach
    
    // Función para generar el código de ruta
    function generarCodigoRuta() {
        const tipoDocumentoId = document.getElementById('tipo_documento').value;
        const nivelUrgenciaId = document.getElementById('nivel_urgencia').value;
        const codigoRutaInput = document.getElementById('codigo_ruta');
        
        if (tipoDocumentoId && nivelUrgenciaId) {
            const tipoLetra = tiposDocumentoMap[tipoDocumentoId] || 'X';
            const urgenciaLetra = nivelesUrgenciaMap[nivelUrgenciaId] || 'X';
            
            // Obtener fecha actual
            const hoy = new Date();
            const anio = hoy.getFullYear();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0');
            const dia = String(hoy.getDate()).padStart(2, '0');
            
            // Por ahora el número será 1 (en producción se obtendría de la BD)
            const numero = 1;
            
            const codigo = `${tipoLetra}${urgenciaLetra}-${anio}-${mes}-${dia}-${numero}`;
            codigoRutaInput.value = codigo;
        } else {
            codigoRutaInput.value = '';
        }
    }
    
    // Escuchar cambios en tipo de documento y nivel de urgencia
    document.getElementById('tipo_documento').addEventListener('change', generarCodigoRuta);
    document.getElementById('nivel_urgencia').addEventListener('change', generarCodigoRuta);
    
    // Generar al cargar si hay valores previos
    window.addEventListener('load', generarCodigoRuta);

    // Validación de Bootstrap
    (() => {
        'use strict';
        window.addEventListener('load', () => {
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    })();
</script>

@endsection
