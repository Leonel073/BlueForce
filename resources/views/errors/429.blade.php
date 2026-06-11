<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Demasiados Intentos</title>
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
            border-top: 6px solid #ff6b6b;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-icon {
            font-size: 5rem;
            color: #ff6b6b;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .error-code {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
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
            border: 2px solid #ff6b6b;
        }

        .btn-secondary-custom:hover {
            background: #ff6b6b;
            color: white;
            transform: translateY(-2px);
        }

        .timer {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            color: #0d1b2a;
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
            <i class="bi bi-exclamation-circle-fill"></i>
        </div>

        <div class="error-code">429</div>

        <h1 class="error-title">Demasiados Intentos</h1>

        <p class="error-message">
            Has realizado demasiadas solicitudes en poco tiempo.
        </p>

        <div class="timer">
            <i class="bi bi-clock"></i> Intenta nuevamente en unos minutos
        </div>

        <div style="margin-top: 2rem;">
            @if(Auth::check())
                <a href="{{ url('dashboard') }}" class="btn-action btn-primary-custom">
                    <i class="bi bi-house-door"></i> Volver al Dashboard
                </a>
            @else
                <a href="{{ url('/') }}" class="btn-action btn-primary-custom">
                    <i class="bi bi-globe"></i> Página Principal
                </a>
            @endif
            <button onclick="window.history.back()" class="btn-action btn-secondary-custom">
                <i class="bi bi-arrow-left"></i> Atrás
            </button>
        </div>

        <div class="error-footer">
            <p>
                <i class="bi bi-info-circle"></i> 
                Esto es una medida de seguridad para proteger el sistema.
            </p>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>