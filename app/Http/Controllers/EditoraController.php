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
}
