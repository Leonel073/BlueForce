<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsuarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Persona;
use App\Models\Rol;

/**
 * UsuarioController (Admin)
 *
 * Gestión completa de usuarios del sistema.
 * Regla principal: solo las PERSONAS INTERNAS pueden tener cuenta de usuario.
 */
class UsuarioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BÚSQUEDA PARA AUTOCOMPLETE
    |--------------------------------------------------------------------------
    */

    public function buscarPersonas(Request $request)
    {
        $q = trim($request->q ?? '');

        // Mínimo 2 caracteres
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Buscar personas internas activas sin usuario asignado
        $personas = Persona::internosSinUsuario()
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'LIKE', "%{$q}%")
                      ->orWhere('ci', 'LIKE', "%{$q}%");
            })
            ->with('cargo', 'departamento')
            ->limit(10)
            ->get([
                'idPersona',
                'nombre',
                'ci',
                'correo',
                'idCargo',
                'idDepartamento'
            ])
            ->map(function ($persona) {
                return [
                    'idPersona'   => $persona->idPersona,
                    'nombre'      => $persona->nombre,
                    'ci'          => $persona->ci,
                    'correo'      => $persona->correo,
                    'cargo'       => $persona->cargo ? $persona->cargo->nombre : 'Sin cargo',
                    'departamento' => $persona->departamento ? $persona->departamento->nombre : 'Sin departamento'
                ];
            });

        return response()->json($personas);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = User::with('persona.cargo', 'persona.departamento', 'rol');

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->buscar . '%')
                  ->orWhere('email', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('rol')) {
            $query->where('idRol', $request->rol);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado);
        }

        $usuarios = $query->paginate(10)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO CREAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        // Solo personas internas activas sin usuario asignado
        $personas = Persona::internosSinUsuario()
            ->with('cargo', 'departamento')
            ->orderBy('nombre')
            ->get();

        $roles = Rol::orderBy('nombre')->get();

        return view('admin.usuarios.create', compact('personas', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR NUEVO USUARIO
    |--------------------------------------------------------------------------
    */

    public function store(StoreUsuarioRequest $request)
    {
        $validated = $request->validated();

        // Verificación adicional de seguridad (doble check)
        $persona = Persona::findOrFail($validated['idPersona']);

        if ($persona->tipo !== 'INTERNO') {
            return back()
                ->withInput()
                ->with('error', 'Solo las personas internas pueden tener cuenta de usuario.');
        }

        if ($persona->usuario()->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Esta persona ya tiene un usuario asignado.');
        }

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'idPersona' => $validated['idPersona'],
            'idRol'     => $validated['idRol'],
            'activo'    => $validated['activo'] ?? true,
        ]);

        return redirect()
            ->route('admin.usuarios')
            ->with('success', 'Usuario creado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $usuario = User::with([
            'persona.cargo',
            'persona.departamento',
            'rol',
            'correspondencias.estado',
            'correspondencias.urgencia',
            'correspondencias.tipoDocumento',
            'correspondencias.seguimientos',
        ])->findOrFail($id);

        return view('admin.usuarios.show', compact('usuario'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $usuario = User::with('persona.cargo', 'persona.departamento', 'rol')
                       ->findOrFail($id);

        $roles = Rol::orderBy('nombre')->get();

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $id,
            'idRol'  => 'required|exists:ROL,idRol',
        ]);

        $usuario->name   = $request->name;
        $usuario->email  = $request->email;
        $usuario->idRol  = $request->idRol;
        $usuario->activo = $request->has('activo');

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:12|confirmed',
            ]);
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()
            ->route('admin.usuarios.show', $usuario->id)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / DESACTIVAR
    |--------------------------------------------------------------------------
    */

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
}
