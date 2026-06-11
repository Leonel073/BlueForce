<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISGED - Escuela de Posgrado de la Armada Boliviana</title>
    
    <!-- Bootstrap para el diseño responsivo -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    
    <style>
        body {
            /* Fondo elegante usando los colores de la Armada de tu sidebar */
            background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 90%;
            text-align: center;
            /* Línea dorada superior */
            border-top: 6px solid #ffc107; 
        }

        .logo-img {
            width: 130px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));
        }

        .title {
            color: #0d1b2a;
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subtitle {
            color: #475569;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 35px;
        }

        .btn-custom-primary {
            background-color: #0d1b2a;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-custom-primary:hover {
            background-color: #1b263b;
            color: #ffc107; /* Texto dorado al pasar el mouse */
            transform: translateY(-2px);
        }

        .btn-custom-secondary {
            background-color: transparent;
            color: #0d1b2a;
            font-weight: 600;
            padding: 12px;
            border: 2px solid #0d1b2a;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-custom-secondary:hover {
            background-color: #0d1b2a;
            color: white;
        }

        .footer-text {
            margin-top: 2rem;
            padding-top: 1rem;
            font-size: 0.8rem;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="welcome-card">
        <!-- Logo de la EPAB (Usa el mismo de tu sidebar) -->
        <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo EPAB" class="logo-img">
        
        <h1 class="title">Escuela de Posgrado de la Armada Boliviana</h1>
        <p class="subtitle">Sistema de Gestión SISGED</p>

        <div class="d-grid gap-3">
            @if (Route::has('login'))
                @auth
                    <!-- Si el usuario ya inició sesión, le mostramos el botón para ir a su panel -->
                    <a href="{{ Auth::user()->idRol == 1 ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-custom-primary w-100">
                        Ir al Panel Principal
                    </a>
                @else
                    <!-- Si no ha iniciado sesión, mostramos botones de Login y Registro -->
                    <a href="{{ route('login') }}" class="btn btn-custom-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>

                    <!--
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-custom-secondary w-100">
                            <i class="bi bi-person-plus"></i> Registrarse
                        </a>
                    @endif-->
                @endauth
            @endif
        </div>
        
        <div class="footer-text">
            &copy; {{ date('Y') }} EPAB. Todos los derechos reservados.
        </div>
    </div>

</body>
</html>