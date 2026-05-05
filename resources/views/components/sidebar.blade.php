<div class="nav-section">
    <div class="nav-section-label">Principal</div>

    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       data-label="Dashboard">
        <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
        <span class="nav-label-text">Dashboard</span>
    </a>

    <a href="{{ route('documentos') }}"
       class="nav-link {{ request()->routeIs('documentos*') ? 'active' : '' }}"
       data-label="documentos">
        <span class="nav-icon"><i class="bi bi-file-text"></i></span>
        <span class="nav-label-text">Documentos</span>
       
        
    </a>

    <!-- <span class="nav-badge">3</span> para pdoer hacer funcionalidad de notificacion -->
</div>