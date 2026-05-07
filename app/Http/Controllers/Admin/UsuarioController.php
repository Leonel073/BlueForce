<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // BUSCAR POR NOMBRE O EMAIL
        if ($request->filled('buscar')) {

            $query->where(function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->buscar . '%')
                ->orWhere('email', 'like', '%' . $request->buscar . '%');

            });

        }

        // FILTRAR POR ROL
        if ($request->filled('rol')) {

            $query->where('idRol', $request->rol);

        }

        // FILTRAR POR ESTADO
        if ($request->filled('estado')) {

            $query->where('activo', $request->estado);

        }

        $usuarios = $query->paginate(10);

        return view(
            'admin.usuarios.index',
            compact('usuarios')
        );
    }
   public function show($id)
    {
        $usuario = User::with([
            'correspondencias.estado',
            'correspondencias.urgencia',
            'correspondencias.tipoDocumento',
            'correspondencias.seguimientos'
        ])->findOrFail($id);

        return view('admin.usuarios.show', compact('usuario'));
    }
    public function toggle($id)
    {
        $usuario = User::findOrFail($id);

        $usuario->activo = !$usuario->activo;

        $usuario->save();

        return back()->with(
            'success',
            'Estado del usuario actualizado correctamente.'
        );
    }
    //edicion de datos 
    public function edit($id)
    {
        $usuario = User::findOrFail($id);

        return view(
            'admin.usuarios.edit',
            compact('usuario')
        );
    }
    //actualizar datos
        public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'idRol' => 'required',
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->idRol = $request->idRol;

        // ACTIVO
        $usuario->activo = $request->has('activo');

        // CAMBIO DE CONTRASEÑA
        if ($request->filled('password')) {

            $request->validate([
                'password' => 'min:8|confirmed'
            ]);

            $usuario->password = bcrypt($request->password);
        }

        $usuario->save();

        return redirect()
            ->route('admin.usuarios.show', $usuario->id)
            ->with(
                'success',
                'Usuario actualizado correctamente.'
            );
    }
}