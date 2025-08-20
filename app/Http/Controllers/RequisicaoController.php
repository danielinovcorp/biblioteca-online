<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\RequisicaoConfirmada;
use Illuminate\Support\Facades\Mail;
use App\Models\AlertaDisponibilidade;
use App\Mail\LivroDisponivelMail;

class RequisicaoController extends Controller
{
	public function index()
	{
		$user = Auth::user();

		if ($user->isAdmin()) {
			$requisicoes = Requisicao::with('livro', 'user')->latest()->get();

			$requisicoesAtivas = Requisicao::where('status', 'ativa')->count();

			$requisicoesUltimos30 = Requisicao::where('created_at', '>=', now()->subDays(30))->count();

			$livrosEntreguesHoje = Requisicao::where('status', 'devolvida')
				->whereDate('data_fim_real', now()->toDateString())
				->count();
		} else {
			$requisicoes = $user->requisicoes()->with('livro')->latest()->get();

			$requisicoesAtivas = $user->requisicoes()->where('status', 'ativa')->count();

			$requisicoesUltimos30 = $user->requisicoes()
				->where('created_at', '>=', now()->subDays(30))
				->count();

			$livrosEntreguesHoje = $user->requisicoes()
				->where('status', 'devolvida')
				->whereDate('data_fim_real', now()->toDateString())
				->count();
		}

		foreach ($requisicoes as $req) {
			if ($req->status === 'devolvida' && $req->data_fim_real && $req->data_inicio) {
				$inicio = \Carbon\Carbon::parse($req->data_inicio)->startOfDay();
				$fim = \Carbon\Carbon::parse($req->data_fim_real)->startOfDay();
				$req->dias_decorridos = $inicio->diffInDays($fim);
			} else {
				$req->dias_decorridos = null;
			}
		}



		return view('requisicoes.index', compact(
			'requisicoes',
			'requisicoesAtivas',
			'requisicoesUltimos30',
			'livrosEntreguesHoje'
		));
	}


	public function create(Request $request)
	{
		$livrosDisponiveis = Livro::whereDoesntHave('requisicoes', function ($query) {
			$query->where('status', 'ativa');
		})->get();

		// Captura o livro_id da query string, se existir
		$livroSelecionadoId = $request->livro_id;

		return view('requisicoes.create', compact('livrosDisponiveis', 'livroSelecionadoId'));
	}

	public function store(Request $request)
	{
		if (!Auth::check()) {
			return redirect()->route('login')->with('error', 'É necessário fazer login para requisitar um livro.');
		}

		$user = Auth::user();

		// Verifica limite de 3 requisições ativas
		$ativas = $user->requisicoes()->where('status', 'ativa')->count();
		if ($ativas >= 3) {
			return redirect()->route('requisicoes.index')->with('error', 'Você já tem 3 livros requisitados.');
		}

		$request->validate([
			'livro_id' => 'required|exists:livros,id',
			'foto_cidadao' => 'required|image|max:2048',
		]);

		try {
			DB::beginTransaction();

			// Verificação segura dentro da transação
			$livroOcupado = Requisicao::where('livro_id', $request->livro_id)
				->where('status', 'ativa')
				->lockForUpdate()
				->exists();

			if ($livroOcupado) {
				DB::rollBack();
				return back()->with('error', 'Este livro já está requisitado por outro cidadão.');
			}

			// Upload da foto
			$path = $request->file('foto_cidadao')->store('fotos_cidadao', 'public');

			// Número sequencial seguro
			$numero = 'REQ-' . str_pad(Requisicao::count() + 1, 4, '0', STR_PAD_LEFT);

			// Criação da requisição
			$requisicao = Requisicao::create([
				'user_id' => $user->id,
				'livro_id' => $request->livro_id,
				'numero' => $numero,
				'foto_cidadao' => $path,
				'status' => 'ativa',
				'data_inicio' => now()->toDateString(),
				'data_fim_prevista' => now()->addDays(5)->toDateString(),
			]);

			// Enviar e-mail ao cidadão
			Mail::to($user->email)->send(new RequisicaoConfirmada($requisicao));

			// Enviar e-mail aos Admins
			$admins = \App\Models\User::where('tipo', 'admin')->get();
			foreach ($admins as $admin) {
				Mail::to($admin->email)->send(new RequisicaoConfirmada($requisicao));
			}

			DB::commit();

			return redirect()->route('requisicoes.index')->with('success', 'Requisição criada com sucesso.');
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'Erro ao criar requisição. Tente novamente.');
		}
	}

	public function confirmarDevolucao(Requisicao $requisicao)
	{
		DB::beginTransaction();

		try {
			// 1) Atualiza a requisição como devolvida
			$requisicao->update([
				'status'        => 'devolvida',
				'data_fim_real' => now(),
			]);

			// 2) Descobre se, com essa devolução, o livro ficou disponível (sem coluna)
			$livro = $requisicao->livro()->first(); // recarrega
			$ficouDisponivel = $livro && !$livro->requisicoes()
				->where('status', 'ativa')
				->exists();

			// 3) Commit antes de mandar e-mails
			DB::commit();

			// 4) Se ficou disponível, busca alertas e envia e-mails, depois apaga alertas
			$enviados = 0;
			if ($ficouDisponivel) {
				$alertas = AlertaDisponibilidade::with('user:id,email')
					->where('livro_id', $livro->id)
					->get();

				foreach ($alertas as $alerta) {
					if ($alerta->user && $alerta->user->email) {
						// Mailable carrega editora via loadMissing no construtor
						Mail::to($alerta->user->email)->send(new LivroDisponivelMail($livro));
						$enviados++;
					}
					$alerta->delete();
				}
			}

			$msg = $ficouDisponivel
				? "Devolução confirmada. {$enviados} alerta(s) notificado(s) por e-mail."
				: 'Devolução confirmada.';

			return redirect()->route('requisicoes.index')->with('success', $msg);
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->with('error', 'Não foi possível confirmar a devolução. Tente novamente.');
		}
	}


	public function minhasDevolvidasPorLivro(\App\Models\Livro $livro)
	{
		$user = Auth::user();
		if (!$user) {
			return response()->json([], 401);
		}

		$requisicoes = Requisicao::query()
			->where('user_id', $user->id)
			->where('livro_id', $livro->id)
			->where('status', 'devolvida')
			->orderByDesc('data_fim_real')
			->get(['id', 'numero', 'data_fim_real']);

		return response()->json($requisicoes, 200);
	}
}
