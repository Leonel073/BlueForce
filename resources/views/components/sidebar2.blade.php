<div class="nav-section">
    <div class="nav-section-label">Principal</div>

    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
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

    <a href="#"
       class="nav-link"
       data-label="Enviadas">
        <span class="nav-icon"><i class="bi bi-send-fill"></i></span>
        <span class="nav-label-text">Enviadas</span>
    </a>

    <a href="#"
       class="nav-link"
       data-label="Recibidas">
        <span class="nav-icon"><i class="bi bi-inbox-fill"></i></span>
        <span class="nav-label-text">Recibidas</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-label">Configuración</div>

    <a href="#"
       class="nav-link"
       data-label="Reportes">
        <span class="nav-icon"><i class="bi bi-bar-chart-fill"></i></span>
        <span class="nav-label-text">Reportes</span>
    </a>

    <a href="#"
       class="nav-link"
       data-label="Configuración">
        <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
        <span class="nav-label-text">Configuración</span>
    </a>
</div>
