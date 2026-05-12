{{-- resources/views/user/configuracion.blade.php --}}

@extends('layouts.app')

@section('title', 'Configuración de Usuario')

@section('content')


<div class="container-fluid py-4 px-4">

    {{-- ENCABEZADO --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg, #0B2D59, #2E608C);">

        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

            <div>
                <h1 class="fw-bold text-white mb-1">
                    <i class="bi bi-gear-fill"></i>
                    Configuración
                </h1>

                <p class="text-light mb-0">
                    Administra tu información personal y seguridad
                </p>
            </div>

            <div class="text-end mt-3 mt-md-0">
                <div class="badge rounded-pill px-4 py-3"
                     style="background-color: #D9A23D; color: #0B2D59; font-size: 15px;">

                    <i class="bi bi-calendar-event-fill"></i>

                    <span id="fechaHora"></span>

                </div>
            </div>

        </div>
    </div>

    {{-- MENSAJES --}}
    @if (session('status'))

        <div class="alert border-0 shadow-sm rounded-4"
             style="background-color: #D9B13B; color: #0B2D59;">

            <i class="bi bi-check-circle-fill"></i>
            Información actualizada correctamente.

        </div>

    @endif

    <div class="row">

        {{-- INFORMACIÓN PERSONAL --}}
        <div class="col-lg-6 mb-4">

            <div class="card border-0 shadow-lg rounded-4 h-100">

                <div class="card-header border-0 rounded-top-4 py-3"
                     style="background-color: #0B2D59;">

                    <h4 class="text-white mb-0">
                        <i class="bi bi-person-fill"></i>
                        Información Personal
                    </h4>

                </div>

                <div class="card-body p-4">

                    <form method="POST" action="{{ route('profile.update') }}">

                        @csrf
                        @method('PATCH')

                        {{-- NOMBRE --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold"
                                   style="color: #0B2D59;">

                                Nombre Completo

                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control rounded-3 border-0 shadow-sm"
                                style="background-color: #f5f7fa;"
                                value="{{ old('name', auth()->user()->name) }}"
                                required
                            >

                            @error('name')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                        {{-- EMAIL --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold"
                                   style="color: #0B2D59;">

                                Correo Electrónico

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control rounded-3 border-0 shadow-sm"
                                style="background-color: #f5f7fa;"
                                value="{{ old('email', auth()->user()->email) }}"
                                required
                            >

                            @error('email')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                        {{-- ROL (SOLO LECTURA) --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold" style="color: #0B2D59;">
                                Rol en el Sistema
                            </label>

                            @php
                                // Obtenemos el nombre real del rol desde la base de datos
                                $nombreRol = auth()->user()->rol->nombre ?? 'Sin Rol Asignado';
                            @endphp

                            <input
                                type="text"
                                class="form-control rounded-3 border-0 shadow-sm text-dark fw-bold"
                                style="background-color: #e9ecef; cursor: not-allowed;"
                                value="{{ $nombreRol }}"
                                readonly
                            >
                            
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle-fill" style="color: #D9A23D;"></i> 
                                @if(strtoupper($nombreRol) === 'ADMINISTRADOR' || strtoupper($nombreRol) === 'ADMIN')
                                    Eres el <strong>Administrador</strong> del sistema. Puedes modificar los roles de otros desde el módulo de Gestión de Usuarios.
                                @else
                                    Tu rol actual es de solo lectura en esta pantalla. Solicita a un Administrador si necesitas cambiar tus privilegios.
                                @endif
                            </small>

                        </div>
                        <button type="submit"
                                class="btn w-100 rounded-3 fw-bold text-white py-2"
                                style="background-color: #0B2D59;">

                            <i class="bi bi-save-fill"></i>
                            Guardar Cambios

                        </button>

                    </form>

                </div>

            </div>

        </div>

        {{-- CAMBIO DE CONTRASEÑA --}}
        <div class="col-lg-6 mb-4">

            <div class="card border-0 shadow-lg rounded-4 h-100">

                <div class="card-header border-0 rounded-top-4 py-3"
                     style="background-color: #2E608C;">

                    <h4 class="text-white mb-0">
                        <i class="bi bi-shield-lock-fill"></i>
                        Seguridad
                    </h4>

                </div>

                <div class="card-body p-4">

                    <form method="POST" action="{{ route('password.update') }}">

                        @csrf
                        @method('PUT')

                        {{-- CONTRASEÑA ACTUAL --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold"
                                   style="color: #0B2D59;">

                                Contraseña Actual

                            </label>

                            <input
                                type="password"
                                name="current_password"
                                class="form-control rounded-3 border-0 shadow-sm"
                                style="background-color: #f5f7fa;"
                                required
                            >

                            @error('current_password')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                        {{-- NUEVA CONTRASEÑA --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold"
                                   style="color: #0B2D59;">

                                Nueva Contraseña

                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control rounded-3 border-0 shadow-sm"
                                style="background-color: #f5f7fa;"
                                required
                            >

                            @error('password')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                        {{-- CONFIRMAR CONTRASEÑA --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold"
                                   style="color: #0B2D59;">

                                Confirmar Contraseña

                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control rounded-3 border-0 shadow-sm"
                                style="background-color: #f5f7fa;"
                                required
                            >

                        </div>

                        <button type="submit"
                                class="btn w-100 rounded-3 fw-bold py-2"
                                style="background-color: #D9B13B; color: #0B2D59;">

                            <i class="bi bi-shield-check"></i>
                            Actualizar Contraseña

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- SCRIPT FECHA Y HORA --}}
<script>

    function actualizarFechaHora() {

        const ahora = new Date();

        const opciones = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };

        document.getElementById('fechaHora').innerHTML =
            ahora.toLocaleDateString('es-ES', opciones);

    }

    actualizarFechaHora();

    setInterval(actualizarFechaHora, 1000);

</script>

@endsection