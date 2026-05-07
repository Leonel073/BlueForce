<div class="nav-section">
    <div class="nav-section-label">Principal</div>

    <a href="{{ Auth::user()->idRol == 1 ? route('admin.dashboard') : route('user.dashboard') }}"
       class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') ? 'active' : '' }}"
       data-label="Dashboard">
        <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
        <span class="nav-label-text">Dashboard</span>
    </a>

    <a href="{{ route('documentos.show') }}"
       class="nav-link {{ request()->routeIs('documentos*') ? 'active' : '' }}"
       data-label="Documentos">
        <span class="nav-icon"><i class="bi bi-file-text"></i></span>
        <span class="nav-label-text">Documentos</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-label">Correspondencia</div>

    <a href="{{ route('enviadas') }}"
       class="nav-link {{ request()->routeIs('enviadas') ? 'active' : '' }}"
       data-label="Enviadas">
        <span class="nav-icon"><i class="bi bi-send-fill"></i></span>
        <span class="nav-label-text">Enviadas</span>
    </a>

    <a href="{{ route('recibidas') }}"
       class="nav-link {{ request()->routeIs('recibidas') ? 'active' : '' }}"
       data-label="Recibidas">
        <span class="nav-icon"><i class="bi bi-inbox-fill"></i></span>
        <span class="nav-label-text">Recibidas</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-label">Configuración</div>

    <a href="#" class="nav-link" data-label="Reportes">
        <span class="nav-icon"><i class="bi bi-bar-chart-fill"></i></span>
        <span class="nav-label-text">Reportes</span>
    </a>

    <a href="{{ route('user.configuracion') }}" class="nav-link {{ request()->routeIs('user.configuracion') ? 'active' : '' }}" data-label="Configuración">
        <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
        <span class="nav-label-text">Configuración</span>
    </a>
</div>