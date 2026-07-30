@extends('layouts.app')

@section('title', 'Registro de Documentos')

@section('content')
@php
    $isAdminDocumento = Auth::user()?->idRol == 1;
    $documentoStoreRoute = route($isAdminDocumento ? 'admin.documentos.store' : 'documentos.store');
    $responsablesEndpoint = $isAdminDocumento
        ? url('/admin/documentos/responsables-departamento')
        : url('/documentos/responsables-departamento');
    $personasBuscarEndpoint = route($isAdminDocumento ? 'admin.personas.buscar-avanzado' : 'personas.buscar-avanzado');
    $duplicadosEndpoint = route($isAdminDocumento ? 'admin.personas.verificar-duplicados' : 'personas.verificar-duplicados');
    $oldOpcionRemitente = old('opcion_remitente', 'yo_mismo');
@endphp

<style>
    .remitente-flow {
        border: 1px solid #d8e0ea;
        border-radius: 8px;
        background: #f8fafc;
        padding: 1rem;
    }

    .remitente-stepper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: .5rem;
        margin-bottom: 1rem;
    }

    .remitente-step {
        border: 1px solid #d8e0ea;
        border-radius: 8px;
        background: #fff;
        color: #64748b;
        padding: .7rem .8rem;
        font-size: .82rem;
        font-weight: 700;
    }

    .remitente-step.active {
        border-color: #D9A23D;
        color: #0B2D59;
        box-shadow: inset 0 3px 0 #D9A23D;
    }

    .remitente-panel {
        border: 1px solid #d8e0ea;
        border-radius: 8px;
        background: #fff;
        padding: 1rem;
    }

    .remitente-panel-title {
        color: #0B2D59;
        font-size: 1rem;
        font-weight: 800;
    }

    .remitente-result-list {
        border: 1px solid #d8e0ea;
        border-radius: 8px;
        background: #fff;
        max-height: 420px;
        overflow-y: auto;
    }

    .remitente-result-card {
        border-bottom: 1px solid #edf2f7;
        padding: .95rem;
        transition: background-color .15s ease, box-shadow .15s ease;
    }

    .remitente-result-card:hover {
        background: #f8fbff;
        box-shadow: inset 3px 0 0 #D9A23D;
    }

    .remitente-selected-card {
        border: 1px solid #b6e3c6;
        border-left: 4px solid #198754;
        border-radius: 8px;
        background: #f7fff9;
    }

    .remitente-selected-card.selected-confirmed {
        box-shadow: 0 0 0 .2rem rgba(25, 135, 84, .12);
    }

    .remitente-form-section {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #fff;
    }

    .remitente-empty {
        border: 1px dashed #d6b458;
        border-radius: 8px;
        background: #fffaf0;
        color: #775c17;
        padding: 1rem;
    }

    @media (min-width: 992px) {
        .documento-side-panel {
            position: sticky;
            top: 1rem;
        }
    }

    @media (max-width: 768px) {
        .remitente-stepper {
            grid-template-columns: 1fr;
        }
    }
</style>

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
    <form action="{{ $documentoStoreRoute }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf

        <div class="row g-4 align-items-start">

            {{-- COLUMNA PRINCIPAL --}}
            <div class="col-md-8 col-lg-8">

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
                                <input type="radio" class="btn-check" name="opcion_remitente" id="yo_mismo" value="yo_mismo" @checked($oldOpcionRemitente === 'yo_mismo') onchange="toggleRemitenteMode()">
                                <label class="btn btn-outline-primary rounded-start-4" for="yo_mismo">
                                    <i class="bi bi-person-circle me-1"></i>
                                    Yo Mismo
                                </label>
                                
                                <input type="radio" class="btn-check" name="opcion_remitente" id="otra_persona" value="otra_persona" @checked($oldOpcionRemitente === 'otra_persona') onchange="toggleRemitenteMode()">
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
                                                    <div>{{ Auth::user()->persona->cargos_nombres ?? 'No asignado' }}</div>
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

                            {{-- Campos ocultos para remitente "yo mismo" que se enviarán al formulario --}}
                            <input type="hidden" name="ci_remitente" value="{{ Auth::user()->persona->ci ?? '' }}">
                            <input type="hidden" name="nombre_remitente" value="{{ Auth::user()->persona->nombre ?? Auth::user()->name }}">
                            <input type="hidden" name="telefono_celular" value="{{ Auth::user()->persona->telefono_celular ?? '' }}">
                            <input type="hidden" name="telefono_fijo" value="{{ Auth::user()->persona->telefono_fijo ?? '' }}">
                            <input type="hidden" name="correo_remitente" value="{{ Auth::user()->persona->correo ?? '' }}">
                            <input type="hidden" name="cargo_remitente" value="{{ Auth::user()->persona->cargos_nombres ?? '' }}">
                            <input type="hidden" name="institucion_remitente" value="{{ Auth::user()->persona->institucion ?? '' }}">
                            <input type="hidden" name="tipo_remitente" value="{{ Auth::user()->persona->tipo ?? 'INTERNO' }}">
                        </div>

                        {{-- BLOQUE: OTRA PERSONA (Oculto por defecto) --}}
                        <div id="bloque_otra_persona" class="remitente-flow" style="display: none;">
                            <div class="remitente-stepper">
                                <div class="remitente-step" data-step="estado_buscador">
                                    <i class="bi bi-search me-1"></i>1. Buscar
                                </div>
                                <div class="remitente-step" data-step="estado_persona_encontrada">
                                    <i class="bi bi-person-check me-1"></i>2. Seleccionar
                                </div>
                                <div class="remitente-step" data-step="estado_crear_nueva">
                                    <i class="bi bi-person-plus me-1"></i>3. Nuevo registro
                                </div>
                            </div>

                            {{-- ========== ESTADO 1: BUSCADOR ========== --}}
                            <div id="estado_buscador" class="remitente-panel" style="display: block;">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="remitente-panel-title">
                                            <i class="bi bi-search me-1"></i>
                                            Buscar remitente existente
                                        </div>
                                        <div class="small text-muted">
                                            Busque por nombre, CI, correo, institución, cargo o departamento.
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary rounded-3" onclick="irAEstadoCrearNueva(); event.preventDefault();">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Nuevo remitente
                                    </button>
                                </div>

                                <label class="form-label fw-semibold">Buscar persona <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text"
                                           id="buscar_persona_input"
                                           class="form-control"
                                           placeholder="Nombre, CI, correo o institución"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusquedaPersona(); event.preventDefault();">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-lightning-charge me-1"></i>
                                    La búsqueda inicia desde 2 caracteres.
                                </small>

                                {{-- RESULTADOS DE BÚSQUEDA --}}
                                <div id="resultados_busqueda" class="mt-4" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-semibold mb-0" style="color:#0B2D59;">
                                            Coincidencias encontradas
                                        </h6>
                                        <span id="contador_resultados" class="badge rounded-pill" style="background-color:#0B2D59;">0</span>
                                    </div>
                                    <div id="lista_resultados" class="remitente-result-list">
                                        <!-- Resultados se cargarán dinámicamente aquí -->
                                    </div>
                                </div>

                                {{-- NO ENCONTRADO --}}
                                <div id="no_encontrado" class="remitente-empty mt-4" style="display: none;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                        <div>
                                            <strong><i class="bi bi-exclamation-triangle me-1"></i>No se encontraron coincidencias</strong>
                                            <div class="small">Puede registrar a la persona como nuevo remitente.</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary rounded-3" onclick="irAEstadoCrearNueva(); event.preventDefault();">
                                            <i class="bi bi-plus-circle me-1"></i>Registrar nuevo
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary w-100 rounded-3 mt-3" onclick="irAEstadoCrearNueva(); event.preventDefault();" style="display: none;" id="btn_crear_nueva_desde_busqueda">
                                    <i class="bi bi-plus-circle me-2"></i>Crear Nueva Persona
                                </button>
                            </div>

                            {{-- ========== ESTADO 2: PERSONA ENCONTRADA ========== --}}
                            <div id="estado_persona_encontrada" style="display: none;">
                                <div id="persona_confirmada_card" class="remitente-selected-card p-3 mb-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:48px;height:48px;">
                                            <i class="bi bi-person-check-fill fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                                <div>
                                                    <div class="small text-success fw-semibold" id="persona_confirmada_hint">
                                                        Persona encontrada en el sistema
                                                    </div>
                                                    <h5 id="sel_nombre" class="fw-bold mb-1" style="color:#0B2D59;"></h5>
                                                    <div class="small text-muted">CI: <span id="sel_ci" class="fw-semibold"></span></div>
                                                </div>
                                                <div><span id="sel_tipo" class="badge"></span></div>
                                            </div>

                                            <div class="row g-3 mt-2">
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">Correo</small>
                                                    <div id="sel_correo" class="small fw-semibold"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">Teléfono celular</small>
                                                    <div id="sel_celular" class="small fw-semibold"></div>
                                                </div>
                                                <div class="col-12">
                                                    <small class="text-muted d-block" id="label_interno_externo"></small>
                                                    <div id="sel_departamento_cargo" class="small fw-semibold"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary flex-grow-1 rounded-3" onclick="finalizarSeleccionPersona(); event.preventDefault();">
                                        <i class="bi bi-check-circle-fill me-2"></i>Confirmar remitente
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" onclick="volverAlBuscador(); event.preventDefault();">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i>Buscar otra
                                    </button>
                                </div>
                            </div>

                            {{-- ========== ESTADO 3: CREAR NUEVA PERSONA ========== --}}
                            <div id="estado_crear_nueva" style="display: none;">
                                <div class="remitente-panel mb-3">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <div class="remitente-panel-title">
                                                <i class="bi bi-person-plus-fill me-1"></i>
                                                Registrar nuevo remitente
                                            </div>
                                            <div class="small text-muted">
                                                Complete los datos necesarios para identificar y contactar a la persona.
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary rounded-3" onclick="volverAlBuscador(); event.preventDefault();">
                                            <i class="bi bi-arrow-left me-1"></i>Volver a buscar
                                        </button>
                                    </div>

                                    <div class="remitente-form-section">
                                        <h6 class="fw-bold mb-3" style="color:#0B2D59;">
                                            <i class="bi bi-person-vcard me-1"></i>Identificación
                                        </h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   id="nombre_remitente_otra"
                                                   name="nombre_remitente"
                                                   class="form-control @error('nombre_remitente') is-invalid @enderror"
                                                   placeholder="Ej: Juan Carlos García López"
                                                   value="{{ old('nombre_remitente') }}">
                                            @error('nombre_remitente')
                                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Carnet de identidad <span class="text-danger">*</span></label>
                                                <input type="text"
                                                       id="ci_remitente_otra"
                                                       name="ci_remitente"
                                                       class="form-control @error('ci_remitente') is-invalid @enderror"
                                                       placeholder="Ej: 1234567-8"
                                                       value="{{ old('ci_remitente') }}"
                                                       onblur="verificarCI()">
                                                @error('ci_remitente')
                                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tipo de remitente <span class="text-danger">*</span></label>
                                                <select id="tipo_remitente_otra"
                                                        name="tipo_remitente"
                                                        class="form-select @error('tipo_remitente') is-invalid @enderror"
                                                        onchange="actualizarCamposTipo()">
                                                    <option value="">-- Seleccione tipo --</option>
                                                    <option value="INTERNO" @selected(old('tipo_remitente') == 'INTERNO')>INTERNO (Dentro de la institución)</option>
                                                    <option value="EXTERNO" @selected(old('tipo_remitente') == 'EXTERNO')>EXTERNO (De afuera)</option>
                                                </select>
                                                @error('tipo_remitente')
                                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="remitente-form-section">
                                        <h6 class="fw-bold mb-3" style="color:#0B2D59;">
                                            <i class="bi bi-telephone-fill me-1"></i>Contacto
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Teléfono celular <span class="text-danger">*</span></label>
                                                <input type="text"
                                                       id="telefono_celular_otra"
                                                       name="telefono_celular"
                                                       class="form-control @error('telefono_celular') is-invalid @enderror"
                                                       placeholder="Ej: +591 71234567"
                                                       value="{{ old('telefono_celular') }}">
                                                @error('telefono_celular')
                                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Teléfono fijo</label>
                                                <input type="text"
                                                       id="telefono_fijo_otra"
                                                       name="telefono_fijo"
                                                       class="form-control"
                                                       value="{{ old('telefono_fijo') }}">
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold">Correo</label>
                                            <input type="email"
                                                   id="correo_remitente_otra"
                                                   name="correo_remitente"
                                                   class="form-control @error('correo_remitente') is-invalid @enderror"
                                                   placeholder="usuario@ejemplo.com"
                                                   value="{{ old('correo_remitente') }}">
                                            @error('correo_remitente')
                                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="remitente-form-section">
                                        <h6 class="fw-bold mb-3" style="color:#0B2D59;">
                                            <i class="bi bi-building-fill me-1"></i>Contexto institucional
                                        </h6>

                                        {{-- CAMPOS CONDICIONALES PARA INTERNO --}}
                                        <div id="campos_interno" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Departamento</label>
                                                    <input type="text"
                                                           id="departamento_nueva"
                                                           name="departamento_nueva"
                                                           class="form-control"
                                                           placeholder="Auto completado (no editable)"
                                                           readonly>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Cargo</label>
                                                    <input type="text"
                                                           id="cargo_remitente_otra"
                                                           name="cargo_remitente"
                                                           class="form-control"
                                                           value="{{ old('cargo_remitente') }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- CAMPOS CONDICIONALES PARA EXTERNO --}}
                                        <div id="campos_externo" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Institución</label>
                                                <input type="text"
                                                       id="institucion_remitente_otra"
                                                       name="institucion_remitente"
                                                       class="form-control @error('institucion_remitente') is-invalid @enderror"
                                                       placeholder="Ej: Ministerio de Educación"
                                                       value="{{ old('institucion_remitente') }}">
                                                @error('institucion_remitente')
                                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="small text-muted">
                                            Seleccione el tipo de remitente para mostrar los campos correspondientes.
                                        </div>
                                    </div>

                                {{-- ALERTA DUPLICADOS --}}
                                <div id="alerta_duplicados" class="alert alert-danger border-0 rounded-4" style="display: none;">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                                        <div>
                                            <strong>Se encontraron personas similares</strong>
                                            <p class="mb-2 small">Estas personas ya existen en el sistema. ¿Quiere usar una de ellas en lugar de crear una nueva?</p>
                                            <div id="lista_duplicados" class="border rounded-2 bg-white p-2" style="max-height: 250px; overflow-y: auto;">
                                                <!-- Se cargarán dinámicamente -->
                                            </div>
                                            <div class="mt-3 d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmarCrearDuplicado()">
                                                    <i class="bi bi-plus-circle me-1"></i>Crear de todos modos
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="volverAlBuscador()">
                                                    <i class="bi bi-arrow-left me-1"></i>Buscar otra persona
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- BOTONES DE ACCIÓN --}}
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary flex-grow-1 rounded-3" onclick="guardarNuevaPersona(); event.preventDefault();">
                                        <i class="bi bi-check-circle-fill me-2"></i>Registrar nueva persona
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" onclick="volverAlBuscador(); event.preventDefault();">
                                        <i class="bi bi-arrow-left me-2"></i>Volver a Buscar
                                    </button>
                                </div>
                            </div>

                            {{-- CAMPOS OCULTOS (se enviarán al backend) --}}
                            <input type="hidden" id="persona_seleccionada_id" name="idPersona_seleccionada" value="{{ old('idPersona_seleccionada') }}">

                        </div>
                        </div>
                        {{-- FIN BLOQUE: OTRA PERSONA --}}

                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-md-4 col-lg-4">
                <div class="documento-side-panel">

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

                {{-- ARCHIVO ADJUNTO (OPCIONAL) --}}
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#0B2D59,#2E608C);">
                        <i class="bi bi-paperclip me-1"></i>
                        ADJUNTAR ARCHIVO (OPCIONAL)
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Archivo adjunto</label>
                            <input type="file" name="archivo_pdf" id="archivo_pdf" class="form-control @error('archivo_pdf') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            @error('archivo_pdf')
                                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>PDF, Word o Excel. Tamaño máximo: 10 MB.</small>
                        </div>
                        <div id="pdf-preview-name" class="d-none mt-2">
                            <span class="badge bg-danger px-3 py-2">
                                <i class="bi bi-paperclip me-1"></i>
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
                    <a href="{{ Auth::user()->idRol == 1 ? route('admin.documentos.index') : route('user.dashboard') }}" class="btn btn-outline-secondary w-100 rounded-4 py-3">
                        <i class="bi bi-arrow-left"></i>
                        Cancelar
                    </a>
                </div>

                </div>

            </div>

        </div>

    </form>

</div>

<script>

const documentosResponsablesEndpoint = @json($responsablesEndpoint);
const personasBuscarEndpoint = @json($personasBuscarEndpoint);
const personasDuplicadosEndpoint = @json($duplicadosEndpoint);
const oldOpcionRemitente = @json(old('opcion_remitente', 'yo_mismo'));
const oldResponsableDestino = @json(old('responsable_destino'));

// Almacenar responsables por departamento
let responsablesPorDepartamento = {};

// Cargar responsables cuando se selecciona un departamento
function cargarResponsables(responsableSeleccionado = null) {
    const departamentoId = document.getElementById('departamento').value;
    const selectResponsable = document.getElementById('responsable_destino');
    const flujoDerivacion = document.getElementById('flujo_derivacion');
    const responsableActual = responsableSeleccionado ? String(responsableSeleccionado) : '';
    
    if (!departamentoId) {
        selectResponsable.innerHTML = '<option value="">-- Seleccione departamento primero --</option>';
        document.getElementById('buscar_responsable').value = '';
        flujoDerivacion.innerHTML = '<span class="badge bg-secondary">Selecciona departamento y responsable</span>';
        return;
    }
    
    // Obtener nombre del departamento
    const departamentoNombre = document.querySelector(`#departamento option[value="${departamentoId}"]`).textContent;
    
    // Obtener responsables del departamento
    fetch(`${documentosResponsablesEndpoint}/${departamentoId}`)
        .then(response => response.json())
        .then(data => {
            responsablesPorDepartamento = data;
            
            let html = '<option value="">-- Seleccione responsable --</option>';
            
            if (data.length === 0) {
                html = '<option value="">No hay responsables disponibles en este departamento</option>';
            } else {
                data.forEach(responsable => {
                    html += `<option value="${responsable.idPersona}" data-nombre="${responsable.nombre}" data-ci="${responsable.ci}">
                        ${responsable.nombre} (${responsable.ci}) - ${responsable.rol || 'Usuario'}
                    </option>`;
                });
            }
            
            selectResponsable.innerHTML = html;
            selectResponsable.value = responsableActual;
            document.getElementById('buscar_responsable').value = '';
            flujoDerivacion.innerHTML = `<span class="badge bg-info">${departamentoNombre} → Selecciona responsable</span>`;
            if (selectResponsable.value) {
                const option = selectResponsable.options[selectResponsable.selectedIndex];
                const responsableNombre = option?.getAttribute('data-nombre') || 'Responsable seleccionado';
                flujoDerivacion.innerHTML = `<span class="badge bg-success">${departamentoNombre} â†’ ${responsableNombre}</span>`;
                flujoDerivacion.innerHTML = `<span class="badge bg-success">${departamentoNombre} -> ${responsableNombre}</span>`;
            }
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

// ============================================================================
// GESTIÓN DE ESTADOS - STATE MACHINE
// ============================================================================

// Estados posibles
const ESTADOS = {
    BUSCADOR: 'estado_buscador',
    PERSONA_ENCONTRADA: 'estado_persona_encontrada',
    CREAR_NUEVA: 'estado_crear_nueva'
};

// Persona actualmente seleccionada (cache)
let personaSeleccionadaActual = null;

/**
 * Gestiona la transición entre estados
 * @param {string} estado - El estado destino (usar constantes ESTADOS)
 */
function irAEstado(estado) {
    // Ocultar todos los estados
    Object.values(ESTADOS).forEach(est => {
        const elemento = document.getElementById(est);
        if (elemento) {
            elemento.style.display = 'none';
        }
    });
    
    // Mostrar el estado solicitado
    const elementoEstado = document.getElementById(estado);
    if (elementoEstado) {
        elementoEstado.style.display = 'block';
    }

    document.querySelectorAll('.remitente-step').forEach(step => {
        step.classList.toggle('active', step.dataset.step === estado);
    });
    
    // Acciones específicas por estado
    if (estado === ESTADOS.BUSCADOR) {
        // Focus en el campo de búsqueda
        setTimeout(() => {
            const inputBuscar = document.getElementById('buscar_persona_input');
            if (inputBuscar) {
                inputBuscar.focus();
            }
        }, 100);
    }
}

/**
 * Vuelve al estado buscador limpiando todo
 */
function volverAlBuscador() {
    // Limpiar búsqueda
    document.getElementById('buscar_persona_input').value = '';
    document.getElementById('resultados_busqueda').style.display = 'none';
    document.getElementById('no_encontrado').style.display = 'none';
    document.getElementById('lista_resultados').innerHTML = '';
    document.getElementById('contador_resultados').textContent = '0';
    
    // Limpiar formulario
    limpiarFormularioOtraPersona();
    
    // Limpiar cache de persona seleccionada
    personaSeleccionadaActual = null;
    document.getElementById('persona_seleccionada_id').value = '';
    
    // Limpiar alerta de duplicados
    document.getElementById('alerta_duplicados').style.display = 'none';
    document.getElementById('lista_duplicados').innerHTML = '';
    
    // Ir al estado buscador
    irAEstado(ESTADOS.BUSCADOR);
}

function limpiarBusquedaPersona() {
    const input = document.getElementById('buscar_persona_input');

    if (input) {
        input.value = '';
        input.focus();
    }

    document.getElementById('resultados_busqueda').style.display = 'none';
    document.getElementById('no_encontrado').style.display = 'none';
    document.getElementById('lista_resultados').innerHTML = '';
    document.getElementById('contador_resultados').textContent = '0';
}

/**
 * Transición al estado "Persona Encontrada"
 * Cuando el usuario presiona "Usar esta persona", se rellenan los campos y se prepara el formulario
 */
function irAEstadoPersonaEncontrada() {
    if (personaSeleccionadaActual) {
        // Rellenar todos los campos ocultos con los datos de la persona seleccionada
        document.getElementById('ci_remitente_otra').value = personaSeleccionadaActual.ci;
        document.getElementById('nombre_remitente_otra').value = personaSeleccionadaActual.nombre;
        document.getElementById('telefono_celular_otra').value = personaSeleccionadaActual.telefono_celular || '';
        document.getElementById('telefono_fijo_otra').value = personaSeleccionadaActual.telefono_fijo || '';
        document.getElementById('correo_remitente_otra').value = personaSeleccionadaActual.correo || '';
        document.getElementById('tipo_remitente_otra').value = personaSeleccionadaActual.tipo;
        
        if (personaSeleccionadaActual.tipo === 'INTERNO') {
            document.getElementById('cargo_remitente_otra').value = personaSeleccionadaActual.cargo || '';
        }
        
        // Guardar ID para envío (campo hidden que usará la API)
        document.getElementById('persona_seleccionada_id').value = personaSeleccionadaActual.idPersona;
    }
    
    // Mostrar estado PERSONA_ENCONTRADA con la tarjeta de confirmación
    irAEstado(ESTADOS.PERSONA_ENCONTRADA);
}

/**
 * Finaliza la selección de una persona encontrada
 * Cierra el bloque de búsqueda y prepara el formulario para envío
 */
function finalizarSeleccionPersona() {
    if (personaSeleccionadaActual) {
        // Los campos ya están rellenados por irAEstadoPersonaEncontrada()
        // Guardar ID para envío (campo hidden que usará la API)
        document.getElementById('persona_seleccionada_id').value = personaSeleccionadaActual.idPersona;
        
        document.getElementById('persona_confirmada_card')?.classList.add('selected-confirmed');
        const hint = document.getElementById('persona_confirmada_hint');

        if (hint) {
            hint.textContent = 'Remitente confirmado para este documento';
        }
    }
}

/**
 * Transición al estado "Crear Nueva"
 */
function irAEstadoCrearNueva() {
    limpiarFormularioOtraPersona();
    irAEstado(ESTADOS.CREAR_NUEVA);
}

// ============================================================================
// TOGGLE REMITENTE MODE
// ============================================================================

function toggleRemitenteMode(preserveValues = false) {
    const yoMismo = document.getElementById('yo_mismo').checked;
    const bloqueYoMismo = document.getElementById('bloque_yo_mismo');
    const bloqueOtra = document.getElementById('bloque_otra_persona');
    
    if (yoMismo) {
        // Seleccionado: "Yo Mismo"
        bloqueYoMismo.style.display = 'block';
        bloqueOtra.style.display = 'none';
        if (!preserveValues) {
            limpiarFormularioOtraPersona();
        }
        
        // Limpiar los campos de formulario que se enviarán
        // De esta manera, cuando se valide, no habrá conflicto con los campos
        // del remitente de "Otra Persona"
    } else {
        // Seleccionado: "Otra Persona"
        bloqueYoMismo.style.display = 'none';
        bloqueOtra.style.display = 'block';
        if (preserveValues) {
            prepararOtraPersonaConValoresPrevios();
        } else {
            // Iniciar en estado buscador
            volverAlBuscador();
        }
    }
}

function prepararOtraPersonaConValoresPrevios() {
    const personaSeleccionadaId = document.getElementById('persona_seleccionada_id').value;
    const tieneDatosPersona =
        document.getElementById('nombre_remitente_otra').value ||
        document.getElementById('ci_remitente_otra').value ||
        document.getElementById('telefono_celular_otra').value ||
        document.getElementById('telefono_fijo_otra').value ||
        document.getElementById('correo_remitente_otra').value ||
        document.getElementById('tipo_remitente_otra').value ||
        document.getElementById('cargo_remitente_otra').value ||
        document.getElementById('institucion_remitente_otra').value ||
        personaSeleccionadaId;

    document.getElementById('buscar_persona_input').value = '';
    document.getElementById('resultados_busqueda').style.display = 'none';
    document.getElementById('no_encontrado').style.display = 'none';
    document.getElementById('lista_resultados').innerHTML = '';
    document.getElementById('contador_resultados').textContent = '0';
    document.getElementById('alerta_duplicados').style.display = 'none';

    if (personaSeleccionadaId) {
        restaurarPersonaSeleccionadaConValoresPrevios();
        return;
    }

    if (tieneDatosPersona) {
        irAEstado(ESTADOS.CREAR_NUEVA);
        actualizarCamposTipo();
        return;
    }

    irAEstado(ESTADOS.BUSCADOR);
}

function restaurarPersonaSeleccionadaConValoresPrevios() {
    const nombre = document.getElementById('nombre_remitente_otra').value || 'Remitente seleccionado';
    const ci = document.getElementById('ci_remitente_otra').value || 'Sin CI';
    const tipo = document.getElementById('tipo_remitente_otra').value || 'INTERNO';
    const correo = document.getElementById('correo_remitente_otra').value || 'Sin correo';
    const celular = document.getElementById('telefono_celular_otra').value || 'Sin telefono';
    const cargo = document.getElementById('cargo_remitente_otra').value || 'Sin cargo';
    const institucion = document.getElementById('institucion_remitente_otra').value || 'Sin institucion';

    document.getElementById('sel_nombre').textContent = nombre;
    document.getElementById('sel_ci').textContent = ci;
    document.getElementById('sel_correo').textContent = correo;
    document.getElementById('sel_celular').textContent = celular;

    const tipoBadge = document.getElementById('sel_tipo');
    tipoBadge.textContent = tipo;
    tipoBadge.className = tipo === 'INTERNO' ? 'badge bg-success' : 'badge bg-warning text-dark';

    if (tipo === 'INTERNO') {
        document.getElementById('label_interno_externo').textContent = 'Cargo';
        document.getElementById('sel_departamento_cargo').innerHTML = `<div class="small">${cargo}</div>`;
    } else {
        document.getElementById('label_interno_externo').textContent = 'Institucion';
        document.getElementById('sel_departamento_cargo').innerHTML = `<div class="small">${institucion}</div>`;
    }

    document.getElementById('persona_confirmada_card')?.classList.add('selected-confirmed');
    document.getElementById('persona_confirmada_hint').textContent = 'Remitente recuperado despues de la validacion';
    irAEstado(ESTADOS.PERSONA_ENCONTRADA);
}

// ============================================================================
// BÚSQUEDA INTELIGENTE CON DEBOUNCE
// ============================================================================

let debounceTimer = null;
const DEBOUNCE_DELAY = 400; // ms

document.getElementById('buscar_persona_input')?.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const buscar = this.value.trim();
    
    // Limpiar resultados previos
    document.getElementById('no_encontrado').style.display = 'none';
    document.getElementById('resultados_busqueda').style.display = 'none';
    
    if (buscar.length < 2) {
        return;
    }
    
    debounceTimer = setTimeout(() => {
        buscarPersonasAvanzado(buscar);
    }, DEBOUNCE_DELAY);
});

function buscarPersonasAvanzado(buscar) {
    fetch(`${personasBuscarEndpoint}?q=${encodeURIComponent(buscar)}`)
        .then(response => response.json())
        .then(data => {
            const resultados = data.resultados || [];
            const listaBusqueda = document.getElementById('lista_resultados');
            
            if (resultados.length === 0) {
                document.getElementById('no_encontrado').style.display = 'block';
                document.getElementById('resultados_busqueda').style.display = 'none';
                // Nota: El botón "Registrar Nueva Persona" ahora es permanente, no lo mostramos aquí
                return;
            }
            
            document.getElementById('contador_resultados').textContent = resultados.length;
            listaBusqueda.innerHTML = '';
            
            resultados.forEach(persona => {
                listaBusqueda.appendChild(crearTarjetaPersona(persona));
            });
            
            document.getElementById('resultados_busqueda').style.display = 'block';
            document.getElementById('no_encontrado').style.display = 'none';
        })
        .catch(error => {
            console.error('Error en búsqueda:', error);
            document.getElementById('no_encontrado').style.display = 'block';
            document.getElementById('btn_crear_nueva_desde_busqueda').style.display = 'block';
        });
}

function crearTarjetaPersona(persona) {
    const div = document.createElement('div');
    div.className = 'remitente-result-card';
    
    let infoAdicional = '';
    if (persona.tipo === 'INTERNO') {
        infoAdicional = `
            <div class="small text-muted mt-1">
                <span class="me-3"><i class="bi bi-briefcase me-1"></i>${persona.cargo || 'Sin cargo'}</span>
                <span><i class="bi bi-building me-1"></i>${persona.departamento || 'Sin departamento'}</span>
            </div>
        `;
    } else {
        infoAdicional = `
            <div class="small text-muted mt-1">
                <i class="bi bi-globe me-1"></i>${persona.institucion || 'Sin institución'}
            </div>
        `;
    }
    
    div.innerHTML = `
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <div class="fw-bold" style="color:#0B2D59;">${persona.nombre}</div>
                    <span class="badge ${persona.tipo === 'INTERNO' ? 'bg-success' : 'bg-warning text-dark'} me-2">
                        ${persona.tipo}
                    </span>
                </div>
                <div class="small text-muted"><strong>CI:</strong> ${persona.ci}</div>
                ${infoAdicional}
                <div class="small text-muted mt-1">
                    ${persona.correo ? `<span class="me-3"><i class="bi bi-envelope me-1"></i>${persona.correo}</span>` : ''}
                    ${persona.telefono_celular ? `<span><i class="bi bi-telephone me-1"></i>${persona.telefono_celular}</span>` : ''}
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-3" onclick="seleccionarPersona(${persona.idPersona})">
                <i class="bi bi-check-circle me-1"></i>Usar
            </button>
        </div>
    `;
    
    return div;
}

function seleccionarPersona(idPersona) {
    const buscar = document.getElementById('buscar_persona_input').value.trim();
    
    fetch(`${personasBuscarEndpoint}?q=${encodeURIComponent(buscar)}`)
        .then(response => response.json())
        .then(data => {
            const persona = data.resultados.find(p => p.idPersona === idPersona);
            if (persona) {
                mostrarPersonaSeleccionada(persona);
            }
        });
}

function mostrarPersonaSeleccionada(persona) {
    // Guardar en cache
    personaSeleccionadaActual = persona;
    
    // Llenar card de persona seleccionada
    document.getElementById('sel_nombre').textContent = persona.nombre;
    document.getElementById('sel_ci').textContent = persona.ci;
    document.getElementById('sel_correo').textContent = persona.correo || 'No registrado';
    document.getElementById('sel_celular').textContent = persona.telefono_celular || 'No registrado';
    
    const tipoElement = document.getElementById('sel_tipo');
    tipoElement.textContent = persona.tipo;
    tipoElement.className = persona.tipo === 'INTERNO' ? 'badge bg-success' : 'badge bg-warning text-dark';
    
    if (persona.tipo === 'INTERNO') {
        document.getElementById('label_interno_externo').textContent = 'Departamento y Cargo';
        document.getElementById('sel_departamento_cargo').innerHTML = `
            <div class="small">${persona.departamento || 'Sin asignar'}</div>
            ${persona.cargo ? `<div class="small text-muted">${persona.cargo}</div>` : ''}
        `;
    } else {
        document.getElementById('label_interno_externo').textContent = 'Institución';
        document.getElementById('sel_departamento_cargo').innerHTML = `
            <div class="small">${persona.institucion || 'Sin institución'}</div>
        `;
    }
    
    // Ir al estado persona encontrada
    irAEstadoPersonaEncontrada();
}

function limpiarFormularioOtraPersona() {
    document.getElementById('ci_remitente_otra').value = '';
    document.getElementById('nombre_remitente_otra').value = '';
    document.getElementById('telefono_celular_otra').value = '';
    document.getElementById('telefono_fijo_otra').value = '';
    document.getElementById('correo_remitente_otra').value = '';
    document.getElementById('tipo_remitente_otra').value = '';
    document.getElementById('cargo_remitente_otra').value = '';
    document.getElementById('institucion_remitente_otra').value = '';
    document.getElementById('alerta_duplicados').style.display = 'none';
}

function actualizarCamposTipo() {
    const tipo = document.getElementById('tipo_remitente_otra').value;
    const camposInterno = document.getElementById('campos_interno');
    const camposExterno = document.getElementById('campos_externo');
    
    if (tipo === 'INTERNO') {
        camposInterno.style.display = 'block';
        camposExterno.style.display = 'none';
    } else if (tipo === 'EXTERNO') {
        camposInterno.style.display = 'none';
        camposExterno.style.display = 'block';
    } else {
        camposInterno.style.display = 'none';
        camposExterno.style.display = 'none';
    }
}

function verificarCI() {
    const ci = document.getElementById('ci_remitente_otra').value.trim();
    if (ci.length > 0) {
        document.getElementById('buscar_persona_input').value = ci;
        buscarPersonasAvanzado(ci);
    }
}

function guardarNuevaPersona() {
    const nombre = document.getElementById('nombre_remitente_otra').value.trim();
    const ci = document.getElementById('ci_remitente_otra').value.trim();
    const correo = document.getElementById('correo_remitente_otra').value.trim();
    const institucion = document.getElementById('institucion_remitente_otra').value.trim();
    const cargo = document.getElementById('cargo_remitente_otra').value.trim();
    
    // Verificar duplicados
    fetch(personasDuplicadosEndpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ci: ci,
            correo: correo,
            nombre: nombre,
            institucion: institucion,
            cargo: cargo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.tiene_duplicados && data.encontrados.length > 0) {
            mostrarAlertaDuplicados(data.encontrados);
        } else {
            confirmarCrearDuplicado();
        }
    })
    .catch(error => {
        console.error('Error al verificar duplicados:', error);
        confirmarCrearDuplicado();
    });
}

function mostrarAlertaDuplicados(encontrados) {
    const listaDuplicados = document.getElementById('lista_duplicados');
    listaDuplicados.innerHTML = '';
    
    encontrados.forEach(item => {
        const persona = item.persona;
        const div = document.createElement('div');
        div.className = 'p-2 border-bottom small';
        
        let info = `<strong>${persona.nombre}</strong> - ${persona.tipo} - CI: ${persona.ci}`;
        if (persona.cargo) info += ` - ${persona.cargo}`;
        if (persona.institucion) info += ` - ${persona.institucion}`;
        
        div.innerHTML = `
            <div>${info}</div>
            <small class="text-muted d-block">${item.razon}</small>
            <button type="button" class="btn btn-xs btn-link mt-1" onclick="seleccionarPersona(${persona.idPersona})">
                Usar esta persona
            </button>
        `;
        listaDuplicados.appendChild(div);
    });
    
    document.getElementById('alerta_duplicados').style.display = 'block';
}

function confirmarCrearDuplicado() {
    // El formulario se enviará con los datos completados
    document.querySelector('form').submit();
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
    toggleRemitenteMode(true);
    actualizarCamposTipo();

    if (document.getElementById('departamento')?.value) {
        cargarResponsables(oldResponsableDestino);
    }
});

</script>

@endsection
