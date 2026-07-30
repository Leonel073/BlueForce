<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FORMULARIO PERFIL
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request): View
    {
        return view('profile.edit', [

            'user' => $request->user(),

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR PERFIL
    |--------------------------------------------------------------------------
    */

    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR DATOS
        |--------------------------------------------------------------------------
        */

        $user->fill(
            $request->validated()
        );

        /*
        |--------------------------------------------------------------------------
        | SI CAMBIÓ EL EMAIL
        |--------------------------------------------------------------------------
        */

        if ($user->isDirty('email')) {

            $user->email_verified_at = null;

        }

        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
        */

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        if ($user->idRol == 1) {

            return Redirect::route(
                'admin.dashboard'
            )->with(
                'status',
                'Perfil actualizado correctamente.'
            );

        }

        return Redirect::route(
            'user.configuracion'
        )->with(
            'status',
            'Perfil actualizado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DESACTIVAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request
    ): RedirectResponse {

        $request->validateWithBag(
            'userDeletion',
            [

                'password' => [
                    'required',
                    'current_password'
                ],

            ]
        );

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | DESACTIVAR EN VEZ DE ELIMINAR
        |--------------------------------------------------------------------------
        */

        $user->activo = false;

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | CERRAR SESIÓN
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN
        |--------------------------------------------------------------------------
        */

        return Redirect::to('/');
    }
}
