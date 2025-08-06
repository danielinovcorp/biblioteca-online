<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleBooksService;
use Illuminate\Support\Facades\DB;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LivroImportController extends Controller
{
	protected GoogleBooksService $googleBooks;

	public function __construct(GoogleBooksService $googleBooks)
	{
		$this->googleBooks = $googleBooks;
	}

	// Página com formulário de busca
	public function index()
	{
		return view('livros.importar');
	}

	// Recebe o termo e retorna os resultados
	public function pesquisar(Request $request)
	{
		$request->validate([
			'termo' => 'required|string|min:3',
		]);

		$termo = $request->input('termo');
		$startIndex = $request->input('startIndex', 0); // valor padrão 0
		$maxResults = 10; // padrão da API

		$livros = $this->googleBooks->pesquisar($termo, $startIndex);

		return view('livros.importar', compact('livros', 'termo', 'startIndex', 'maxResults'));
	}


	public function salvar(Request $request)
	{
		$dados = json_decode(base64_decode($request->input('dados')), true);

		DB::beginTransaction();

		try {
			// Validação básica
			if (Livro::where('isbn', $dados['isbn'])->exists()) {
				return redirect()->back()->with('error', 'Livro com ISBN já existe!');
			}

			// Verifica se editora já existe
			$editora = Editora::firstOrCreate([
				'nome' => trim($dados['editora'])
			]);

			// Salva imagem localmente
			$imagemLocal = $dados['imagem_capa'] ? $this->salvarImagemLocalmente($dados['imagem_capa']) : null;

			// Cria o livro
			$livro = Livro::create([
				'isbn' => $dados['isbn'],
				'nome' => $dados['nome'],
				'editora_id' => $editora->id,
				'bibliografia' => $dados['bibliografia'],
				'imagem_capa' => $imagemLocal,
				'preco' => $dados['preco']
			]);

			// Autores: evitar duplicações
			foreach ($dados['autores'] as $nomeAutor) {
				$autor = Autor::firstOrCreate(['nome' => trim($nomeAutor)]);
				$livro->autores()->attach($autor->id);
			}

			DB::commit();

			return redirect()->back()->with('success', 'Livro importado com sucesso!');
		} catch (\Exception $e) {
			DB::rollBack();
			return redirect()->back()->with('error', 'Erro ao importar: ' . $e->getMessage());
		}
	}

	private function salvarImagemLocalmente($url)
	{
		try {
			$conteudo = @file_get_contents($url);
			if (!$conteudo) return null;

			$nomeArquivo = 'livros/' . Str::uuid() . '.jpg';

			Storage::disk('public')->put($nomeArquivo, $conteudo);

			return 'storage/' . $nomeArquivo;
		} catch (\Exception $e) {
			return null;
		}
	}
}
