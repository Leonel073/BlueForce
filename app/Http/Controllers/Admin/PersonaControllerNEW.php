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
    /*
    |--------------------------------------------------------------------------
    | ÍNDICE - Separado por tipos
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Separar trabajadores (con departamento) y remitentes (sin departamento)
        $tab = $request->get('tab', 'trabajadores');

        if ($tab === 'remitentes') {
            // Personas remitentes (sin departamento asignado)
            $personas = Persona::remitentes()
                ->with('cargo')
                ->orderBy('nombre')
                ->get();

            return view('admin.personas.index', compact('personas', 'tab'));
        }

        // Personas que trabajan en departamentos
        $personas = Persona::trabajadores()
            ->with('departamento', 'cargo')
            ->orderBy('nombre')
            ->get();

        return view('admin.personas.index', compact('personas', 'tab'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR PERSONA
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $departamentos = Departamento::activos()->orderBy('nombre')->get();
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

        return view('admin.personas.create', compact('departamentos', 'cargos'));
    }

    public function store(StorePersonaRequest $request)
    {
        $validated = $request->validated();

        // Crear persona
        $persona = Persona::create([
            'nombre' => strtoupper(trim($validated['nombre'])),
            'ci' => trim($validated['ci']),
            'tipo' => $validated['tipo'],
            'telefono_celular' => $validated['telefono_celular'],
            'telefono_fijo' => $validated['telefono_fijo'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'institucion' => $validated['institucion'] ?? null,
            'idCargo' => $validated['idCargo'],
            'idDepartamento' => $validated['idDepartamento'],
            'activo' => true,
            'fecha_creacion' => now(),
        ]);

        // Si es responsable, crear registro de auditoría
        if ($request->boolean('es_responsable')) {
            $departamento = Departamento::find($validated['idDepartamento']);
            $departamento->asignarResponsable($persona->idPersona);
        }

        return redirect()
            ->route('admin.personas.index')
            ->with('success', 'Persona creada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR PERSONA
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $persona = Persona::findOrFail($id);
        $departamentos = Departamento::activos()->orderBy('nombre')->get();
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

        return view(
            'admin.personas.edit',
            compact('persona', 'departamentos', 'cargos')
        );
    }

    public function update(UpdatePersonaRequest $request, $id)
    {
        $persona = Persona::findOrFail($id);
        $validated = $request->validated();

        $persona->update([
            'nombre' => strtoupper(trim($validated['nombre'])),
            'ci' => trim($validated['ci']),
            'tipo' => $validated['tipo'],
            'telefono_celular' => $validated['telefono_celular'],
            'telefono_fijo' => $validated['telefono_fijo'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'institucion' => $validated['institucion'] ?? null,
            'idCargo' => $validated['idCargo'],
            'idDepartamento' => $validated['idDepartamento'],
        ]);

        // Manejar responsabilidad
        $departamento = Departamento::find($validated['idDepartamento']);

        if ($request->boolean('es_responsable')) {
            // Asignar como responsable
            if (!$persona->esResponsableActual()) {
                $departamento->asignarResponsable($persona->idPersona);
            }
        } else {
            // Declinar responsabilidad si la tiene
            if ($persona->esResponsableActual()) {
                $departamento->declinarResponsable();
            }
        }

        return redirect()
            ->route('admin.personas.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESHABILITAR PERSONA (Borrado Lógico)
    |--------------------------------------------------------------------------
    */

    public function disable($id)
    {
        $persona = Persona::findOrFail($id);

        // Si es responsable, declinar
        if ($persona->esResponsableActual()) {
            foreach ($persona->departamentosResponsables() as $depto) {
                $depto->declinarResponsable();
            }
        }

        // Deshabilitar
        $persona->deshabilitar();

        return redirect()
            ->back()
            ->with('success', 'Persona deshabilitada correctamente.');
    }

    public function enable($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->reactivar();

        return redirect()
            ->back()
            ->with('success', 'Persona reactivada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | BÚSQUEDA RÁPIDA
    |--------------------------------------------------------------------------
    */

    public function buscar(Request $request)
    {
        $q = trim($request->q);

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $personas = Persona::activas()
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
}
