<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB; // <-- AÑADE ESTA LÍNEA

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
public function store(Request $request): RedirectResponse
    {
        // Validación estricta con mensajes personalizados en español
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required', 
                'confirmed', 
                // Exigimos 8 caracteres, al menos 1 mayúscula y 1 símbolo/carácter especial
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->symbols()
            ],
        ], [
            // MENSAJES DE ERROR EN ESPAÑOL
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener al menos una letra mayúscula.',
            'password.symbols' => 'La contraseña debe contener al menos un carácter especial (@, $, !, %, etc.).'
        ]);

        // 1. Crear a la PERSONA primero y obtener su ID
        $idPersona = DB::table('PERSONA')->insertGetId([
            'nombre' => $request->name,
            'correo' => $request->email,
            'tipo' => 'INTERNO',
        ]);

        // 2. Crear al usuario vinculado a esa persona
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idPersona' => $idPersona,
            'idRol' => 2, // 2 = Rol de Usuario Normal
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('user.dashboard', absolute: false));
    }
}
