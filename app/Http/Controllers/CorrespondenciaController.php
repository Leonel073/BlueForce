<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Correspondencia;
use App\Models\TipoDocumento;
use App\Models\EstadoDocumento;
use App\Models\NivelUrgencia;
use App\Models\Persona;

class CorrespondenciaController extends Controller
{
    // 📄 LISTADO (GESTIÓN)
    public function index(Request $request)
    {
        $query = Correspondencia::with([
            'tipo',
            'estado',
            'urgencia',
            'remitente'
        ]);

        // 🔎 FILTROS
        if ($request->buscar) {
            $query->where('asunto', 'like', '%' . $request->buscar . '%');
        }

        if ($request->estado) {
            $query->where('idEstado', $request->estado);
        }

        $docs = $query->orderBy('fecha', 'desc')->get();
        $estados = EstadoDocumento::all();

        return view('user.correspondencia.index', compact('docs', 'estados'));
    }

    // ➕ FORMULARIO CREAR
    public function create()
    {
        return view('user.correspondencia.create', [
            'tipos' => TipoDocumento::all(),
            'estados' => EstadoDocumento::all(),
            'urgencias' => NivelUrgencia::all(),
            'personas' => Persona::where('activo', true)->get(),
        ]);
    }

    // 💾 GUARDAR DOCUMENTO
    public function store(Request $request)
    {
        $request->validate([
            'cite' => 'required|string|max:100',
            'asunto' => 'required|string',
            'idTipoDocumento' => 'required|integer',
            'idEstado' => 'required|integer',
            'idUrgencia' => 'required|integer',
            'idRemitente' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {

            $doc = Correspondencia::create([
                'cite' => $request->cite,
                'asunto' => $request->asunto,
                'fecha' => now(),
                'idTipoDocumento' => $request->idTipoDocumento,
                'idEstado' => $request->idEstado,
                'idUrgencia' => $request->idUrgencia,
                'idUsuario' => 1, // 🔁 luego: auth()->id()
                'idRemitente' => $request->idRemitente,
            ]);

            DB::commit();

           return redirect()->route('documentos')
    ->with('success', 'Documento registrado correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Error al guardar el documento');
        }
    }

    // 👁️ VER DETALLE (🔥 IMPORTANTE)
    public function show($id)
    {
        $doc = Correspondencia::with([
            'tipo',
            'estado',
            'urgencia',
            'remitente'
        ])->findOrFail($id);

        return view('user.correspondencia.show', compact('doc'));
    }

    // ✏️ FORM EDITAR
    public function edit($id)
    {
        $doc = Correspondencia::findOrFail($id);

        return view('correspondencia.edit', [
            'doc' => $doc,
            'tipos' => TipoDocumento::all(),
            'estados' => EstadoDocumento::all(),
            'urgencias' => NivelUrgencia::all(),
            'personas' => Persona::where('activo', true)->get(),
        ]);
    }

    // 🔄 ACTUALIZAR
    public function update(Request $request, $id)
    {
        $request->validate([
            'cite' => 'required',
            'asunto' => 'required',
        ]);

        $doc = Correspondencia::findOrFail($id);

        $doc->update([
            'cite' => $request->cite,
            'asunto' => $request->asunto,
            'idTipoDocumento' => $request->idTipoDocumento,
            'idEstado' => $request->idEstado,
            'idUrgencia' => $request->idUrgencia,
            'idRemitente' => $request->idRemitente,
        ]);

        return redirect()->route('documentos')
            ->with('success', 'Documento actualizado');
    }
}