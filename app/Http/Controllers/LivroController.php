<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LivrosExport;

class LivroController extends Controller
{
	public function index(Request $request)
	{
		$query = Livro::with(['editora', 'autores']);

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('nome', 'like', "%{$search}%")
				->orWhere('isbn', 'like', "%{$search}%");
			});
		}

		if ($request->filled('editora_id')) {
			$query->where('editora_id', $request->editora_id);
		}

		if ($request->filled('autor_id')) {
			$query->whereHas('autores', function ($q) use ($request) {
				$q->where('autor_id', $request->autor_id);
			});
		}

		$sortField = $request->get('sort', 'nome');
		$sortDirection = $request->get('direction', 'asc');

		$livros = $query->orderBy($sortField, $sortDirection)->get();
		$editoras = Editora::all();
		$autores = Autor::all();

		return view('livros.index', compact('livros', 'editoras', 'autores', 'sortField', 'sortDirection'));
	}

    public function export()
    {
        return Excel::download(new LivrosExport, 'livros.xlsx');
    }
}

