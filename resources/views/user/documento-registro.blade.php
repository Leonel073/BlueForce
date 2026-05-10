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

    {{-- ALERTAS --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill"></i>

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    {{-- VALIDACIONES --}}
    @if($errors->any())

        <div class="alert alert-warning alert-dismissible fade show">

            <strong>

                Existen errores en el formulario

            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

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
                                      required>{{ old('asunto') }}</textarea>

                            @error('asunto')

                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>

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

                                        Seleccione

                                    </option>

                                    @foreach($tiposDocumento as $tipo)

                                        <option value="{{ $tipo->idTipoDocumento }}"
                                            @selected(old('tipo_documento') == $tipo->idTipoDocumento)>

                                            {{ $tipo->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('tipo_documento')

                                    <div class="invalid-feedback">

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

                                        Seleccione

                                    </option>

                                    @foreach($nivelesUrgencia as $urgencia)

                                        <option value="{{ $urgencia->idUrgencia }}"
                                            @selected(old('nivel_urgencia') == $urgencia->idUrgencia)>

                                            {{ $urgencia->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('nivel_urgencia')

                                    <div class="invalid-feedback">

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
                                    value="{{ old('ci_remitente') }}"
                                    required>

                                @error('ci_remitente')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

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
                                    value="{{ old('telefono_celular') }}"
                                    required>

                                @error('telefono_celular')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

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

                            {{-- DEPARTAMENTO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Departamento

                                </label>

                                <select name="departamento_remitente"
                                        id="departamento_remitente"
                                        class="form-select">

                                    <option value="">

                                        Seleccione

                                    </option>

                                    @foreach($departamentos as $depto)

                                        <option value="{{ $depto->idDepartamento }}">

                                            {{ $depto->nombre }}

                                        </option>

                                    @endforeach

                                </select>

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
                                value="{{ old('nombre_remitente') }}"
                                required>

                            @error('nombre_remitente')

                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>

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
                                    class="form-control"
                                    value="{{ old('correo_remitente') }}">

                            </div>

                            {{-- CARGO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Cargo

                                </label>

                                <input type="text"
                                    id="cargo_remitente"
                                    name="cargo_remitente"
                                    class="form-control"
                                    value="{{ old('cargo_remitente') }}">

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
                                    class="form-control"
                                    value="{{ old('institucion_remitente') }}">

                            </div>

                            {{-- TIPO --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Tipo
                                    <span class="text-danger">*</span>

                                </label>

                                <select name="tipo_remitente"
                                        id="tipo_remitente"
                                        class="form-select"
                                        required>

                                    <option value="">

                                        Seleccione

                                    </option>

                                    <option value="INTERNO">

                                        INTERNO

                                    </option>

                                    <option value="EXTERNO">

                                        EXTERNO

                                    </option>

                                </select>

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

                                    Seleccione departamento

                                </option>

                                @foreach($departamentos as $depto)

                                    <option value="{{ $depto->idDepartamento }}">

                                        {{ $depto->nombre }}

                                    </option>

                                @endforeach

                            </select>

                            @error('departamento')

                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>

                            @enderror

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

                document.getElementById('cargo_remitente')
                    .value = p.cargo || '';

                document.getElementById('institucion_remitente')
                    .value = p.institucion || '';

                document.getElementById('telefono_celular')
                    .value = p.telefono_celular || '';

                document.getElementById('telefono_fijo')
                    .value = p.telefono_fijo || '';

                document.getElementById('tipo_remitente')
                    .value = p.tipo || '';

                document.getElementById('departamento_remitente')
                    .value = p.idDepartamento || '';

                document.getElementById('personaEncontrada')
                    .classList.remove('d-none');
            }
            else
            {
                document.getElementById('personaEncontrada')
                    .classList.add('d-none');
            }

        });

    });

</script>

@endsection