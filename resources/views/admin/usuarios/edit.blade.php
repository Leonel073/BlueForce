@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')

<div class="container py-4">

    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white"
             style="background-color:#0B2D59;">

            <h4 class="mb-0">

                <i class="bi bi-pencil-square"></i>

                Editar Usuario

            </h4>

        </div>

        <div class="card-body">

            <form action="{{ route('admin.usuarios.update', $usuario->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Nombre

                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $usuario->name) }}"
                           required>

                </div>

                {{-- EMAIL --}}
                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Email

                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email', $usuario->email) }}"
                           required>

                </div>

                {{-- ROL --}}
                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Rol

                    </label>

                    <select name="idRol"
                            class="form-select">

                        <option value="1"
                            {{ $usuario->idRol == 1 ? 'selected' : '' }}>

                            Administrador

                        </option>

                        <option value="2"
                            {{ $usuario->idRol == 2 ? 'selected' : '' }}>

                            Usuario

                        </option>

                    </select>

                </div>

                {{-- ACTIVO --}}
                <div class="form-check mb-4">

                    <input class="form-check-input"
                           type="checkbox"
                           name="activo"
                           id="activo"
                           {{ $usuario->activo ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="activo">

                        Usuario activo

                    </label>

                </div>

                {{-- CONTRASEÑA --}}
                <hr>

                <h5 class="mb-3"
                    style="color:#0B2D59;">

                    Cambiar Contraseña

                </h5>

                <div class="mb-3">

                    <label class="form-label">

                        Nueva contraseña

                    </label>

                    <input type="password"
                           name="password"
                           class="form-control">

                </div>

                <div class="mb-4">

                    <label class="form-label">

                        Confirmar contraseña

                    </label>

                    <input type="password"
                           name="password_confirmation"
                           class="form-control">

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn text-white"
                            style="background-color:#0B2D59;">

                        <i class="bi bi-save-fill"></i>

                        Guardar Cambios

                    </button>

                    <a href="{{ route('admin.usuarios') }}"
                       class="btn btn-secondary">

                        Cancelar

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection