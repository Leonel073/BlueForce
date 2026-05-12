<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'BlueForce')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --sidebar-w: 260px;
    --sidebar-collapsed: 72px;
    --accent: #ffc107;
    --text-muted: #94a3b8;
    --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* { box-sizing: border-box; }
body { background: #f1f5f9; font-family: system-ui, sans-serif; }

.app-layout {
    display: flex;
    min-height: 100vh;
}

/* ── SIDEBAR ── */
.sidebar {
    width: var(--sidebar-w);
    min-width: var(--sidebar-w);
    background: linear-gradient(170deg, #0d1b2a 0%, #1b263b 60%, #0f2240 100%);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    z-index: 100;
    overflow: hidden;
    transition: width var(--transition), min-width var(--transition), transform var(--transition);
}

.sidebar::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,193,7,0.5), transparent);
    animation: shimmer 3s ease-in-out infinite;
}
@keyframes shimmer { 0%,100%{opacity:0.3} 50%{opacity:1} }

/* ── TOGGLE BUTTON ── */
.sidebar-toggle {
    position: absolute;
    top: 22px; right: -13px;
    width: 26px; height: 26px;
    background: var(--accent);
    border: 2px solid #0d1b2a;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    z-index: 10;
    font-size: 10px; color: #000;
    transition: transform var(--transition), background var(--transition), opacity var(--transition);
    box-shadow: 0 2px 8px rgba(0,0,0,0.4);
    opacity: 0;
}
.sidebar:hover .sidebar-toggle,
.sidebar.collapsed .sidebar-toggle { opacity: 1; }
.sidebar.collapsed .sidebar-toggle { transform: rotate(180deg); }
.sidebar-toggle:hover { background: #e6a800; }

/* ── LOGO ── */
.sidebar-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 20px 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    text-decoration: none;
}
.logo-icon-wrap {
    width: 87px; min-width: 87px; height: 87px;
    border-radius: 100%;
    background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 0 1px rgba(255,193,7,0.3), 0 4px 14px rgba(255,193,7,0.25);
    transition: transform var(--transition), box-shadow var(--transition);
    overflow: hidden;
    flex-shrink: 0;
}
.logo-icon-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    border-radius: 11px;
}
.logo-icon-wrap:hover {
    transform: scale(1.06);
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.9), 0 6px 18px rgba(255,193,7,0.35);
}
.logo-text-wrap {
    overflow: hidden;
    transition: opacity var(--transition), max-width var(--transition);
    max-width: 200px;
    white-space: nowrap;
}
.logo-name {
    font-size: 20px; font-weight: 600;
    color: #f1f5f9;
    letter-spacing: 0.02em;
}
.logo-tagline {
    font-size: 15px;
    color: var(--text-muted);
}

/* ── NAV ── */
.sidebar-nav {
    padding: 12px 10px 0;
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: none;
}
.sidebar-nav::-webkit-scrollbar { display: none; }

.nav-section { margin-bottom: 4px; }
.nav-section-label {
    font-size: 10px; letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #475569;
    padding: 6px 8px;
    white-space: nowrap;
    transition: opacity var(--transition), max-height var(--transition);
    overflow: hidden;
    max-height: 30px;
}
.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 8px;
    color: var(--text-muted);
    cursor: pointer;
    transition: background var(--transition), color var(--transition), 
                transform 0.15s, padding var(--transition), gap var(--transition),
                justify-content var(--transition);
    text-decoration: none !important;
    margin-bottom: 2px;
    position: relative;
    white-space: nowrap;
    overflow: visible;
}
.nav-link::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--accent);
    border-radius: 0 3px 3px 0;
    transform: scaleY(0);
    transition: transform var(--transition);
}
.nav-link:hover {
    background: rgba(255,255,255,0.07);
    color: #e2e8f0;
    transform: translateX(2px);
}
.nav-link.active {
    background: rgba(255,193,7,0.12);
    color: var(--accent) !important;
}
.nav-link.active::before { transform: scaleY(1); }

.nav-icon {
    width: 22px; min-width: 22px; height: 22px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.nav-label-text {
    font-size: 13.5px;
    transition: opacity var(--transition), max-width var(--transition);
    overflow: hidden;
    max-width: 160px;
    white-space: nowrap;
}

/* ── USER ── */
.sidebar-user {
    border-top: 1px solid rgba(255,255,255,0.07);
    padding: 10px;
    flex-shrink: 0;
}
.user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 8px;
    transition: background var(--transition), padding var(--transition), gap var(--transition);
    overflow: hidden;
    text-decoration: none;
}
.user-btn:hover { background: rgba(255,255,255,0.07); }
.user-avatar {
    width: 32px; height: 32px; min-width: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #378add, #185fa5);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600; color: #fff;
    flex-shrink: 0;
}
.user-info {
    overflow: hidden;
    transition: opacity var(--transition), max-width var(--transition);
    max-width: 160px;
}
.user-name { font-size: 13px; font-weight: 500; color: #e2e8f0; white-space: nowrap; }
.user-role { font-size: 11px; color: #475569; white-space: nowrap; }

/* ═══════════════════════════════════════
   ESTADO COLAPSADO (desktop)
═══════════════════════════════════════ */
.sidebar.collapsed { width: var(--sidebar-collapsed); min-width: var(--sidebar-collapsed); }
.sidebar.collapsed .logo-text-wrap,
.sidebar.collapsed .nav-label-text,
.sidebar.collapsed .user-info { opacity: 0; max-width: 0; overflow: hidden; pointer-events: none; }
.sidebar.collapsed .nav-section-label { opacity: 0; max-height: 0; padding-top: 0; padding-bottom: 0; pointer-events: none; }
.sidebar.collapsed .sidebar-logo,
.sidebar.collapsed .nav-link,
.sidebar.collapsed .user-btn { justify-content: center; padding-left: 0; padding-right: 0; gap: 0; }
.sidebar.collapsed .nav-link::after {
    content: attr(data-label);
    position: absolute; left: calc(var(--sidebar-collapsed) - 4px); top: 50%;
    transform: translateY(-50%) translateX(4px);
    background: #1e293b; color: #f1f5f9; font-size: 12px; padding: 5px 10px;
    border-radius: 6px; white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    pointer-events: none; z-index: 200; opacity: 0; transition: opacity 0.15s ease, transform 0.15s ease;
}
.sidebar.collapsed .nav-link:hover::after { opacity: 1; transform: translateY(-50%) translateX(10px); }

/* ── CONTENIDO ── */
.main-content {
    margin-left: var(--sidebar-w);
    flex: 1;
    padding: 2rem;
    transition: margin-left var(--transition);
    min-width: 0;
}
body.sidebar-collapsed .main-content { margin-left: var(--sidebar-collapsed); }

/* ── OVERLAY MÓVIL ── */
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px); }
.sidebar-overlay.active { display: block; animation: fadeIn 0.2s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }

/* ── RESPONSIVE MÓVIL ── */
@media (max-width: 992px) {
    .sidebar { transform: translateX(-100%); width: var(--sidebar-w) !important; min-width: var(--sidebar-w) !important; }
    .sidebar.mobile-open { transform: translateX(0); }
    .main-content { margin-left: 0 !important; }
    .sidebar-toggle { display: none; }
    .sidebar.mobile-open .logo-text-wrap, .sidebar.mobile-open .nav-label-text, .sidebar.mobile-open .user-info { opacity: 1; max-width: 200px; }
    .sidebar.mobile-open .nav-section-label { opacity: 1; max-height: 30px; }
    .sidebar.mobile-open .sidebar-logo, .sidebar.mobile-open .nav-link, .sidebar.mobile-open .user-btn { justify-content: flex-start; padding-left: 10px; padding-right: 10px; gap: 10px; }
}

/* ── HAMBURGUESA ── */
.btn-sidebar-mobile {
    position: fixed; top: 14px; left: 14px; z-index: 98; width: 40px; height: 40px;
    background: #1b263b; border: none; border-radius: 10px; color: var(--accent);
    display: none; align-items: center; justify-content: center; font-size: 18px;
    cursor: pointer; box-shadow: 0 2px 12px rgba(0,0,0,0.25); transition: background 0.2s;
}
.btn-sidebar-mobile:hover { background: #243554; }
@media (max-width: 992px) { .btn-sidebar-mobile { display: flex; } }
</style>
</head>
<body>

<button class="btn-sidebar-mobile" id="btnMobile">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar-overlay" id="overlay" onclick="closeMobileSidebar()"></div>

<div class="app-layout">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="desktopToggle" onclick="toggleDesktop()" title="Colapsar">
            <i class="bi bi-chevron-left" style="font-size:10px;"></i>
        </div>

        <a href="{{ url('/') }}" class="sidebar-logo flex-column text-center">
            <div class="logo-text-wrap">
                <div class="logo-name">EPAB</div>
                <hr class="w-100 my-1 opacity-25">
                <div class="logo-tagline">Sistema de Gestión SISGED</div>
            </div>
           <div class="logo-icon-wrap mt-2">

    <img src="{{ asset('images/LogoEmpresa.png') }}"
         alt="Logo"
         style="
            width:90px;
            height:90px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid rgba(255,255,255,0.8);
            box-shadow:0 4px 12px rgba(0,0,0,0.25);
         ">

</div>
        </a>

        <nav class="sidebar-nav">
            @include('components.sidebar_updated')
        </nav>

        <div class="sidebar-user">
            <div class="user-btn">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="user-role">{{ auth()->user()->idRol == 1 ? 'Administrador' : 'Usuario' }}</div>
                </div>
            </div>
            <!-- Integración del Botón de Cerrar Sesión -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2 text-center" style="padding: 0 5px;">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100" style="border-radius: 8px;">
                    <i class="bi bi-box-arrow-left"></i> <span class="nav-label-text">Salir</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

function toggleDesktop() {
    sidebar.classList.toggle('collapsed');
    document.body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

if (localStorage.getItem('sidebarCollapsed') === 'true') {
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
}

document.getElementById('btnMobile').addEventListener('click', () => {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('active');
});

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
}

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 992) closeMobileSidebar();
    });
});
</script>
</body>
</html>