<div class="nav-section">
    <div class="nav-section-label">Principal</div>

    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       data-label="Dashboard">
        <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
        <span class="nav-label-text">Dashboard</span>
    </a>

    <a href="{{ route('documentos') }}"
       class="nav-link {{ request()->routeIs('reportes*') ? 'active' : '' }}"
       data-label="Reportes">
        <span class="nav-icon"><i class="bi bi-bar-chart-fill"></i></span>
        <span class="nav-label-text">Reportes</span>
        <span class="nav-badge">3</span>
    </a>
</div>