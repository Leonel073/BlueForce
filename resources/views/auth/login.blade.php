<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-xl font-bold text-center text-gray-800 mb-6">INICIO DE SESIÓN</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo Electrónico -->
        <div>
            <x-input-label for="email" value="Correo Electrónico Institucional" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ejemplo@armada.mil.bo" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recordarme -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-900 shadow-sm focus:ring-blue-900" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordar mis datos</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                    ¿Olvidó su contraseña?
                </a>
            @endif

            <x-primary-button class="ms-3 bg-blue-900 hover:bg-blue-800">
                INGRESAR
            </x-primary-button>
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </form>
</x-guest-layout>
