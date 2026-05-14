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

    {{-- ALERTAS DE ÉXITO --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            <strong>¡Éxito!</strong> {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>

        </div>

    @endif

    {{-- ALERTAS DE ERROR GENERAL --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <strong>Error:</strong> {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>

        </div>

    @endif

    {{-- VALIDACIONES CON DETALLE POR CAMPO --}}
    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">

            <div class="d-flex align-items-start">

                <div>

                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>

                </div>

                <div class="flex-grow-1">

                    <strong class="d-block mb-2">

                        Se encontraron {{ $errors->count() }} 
                        error{{ $errors->count() > 1 ? 'es' : '' }} en el formulario

                    </strong>

                    <ul class="mb-0 ms-3 small">

                        @foreach($errors->all() as $error)

                            <li class="mb-1">

                                <i class="bi bi-dash-circle text-danger me-1"></i>

                                {{ $error }}

                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>

        </div>

    @endif

    {{-- FORMULARIO --}}
    <form action="{{ route('documentos.store') }}"
          method="POST"
          class="needs-validation"
          novalidate>

        @csrf

        <div class="row">

            {{-- COLUMNA PRINCIPAL --}}
            <div class="col-lg-8">

                {{-- DOCUMENTO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">

                    <div class="card-header text-white rounded-top-4"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

                        <i class="bi bi-file-earmark-text-fill"></i>

                        DATOS DEL DOCUMENTO

                    </div>

                    <div class="card-body">

                        {{-- CÓDIGO --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Código de Ruta

                            </label>

                            <input type="text"
                                   id="codigo_ruta"
                                   class="form-control"
                                   disabled>

                            <small class="text-muted">

                                El sistema genera automáticamente el código.

                            </small>

                        </div>

                        {{-- ASUNTO --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Asunto
                                <span class="text-danger">*</span>

                            </label>

                            <textarea name="asunto"
                                      rows="4"
                                      class="form-control @error('asunto') is-invalid @enderror"
                                      placeholder="Ingrese una descripción clara del asunto del documento"
                                      required>{{ old('asunto') }}</textarea>

                            @error('asunto')

                                <div class="invalid-feedback d-block">

                                    <i class="bi bi-exclamation-circle me-1"></i>

                                    {{ $message }}

                                </div>

                            @else

                                <small class="text-muted">

                                    <i class="bi bi-info-circle me-1"></i>

                                    Máximo 500 caracteres.

                                </small>

                            @enderror

                        </div>

                        {{-- TIPO Y URGENCIA --}}
                        <div class="row">

                            {{-- TIPO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Tipo Documento
                                    <span class="text-danger">*</span>

                                </label>

                                <select name="tipo_documento"
                                        id="tipo_documento"
                                        class="form-select @error('tipo_documento') is-invalid @enderror"
                                        required>

                                    <option value="">

                                        -- Seleccione un tipo --

                                    </option>

                                    @foreach($tiposDocumento as $tipo)

                                        <option value="{{ $tipo->idTipoDocumento }}"
                                            @selected(old('tipo_documento') == $tipo->idTipoDocumento)>

                                            {{ $tipo->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('tipo_documento')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                            {{-- URGENCIA --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Nivel Urgencia
                                    <span class="text-danger">*</span>

                                </label>

                                <select name="nivel_urgencia"
                                        id="nivel_urgencia"
                                        class="form-select @error('nivel_urgencia') is-invalid @enderror"
                                        required>

                                    <option value="">

                                        -- Seleccione nivel --

                                    </option>

                                    @foreach($nivelesUrgencia as $urgencia)

                                        <option value="{{ $urgencia->idUrgencia }}"
                                            @selected(old('nivel_urgencia') == $urgencia->idUrgencia)>

                                            {{ $urgencia->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('nivel_urgencia')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                        </div>

                    </div>

                </div>

               {{-- REMITENTE --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">

                    <div class="card-header text-white rounded-top-4"
                        style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

                        <i class="bi bi-person-fill"></i>

                        DATOS DEL REMITENTE

                    </div>

                    <div class="card-body">

                        {{-- ALERTA --}}
                        <div class="alert alert-info border-0 rounded-4">

                            <i class="bi bi-search"></i>

                            Ingrese el número de carnet para buscar automáticamente
                            si el remitente ya existe en el sistema.

                        </div>

                        {{-- CI Y CELULAR --}}
                        <div class="row">

                            {{-- CI --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Carnet de Identidad
                                    <span class="text-danger">*</span>

                                </label>

                                <input type="text"
                                    id="ci_remitente"
                                    name="ci_remitente"
                                    class="form-control @error('ci_remitente') is-invalid @enderror"
                                    placeholder="Ej: 1234567-8"
                                    value="{{ old('ci_remitente') }}"
                                    required>

                                @error('ci_remitente')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @else

                                    <small class="text-muted">

                                        <i class="bi bi-info-circle me-1"></i>

                                        Ingrese su número de cédula.

                                    </small>

                                @enderror

                            </div>

                            {{-- CELULAR --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Teléfono Celular
                                    <span class="text-danger">*</span>

                                </label>

                                <input type="text"
                                    id="telefono_celular"
                                    name="telefono_celular"
                                    class="form-control @error('telefono_celular') is-invalid @enderror"
                                    placeholder="Ej: +591 71234567"
                                    value="{{ old('telefono_celular') }}"
                                    required>

                                @error('telefono_celular')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @else

                                    <small class="text-muted">

                                        <i class="bi bi-info-circle me-1"></i>

                                        Incluya el prefijo del país si es necesario.

                                    </small>

                                @enderror

                            </div>

                        </div>

                        {{-- FIJO Y DEPARTAMENTO --}}
                        <div class="row">

                            {{-- FIJO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Teléfono Fijo

                                </label>

                                <input type="text"
                                    id="telefono_fijo"
                                    name="telefono_fijo"
                                    class="form-control"
                                    value="{{ old('telefono_fijo') }}">

                            </div>

                        </div>

                        {{-- NOMBRE --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Nombre Completo
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                id="nombre_remitente"
                                name="nombre_remitente"
                                class="form-control @error('nombre_remitente') is-invalid @enderror"
                                placeholder="Ej: Juan Carlos García López"
                                value="{{ old('nombre_remitente') }}"
                                required>

                            @error('nombre_remitente')

                                <div class="invalid-feedback d-block">

                                    <i class="bi bi-exclamation-circle me-1"></i>

                                    {{ $message }}

                                </div>

                            @else

                                <small class="text-muted">

                                    <i class="bi bi-info-circle me-1"></i>

                                    Solo se permiten letras y espacios.

                                </small>

                            @enderror

                        </div>

                        {{-- CORREO Y CARGO --}}
                        <div class="row">

                            {{-- CORREO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Correo

                                </label>

                                <input type="email"
                                    id="correo_remitente"
                                    name="correo_remitente"
                                    class="form-control @error('correo_remitente') is-invalid @enderror"
                                    placeholder="usuario@ejemplo.com"
                                    value="{{ old('correo_remitente') }}">

                                @error('correo_remitente')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @else

                                    <small class="text-muted">

                                        <i class="bi bi-info-circle me-1"></i>

                                        Opcional - Formato: usuario@ejemplo.com

                                    </small>

                                @enderror

                            </div>

                            {{-- CARGO (Solo para internos) --}}
                            <div class="col-md-6 mb-3" id="cargo_remitente_section" style="display: none;">

                                <label class="form-label fw-semibold">

                                    Cargo

                                </label>

                                <input type="text"
                                    id="cargo_remitente"
                                    name="cargo_remitente"
                                    class="form-control"
                                    placeholder="Auto completado para internos"
                                    readonly>

                                <small class="text-muted d-block mt-2">

                                    <i class="bi bi-info-circle me-1"></i>

                                    Se completa automáticamente para personas internas del sistema.

                                </small>

                            </div>

                        </div>

                        {{-- INSTITUCIÓN Y TIPO --}}
                        <div class="row">

                            {{-- INSTITUCIÓN --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Institución

                                </label>

                                <input type="text"
                                    id="institucion_remitente"
                                    name="institucion_remitente"
                                    class="form-control @error('institucion_remitente') is-invalid @enderror"
                                    placeholder="Ej: Ministerio de Educación"
                                    value="{{ old('institucion_remitente') }}">

                                @error('institucion_remitente')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @else

                                    <small class="text-muted">

                                        <i class="bi bi-info-circle me-1"></i>

                                        Opcional - Letras, números y espacios.

                                    </small>

                                @enderror

                            </div>

                            {{-- TIPO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Tipo de Remitente
                                    <span class="text-danger">*</span>

                                </label>

                                <select name="tipo_remitente"
                                        id="tipo_remitente"
                                        class="form-select @error('tipo_remitente') is-invalid @enderror"
                                        required>

                                    <option value="">

                                        -- Seleccione tipo --

                                    </option>

                                    <option value="INTERNO" @selected(old('tipo_remitente') == 'INTERNO')>

                                        <i class="bi bi-person-circle"></i> INTERNO

                                    </option>

                                    <option value="EXTERNO" @selected(old('tipo_remitente') == 'EXTERNO')>

                                        <i class="bi bi-globe"></i> EXTERNO

                                    </option>

                                </select>

                                @error('tipo_remitente')

                                    <div class="invalid-feedback d-block">

                                        <i class="bi bi-exclamation-circle me-1"></i>

                                        {{ $message }}

                                    </div>

                                @else

                                    <small class="text-muted">

                                        <i class="bi bi-info-circle me-1"></i>

                                        Interno: dentro de la institución | Externo: de afuera.

                                    </small>

                                @enderror

                            </div>

                        </div>

                        {{-- MENSAJE --}}
                        <div id="personaEncontrada"
                            class="alert alert-success d-none rounded-4 border-0">

                            <i class="bi bi-check-circle-fill"></i>

                            Persona encontrada en el sistema.
                            Datos autocompletados.

                        </div>

                    </div>

                </div>

            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-lg-4">

                {{-- DESTINO --}}
                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-header text-white rounded-top-4"
                         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

                        <i class="bi bi-building-fill"></i>

                        DESTINO DOCUMENTAL

                    </div>

                    <div class="card-body">

                        {{-- DEPARTAMENTO --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Departamento Destino
                                <span class="text-danger">*</span>

                            </label>

                            <select name="departamento"
                                    id="departamento"
                                    class="form-select @error('departamento') is-invalid @enderror"
                                    required>

                                <option value="">

                                    -- Seleccione departamento --

                                </option>

                                @foreach($departamentos as $depto)

                                    <option value="{{ $depto->idDepartamento }}"
                                        @selected(old('departamento') == $depto->idDepartamento)>

                                        {{ $depto->nombre }}

                                    </option>

                                @endforeach

                            </select>

                            @error('departamento')

                                <div class="invalid-feedback d-block">

                                    <i class="bi bi-exclamation-circle me-1"></i>

                                    {{ $message }}

                                </div>

                            @else

                                <small class="text-muted">

                                    <i class="bi bi-info-circle me-1"></i>

                                    Seleccione a dónde será derivado el documento.

                                </small>

                            @enderror

                        </div>

                        {{-- PERSONA DESTINATARIA (Cargada dinámicamente) --}}
                        <div class="mb-3" id="destinatario_section" style="display: none;">

                            <label class="form-label fw-semibold">

                                <i class="bi bi-person-check"></i>

                                Persona Destinataria

                            </label>

                            <select id="persona_destinataria"
                                    name="persona_destinataria"
                                    class="form-select">

                                <option value="">

                                    -- Seleccione una persona --

                                </option>

                            </select>

                            <small class="text-muted d-block mt-2">

                                <i class="bi bi-info-circle me-1"></i>

                                Personas activas del departamento seleccionado.

                            </small>

                        </div>

                        {{-- ALERTA --}}
                        <div class="alert alert-warning small">

                            <i class="bi bi-arrow-repeat"></i>

                            El documento será derivado automáticamente
                            al departamento seleccionado.

                        </div>

                        {{-- FLUJO --}}
                        <div class="border rounded-4 p-3 bg-light">

                            <small class="text-muted fw-semibold">

                                Flujo automático

                            </small>

                            <div class="mt-3 text-center">

                                <i class="bi bi-person-fill"></i>

                                Usuario

                                <i class="bi bi-arrow-right mx-2"></i>

                                <i class="bi bi-building"></i>

                                Departamento

                            </div>

                        </div>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="mt-4">

                    <button type="submit"
                            class="btn text-white w-100 mb-2 rounded-4 py-3"
                            style="background-color:#0B2D59;">

                        <i class="bi bi-check-circle-fill"></i>

                        Registrar Documento

                    </button>

                    <a href="{{ Auth::user()->idRol == 1
                        ? route('admin.dashboard')
                        : route('user.dashboard') }}"
                       class="btn btn-outline-secondary w-100 rounded-4 py-3">

                        <i class="bi bi-arrow-left"></i>

                        Cancelar

                    </a>

                </div>

            </div>

        </div>

    </form>

</div>

{{-- SCRIPT --}}
<script>

    /*
    |--------------------------------------------------------------------------
    | GENERAR CÓDIGO DE RUTA
    |--------------------------------------------------------------------------
    */

    const tiposDocumentoMap = {};
    const nivelesUrgenciaMap = {};

    @foreach($tiposDocumento as $tipo)

        tiposDocumentoMap[{{ $tipo->idTipoDocumento }}]
            = '{{ strtoupper(substr($tipo->nombre,0,1)) }}';

    @endforeach

    @foreach($nivelesUrgencia as $urgencia)

        nivelesUrgenciaMap[{{ $urgencia->idUrgencia }}]
            = '{{ strtoupper(substr($urgencia->nombre,0,1)) }}';

    @endforeach

    function generarCodigoRuta()
    {
        const tipo =
            document.getElementById('tipo_documento').value;

        const urgencia =
            document.getElementById('nivel_urgencia').value;

        if(tipo && urgencia)
        {
            const letraTipo =
                tiposDocumentoMap[tipo] || 'X';

            const letraUrgencia =
                nivelesUrgenciaMap[urgencia] || 'X';

            const hoy = new Date();

            const anio = hoy.getFullYear();

            const mes =
                String(hoy.getMonth()+1).padStart(2,'0');

            const dia =
                String(hoy.getDate()).padStart(2,'0');

            document.getElementById('codigo_ruta').value =
                `${letraTipo}${letraUrgencia}-${anio}-${mes}-${dia}-AUTO`;
        }
    }

    document.getElementById('tipo_documento')
        .addEventListener('change', generarCodigoRuta);

    document.getElementById('nivel_urgencia')
        .addEventListener('change', generarCodigoRuta);

    generarCodigoRuta();

    /*
    |--------------------------------------------------------------------------
    | BUSCAR PERSONA POR CI
    |--------------------------------------------------------------------------
    */

    document.getElementById('ci_remitente')
    .addEventListener('blur', function () {

        let ci = this.value;

        if(ci.length < 3)
            return;

        fetch(`/persona/buscar/${ci}`)

        .then(response => response.json())

        .then(data => {

            if(data.success)
            {
                let p = data.persona;

                document.getElementById('nombre_remitente')
                    .value = p.nombre || '';

                document.getElementById('correo_remitente')
                    .value = p.correo || '';

                document.getElementById('institucion_remitente')
                    .value = p.institucion || '';

                document.getElementById('telefono_celular')
                    .value = p.telefono_celular || '';

                document.getElementById('telefono_fijo')
                    .value = p.telefono_fijo || '';

                document.getElementById('tipo_remitente')
                    .value = p.tipo || '';

                // Mostrar/ocultar cargo según tipo
                toggleCargoField();

                // Si es interno y tiene cargo, mostrar
                if (p.es_interno && p.cargo) {
                    document.getElementById('cargo_remitente').value = p.cargo;
                    document.getElementById('cargo_remitente_section').style.display = 'block';
                } else {
                    document.getElementById('cargo_remitente').value = '';
                    document.getElementById('cargo_remitente_section').style.display = 'none';
                }

                document.getElementById('personaEncontrada')
                    .classList.remove('d-none');
            }
            else
            {
                document.getElementById('personaEncontrada')
                    .classList.add('d-none');
                    
                // Limpiar cargo
                document.getElementById('cargo_remitente').value = '';
                document.getElementById('cargo_remitente_section').style.display = 'none';
            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR/OCULTAR CARGO SEGÚN TIPO DE REMITENTE
    |--------------------------------------------------------------------------
    */

    const tipoRemitenteSelect = document.getElementById('tipo_remitente');
    const cargoRemitenteSection = document.getElementById('cargo_remitente_section');
    const cargoRemitenteInput = document.getElementById('cargo_remitente');

    function toggleCargoField() {
        const tipoValue = tipoRemitenteSelect.value;

        if (tipoValue === 'INTERNO') {
            cargoRemitenteSection.style.display = 'block';
        } else {
            cargoRemitenteSection.style.display = 'none';
            cargoRemitenteInput.value = '';
        }
    }

    tipoRemitenteSelect.addEventListener('change', toggleCargoField);

    /*
    |--------------------------------------------------------------------------
    | CARGAR PERSONAS AL SELECCIONAR DEPARTAMENTO
    |--------------------------------------------------------------------------
    */

    const departamentoSelect = document.getElementById('departamento');
    const destinatarioSection = document.getElementById('destinatario_section');
    const personaDestinaria = document.getElementById('persona_destinataria');

    departamentoSelect.addEventListener('change', function () {
        const idDepartamento = this.value;

        if (!idDepartamento) {
            destinatarioSection.style.display = 'none';
            personaDestinaria.innerHTML = '<option value="">-- Seleccione una persona --</option>';
            return;
        }

        // Cargar personas del departamento
        fetch(`/documentos/departamento/${idDepartamento}/personas`)
            .then(response => response.json())
            .then(personas => {
                let html = '<option value="">-- Seleccione una persona --</option>';

                if (personas.length > 0) {
                    personas.forEach(persona => {
                        html += `<option value="${persona.idPersona}">
                            ${persona.nombre} (${persona.cargo})
                        </option>`;
                    });

                    destinatarioSection.style.display = 'block';
                } else {
                    html += '<option disabled>No hay personas en este departamento</option>';
                    destinatarioSection.style.display = 'block';
                }

                personaDestinaria.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                personaDestinaria.innerHTML = '<option value="">Error al cargar personas</option>';
            });
    });

</script>

@endsection