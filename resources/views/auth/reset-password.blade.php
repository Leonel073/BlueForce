<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-12" type="password" name="password" required autocomplete="new-password" />
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500"
                        onclick="togglePasswordVisibility('password', 'icon-password')"
                        aria-label="Mostrar contrasena">
                    <i class="bi bi-eye" id="icon-password"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-12"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500"
                        onclick="togglePasswordVisibility('password_confirmation', 'icon-password-confirmation')"
                        aria-label="Mostrar confirmacion de contrasena">
                    <i class="bi bi-eye" id="icon-password-confirmation"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            icon.classList.toggle('bi-eye', visible);
            icon.classList.toggle('bi-eye-slash', !visible);
        }
    </script>
</x-guest-layout>
