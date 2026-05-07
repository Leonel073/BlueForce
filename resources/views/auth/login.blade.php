<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SISGED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, sans-serif; margin: 0; padding: 20px; }
        .auth-card { background: rgba(255, 255, 255, 0.98); border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); padding: 2.5rem; width: 100%; max-width: 420px; border-top: 6px solid #ffc107; }
        .auth-logo { width: 90px; margin-bottom: 10px; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1)); }
        .btn-custom { background-color: #0d1b2a; color: white; font-weight: 600; padding: 10px; border-radius: 8px; border: none; transition: 0.3s; }
        .btn-custom:hover { background-color: #1b263b; color: #ffc107; transform: translateY(-2px); }
        .form-control:focus { border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); }
        .text-muted-custom { color: #64748b; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .text-muted-custom:hover { color: #0d1b2a; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="/">
                <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo EPAB" class="auth-logo">
            </a>
            <h4 class="text-dark fw-bold mb-0">Iniciar Sesión</h4>
            <p class="text-muted small">Sistema de Gestión SISGED</p>
        </div>

        <!-- Mensaje de estado (por si se restablece la contraseña) -->
        <x-auth-session-status class="mb-4 text-success text-center fw-bold" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Correo Electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ejemplo@armada.mil.bo">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-dark">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required placeholder="••••••••">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

          

            <button type="submit" class="btn btn-custom w-100">
                <i class="bi bi-box-arrow-in-right"></i> Ingresar al Sistema
            </button>
            <!--
            @if (Route::has('register'))
                <div class="text-center mt-3">
                    <span class="text-muted small">¿No tienes cuenta?</span> 
                    <a href="{{ route('register') }}" class="text-muted-custom fw-bold">Regístrate aquí</a>
                </div>
            @endif -->
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>