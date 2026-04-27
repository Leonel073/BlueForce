<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background: #f5f7fb; }

.sidebar {
    width: 260px;
    min-height: 100vh;
    background: linear-gradient(180deg, #0d1b2a, #1b263b);
    color: #fff;
}

.sidebar .nav-link {
    color: #cbd5e1;
    border-radius: 10px;
    margin-bottom: 5px;
    padding: 10px;
}

.sidebar .nav-link:hover {
    background: rgba(255,255,255,0.1);
}

.sidebar .nav-link.active {
    background: #ffc107;
    color: #000;
}

@media (max-width: 992px) {
    .sidebar { width: 80px; }
    .text-label { display: none; }
}
</style>
</head>

<body>

<!-- BOTÓN MÓVIL -->
<button class="btn btn-dark m-2 d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
    ☰
</button>

<div class="d-flex">

<div class="sidebar d-none d-lg-flex flex-column p-3">

    <!-- LOGO -->
    <div class="text-center mb-4">
        <img src="{{ asset('images/LogoEmpresa.png') }}" 
             alt="Logo" 
             class="img-fluid" 
             style="max-height: 100px;">
    </div>

    <h6 class="text-center">Sistema</h6>

    <x-sidebar />
    </div>

    <!-- CONTENIDO -->
    <div class="flex-grow-1 p-4">
        @yield('content')
    </div>

</div>

<!-- SIDEBAR MOBILE -->
<div class="offcanvas offcanvas-start" id="mobileSidebar">
    <div class="offcanvas-body p-0">
        <div class="sidebar p-3">
            <x-sidebar />
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>