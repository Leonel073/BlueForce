<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Departamento;
use App\Models\Cargo;
use App\Http\Requests\Admin\StorePersonaRequest;

class PersonaController extends Controller
{
    /**
     * Listar personas con separación por tipo (INTERNO/EXTERNO)
     * Cada tipo tiene paginación independiente
     */
    public function index(Request $request)
    {
        // Obtener pestaña activa (por defecto: internas)
        $tab = $request->get('tab', 'internas');
        
        // Búsqueda (independiente por tab)
        $search = trim($request->get('q', ''));

        // PERSONAS INTERNAS
        $queryInternas = Persona::where('tipo', 'INTERNO')
            ->activas();

        if ($search) {
            $queryInternas->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhere('correo', 'LIKE', "%{$search}%");
            });
        }

        $personasInternas = $queryInternas
            ->orderBy('nombre')
            ->paginate(15, ['*'], 'page_internas')
            ->appends(['tab' => 'internas', 'q' => $search]);

        // PERSONAS EXTERNAS
        $queryExternas = Persona::where('tipo', 'EXTERNO')
            ->activas();

        if ($search) {
            $queryExternas->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhere('correo', 'LIKE', "%{$search}%");
            });
        }

        $personasExternas = $queryExternas
            ->orderBy('nombre')
            ->paginate(15, ['*'], 'page_externas')
            ->appends(['tab' => 'externas', 'q' => $search]);

        // Contadores totales (sin búsqueda)
        $totalInternas = Persona::where('tipo', 'INTERNO')->activas()->count();
        $totalExternas = Persona::where('tipo', 'EXTERNO')->activas()->count();

        return view('admin.personas.index', compact(
            'personasInternas',
            'personasExternas',
            'totalInternas',
            'totalExternas',
            'tab',
            'search'
        ));
    }

    /**
     * Formulario para crear nueva persona
     */
    public function create()
    {
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();
        $departamentos = Departamento::orderBy('nombre')->get();
        
        return view('admin.personas.create', compact('cargos', 'departamentos'));
    }

    /**
     * Guardar nueva persona
     */
    public function store(StorePersonaRequest $request)
    {
        $validated = $request->validated();

        // Si tipo=INTERNO, la institución debe ser EPAB
        if ($validated['tipo'] === 'INTERNO') {
            $validated['institucion'] = 'EPAB';
        }

        $persona = Persona::create([
            'nombre'               => strtoupper(trim($validated['nombre'])),
            'ci'                   => trim($validated['ci']),
            'tipo'                 => $validated['tipo'],
            'telefono_celular'     => $validated['telefono_celular'] ?? null,
            'telefono_fijo'        => $validated['telefono_fijo'] ?? null,
            'correo'               => $validated['correo'] ?? null,
            'institucion'          => $validated['institucion'] ?? null,
            'idCargo'              => null,
            'idDepartamento'       => $validated['tipo'] === 'INTERNO' ? ($validated['idDepartamento'] ?? null) : null,
            'activo'               => true,
            'fecha_creacion'       => now(),
        ]);

        // Sincronizar cargos desde el formulario (nombres separados por coma)
        if ($validated['tipo'] === 'INTERNO' && !empty($validated['cargos_nombres'])) {
            $this->syncCargos($persona, $validated['cargos_nombres']);
        }

        return redirect()->route('admin.personas.index')
            ->with('success', 'Persona creada correctamente.');
    }

    /**
     * Formulario para editar persona
     */
    public function edit($id)
    {
        $persona = Persona::findOrFail($id);
        $departamentos = Departamento::orderBy('nombre')->get();
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

        return view('admin.personas.edit', compact('persona', 'departamentos', 'cargos'));
    }

    /**
     * Actualizar datos de persona
     */
    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $validated = $request->validate([
            'nombre'               => 'required|string|max:200|regex:/^[\pL\s]+$/u',
            'ci'                   => 'required|string|max:20|unique:PERSONA,ci,' . $persona->idPersona . ',idPersona',
            'correo'               => 'nullable|email|max:150',
            'telefono_celular'     => 'nullable|string|max:20',
            'telefono_fijo'        => 'nullable|string|max:20',
            'institucion'          => 'nullable|string|max:200',
            'tipo'                 => 'required|in:INTERNO,EXTERNO',
            'idDepartamento'       => 'nullable|exists:DEPARTAMENTO,idDepartamento',
            'cargos_nombres'       => 'nullable|string',
        ]);

        // Si es EXTERNO, no puede tener departamento
        if ($validated['tipo'] === 'EXTERNO') {
            $validated['idDepartamento'] = null;
        } else {
            $validated['institucion'] = 'EPAB';
        }

        $persona->update([
            'nombre'               => strtoupper(trim($validated['nombre'])),
            'ci'                   => trim($validated['ci']),
            'correo'               => $validated['correo'] ?? null,
            'telefono_celular'     => $validated['telefono_celular'] ?? null,
            'telefono_fijo'        => $validated['telefono_fijo'] ?? null,
            'institucion'          => $validated['institucion'] ?? null,
            'tipo'                 => $validated['tipo'],
            'idDepartamento'       => $validated['idDepartamento'] ?? null,
        ]);

        // Sincronizar cargos
        if ($validated['tipo'] === 'INTERNO' && !empty($validated['cargos_nombres'])) {
            $this->syncCargos($persona, $validated['cargos_nombres']);
        } else {
            // Si es EXTERNO, desactivar todos los cargos del pivote
            $persona->cargos()->detach();
        }

        return redirect()
            ->route('admin.personas.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    /**
     * Sincronizar cargos de una persona desde una cadena separada por comas.
     * Crea cargos nuevos si no existen (evitando duplicados por nombre).
     * Marca el primero como principal.
     */
    private function syncCargos(Persona $persona, string $cargosNombres): void
    {
        $nombres = array_filter(
            array_map('trim', explode(',', $cargosNombres)),
            fn($n) => $n !== ''
        );

        if (empty($nombres)) {
            return;
        }

        $cargosIds = [];

        foreach ($nombres as $nombre) {
            $nombreUpper = strtoupper($nombre);

            // Buscar o crear el cargo (evitar duplicados por nombre)
            $cargo = Cargo::firstOrCreate(
                ['nombre' => $nombreUpper],
                [
                    'activo' => true,
                    'nivel'  => 'Operativo',
                ]
            );

            $cargosIds[] = $cargo->idCargo;
        }

        // Desactivar cargos anteriores
        DB::table('PERSONA_CARGO')
            ->where('idPersona', $persona->idPersona)
            ->update(['activo' => false]);

        // Insertar nuevos cargos (el primero como principal)
        foreach ($cargosIds as $index => $cargoId) {
            DB::table('PERSONA_CARGO')->updateOrInsert(
                [
                    'idPersona' => $persona->idPersona,
                    'idCargo'   => $cargoId,
                ],
                [
                    'activo'           => true,
                    'principal'        => $index === 0,
                    'fecha_asignacion' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }

        // Actualizar el campo legacy idCargo con el principal
        $persona->update(['idCargo' => $cargosIds[0] ?? null]);
    }

    /**
     * Alternar estado activo/inactivo
     */
    public function toggle($id)
    {
        $persona = Persona::findOrFail($id);
        
        if ($persona->fecha_deshabilitacion) {
            $persona->reactivar();
            $mensaje = 'Persona reactivada correctamente.';
        } else {
            // Declinar responsabilidades antes de desactivar
            if ($persona->esResponsableActual()) {
                foreach ($persona->departamentosResponsables() as $depto) {
                    $depto->declinarResponsable();
                }
            }
            $persona->deshabilitar();
            $mensaje = 'Persona deshabilitada correctamente.';
        }

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Buscar personas (AJAX)
     * Retorna personas activas que coincidan con nombre o CI
     */
    public function buscar(Request $request)
    {
        $q = trim($request->q ?? '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $personas = Persona::activas()
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'LIKE', "%{$q}%")
                      ->orWhere('ci', 'LIKE', "%{$q}%");
            })
            ->with('cargos', 'departamento')
            ->limit(10)
            ->get([
                'idPersona',
                'nombre',
                'ci',
                'tipo',
                'idCargo',
                'idDepartamento',
                'institucion',
            ])
            ->map(function ($persona) {
                return [
                    'idPersona'          => $persona->idPersona,
                    'nombre'             => $persona->nombre,
                    'ci'                 => $persona->ci,
                    'tipo'               => $persona->tipo,
                    'cargo'              => $persona->cargos_nombres,
                    'departamento'       => $persona->departamento?->nombre ?? null,
                    'institucion'        => $persona->institucion ?? null,
                ];
            });

        return response()->json($personas);
    }

    /**
     * Buscar cargos existentes (AJAX) - para autocompletado
     */
    public function buscarCargos(Request $request)
    {
        $q = trim($request->q ?? '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $cargos = Cargo::where('activo', true)
            ->where('nombre', 'LIKE', "%{$q}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get(['idCargo', 'nombre']);

        return response()->json($cargos);
    }
}
