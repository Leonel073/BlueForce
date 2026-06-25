@extends('layouts.app')

@section('title', 'Registro de Documentos')

@section('content')

<div class="container-fluid">

    {{-- TÍTULO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-file-earmark-plus-fill"></i>
                Registro de Documentos
            </h2>
            <p class="text-muted mb-0">
                Registro institucional con derivación automática
            </p>
        </div>
    </div>

    {{-- ALERTAS DE ÉXITO Y ERROR --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <strong class="d-block mb-2">{{ $errors->count() }} error{{ $errors->count() > 1 ? 'es' : '' }} encontrados</strong>
                    <ul class="mb-0 ms-3 small">
                        @foreach($errors->all() as $error)
                            <li class="mb-1"><i class="bi bi-dash-circle text-danger me-1"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf

        <div class="row">

            {{-- COLUMNA PRINCIPAL --}}
            <div class="col-lg-8">

                {{-- DOCUMENTO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        DATOS DEL DOCUMENTO
                    </div>
                    <div class="card-body">
                        {{-- ASUNTO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
                            <textarea name="asunto" rows="4" class="form-control @error('asunto') is-invalid @enderror" placeholder="Ingrese una descripción clara del asunto" required>{{ old('asunto') }}</textarea>
                            @error('asunto')
                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Máximo 500 caracteres.</small>
                        </div>

                        {{-- TIPO Y URGENCIA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" id="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                                    <option value="">-- Seleccione un tipo --</option>
                                    @foreach($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->idTipoDocumento }}" @selected(old('tipo_documento') == $tipo->idTipoDocumento)>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nivel Urgencia <span class="text-danger">*</span></label>
                                <select name="nivel_urgencia" id="nivel_urgencia" class="form-select @error('nivel_urgencia') is-invalid @enderror" required>
                                    <option value="">-- Seleccione nivel --</option>
                                    @foreach($nivelesUrgencia as $urgencia)
                                        <option value="{{ $urgencia->idUrgencia }}" @selected(old('nivel_urgencia') == $urgencia->idUrgencia)>{{ $urgencia->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('nivel_urgencia')
                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REMITENTE - MEJORADO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-person-fill"></i>
                        REMITENTE
                    </div>
                    <div class="card-body">
                        {{-- SELECTOR: YO MISMO vs OTRA PERSONA --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">¿Quién es el remitente?</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="opcion_remitente" id="yo_mismo" value="yo_mismo" checked onchange="toggleRemitenteMode()">
                                <label class="btn btn-outline-primary rounded-start-4" for="yo_mismo">
                                    <i class="bi bi-person-circle me-1"></i>
                                    Yo Mismo
                                </label>
                                
                                <input type="radio" class="btn-check" name="opcion_remitente" id="otra_persona" value="otra_persona" onchange="toggleRemitenteMode()">
                                <label class="btn btn-outline-primary rounded-end-4" for="otra_persona">
                                    <i class="bi bi-person me-1"></i>
                                    Otra Persona
                                </label>
                            </div>
                        </div>

                        {{-- BLOQUE: YO MISMO (Por defecto visible) --}}
                        <div id="bloque_yo_mismo" style="display: block;">
                            <div class="alert alert-success border-0 rounded-4">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Remitente Actual</strong>
                            </div>
                            
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Nombre</label>
                                        <div class="fw-semibold fs-5">{{ Auth::user()->persona->nombre ?? Auth::user()->name }}</div>
                                    </div>

                                    @if(Auth::user()->persona)
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small">CI</label>
                                                <div>{{ Auth::user()->persona->ci ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small">Tipo</label>
                                                <div>
                                                    <span class="badge {{ Auth::user()->persona->tipo === 'INTERNO' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ Auth::user()->persona->tipo }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small">Correo</label>
                                                <div>{{ Auth::user()->persona->correo ?? 'No registrado' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small">Teléfono Celular</label>
                                                <div>{{ Auth::user()->persona->telefono_celular ?? 'No registrado' }}</div>
                                            </div>
                                        </div>

                                        @if(Auth::user()->persona->tipo === 'INTERNO')
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted small">Departamento</label>
                                                    <div>{{ Auth::user()->persona->departamento?->nombre ?? 'No asignado' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted small">Cargo</label>
                                                    <div>{{ Auth::user()->persona->cargo?->nombre ?? 'No asignado' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-3">
                                                <label class="form-label text-muted small">Institución</label>
                                                <div>{{ Auth::user()->persona->institucion ?? 'No especificada' }}</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Campos ocultos para remitente "yo mismo" --}}
                            <input type="hidden" name="ci_remitente" value="{{ Auth::user()->persona->ci ?? '' }}">
                            <input type="hidden" name="nombre_remitente" value="{{ Auth::user()->persona->nombre ?? Auth::user()->name }}">
                            <input type="hidden" name="telefono_celular" value="{{ Auth::user()->persona->telefono_celular ?? '' }}">
                            <input type="hidden" name="telefono_fijo" value="{{ Auth::user()->persona->telefono_fijo ?? '' }}">
                            <input type="hidden" name="correo_remitente" value="{{ Auth::user()->persona->correo ?? '' }}">
                            <input type="hidden" name="cargo_remitente" value="{{ Auth::user()->persona->cargo?->nombre ?? '' }}">
                            <input type="hidden" name="institucion_remitente" value="{{ Auth::user()->persona->institucion ?? '' }}">
                            <input type="hidden" name="tipo_remitente" value="{{ Auth::user()->persona->tipo ?? 'INTERNO' }}">
                        </div>

                        {{-- BLOQUE: OTRA PERSONA (Oculto por defecto) --}}
                        <div id="bloque_otra_persona" style="display: none;">
                            <div class="alert alert-info border-0 rounded-4">
                                <i class="bi bi-search"></i>
                                Ingrese el número de carnet para buscar automáticamente si el remitente ya existe en el sistema.
                            </div>

                            {{-- CI Y CELULAR --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Carnet de Identidad <span class="text-danger">*</span></label>
                                    <input type="text" id="ci_remitente" name="ci_remitente_manual" class="form-control @error('ci_remitente') is-invalid @enderror" placeholder="Ej: 1234567-8" value="{{ old('ci_remitente') }}">
                                    @error('ci_remitente')
                                        <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @else
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Ingrese su número de cédula.</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Teléfono Celular <span class="text-danger">*</span></label>
                                    <input type="text" id="telefono_celular" name="telefono_celular_manual" class="form-control @error('telefono_celular') is-invalid @enderror" placeholder="Ej: +591 71234567" value="{{ old('telefono_celular') }}">
                                    @error('telefono_celular')
                                        <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @else
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Incluya el prefijo del país si es necesario.</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Teléfono Fijo</label>
                                    <input type="text" id="telefono_fijo" name="telefono_fijo_manual" class="form-control" value="{{ old('telefono_fijo') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" id="nombre_remitente" name="nombre_remitente_manual" class="form-control @error('nombre_remitente') is-invalid @enderror" placeholder="Ej: Juan Carlos García López" value="{{ old('nombre_remitente') }}">
                                @error('nombre_remitente')
                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @else
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Solo se permiten letras y espacios.</small>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Correo</label>
                                    <input type="email" id="correo_remitente" name="correo_remitente_manual" class="form-control @error('correo_remitente') is-invalid @enderror" placeholder="usuario@ejemplo.com" value="{{ old('correo_remitente') }}">
                                    @error('correo_remitente')
                                        <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @else
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Opcional - Formato: usuario@ejemplo.com</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3" id="cargo_remitente_section" style="display: none;">
                                    <label class="form-label fw-semibold">Cargo</label>
                                    <input type="text" id="cargo_remitente" name="cargo_remitente_manual" class="form-control" placeholder="Auto completado para internos" readonly>
                                    <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i>Se completa automáticamente para personas internas.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Institución</label>
                                    <input type="text" id="institucion_remitente" name="institucion_remitente_manual" class="form-control @error('institucion_remitente') is-invalid @enderror" placeholder="Ej: Ministerio de Educación" value="{{ old('institucion_remitente') }}">
                                    @error('institucion_remitente')
                                        <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @else
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Opcional - Letras, números y espacios.</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Tipo de Remitente <span class="text-danger">*</span></label>
                                    <select name="tipo_remitente_manual" id="tipo_remitente" class="form-select @error('tipo_remitente') is-invalid @enderror" required>
                                        <option value="">-- Seleccione tipo --</option>
                                        <option value="INTERNO" @selected(old('tipo_remitente') == 'INTERNO')>INTERNO</option>
                                        <option value="EXTERNO" @selected(old('tipo_remitente') == 'EXTERNO')>EXTERNO</option>
                                    </select>
                                    @error('tipo_remitente')
                                        <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @else
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Interno: dentro de la institución | Externo: de afuera.</small>
                                    @enderror
                                </div>
                            </div>

                            <div id="personaEncontrada" class="alert alert-success d-none rounded-4 border-0">
                                <i class="bi bi-check-circle-fill"></i>
                                Persona encontrada en el sistema. Datos autocompletados.
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-lg-4">

                {{-- DESTINO --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-building-fill"></i>
                        DESTINO DOCUMENTAL
                    </div>
                    <div class="card-body">
                        {{-- DEPARTAMENTO DESTINO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Departamento Destino <span class="text-danger">*</span></label>
                            <select name="departamento" id="departamento" class="form-select @error('departamento') is-invalid @enderror" required onchange="cargarResponsables()">
                                <option value="">-- Seleccione departamento --</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->idDepartamento }}" @selected(old('departamento') == $depto->idDepartamento)>{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                            @error('departamento')
                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @else
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Seleccione el departamento destino.</small>
                            @enderror
                        </div>

                        {{-- RESPONSABLE DESTINO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Responsable Destino <span class="text-danger">*</span></label>
                            <select name="responsable_destino" id="responsable_destino" class="form-select @error('responsable_destino') is-invalid @enderror" required>
                                <option value="">-- Seleccione departamento primero --</option>
                            </select>
                            @error('responsable_destino')
                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @else
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Personas internas activas del departamento.</small>
                            @enderror
                        </div>

                        {{-- BUSCAR RESPONSABLE --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Buscar Responsable</label>
                            <input type="text" id="buscar_responsable" class="form-control rounded-3" placeholder="Ingrese nombre o CI..." onkeyup="filtrarResponsables()">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Filtra los responsables por nombre o CI.</small>
                        </div>

                        {{-- AYUDA VISUAL: FLUJO AUTOMÁTICO --}}
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            <strong>Derivación automática:</strong>
                            <div id="flujo_derivacion" class="mt-2">
                                <span class="badge bg-secondary">Selecciona departamento y responsable</span>
                            </div>
                        </div>

                        {{-- INFORMACIÓN SOBRE EL FLUJO --}}
                        <div class="border rounded-4 p-3 bg-light">
                            <small class="text-muted fw-semibold">
                                <i class="bi bi-info-circle me-1"></i>
                                Información
                            </small>
                            <div class="mt-2 small text-muted">
                                El documento será derivado directamente al responsable seleccionado.
                                No quedará en tu bandeja, irá automáticamente a la del responsable.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ARCHIVO PDF (OPCIONAL) --}}
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-file-pdf-fill me-1"></i>
                        ADJUNTAR PDF (OPCIONAL)
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Archivo PDF</label>
                            <input type="file" name="archivo_pdf" id="archivo_pdf" class="form-control @error('archivo_pdf') is-invalid @enderror" accept=".pdf,application/pdf">
                            @error('archivo_pdf')
                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Solo archivos PDF. Tamaño máximo: 10 MB.</small>
                        </div>
                        <div id="pdf-preview-name" class="d-none mt-2">
                            <span class="badge bg-danger px-3 py-2">
                                <i class="bi bi-file-pdf me-1"></i>
                                <span id="pdf-filename"></span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="mt-4">
                    <button type="submit" class="btn text-white w-100 mb-2 rounded-4 py-3" style="background-color:#0B2D59;">
                        <i class="bi bi-check-circle-fill"></i>
                        Registrar Documento
                    </button>
                    <a href="{{ Auth::user()->idRol == 1 ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-outline-secondary w-100 rounded-4 py-3">
                        <i class="bi bi-arrow-left"></i>
                        Cancelar
                    </a>
                </div>

            </div>

        </div>

    </form>

</div>

<script>

// Almacenar responsables por departamento
let responsablesPorDepartamento = {};

// Cargar responsables cuando se selecciona un departamento
function cargarResponsables() {
    const departamentoId = document.getElementById('departamento').value;
    const selectResponsable = document.getElementById('responsable_destino');
    const flujoDerivacion = document.getElementById('flujo_derivacion');
    
    if (!departamentoId) {
        selectResponsable.innerHTML = '<option value="">-- Seleccione departamento primero --</option>';
        document.getElementById('buscar_responsable').value = '';
        flujoDerivacion.innerHTML = '<span class="badge bg-secondary">Selecciona departamento y responsable</span>';
        return;
    }
    
    // Obtener nombre del departamento
    const departamentoNombre = document.querySelector(`#departamento option[value="${departamentoId}"]`).textContent;
    
    // Obtener responsables del departamento
    fetch(`/documentos/responsables-departamento/${departamentoId}`)
        .then(response => response.json())
        .then(data => {
            responsablesPorDepartamento = data;
            
            let html = '<option value="">-- Seleccione responsable --</option>';
            
            if (data.length === 0) {
                html = '<option value="">No hay responsables disponibles en este departamento</option>';
            } else {
                data.forEach(responsable => {
                    html += `<option value="${responsable.idPersona}" data-nombre="${responsable.nombre}" data-ci="${responsable.ci}">
                        ${responsable.nombre} (${responsable.ci})
                    </option>`;
                });
            }
            
            selectResponsable.innerHTML = html;
            selectResponsable.value = '';
            document.getElementById('buscar_responsable').value = '';
            flujoDerivacion.innerHTML = `<span class="badge bg-info">${departamentoNombre} → Selecciona responsable</span>`;
        })
        .catch(error => {
            console.error('Error:', error);
            selectResponsable.innerHTML = '<option value="">Error al cargar responsables</option>';
        });
}

// Filtrar responsables por búsqueda
function filtrarResponsables() {
    const busqueda = document.getElementById('buscar_responsable').value.toLowerCase();
    const selectResponsable = document.getElementById('responsable_destino');
    const options = selectResponsable.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const nombre = option.getAttribute('data-nombre').toLowerCase();
        const ci = option.getAttribute('data-ci').toLowerCase();
        
        if (nombre.includes(busqueda) || ci.includes(busqueda)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Actualizar flujo de derivación cuando se selecciona responsable
document.getElementById('responsable_destino')?.addEventListener('change', function() {
    const flujoDerivacion = document.getElementById('flujo_derivacion');
    const departamentoNombre = document.querySelector(`#departamento option:checked`).textContent;
    
    if (this.value) {
        const responsableNombre = this.options[this.selectedIndex].getAttribute('data-nombre');
        flujoDerivacion.innerHTML = `<span class="badge bg-success">${departamentoNombre} → ${responsableNombre}</span>`;
    } else {
        flujoDerivacion.innerHTML = `<span class="badge bg-info">${departamentoNombre} → Selecciona responsable</span>`;
    }
});

function toggleRemitenteMode() {
    const yoMismo = document.getElementById('yo_mismo').checked;
    const bloqueYoMismo = document.getElementById('bloque_yo_mismo');
    const bloqueOtra = document.getElementById('bloque_otra_persona');
    
    if (yoMismo) {
        bloqueYoMismo.style.display = 'block';
        bloqueOtra.style.display = 'none';
        
        // Asegurar que los campos ocultos se envíen
        document.querySelectorAll('#bloque_yo_mismo input[type="hidden"]').forEach(input => {
            input.disabled = false;
        });
        document.querySelectorAll('#bloque_otra_persona input').forEach(input => {
            if (input.type !== 'hidden') input.disabled = true;
        });
        
    } else {
        bloqueYoMismo.style.display = 'none';
        bloqueOtra.style.display = 'block';
        
        // Deshabilitar campos ocultos y habilitar entrada manual
        document.querySelectorAll('#bloque_yo_mismo input[type="hidden"]').forEach(input => {
            input.disabled = true;
        });
        document.querySelectorAll('#bloque_otra_persona input').forEach(input => {
            if (input.type !== 'hidden') input.disabled = false;
        });
        
        // Copiar valores de campos "otra persona" a los campos ocultos para que se envíen
        document.getElementById('bloque_otra_persona').addEventListener('input', function(e) {
            const yoMismoDiv = document.getElementById('bloque_yo_mismo');
            if (e.target.name === 'ci_remitente_manual') {
                yoMismoDiv.querySelector('input[name="ci_remitente"]').value = e.target.value;
            } else if (e.target.name === 'nombre_remitente_manual') {
                yoMismoDiv.querySelector('input[name="nombre_remitente"]').value = e.target.value;
            } else if (e.target.name === 'telefono_celular_manual') {
                yoMismoDiv.querySelector('input[name="telefono_celular"]').value = e.target.value;
            } else if (e.target.name === 'telefono_fijo_manual') {
                yoMismoDiv.querySelector('input[name="telefono_fijo"]').value = e.target.value;
            } else if (e.target.name === 'correo_remitente_manual') {
                yoMismoDiv.querySelector('input[name="correo_remitente"]').value = e.target.value;
            } else if (e.target.name === 'cargo_remitente_manual') {
                yoMismoDiv.querySelector('input[name="cargo_remitente"]').value = e.target.value;
            } else if (e.target.name === 'institucion_remitente_manual') {
                yoMismoDiv.querySelector('input[name="institucion_remitente"]').value = e.target.value;
            } else if (e.target.name === 'tipo_remitente_manual') {
                yoMismoDiv.querySelector('input[name="tipo_remitente"]').value = e.target.value;
            }
        });
    }
}

// Manejo de archivos PDF
document.getElementById('archivo_pdf')?.addEventListener('change', function() {
    const preview = document.getElementById('pdf-preview-name');
    const filename = document.getElementById('pdf-filename');
    if (this.files.length > 0) {
        filename.textContent = this.files[0].name;
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
});

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    toggleRemitenteMode();
});

</script>

@endsection
