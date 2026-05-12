<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Persona;

class DepartamentoController extends Controller
{
    // creacion de departamento
public function index(Request $request)
{
    $query = Departamento::with('encargado');

    /*
    |--------------------------------------------------------------------------
    | BUSCADOR
    |--------------------------------------------------------------------------
    */

    if($request->filled('buscar'))
    {
        $query->where(
            'nombre',
            'LIKE',
            '%' . $request->buscar . '%'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO ESTADO
    |--------------------------------------------------------------------------
    */

    if($request->estado !== null
        && $request->estado !== '')
    {
        $query->where(
            'activo',
            $request->estado
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ORDEN
    |--------------------------------------------------------------------------
    */

    if($request->orden == 'az')
    {
        $query->orderBy('nombre', 'asc');
    }
    elseif($request->orden == 'za')
    {
        $query->orderBy('nombre', 'desc');
    }
    else
    {
        $query->orderBy(
            'idDepartamento',
            'desc'
        );
    }

    $departamentos = $query->get();

    return view(
        'admin.departamentos.index',
        compact('departamentos')
    );
}
public function create()
{
    return view(
        'admin.departamentos.create'
    );
}
public function store(Request $request)
{
    $validated = $request->validate([

        'nombre' =>
            'required|string|max:150',

        'descripcion' =>
            'nullable|string|max:500',

        'idPersonaEncargada' =>
            'nullable|exists:PERSONA,idPersona',

    ]);

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR SI YA ES ENCARGADO
    |--------------------------------------------------------------------------
    */

    if($validated['idPersonaEncargada'] ?? null)
    {
        $existe = Departamento::where(
            'idPersonaEncargada',
            $validated['idPersonaEncargada']
        )->exists();

        if($existe)
        {
            return back()
                ->withInput()
                ->withErrors([

                    'idPersonaEncargada' =>
                        'La persona ya pertenece a otro departamento.'

                ]);
        }
    }

    Departamento::create([

        'nombre' =>
            $validated['nombre'],

        'descripcion' =>
            $validated['descripcion'] ?? null,

        'idPersonaEncargada' =>
            $validated['idPersonaEncargada'] ?? null,

        'activo' => true,

    ]);

    return redirect()
        ->route('admin.departamentos.index')
        ->with(
            'success',
            'Departamento creado correctamente.'
        );
}
public function edit($id)
{
    $departamento = Departamento::findOrFail($id);

    $personas = Persona::where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view(
        'admin.departamentos.edit',
        compact(
            'departamento',
            'personas'
        )
    );
}
public function update(Request $request, $id)
{
    $departamento = Departamento::findOrFail($id);

    $validated = $request->validate([

        'nombre' =>
            'required|string|max:150',

        'descripcion' =>
            'nullable|string|max:500',

        'idPersonaEncargada' =>
            'nullable|exists:PERSONA,idPersona',

        'activo' =>
            'required|boolean',

    ]);

    $departamento->update($validated);

    return redirect()
        ->route('admin.departamentos.index')
        ->with(
            'success',
            'Departamento actualizado correctamente.'
        );
}
public function toggle($id)
{
    $departamento = Departamento::findOrFail($id);

    $departamento->activo =
        !$departamento->activo;

    $departamento->save();

    return redirect()
        ->back()
        ->with(
            'success',
            'Estado del departamento actualizado.'
        );
}
public function buscarPersonas(Request $request)
{
    $q = trim($request->q);

    $personas = Persona::where(function($query) use ($q){

            $query->where('nombre', 'LIKE', "%{$q}%")
                  ->orWhere('ci', 'LIKE', "%{$q}%");

        })
        ->limit(10)
        ->get();

    $resultado = $personas->map(function($persona){

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SI YA ES ENCARGADO
        |--------------------------------------------------------------------------
        */

        $departamento = Departamento::where(
            'idPersonaEncargada',
            $persona->idPersona
        )->first();

        return [

            'idPersona' =>
                $persona->idPersona,

            'nombre' =>
                $persona->nombre,

            'ci' =>
                $persona->ci,

            'ocupado' =>
                $departamento ? true : false,

            'departamento' =>
                $departamento?->nombre,

        ];

    });

    return response()->json($resultado);
}
}
