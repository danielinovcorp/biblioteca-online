<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LivrosExport;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AlertaDisponibilidade;

class LivroController extends Controller
{
	public function index(Request $request)
	{
		$query = Livro::with(['editora', 'autores', 'requisicoes.user']);

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

	public function store(Request $request)
	{
		$validated = $request->validate([
			'isbn' => 'required|string|unique:livros,isbn',
			'nome' => 'required|string|max:255',
			'editora_id' => 'required|exists:editoras,id',
			'autores' => 'required|array',
			'autores.*' => 'exists:autors,id',
			'preco' => 'required|numeric|min:0',
			'imagem_capa' => 'nullable|image',
			'bibliografia' => 'nullable|string',
		]);

		if ($request->hasFile('imagem_capa')) {
			$path = $request->file('imagem_capa')->store('livros', 'public');
			$validated['imagem_capa'] = '/storage/' . $path;
		}

		$livro = Livro::create($validated);
		$livro->autores()->sync($validated['autores']);

		return redirect()->route('livros.index')->with('success', 'Livro adicionado com sucesso!');
	}

	public function edit(Livro $livro)
	{
		$editoras = Editora::all();
		$autores = Autor::all();
		$livro->load('autores');

		return view('livros.edit', compact('livro', 'editoras', 'autores'));
	}

	public function update(Request $request, Livro $livro)
	{
		$validated = $request->validate([
			'isbn' => 'required|string|unique:livros,isbn,' . $livro->id,
			'nome' => 'required|string|max:255',
			'editora_id' => 'required|exists:editoras,id',
			'autores' => 'required|array',
			'autores.*' => 'exists:autors,id',
			'preco' => 'required|numeric|min:0',
			'imagem_capa' => 'nullable|image',
			'bibliografia' => 'nullable|string',
		]);

		if ($request->hasFile('imagem_capa')) {
			$path = $request->file('imagem_capa')->store('livros', 'public');
			$validated['imagem_capa'] = '/storage/' . $path;
		}

		$livro->update($validated);
		$livro->autores()->sync($validated['autores']);

		return redirect()->route('livros.index')->with('success', 'Livro atualizado com sucesso!');
	}

	public function destroy(Livro $livro)
	{
		$livro->autores()->detach();
		$livro->delete();

		return redirect()->route('livros.index')->with('success', 'Livro excluído com sucesso!');
	}

	public function show(Livro $livro)
	{
		// mantém teus eager-loads
		$livro->load(['editora', 'autores', 'requisicoes.user']);

		// pega as requisições do livro
		$requisicoes = $livro->requisicoes()->latest()->get();

		// 👇 NOVO: livros relacionados (carrega editora para o card)
		$relacionados = $livro->related(8)->load(['editora']);

		return view('livros.show', compact('livro', 'requisicoes', 'relacionados'));
	}

	public function alertarDisponibilidade(Request $request)
	{
		$request->validate([
			'livro_id' => 'required|exists:livros,id',
		]);

		$livroId = (int) $request->input('livro_id');
		$userId  = auth()->id();

		// Evitar duplicado
		$jaExiste = AlertaDisponibilidade::where('user_id', $userId)
			->where('livro_id', $livroId)
			->exists();

		if ($jaExiste) {
			return back()->with('info', 'Você já será avisado quando este livro ficar disponível.');
		}

		AlertaDisponibilidade::create([
			'user_id' => $userId,
			'livro_id' => $livroId,
		]);

		return back()->with('success', 'Tudo certo! Vamos avisar por e-mail quando o livro ficar disponível.');
	}

	public function relacionadosJson(Livro $livro)
	{
		$rel = $livro->related(8)->load('editora');

		return response()->json(
			$rel->map(function ($x) {
				return [
					'id'           => $x->id,
					'nome'         => $x->nome,
					'isbn'         => $x->isbn,
					'imagem_capa'  => $x->imagem_capa,
					'editora'      => optional($x->editora)->nome,
					'autores'      => $x->autores()->orderBy('nome')->get(['id', 'nome']),
					'preco'        => $x->preco,
					'bibliografia' => $x->bibliografia,
					'disponivel'   => (bool) $x->disponivel,
					'url'          => null,
				];
			})
		);
	}
}
