<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Departamento;

class PersonaController extends Controller
{
    public function index()
{
    $personas = Persona::with('departamento')
        ->orderBy('nombre')
        ->get();

    return view(
        'admin.personas.index',
        compact('personas')
    );
}
public function edit($id)
{
    $persona = Persona::findOrFail($id);

    $departamentos = Departamento::orderBy('nombre')->get();

    return view(
        'admin.personas.edit',
        compact(
            'persona',
            'departamentos'
        )
    );
}
public function update(Request $request, $id)
{
    $persona = Persona::findOrFail($id);

    $validated = $request->validate([

        'nombre' =>
            'required|string|max:200|regex:/^[\pL\s]+$/u',

        'ci' =>
            'required|string|max:20|unique:PERSONA,ci,' .
            $persona->idPersona .
            ',idPersona',

        'correo' =>
            'nullable|email|max:150',

        'telefono_celular' =>
            'nullable|string|max:20',

        'telefono_fijo' =>
            'nullable|string|max:20',

        'cargo' =>
            'nullable|string|max:150',

        'institucion' =>
            'nullable|string|max:200',

        'tipo' =>
            'required|in:INTERNO,EXTERNO',

        'idDepartamento' =>
            'nullable|exists:DEPARTAMENTO,idDepartamento',

    ]);

    $persona->update([

        'nombre' =>
            strtoupper(trim($validated['nombre'])),

        'ci' =>
            trim($validated['ci']),

        'correo' =>
            $validated['correo'] ?? null,

        'telefono_celular' =>
            $validated['telefono_celular'] ?? null,

        'telefono_fijo' =>
            $validated['telefono_fijo'] ?? null,

        'cargo' =>
            $validated['cargo'] ?? null,

        'institucion' =>
            $validated['institucion'] ?? null,

        'tipo' =>
            $validated['tipo'],

        'idDepartamento' =>
            $validated['idDepartamento'] ?? null,

    ]);

    return redirect()
        ->route('admin.personas.index')
        ->with(
            'success',
            'Persona actualizada correctamente.'
        );
}
public function toggle($id)
{
    $persona = Persona::findOrFail($id);

    $persona->activo = !$persona->activo;

    $persona->save();

    return redirect()
        ->back()
        ->with(
            'success',
            'Estado actualizado correctamente.'
        );
}
public function buscar(Request $request)
{
    $q = trim($request->q);

    $personas = Persona::query()

        ->where(function ($query) use ($q) {

            $query->where('nombre', 'LIKE', "%{$q}%")
                  ->orWhere('ci', 'LIKE', "%{$q}%");

        })

        ->limit(10)

        ->get([
            'idPersona',
            'nombre',
            'ci'
        ]);

    return response()->json($personas);
}
}
