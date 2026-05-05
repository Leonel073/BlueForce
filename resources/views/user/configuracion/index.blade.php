@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<style>
    .bg-mi-fondo {
    background-color: #0b295b;
    color: white;
}

.btn-mi-amarillo {
    background-color: #d3af37;
    color: #0b295b;
    border: none;
}
.btn-mi-azul {
    background-color: #0b295b;
    color: white;
    border: none;
}

.btn-mi-verde {
    background-color: #387a5b;
    color: white;
    border: none;
}
.btn-mi-rojo {
    background-color: #8a2b2b;
    color: white;
    border: none;
}

.btn-mi-verde:hover {
    background-color: #387a5b;
}

.text-mi-azul {
    color:  #0b295b;
}
</style>
<!-- 🔝 HEADER FIJO -->
<div class="sticky-top bg-white shadow-sm p-3 mb-4" style="z-index:10;">
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h4 class="mb-0 text-mi-azul fw-bold"> Configuración</h4>
            <small class="text-muted text-mi-azul">Gestiona tu cuenta</small>
        </div>

        <!-- ⏰ HORA -->
        <div class="text-end">
            <div id="hora" class="fw-bold"></div>
            <small class="text-muted" id="fecha"></small>
        </div>

    </div>
</div>

<div class="container-fluid">

    <div class="row">

        <!-- 🧑 DATOS PERSONALES -->
        <div class="col-md-6">
            <div class="card shadow mb-4">

                <div class="card-header bg-mi-fondo text-white fw-bold">
                     Datos Personales
                </div>

                <div class="card-body">

                    <form>

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" class="form-control" value="Usuario Demo">
                        </div>

                        <div class="mb-3">
                            <label>Correo</label>
                            <input type="email" class="form-control" value="admin@test.com">
                        </div>

                        <div class="mb-3">
                            <label>Rol</label>
                            <input type="text" class="form-control" value="Administrador" disabled>
                        </div>

                        <button class="btn btn-mi-verde w-100">
                            Guardar Datos
                        </button>

                    </form>

                </div>

            </div>
        </div>

        <!-- 🔐 SEGURIDAD -->
        <div class="col-md-6">
            <div class="card shadow mb-4">

                <div class="card-header bg-mi-fondo text-white fw-bold">
                     Seguridad
                </div>

                <div class="card-body">

                    <form>

                        <div class="mb-3">
                            <label>Contraseña Actual</label>
                            <input type="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Nueva Contraseña</label>
                            <input type="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Confirmar Contraseña</label>
                            <input type="password" class="form-control">
                        </div>

                        <button class="btn btn-mi-rojo w-100">
                            Cambiar Contraseña
                        </button>

                    </form>

                </div>

            </div>
        </div>

    </div>

</div>

@endsection

<!-- ⏰ SCRIPT HORA -->
<script>
function actualizarHora() {
    const now = new Date();

    const hora = now.toLocaleTimeString();
    const fecha = now.toLocaleDateString();

    document.getElementById('hora').innerText = hora;
    document.getElementById('fecha').innerText = fecha;
}

setInterval(actualizarHora, 1000);
actualizarHora();
</script>