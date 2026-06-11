
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISGED - EPAB</title>

    <!-- ================================================
         DEPENDENCIAS EXTERNAS
    ================================================ -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ================================================
         VARIABLES GLOBALES Y ESTILOS BASE
    ================================================ -->
    <style>
        /* --- Variables de marca EPAB --- */
        :root {
            --epab-dark-navy:   #050a1f;
            --epab-gold:        #f8d468;
            --epab-blue-action: #0f57fb;
        }

        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
        }
    </style>

    <!-- ================================================
         ESTILOS DEL HEADER
    ================================================ -->
    <style>
        .header-epab {
            background-color: var(--epab-dark-navy);
            padding: 15px 0;
            color: white;
            border-bottom: 4px solid var(--epab-gold);
        }

        .navbar-brand img {
            max-height: 70px;
            filter: drop-shadow(0 0 5px rgba(255,255,255,0.2));
        }

        .brand-text {
            border-left: 1px solid rgba(255,255,255,0.3);
            margin-left: 15px;
            padding-left: 15px;
            text-transform: uppercase;
        }

        .brand-text h1 {
            font-size: 1rem;
            margin: 0;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .brand-text p {
            font-size: 0.8rem;
            margin: 0;
            color: #d1d1d1;
        }

        .nav-link-custom {
            color: white !important;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin: 0 15px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-link-custom:hover {
            color: var(--epab-gold) !important;
        }

        .btn-login-navy {
            background-color: var(--epab-blue-action);
            color: white !important;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 6px;
            text-transform: uppercase;
            font-size: 0.8rem;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .btn-login-navy:hover {
            background-color: #0044cc;
            box-shadow: 0 0 15px rgba(15, 87, 251, 0.4);
        }

        /* Etiqueta lateral "Campus Virtual" */
        .campus-tag {
            background-color: var(--epab-gold);
            color: #050a1f;
            padding: 10px 5px;
            font-weight: bold;
            writing-mode: vertical-rl;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 2px;
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            display: flex;
            align-items: center;
        }
    </style>

    <!-- ================================================
         ESTILOS DEL CAROUSEL
    ================================================ -->
    <style>
        .carousel-item img {
            filter: brightness(0.9);
        }

        /* Overlay degradado Slide 2 (de derecha a izquierda) */
        .overlay-formal-right {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(to left, rgba(5,10,31,0.9) 0%, rgba(5,10,31,0) 60%);
            z-index: 1;
        }

        /* Caption alineado a la derecha (Slide 2) */
        .custom-caption-right {
            z-index: 2;
            bottom: 20%;
            right: 8% !important;
            left: auto !important;
            max-width: 600px;
        }

        /* Botón dorado del carousel */
        .btn-epab-gold {
            background-color: var(--epab-gold);
            color: #050a1f !important;
            font-weight: 800;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-epab-gold:hover {
            background-color: #ffffff;
            transform: scale(1.05);
        }
    </style>

    <!-- ================================================
         ESTILOS DEL FOOTER
    ================================================ -->
    <style>
        /* Título decorativo con líneas doradas */
        .titulo-interes-central {
            display: flex;
            align-items: center;
            text-align: center;
            color: #243673;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0;
        }

        .titulo-interes-central::before,
        .titulo-interes-central::after {
            content: '';
            flex: 1;
            border-bottom: 2px solid var(--epab-gold);
        }

        .titulo-interes-central:not(:empty)::before { margin-right: 20px; }
        .titulo-interes-central:not(:empty)::after  { margin-left:  20px; }

        /* Links institucionales */
        .enlace-institucional {
            text-decoration: none;
            display: block;
            transition: transform 0.3s ease;
        }

        .enlace-institucional:hover {
            transform: translateY(-5px);
        }

        .logo-filtro {
            max-height: 120px;
            transition: filter 0.3s ease;
        }

        .enlace-institucional:hover .logo-filtro {
            filter: brightness(1.1);
        }

        /* Sobreescritura color warning para dorado EPAB */
        .text-warning { color: var(--epab-gold) !important; }

        footer i:hover { color: var(--epab-gold); transition: color 0.3s; }
    </style>
</head>

<body>

<!-- ====================================================
     HEADER
==================================================== -->
<header class="header-epab position-relative">
    <div class="container d-flex align-items-center justify-content-between">

        <!-- Logo y título -->
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/OIP.png') }}"
     alt="Logo EPAB"
     style="
        width:75px;
        height:75px;
        border-radius:50%;
        object-fit:cover;
        border:3px solid white;
     ">
            <div class="brand-text d-none d-md-block">
                <h1>Escuela de Posgrado de la <br> Armada Boliviana</h1>
            </div>
        </div>

        <!-- Navegación y botón de acceso -->
        <div class="d-flex align-items-center">
            <nav class="d-none d-lg-flex">
                <a href="#"                         class="nav-link-custom">Inicio</a>
                <a href="#"                         class="nav-link-custom">Nosotros</a>
                <a href="https://epab.edu.bo/" target="_blank" class="nav-link-custom">Web Oficial</a>
            </nav>
           <a href="{{ route('page') }}" class="btn-login-navy ms-4">
    <i class="fa fa-user"></i> Iniciar Sesión
</a>

        </div>
    </div>

    <!-- Etiqueta lateral Campus Virtual -->
    <div class="campus-tag d-none d-xl-flex">CAMPUS VIRTUAL</div>
</header>


<!-- ====================================================
     CAROUSEL PRINCIPAL
==================================================== -->
<div id="carouselEPAB" class="carousel slide carousel-fade" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselEPAB" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#carouselEPAB" data-bs-slide-to="1"></button>
    </div>

    <div class="carousel-inner">

        <!-- Slide 1 -->
        <div class="carousel-item active" data-bs-interval="6000">
            <img src="{{ asset('img/Fondo2.jpg') }}"
                 class="d-block w-100"
                 style="height: 450px; object-fit: cover; object-position: left center;">

            <div class="carousel-caption d-none d-md-block custom-caption-end">
                <h1 class="display-4 fw-bold">
                    Bienvenido al <span style="color: var(--epab-gold);">SISGED</span>
                </h1>
                <p class="lead fs-4">
                    Sistema de Gestión Documental <br>
                    <span class="text-white-50 small">Escuela de Posgrado de la Armada Boliviana</span>
                </p>
                <div class="mt-4">
                    <a href="/login" class="btn-epab-gold">
                        INGRESAR AL PORTAL <i class="fa fa-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item" data-bs-interval="6000">
            <div class="overlay-formal-right"></div>
            <img src="{{ asset('img/Fondo.png') }}"
                 class="d-block w-100"
                 style="height: 500px; object-fit: contain; background-color: var(--epab-dark-navy);">

            <div class="carousel-caption d-none d-md-block text-end custom-caption-right">
                <h2 class="display-4 fw-bold">
                    CONTROL <span style="color: var(--epab-gold);">EFICIENTE</span>
                </h2>
                <p class="lead">
                    Trazabilidad y seguridad en cada <br> documento institucional.
                </p>
            </div>
        </div>

    </div><!-- /.carousel-inner -->
</div><!-- /#carouselEPAB -->


<!-- ====================================================
     FOOTER
==================================================== -->
<footer class="mt-5">

    <!-- Nivel 1: Páginas de interés (fondo blanco) -->
    <div class="py-4 bg-white border-bottom border-top">
        <div class="container">

            <div class="row mb-4">
                <div class="col text-center">
                    <h6 class="titulo-interes-central">PÁGINAS DE INTERÉS</h6>
                </div>
            </div>

            <div class="row align-items-center text-center g-4">

                <div class="col-md-3">
                    <a href="https://www.mindef.gob.bo/" target="_blank" class="enlace-institucional">
                        <img src="{{ asset('img/Ministerio_de_defensa.png') }}" alt="Ministerio de Defensa" class="img-fluid mb-2 logo-filtro">
                        <p class="small fw-bold text-uppercase mb-0 text-dark">Ministerio de Defensa</p>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="https://www.armada.mil.bo/" target="_blank" class="enlace-institucional">
                        <img src="{{ asset('img/Armada_Boliviana.png') }}" alt="Armada Boliviana" class="img-fluid mb-2 logo-filtro">
                        <p class="small fw-bold text-uppercase mb-0 text-dark">Armada Boliviana</p>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="https://universidadmilitar.edu.bo/" target="_blank" class="enlace-institucional">
                        <img src="{{ asset('img/Universidad_Militar_Mcal_Bernardino_Bilbao_Rioja.png') }}" alt="Universidad Militar" class="img-fluid mb-2 logo-filtro">
                        <p class="small fw-bold text-uppercase mb-0 text-dark">Universidad Militar</p>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="https://www.minedu.gob.bo/" target="_blank" class="enlace-institucional">
                        <img src="{{ asset('img/Ministerio_de_Educación.png') }}" alt="Ministerio de Educación" class="img-fluid mb-2 logo-filtro">
                        <p class="small fw-bold text-uppercase mb-0 text-dark">Ministerio de Educación</p>
                    </a>
                </div>

            </div><!-- /.row logos -->
        </div><!-- /.container -->
    </div><!-- /nivel 1 -->


    <!-- Nivel 2: Contacto y mapa (fondo azul marino) -->
    <div class="py-5 text-white" style="background-color: var(--epab-dark-navy);">
        <div class="container">
            <div class="row g-5">

                <!-- Columna izquierda: Logo + redes sociales -->
                <div class="col-md-4 text-center text-md-start border-end border-secondary border-opacity-25">
                    <div class="d-flex align-items-center mb-4 justify-content-center justify-content-md-start">
                        <img src="{{ asset('img/OIP.png') }}" alt="Logo EPAB" width="80" class="me-3">
                        <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">
                            Escuela de Posgrado de la <br> Armada Boliviana
                        </h6>
                    </div>
                    <div class="d-flex gap-3 justify-content-center justify-content-md-start mt-4">
                        <a href="https://www.facebook.com/people/Epab-Escuela-de-Posgrado-de-la-Armada-Boliviana/61586980961148/" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="https://wa.link/uvksr4" class="text-white fs-4"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://www.youtube.com/@EPAB24" class="text-white fs-4"><i class="fab fa-youtube"></i></a>
                        <a href="https://x.com/EPAB_2024" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <!-- Columna central: Datos de contacto -->
                <div class="col-md-4 px-md-5">
                    <div class="mb-4">
                        <h6 class="text-warning fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Dirección:</h6>
                        <p class="small mb-0 text-white-50">La Paz, Av. Ismael Montes Calle Vicenta J. Eguino Nro. 400 Zona Central</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-warning fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Horario de Atención:</h6>
                        <p class="small mb-0 text-white-50">Lunes a jueves: 8:30 a 12:00 – 13:30 a 17:00</p>
                        <p class="small mb-0 text-white-50">Viernes: 8:30 a 12:00</p>
                    </div>
                    <div>
                        <h6 class="text-warning fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Teléfonos:</h6>
                        <p class="small mb-0 text-white-50">64207918 — 63258027</p>
                    </div>
                </div>

                <!-- Columna derecha: Mapa -->
                <div class="col-md-4">
                    <h6 class="text-warning fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Ubicación:</h6>
                    <div class="rounded-3 overflow-hidden shadow-lg border border-secondary border-opacity-50" style="height: 220px;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3158.2112161903788!2d-68.14407712580261!3d-16.48973088425237!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f21b581486d7f%3A0x7cdea43212fa8953!2sEscuela%20Idiomas%20Armada%20Boliviana!5e1!3m2!1ses-419!2sbo!4v1778026948518!5m2!1ses-419!2sbo"
                            width="100%" height="100%"
                            style="border:0;"
                            allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

            </div><!-- /.row -->

            <!-- Copyright -->
            <div class="row mt-5 pt-4 border-top border-secondary border-opacity-25">
                <div class="col-12 text-center">
                    <p class="small mb-0 text-white-50">
                        Copyright © 2024 <span class="text-warning fw-bold">EPAB</span>
                    </p>
                </div>
            </div>

        </div><!-- /.container -->
    </div><!-- /nivel 2 -->

</footer>


<!-- ====================================================
     SCRIPTS
==================================================== -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>