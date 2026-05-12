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
            : '' }}">

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
            : '' }}">

        <span class="nav-icon">
            <i class="bi bi-file-earmark-text-fill"></i>
        </span>

        <span class="nav-label-text">
            Documentos
        </span>

    </a>

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
       class="nav-link {{ request()->routeIs('envios.bandeja') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-inbox-fill"></i>
        </span>

        <span class="nav-label-text">
            Mi Bandeja
        </span>

    </a>

    {{-- ENVIADOS --}}
    <a href="{{ route('envios.index') }}"
       class="nav-link {{ request()->routeIs('envios.*') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-send-fill"></i>
        </span>

        <span class="nav-label-text">
            Enviadas
        </span>

    </a>

</div>

{{-- =========================
| ADMINISTRACIÓN
========================= --}}
@if(Auth::user()->idRol == 1)

<div class="nav-section">

    <div class="nav-section-label">
        Administración
    </div>

    {{-- USUARIOS --}}
    <a href="{{ route('admin.usuarios') }}"
       class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-people-fill"></i>
        </span>

        <span class="nav-label-text">
            Usuarios
        </span>

    </a>

    {{-- DEPARTAMENTOS --}}
    <a href="{{ route('admin.departamentos.index') }}"
       class="nav-link {{ request()->routeIs('admin.departamentos.*') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-building-fill"></i>
        </span>

        <span class="nav-label-text">
            Departamentos
        </span>

    </a>

    {{-- PERSONAS --}}
    <a href="{{ route('admin.personas.index') }}"
       class="nav-link {{ request()->routeIs('admin.personas.*') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-person-vcard-fill"></i>
        </span>

        <span class="nav-label-text">
            Personas
        </span>

    </a>

    {{-- GESTIÓN DOCUMENTAL --}}
    <a href="{{ route('admin.documentos.index') }}"
       class="nav-link {{ request()->routeIs('admin.documentos.*') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-folder2-open"></i>
        </span>

        <span class="nav-label-text">
            Gestión Documental
        </span>

    </a>

    {{-- REPORTES --}}
    <a href="{{ route('admin.reportes.index') }}"
       class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-bar-chart-fill"></i>
        </span>

        <span class="nav-label-text">
            Reportes
        </span>

    </a>

</div>

@endif

{{-- =========================
| CONFIGURACIÓN
========================= --}}
<div class="nav-section">

    <div class="nav-section-label">
        Configuración
    </div>

    <a href="{{ route('user.configuracion') }}"
       class="nav-link {{ request()->routeIs('user.configuracion') ? 'active' : '' }}">

        <span class="nav-icon">
            <i class="bi bi-gear-fill"></i>
        </span>

        <span class="nav-label-text">
            Configuración
        </span>

    </a>

</div>