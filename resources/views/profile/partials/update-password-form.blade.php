<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="relative mt-1">
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full pr-12" autocomplete="current-password" />
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500"
                        onclick="togglePasswordVisibility('update_password_current_password', 'icon-update-current-password')"
                        aria-label="Mostrar contrasena actual">
                    <i class="bi bi-eye" id="icon-update-current-password"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="relative mt-1">
                <x-text-input id="update_password_password" name="password" type="password" class="block w-full pr-12" autocomplete="new-password" />
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500"
                        onclick="togglePasswordVisibility('update_password_password', 'icon-update-password')"
                        aria-label="Mostrar nueva contrasena">
                    <i class="bi bi-eye" id="icon-update-password"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full pr-12" autocomplete="new-password" />
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500"
                        onclick="togglePasswordVisibility('update_password_password_confirmation', 'icon-update-password-confirmation')"
                        aria-label="Mostrar confirmacion de contrasena">
                    <i class="bi bi-eye" id="icon-update-password-confirmation"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
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
</section>
