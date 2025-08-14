<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
	/**
	 * Listagem de reviews com filtro por status e busca por livro/cidadão.
	 */
	public function index(Request $request)
	{
		$status = $request->get('status'); // suspenso|ativo|recusado
		$term   = trim((string) $request->get('q')); // busca livre

		$q = Review::with(['user', 'livro'])->latest();

		if (in_array($status, ['suspenso', 'ativo', 'recusado'])) {
			$q->where('status', $status);
		}

		if ($term !== '') {
			$q->where(function ($w) use ($term) {
				$w->whereHas('livro', fn($l) => $l->where('nome', 'like', "%{$term}%"))
					->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
			});
		}

		$reviews = $q->paginate(15)->withQueryString();

		return view('admin.reviews.index', compact('reviews', 'status', 'term'));
	}

	/**
	 * Detalhe do review.
	 */
	public function show(Review $review)
	{
		$review->load(['user', 'livro', 'requisicao']);
		return view('admin.reviews.show', compact('review'));
	}

	/**
	 * Atualiza o status (Ativar ou Recusar) e salva justificativa se recusado.
	 * (Emails serão adicionados no próximo passo para não quebrar agora.)
	 */
	public function updateStatus(Request $request, Review $review)
	{
		// Validação: só permite 'ativo' ou 'recusado'
		$data = $request->validate([
			'status'        => ['required', 'in:ativo,recusado'],
			'justificativa'  => ['nullable', 'string', 'max:1000'],
		]);

		// Se recusado, obrigar justificativa
		if ($data['status'] === 'recusado') {
			$request->validate([
				'justificativa' => ['required', 'string', 'max:1000'],
			]);
		}

		// Atualiza e salva
		$review->status = $data['status'];
		$review->justificativa = $data['status'] === 'recusado' ? $data['justificativa'] : null;
		$review->save();

		// Envia e-mail ao cidadão (sem queue, envio imediato)
		try {
			\Illuminate\Support\Facades\Mail::to($review->user->email)
				->send(new \App\Mail\ReviewStatusChangedMail($review));
		} catch (\Throwable $e) {
			// Loga, mas não quebra o fluxo do admin
			\Illuminate\Support\Facades\Log::error('Falha ao enviar e-mail de status da review', [
				'review_id' => $review->id,
				'erro'      => $e->getMessage(),
			]);
		}

		// Responde conforme o tipo de request
		if ($request->wantsJson()) {
			return response()->json(['message' => 'Estado atualizado e e-mail enviado ao cidadão.']);
		}

		return back()->with('success', 'Estado atualizado e e-mail enviado ao cidadão.');
	}
}
