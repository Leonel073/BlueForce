@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')

<div class="container py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold text-white mb-1">
                    <i class="bi bi-pencil-square"></i>
                    Editar Usuario
                </h1>
                <p class="text-light mb-0">
                    {{ $usuario->name }} — {{ $usuario->email }}
                </p>
            </div>
            <a href="{{ route('admin.usuarios') }}"
               class="btn btn-light rounded-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">

        {{-- FORMULARIO PRINCIPAL --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header text-white rounded-top-4"
                     style="background-color:#0B2D59;">
                    <i class="bi bi-pencil-fill"></i>
                    Datos del Usuario
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.usuarios.update', $usuario->id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        {{-- PERSONA VINCULADA (solo lectura) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">
                                <i class="bi bi-person-badge-fill me-1"></i>
                                Persona vinculada
                                <span class="badge bg-secondary ms-1" style="font-size:11px;">Solo lectura</span>
                            </label>
                            <div class="bg-light rounded-3 p-3 border">
                                @if($usuario->persona)
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Nombre</small>
                                            <strong>{{ $usuario->persona->nombre }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">CI</small>
                                            <strong>{{ $usuario->persona->ci ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Cargo</small>
                                            <span>{{ $usuario->persona->cargo?->nombre ?? 'Sin cargo' }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Departamento</small>
                                            <span>{{ $usuario->persona->departamento?->nombre ?? 'Sin departamento' }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Tipo persona</small>
                                            <span class="badge {{ $usuario->persona->tipo_persona === 'trabajador' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($usuario->persona->tipo_persona ?? 'N/A') }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Este usuario no tiene persona vinculada. Requiere corrección.
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted">
                                La persona vinculada no puede cambiarse para mantener la integridad del sistema.
                            </small>
                        </div>

                        <hr class="my-4">

                        {{-- NOMBRE --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   class="form-control rounded-3 @error('name') is-invalid @enderror"
                                   value="{{ old('name', $usuario->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- EMAIL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   class="form-control rounded-3 @error('email') is-invalid @enderror"
                                   value="{{ old('email', $usuario->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ROL — dinámico desde BD --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Rol <span class="text-danger">*</span>
                            </label>
                            <select name="idRol"
                                    class="form-select rounded-3 @error('idRol') is-invalid @enderror"
                                    required>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->idRol }}"
                                            @selected(old('idRol', $usuario->idRol) == $rol->idRol)>
                                        {{ $rol->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idRol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ESTADO --}}
                        <div class="form-check mb-4">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="activo"
                                   id="activo"
                                   value="1"
                                   {{ $usuario->activo ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">
                                Usuario activo
                            </label>
                            <div class="form-text">
                                Los usuarios inactivos no pueden iniciar sesión.
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- CAMBIO DE CONTRASEÑA --}}
                        <h6 class="fw-bold mb-3" style="color:#0B2D59;">
                            <i class="bi bi-shield-lock-fill me-1"></i>
                            Cambiar Contraseña
                            <small class="text-muted fw-normal">(dejar en blanco para no cambiar)</small>
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control rounded-start-3 @error('password') is-invalid @enderror"
                                       placeholder="Dejar en blanco para no cambiar">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="togglePass('password')">
                                    <i class="bi bi-eye" id="icon-password"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Si ingresa contraseña: mínimo 8 caracteres, confirmación requerida.
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirmar contraseña</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control rounded-start-3"
                                       placeholder="Repita la nueva contraseña">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="togglePass('password_confirmation')">
                                    <i class="bi bi-eye" id="icon-password_confirmation"></i>
                                </button>
                            </div>
                        </div>

                        {{-- BOTONES --}}
                        <div class="d-flex gap-3">
                            <button type="submit"
                                    class="btn text-white px-4 rounded-3"
                                    style="background-color:#0B2D59;">
                                <i class="bi bi-save-fill me-1"></i>
                                Guardar Cambios
                            </button>
                            <a href="{{ route('admin.usuarios.show', $usuario->id) }}"
                               class="btn btn-outline-secondary rounded-3">
                                Cancelar
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- PANEL LATERAL --}}
        <div class="col-lg-4">

            {{-- ACCIONES RÁPIDAS --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:#0B2D59;">Acciones rápidas</h6>

                    <form action="{{ route('admin.usuarios.toggle', $usuario->id) }}"
                          method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        @if($usuario->activo)
                            <button type="submit"
                                    class="btn btn-danger w-100 rounded-3"
                                    onclick="return confirm('¿Desactivar este usuario?')">
                                <i class="bi bi-person-x-fill me-1"></i>
                                Desactivar usuario
                            </button>
                        @else
                            <button type="submit"
                                    class="btn btn-success w-100 rounded-3">
                                <i class="bi bi-person-check-fill me-1"></i>
                                Activar usuario
                            </button>
                        @endif
                    </form>

                    <a href="{{ route('admin.usuarios.show', $usuario->id) }}"
                       class="btn btn-outline-secondary w-100 rounded-3">
                        <i class="bi bi-eye-fill me-1"></i>
                        Ver detalle completo
                    </a>
                </div>
            </div>

            {{-- INFO --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:#0B2D59;">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Información
                    </h6>
                    <p class="small text-muted mb-2">
                        <strong>ID:</strong> {{ $usuario->id }}
                    </p>
                    <p class="small text-muted mb-2">
                        <strong>Creado:</strong>
                        {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </p>
                    <p class="small text-muted mb-0">
                        <strong>Estado:</strong>
                        @if($usuario->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
function togglePass(fieldId) {
    const input = document.getElementById(fieldId);
    const icon  = document.getElementById('icon-' + fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

@endsection
