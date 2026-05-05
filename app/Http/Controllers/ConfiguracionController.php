<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class ConfiguracionController extends Controller
{
    // 🔹 Mostrar vista
    public function index()
    {
        return view('user.configuracion.index', [
            'user' => Auth::user()
        ]);
    }

    // 🔹 Actualizar datos (USUARIO)
    public function updatePerfil(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ]);

        $user = Auth::user();

        $user->correo = $request->correo;
        $user->save();

        return back()->with('success', 'Datos actualizados correctamente');
    }

    // 🔐 Cambiar contraseña
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // 🔥 VALIDAR CONTRASEÑA REAL
        if (!Hash::check($request->current_password, $user->contrasena)) {
            return back()->with('error', 'La contraseña actual es incorrecta');
        }

        // 🔒 GUARDAR NUEVA
        $user->contrasena = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente');
    }
}