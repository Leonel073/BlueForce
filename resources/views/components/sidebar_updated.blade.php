{{-- =========================
| PRINCIPAL
========================= --}}
<div class="nav-section">
    <div class="nav-section-label">Principal</div>

    <a href="{{ Auth::user()->idRol == 1 ? route('admin.dashboard') : route('user.dashboard') }}"
       class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') ? 'active' : '' }}"
       data-label="Dashboard">
        <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
        <span class="nav-label-text">Dashboard</span>
    </a>

    @if(Auth::user()->idRol != 1)
        <a href="{{ route('user.anuncios.index') }}"
           class="nav-link {{ request()->routeIs('user.anuncios.*') ? 'active' : '' }}"
           data-label="Anuncios">
            <span class="nav-icon"><i class="bi bi-megaphone-fill"></i></span>
            <span class="nav-label-text">Anuncios</span>
        </a>
    @endif
</div>

{{-- =========================
| DOCUMENTOS
========================= --}}
<div class="nav-section">
    <div class="nav-section-label">Documentos</div>

    @if(Auth::user()->idRol == 1)
        <a href="{{ route('admin.documentos.index') }}"
           class="nav-link {{ request()->routeIs('admin.documentos.index') || request()->routeIs('admin.documentos.detalle') || request()->routeIs('admin.documentos.edit') ? 'active' : '' }}"
           data-label="Documentos">
            <span class="nav-icon"><i class="bi bi-folder2-open"></i></span>
            <span class="nav-label-text">Documentos</span>
        </a>

        <a href="{{ route('admin.documentos.crear') }}"
           class="nav-link {{ request()->routeIs('admin.documentos.crear') ? 'active' : '' }}"
           data-label="Registrar">
            <span class="nav-icon"><i class="bi bi-file-earmark-plus-fill"></i></span>
            <span class="nav-label-text">Registrar</span>
        </a>

        <a href="{{ route('admin.correspondencia') }}"
           class="nav-link {{ request()->routeIs('admin.correspondencia*') ? 'active' : '' }}"
           data-label="Correspondencia">
            <span class="nav-icon"><i class="bi bi-files"></i></span>
            <span class="nav-label-text">Correspondencia</span>
        </a>

        <a href="{{ route('admin.bandeja', ['solo_mis' => 1]) }}"
           class="nav-link {{ request()->routeIs('admin.bandeja*') ? 'active' : '' }}"
           data-label="Bandeja / Derivar">
            <span class="nav-icon"><i class="bi bi-inbox-fill"></i></span>
            <span class="nav-label-text">Bandeja / Derivar</span>
        </a>

        <a href="{{ route('admin.envios') }}"
           class="nav-link {{ request()->routeIs('admin.envios*') ? 'active' : '' }}"
           data-label="Enviados">
            <span class="nav-icon"><i class="bi bi-send-fill"></i></span>
            <span class="nav-label-text">Enviados</span>
        </a>
    @else
        <a href="{{ route('envios.bandeja') }}"
           class="nav-link {{ request()->routeIs('envios.bandeja') || request()->routeIs('envios.derivar.*') ? 'active' : '' }}"
           data-label="Mi Correspondencia">
            <span class="nav-icon"><i class="bi bi-inbox-fill"></i></span>
            <span class="nav-label-text">Mi Correspondencia</span>
        </a>

        <a href="{{ route('documentos.index') }}"
           class="nav-link {{ request()->routeIs('documentos.index') || request()->routeIs('documentos.detalle') ? 'active' : '' }}"
           data-label="Mis Documentos">
            <span class="nav-icon"><i class="bi bi-folder2-open"></i></span>
            <span class="nav-label-text">Mis Documentos</span>
        </a>

        <a href="{{ route('documentos.crear') }}"
           class="nav-link {{ request()->routeIs('documentos.crear') ? 'active' : '' }}"
           data-label="Registrar">
            <span class="nav-icon"><i class="bi bi-file-earmark-plus-fill"></i></span>
            <span class="nav-label-text">Registrar</span>
        </a>

        <a href="{{ route('envios.index') }}"
           class="nav-link {{ request()->routeIs('envios.index') ? 'active' : '' }}"
           data-label="Mis Envios">
            <span class="nav-icon"><i class="bi bi-send-fill"></i></span>
            <span class="nav-label-text">Mis Envios</span>
        </a>
    @endif
</div>

@if(Auth::user()->idRol == 1)
{{-- =========================
| ADMINISTRACION
========================= --}}
<div class="nav-section">
    <div class="nav-section-label">Administracion</div>

    <a href="{{ route('admin.usuarios') }}"
       class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}"
       data-label="Usuarios">
        <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
        <span class="nav-label-text">Usuarios</span>
    </a>

    <a href="{{ route('admin.departamentos.index') }}"
       class="nav-link {{ request()->routeIs('admin.departamentos.*') ? 'active' : '' }}"
       data-label="Departamentos">
        <span class="nav-icon"><i class="bi bi-building-fill"></i></span>
        <span class="nav-label-text">Departamentos</span>
    </a>

    <a href="{{ route('admin.personas.index') }}"
       class="nav-link {{ request()->routeIs('admin.personas.*') ? 'active' : '' }}"
       data-label="Personas">
        <span class="nav-icon"><i class="bi bi-person-vcard-fill"></i></span>
        <span class="nav-label-text">Personas</span>
    </a>

    <a href="{{ route('admin.anuncios.index') }}"
       class="nav-link {{ request()->routeIs('admin.anuncios.*') ? 'active' : '' }}"
       data-label="Anuncios">
        <span class="nav-icon"><i class="bi bi-megaphone-fill"></i></span>
        <span class="nav-label-text">Anuncios</span>
    </a>

    <a href="{{ route('admin.reportes.index') }}"
       class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}"
       data-label="Reportes">
        <span class="nav-icon"><i class="bi bi-bar-chart-fill"></i></span>
        <span class="nav-label-text">Reportes</span>
    </a>

    <a href="{{ route('admin.auditoria.index') }}"
       class="nav-link {{ request()->routeIs('admin.auditoria.*') ? 'active' : '' }}"
       data-label="Auditoria">
        <span class="nav-icon"><i class="bi bi-shield-check"></i></span>
        <span class="nav-label-text">Auditoria</span>
    </a>
</div>
@endif

{{-- =========================
| CONFIGURACION
========================= --}}
<div class="nav-section">
    <div class="nav-section-label">Configuracion</div>

    <a href="{{ Auth::user()->idRol == 1 ? route('admin.configuracion') : route('user.configuracion') }}"
       class="nav-link {{ request()->routeIs('admin.configuracion') || request()->routeIs('user.configuracion') ? 'active' : '' }}"
       data-label="Configuracion">
        <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
        <span class="nav-label-text">Configuracion</span>
    </a>
</div>
