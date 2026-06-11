<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Servicio No Disponible</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .error-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            margin: 1.5rem;
            border-top: 6px solid #17a2b8;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-icon {
            font-size: 5rem;
            color: #17a2b8;
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-code {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #17a2b8, #138496);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 1rem 0;
            letter-spacing: 2px;
        }

        .error-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0d1b2a;
            margin: 1rem 0;
        }

        .error-message {
            font-size: 1rem;
            color: #64748b;
            margin: 1.5rem 0;
            line-height: 1.6;
        }

        .btn-action {
            display: inline-block;
            padding: 0.75rem 2rem;
            margin: 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #0d1b2a, #1b263b);
            color: white;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #1b263b, #0d1b2a);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(13, 27, 42, 0.3);
            color: #ffc107;
        }

        .btn-secondary-custom {
            background: transparent;
            color: #0d1b2a;
            border: 2px solid #17a2b8;
        }

        .btn-secondary-custom:hover {
            background: #17a2b8;
            color: white;
            transform: translateY(-2px);
        }

        .maintenance-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 1rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            color: #0c5460;
            font-weight: 600;
        }

        .error-footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .error-container { padding: 2rem 1.5rem; }
            .error-code { font-size: 3rem; }
            .error-title { font-size: 1.5rem; }
            .btn-action { display: block; width: 100%; margin: 0.75rem 0; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-tools"></i>
        </div>

        <div class="error-code">503</div>

        <h1 class="error-title">Sistema en Mantenimiento</h1>

        <p class="error-message">
            Estamos realizando tareas de mantenimiento para mejorar nuestros servicios.
        </p>

        <div class="maintenance-info">
            <i class="bi bi-info-circle"></i> 
            Estaremos disponibles muy pronto. Gracias por tu paciencia.
        </div>

        <div style="margin-top: 2rem;">
            @if(Auth::check())
                <p class="error-message" style="color: #17a2b8; font-weight: 600;">
                    <i class="bi bi-person-fill"></i> 
                    Hola {{ Auth::user()->name }}, intenta de nuevo en unos momentos.
                </p>
            @else
                <p class="error-message">
                    Pronto estaremos de vuelta en línea.
                </p>
            @endif
            
            <button onclick="location.reload()" class="btn-action btn-primary-custom">
                <i class="bi bi-arrow-clockwise"></i> Reintentar
            </button>
            <a href="{{ url('/') }}" class="btn-action btn-secondary-custom">
                <i class="bi bi-globe"></i> Página Principal
            </a>
        </div>

        <div class="error-footer">
            <p>
                <i class="bi bi-calendar2-event"></i> 
                Mantenimiento programado
            </p>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>