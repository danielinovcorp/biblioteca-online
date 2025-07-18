<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Autor;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AutoresExport;
class AutorController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'nome');
        $sortDirection = $request->input('direction', 'asc');

        $query = Autor::query();

        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->input('q') . '%');
        }

        $autores = $query->orderBy($sortField, $sortDirection)->get();

        return view('autores.index', compact('autores', 'sortField', 'sortDirection'));
    }

    public function export(Request $request)
    {
        $ids = explode(',', $request->input('ids'));
        $q = $request->input('q');
        $sortField = $request->input('sort', 'nome');

        $sortDirection = strtolower($request->input('direction', 'asc'));
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $query = Autor::query();

        if (!empty($ids) && $ids[0] !== '') {
            $query->whereIn('id', $ids);
        } elseif ($q) {
            $query->where('nome', 'like', '%' . $q . '%');
        }

        $autores = $query->orderBy($sortField, $sortDirection)->get();

        return Excel::download(new AutoresExport($autores->pluck('id')->toArray()), 'autores.xlsx');
    }

}
