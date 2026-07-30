{{-- =========================
| MENÚ PRINCIPAL
========================= --}}
<div class="nav-section">

    <div class="nav-section-label">

        Principal

    </div>

    {{-- DASHBOARD --}}
    <a href="{{ Auth::user()->idRol == 1
        ? route('admin.dashboard')
        : route('user.dashboard') }}"

       class="nav-link
       {{ request()->routeIs('admin.dashboard')
            || request()->routeIs('user.dashboard')
            ? 'active'
            : '' }}"

       data-label="Dashboard">

        <span class="nav-icon">

            <i class="bi bi-grid-fill"></i>

        </span>

        <span class="nav-label-text">

            Dashboard

        </span>

    </a>

    {{-- DOCUMENTOS --}}
    <a href="{{ Auth::user()->idRol == 1
        ? route('admin.correspondencia')
        : route('correspondencia.index') }}"

       class="nav-link
       {{ request()->routeIs('correspondencia.*')
            || request()->routeIs('admin.correspondencia*')
            || request()->routeIs('documentos.*')
            ? 'active'
            : '' }}"

       data-label="Documentos">

        <span class="nav-icon">

            <i class="bi bi-file-text-fill"></i>

        </span>

        <span class="nav-label-text">

            Correspondencia

        </span>

    </a>

    {{-- USUARIOS SOLO ADMIN --}}
    @if(Auth::user()->idRol == 1)

        <a href="{{ route('admin.usuarios') }}"

           class="nav-link
           {{ request()->routeIs('admin.usuarios*')
                ? 'active'
                : '' }}"

           data-label="Usuarios">

            <span class="nav-icon">

                <i class="bi bi-people-fill"></i>

            </span>

            <span class="nav-label-text">

                Usuarios

            </span>

        </a>

    @endif

</div>

{{-- =========================
| CORRESPONDENCIA
========================= --}}
<div class="nav-section">

    <div class="nav-section-label">

        Correspondencia

    </div>

    {{-- MI BANDEJA --}}
    <a href="{{ Auth::user()->idRol == 1
        ? route('admin.bandeja')
        : route('envios.bandeja') }}"

       class="nav-link
       {{ request()->routeIs('admin.bandeja', 'envios.bandeja')
            ? 'active'
            : '' }}"

       data-label="Mi Bandeja">

        <span class="nav-icon">

            <i class="bi bi-inbox-fill"></i>

        </span>

        <span class="nav-label-text">

            @if(Auth::user()->idRol == 1) Bandeja @else Mi Bandeja @endif

        </span>

    </a>

    {{-- ENVIADAS --}}
    <a href="{{ Auth::user()->idRol == 1
        ? route('admin.envios')
        : route('envios.index') }}"

       class="nav-link
       {{ request()->routeIs('admin.envios*', 'envios.*')
            ? 'active'
            : '' }}"

       data-label="Enviadas">

        <span class="nav-icon">

            <i class="bi bi-send-fill"></i>

        </span>

        <span class="nav-label-text">

            @if(Auth::user()->idRol == 1) Envíos @else Mis Envíos @endif

        </span>

    </a>

    {{-- REPORTES --}}
    @if(Auth::user()->idRol == 1)
        <a href="{{ route('admin.reportes.index') }}"

           class="nav-link
           {{ request()->routeIs('admin.reportes.*')
                ? 'active'
                : '' }}"

           data-label="Reportes">

            <span class="nav-icon">

                <i class="bi bi-bar-chart-fill"></i>

            </span>

            <span class="nav-label-text">

                Reportes

            </span>

        </a>
    @endif

</div>

{{-- =========================
| CONFIGURACIÓN
========================= --}}
<div class="nav-section">

    <div class="nav-section-label">

        Configuración

    </div>

    <a href="{{ Auth::user()->idRol == 1 ? route('admin.configuracion') : route('user.configuracion') }}"

       class="nav-link
       {{ request()->routeIs('admin.configuracion') || request()->routeIs('user.configuracion')
            ? 'active'
            : '' }}"

       data-label="Configuración">

        <span class="nav-icon">

            <i class="bi bi-gear-fill"></i>

        </span>

        <span class="nav-label-text">

            Configuración

        </span>

    </a>

</div>
