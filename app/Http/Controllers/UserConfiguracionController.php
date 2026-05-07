<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserConfiguracionController extends Controller
{
    public function edit()
    {
        return view('user.configuracion');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // CAMBIO DE CONTRASEÑA
        if ($request->filled('password')) {

            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            // VERIFICAR CONTRASEÑA ACTUAL
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'La contraseña actual es incorrecta.'
                ]);
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}