<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Review;
use App\Models\Requisicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewCriadoAdminMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
	// GET /livros/{livro}/reviews  -> JSON com reviews ativas
	public function listAtivos($livroId)
	{
		// Busca o livro pelo ID
		$livroModel = Livro::find($livroId);

		// Se não encontrar, retorna JSON vazio (ou mensagem)
		if (!$livroModel) {
			return response()->json([], 200);
		}

		// Busca apenas reviews ativas
		$reviews = $livroModel->reviews()
			->where('status', 'ativo')
			->with(['user:id,name'])
			->latest()
			->get(['id', 'user_id', 'rating', 'comentario', 'created_at']);

		return response()->json($reviews);
	}


	// POST /requisicoes/{requisicao}/reviews  -> cria review "suspenso"
	public function store(Request $request, Requisicao $requisicao)
	{
		$user = Auth::user();

		// 1) Autorização básica
		if (!$user || $requisicao->user_id !== $user->id) {
			return $this->respond($request, 403, 'Você não pode avaliar esta requisição.');
		}
		if ($requisicao->status !== 'devolvida') {
			return $this->respond($request, 422, 'Só é possível avaliar após devolver o livro.');
		}

		// 2) Validação
		$data = $request->validate([
			'rating'     => ['required', 'integer', 'min:1', 'max:5'],
			'comentario' => ['nullable', 'string', 'max:2000'],
		]);

		// 3) Evitar duplicidade
		$jaTem = Review::where('requisicao_id', $requisicao->id)
			->where('user_id', $user->id)
			->exists();
		if ($jaTem) {
			return $this->respond($request, 409, 'Você já enviou uma review desta requisição.');
		}

		// 4) Criar review (status "suspenso")
		$review = Review::create([
			'livro_id'      => $requisicao->livro_id,
			'requisicao_id' => $requisicao->id,
			'user_id'       => $user->id,
			'rating'        => $data['rating'],
			'comentario'    => $data['comentario'] ?? null,
			'status'        => 'suspenso',
		]);

		// 5) Enviar e-mail aos admins (forçar síncrono)
		try {
			$emails = \App\Models\User::where('role', 'admin')   // <- IMPORTANTE: role (não 'tipo')
				->whereNotNull('email')
				->pluck('email')
				->filter()
				->unique()
				->values()
				->all();

			\Log::info('ReviewCriadoAdminMail: destinatários (envio síncrono)', [
				'review_id'    => $review->id,
				'qtd'          => count($emails),
				'destinatarios' => $emails,
				'mailer'       => config('mail.default'),
				'from'         => [config('mail.from.address'), config('mail.from.name')],
			]);

			if (empty($emails)) {
				\Log::warning('ReviewCriadoAdminMail: nenhum admin com e-mail.');
			} else {
				// ENVIO DIRETO (sem fila)
				\Mail::to($emails)->send(new \App\Mail\ReviewCriadoAdminMail($review));
				\Log::info('ReviewCriadoAdminMail: enviado síncrono OK', ['review_id' => $review->id]);
			}
		} catch (\Throwable $e) {
			\Log::error('ReviewCriadoAdminMail: falha no envio síncrono', [
				'review_id' => $review->id,
				'error'     => $e->getMessage(),
			]);
		}

		return $this->respond($request, 200, 'Review enviada e aguardando moderação do admin.');
	}

	private function respond(Request $request, int $status, string $message)
	{
		if ($request->wantsJson()) {
			return response()->json(['message' => $message], $status);
		}
		$flashType = $status >= 400 ? 'error' : 'success';
		return back()->with($flashType, $message);
	}
}
