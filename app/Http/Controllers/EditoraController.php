<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Editora;
use App\Exports\EditorasExport;
use Maatwebsite\Excel\Facades\Excel;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'nome');
        $sortDirection = strtolower($request->input('direction', 'asc'));
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $query = Editora::query();

        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->input('q') . '%');
        }

        $editoras = $query->orderBy($sortField, $sortDirection)->get();

        return view('editoras.index', compact('editoras', 'sortField', 'sortDirection'));
    }

    public function export(Request $request)
    {
        $ids = explode(',', $request->input('ids'));
        $q = $request->input('q');
        $sortField = $request->input('sort', 'nome');
        $sortDirection = strtolower($request->input('direction', 'asc'));
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $query = Editora::query();

        if (!empty($ids) && $ids[0] !== '') {
            $query->whereIn('id', $ids);
        } elseif ($q) {
            $query->where('nome', 'like', '%' . $q . '%');
        }

        $editoras = $query->orderBy($sortField, $sortDirection)->get();

        return Excel::download(new EditorasExport($editoras->pluck('id')->toArray()), 'editoras.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        $editora = new Editora();
        $editora->nome = $request->nome;

        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('editoras', 'public');
            $editora->logotipo = 'storage/' . $path;
        }

        $editora->save();

        return redirect()->route('editoras.index')->with('success', 'Editora criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $editora = Editora::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        $editora->nome = $request->nome;

        if ($request->hasFile('logotipo')) {
            // Apaga o antigo
            if ($editora->logotipo && file_exists(public_path($editora->logotipo))) {
                unlink(public_path($editora->logotipo));
            }

            $path = $request->file('logotipo')->store('editoras', 'public');
            $editora->logotipo = 'storage/' . $path;
        }

        $editora->save();

        return redirect()->route('editoras.index')->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $editora = Editora::findOrFail($id);

        if ($editora->logotipo && file_exists(public_path($editora->logotipo))) {
            unlink(public_path($editora->logotipo));
        }

        $editora->delete();

        return redirect()->route('editoras.index')->with('success', 'Editora excluída com sucesso!');
    }
}
