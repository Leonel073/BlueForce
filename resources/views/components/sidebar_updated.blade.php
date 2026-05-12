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
    <a href="{{ route('correspondencia.index') }}"

       class="nav-link
       {{ request()->routeIs('correspondencia.*')
            || request()->routeIs('documentos.*')
            ? 'active'
            : '' }}"

       data-label="Documentos">

        <span class="nav-icon">

            <i class="bi bi-file-text-fill"></i>

        </span>

        <span class="nav-label-text">

            Documentos

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

    {{-- BANDEJA --}}
    <a href="{{ route('envios.bandeja') }}"

       class="nav-link
       {{ request()->routeIs('envios.bandeja')
            ? 'active'
            : '' }}"

       data-label="Mi Bandeja">

        <span class="nav-icon">

            <i class="bi bi-inbox-fill"></i>

        </span>

        <span class="nav-label-text">

            Mi Bandeja

        </span>

    </a>

    {{-- ENVIADOS --}}
    <a href="{{ route('envios.index') }}"

       class="nav-link
       {{ request()->routeIs('envios.index')
            || request()->routeIs('envios.derivar*')
            ? 'active'
            : '' }}"

       data-label="Enviadas">

        <span class="nav-icon">

            <i class="bi bi-send-fill"></i>

        </span>

        <span class="nav-label-text">

            Enviadas

        </span>

    </a>

    {{-- REPORTES SOLO ADMIN --}}
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

    <a href="{{ route('user.configuracion') }}"

       class="nav-link
       {{ request()->routeIs('user.configuracion')
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