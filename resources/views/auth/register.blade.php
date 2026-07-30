<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - SISGED</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <style>
        body { background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, sans-serif; margin: 0; padding: 20px; }
        .auth-card { background: rgba(255, 255, 255, 0.98); border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); padding: 2.5rem; width: 100%; max-width: 450px; border-top: 6px solid #ffc107; }
        .auth-logo { width: 80px; margin-bottom: 10px; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));}
        .btn-custom { background-color: #0d1b2a; color: white; font-weight: 600; padding: 10px; border-radius: 8px; border: none; transition: 0.3s; }
        .btn-custom:hover:not(:disabled) { background-color: #1b263b; color: #ffc107; transform: translateY(-2px); }
        .btn-custom:disabled { background-color: #6c757d; opacity: 0.65; cursor: not-allowed; }
        .form-control:focus { border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); }
        .text-muted-custom { color: #64748b; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .text-muted-custom:hover { color: #0d1b2a; text-decoration: underline; }
        
        /* Estilos para la lista de requisitos */
        .req-list { list-style: none; padding-left: 0; margin-top: 8px; font-size: 0.85rem; }
        .req-item { transition: color 0.3s ease; margin-bottom: 3px; color: #dc3545; }
        .req-item.valid { color: #198754; }
        .req-icon { margin-right: 5px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="/">
                <img src="{{ asset('images/LogoEmpresa.png') }}" alt="Logo EPAB" class="auth-logo">
            </a>
            <h4 class="text-dark fw-bold mb-0">Crear Cuenta</h4>
            <p class="text-muted small">Regístrate en el sistema SISGED</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nombre -->
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-dark">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Ej: Jonathan Pérez">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Correo Electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required placeholder="ejemplo@armada.mil.bo">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Contraseña con Validación Dinámica -->
            <div class="mb-2">
                <label for="password" class="form-label fw-semibold text-dark">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required placeholder="Escribe tu contraseña segura">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <!-- Lista de Requisitos -->
                <ul class="req-list" id="passwordRequirements">
                    <li id="req-length" class="req-item"><i class="bi bi-x-circle req-icon"></i>Mínimo 8 caracteres</li>
                    <li id="req-mixed" class="req-item"><i class="bi bi-x-circle req-icon"></i>Mayúsculas y minúsculas</li>
                    <li id="req-special" class="req-item"><i class="bi bi-x-circle req-icon"></i>Mínimo 1 carácter especial (@, $, !, %, *, ?, &)</li>
                </ul>
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold text-dark">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required placeholder="Repite tu contraseña">
                </div>
                <div id="req-match" class="small mt-1 text-danger" style="display: none;">
                    <i class="bi bi-exclamation-circle"></i> Las contraseñas no coinciden
                </div>
            </div>

            <button type="submit" id="btn-submit" class="btn btn-custom w-100" disabled>
                <i class="bi bi-person-plus"></i> Registrarme
            </button>
            
            <div class="text-center mt-3">
                <span class="text-muted small">¿Ya tienes una cuenta?</span> 
                <a href="{{ route('login') }}" class="text-muted-custom fw-bold">Inicia sesión aquí</a>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const btnSubmit = document.getElementById('btn-submit');
            const reqMatch = document.getElementById('req-match');

            // Elementos de la lista
            const reqLength = document.getElementById('req-length');
            const reqMixed = document.getElementById('req-mixed');
            const reqSpecial = document.getElementById('req-special');

            function updateItem(el, isValid) {
                const icon = el.querySelector('i');
                if (isValid) {
                    el.classList.add('valid');
                    icon.classList.remove('bi-x-circle');
                    icon.classList.add('bi-check-circle-fill');
                } else {
                    el.classList.remove('valid');
                    icon.classList.remove('bi-check-circle-fill');
                    icon.classList.add('bi-x-circle');
                }
            }

            function validatePassword() {
                const val = password.value;
                const confVal = confirmPassword.value;
                
                // Expresiones regulares
                const hasLength = val.length >= 8;
                const hasMixed = /[A-Z]/.test(val) && /[a-z]/.test(val);
                // Busca cualquier cosa que NO sea letra o número
                const hasSpecial = /[^A-Za-z0-9]/.test(val); 
                const isMatch = val === confVal && val !== '';

                // Actualizar interfaz visual
                updateItem(reqLength, hasLength);
                updateItem(reqMixed, hasMixed);
                updateItem(reqSpecial, hasSpecial);

                // Validar coincidencia de confirmación
                if (confVal.length > 0 && !isMatch) {
                    reqMatch.style.display = 'block';
                } else {
                    reqMatch.style.display = 'none';
                }

                // Habilitar o deshabilitar botón
                if (hasLength && hasMixed && hasSpecial && isMatch) {
                    btnSubmit.disabled = false;
                } else {
                    btnSubmit.disabled = true;
                }
            }

            // Escuchar mientras el usuario escribe
            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>
