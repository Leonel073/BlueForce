<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Departamento;
use App\Models\Cargo;
use App\Models\DepartamentoResponsable;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;

class PersonaController extends Controller
{
    public function index()
{
    $personas = Persona::with('departamento', 'cargo')
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
    
    $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

    return view(
        'admin.personas.edit',
        compact(
            'persona',
            'departamentos',
            'cargos'
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

        'idCargo' =>
            'nullable|exists:CARGO,idCargo',

        'institucion' =>
            'nullable|string|max:200',

        'tipo' =>
            'required|in:INTERNO,EXTERNO',

        'idDepartamento' =>
            'nullable|exists:DEPARTAMENTO,idDepartamento',

    ]);

    // Si es EXTERNO, no puede tener cargo
    if ($validated['tipo'] === 'EXTERNO') {
        $validated['idCargo'] = null;
    }

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

        'idCargo' =>
            $validated['idCargo'] ?? null,

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

    if (strlen($q) < 2) {
        return response()->json([]);
    }

    $personas = Persona::query()
        ->where('activo', true)
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
            'tipo',
            'idCargo',
            'idDepartamento'
        ])
        ->map(function ($persona) {
            return [
                'idPersona' => $persona->idPersona,
                'nombre' => $persona->nombre,
                'ci' => $persona->ci,
                'tipo' => $persona->tipo,
                'cargo' => $persona->cargo ? $persona->cargo->nombre : null,
                'departamento' => $persona->departamento ? $persona->departamento->nombre : null
            ];
        });

    return response()->json($personas);
}

public function create()
{
    $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();
    return view('admin.personas.create', compact('cargos'));
}

public function store(StorePersonaRequest $request)
{
    $validated = $request->validated();
    $persona = Persona::create([
        'nombre' => strtoupper(trim($validated['nombre'])),
        'ci' => trim($validated['ci']),
        'tipo' => $validated['tipo'] ?? 'INTERNO',
        'telefono_celular' => $validated['telefono_celular'],
        'telefono_fijo' => $validated['telefono_fijo'] ?? null,
        'correo' => $validated['correo'] ?? null,
        'institucion' => $validated['institucion'] ?? null,
        'idCargo' => $validated['idCargo'],
        'idDepartamento' => null,
        'activo' => true,
        'fecha_creacion' => now(),
    ]);

    return redirect()->route('admin.personas.index')
        ->with('success', 'Persona creada correctamente.');
}

public function disable($id)
{
    $persona = Persona::findOrFail($id);
    if ($persona->esResponsableActual()) {
        foreach ($persona->departamentosResponsables() as $depto) {
            $depto->declinarResponsable();
        }
    }
    $persona->deshabilitar();
    return redirect()->back()
        ->with('success', 'Persona deshabilitada correctamente.');
}

public function enable($id)
{
    $persona = Persona::findOrFail($id);
    $persona->reactivar();
    return redirect()->back()
        ->with('success', 'Persona reactivada correctamente.');
}
}
