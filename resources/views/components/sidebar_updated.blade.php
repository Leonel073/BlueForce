<div class="nav-section">
    <div class="nav-section-label">Principal</div>

    <a href="{{ Auth::user()->idRol == 1 ? route('admin.dashboard') : route('user.dashboard') }}"
       class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') ? 'active' : '' }}"
       data-label="Dashboard">
        <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
        <span class="nav-label-text">Dashboard</span>
    </a>

<a href="{{ auth()->user()->idRol == 1 
            ? route('admin.correspondencia') 
            : route('documentos.show') }}"
   class="nav-link 
   {{ request()->routeIs('documentos*') || request()->routeIs('admin.correspondencia') ? 'active' : '' }}"
   data-label="Documentos">
    <span class="nav-icon">
        <i class="bi bi-file-text"></i>
    </span>
    <span class="nav-label-text">
        Documentos
    </span>
</a>
    @if(Auth::user()->idRol == 1)
    <a href="{{ route('admin.usuarios') }}"
       class="nav-link {{ request()->routeIs('usuarios*') ? 'active' : '' }}"
       data-label="Usuarios">
        <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
        <span class="nav-label-text">Usuarios</span>
    </a>
@endif
</div>

<div class="nav-section">
    <div class="nav-section-label">Correspondencia</div>
        <a href="{{ route('envios.bandeja') }}"
        class="nav-link {{ request()->routeIs('envios.bandeja') ? 'active' : '' }}"
        data-label="Mi Bandeja">

            <span class="nav-icon">

                <i class="bi bi-inbox-fill"></i>

            </span>

            <span class="nav-label-text">

                Mi Bandeja

            </span>

        </a>
    </a>
    <a href="{{ route('envios.index') }}"
    class="nav-link {{ request()->routeIs('envios.*') ? 'active' : '' }}"
    data-label="Enviadas">

        <span class="nav-icon">
            <i class="bi bi-send-fill"></i>
        </span>

        <span class="nav-label-text">
            Enviadas
        </span>

    </a>

   <a href="{{ route('admin.reportes.index') }}"
   class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}"
   data-label="Reportes">

    <span class="nav-icon">

        <i class="bi bi-bar-chart-fill"></i>

    </span>

    <span class="nav-label-text">

        Reportes

    </span>

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