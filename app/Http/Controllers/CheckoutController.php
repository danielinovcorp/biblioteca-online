<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
	public function showMorada(Request $request)
	{
		$carrinhoId = (int) $request->query('c');

		$query = Carrinho::where('user_id', Auth::id())->with('livros');

		if ($carrinhoId) {
			$query->whereKey($carrinhoId);
		}

		// fallback: o mais recente do utilizador
		$carrinho = $query->orderByDesc('updated_at')->first();

		if (!$carrinho || $carrinho->livros->isEmpty()) {
			return redirect()->route('carrinho.index')->with('error', 'O carrinho está vazio.');
		}

		return view('checkout.morada', [
			'carrinho' => $carrinho,
			'total'    => $carrinho->total(),
		]);
	}


	public function storeMorada(Request $request)
	{
		$data = $request->validate([
			'morada'        => 'required|string|max:255',
			'cidade'        => 'nullable|string|max:120',
			'codigo_postal' => 'nullable|string|max:20',
			'telefone'      => 'nullable|string|max:30',
			'carrinho_id'   => 'required|integer',
		]);

		$carrinho = Carrinho::whereKey($data['carrinho_id'])
			->where('user_id', Auth::id())
			->with('livros')
			->first();

		if (!$carrinho || $carrinho->livros->isEmpty()) {
			return redirect()->route('carrinho.index')->with('error', 'O carrinho está vazio.');
		}

		try {
			// 1) cria encomenda SEM limpar o carrinho ainda
			/** @var Encomenda $encomenda */
			$encomenda = $carrinho->criarEncomenda($data, false);

			// 2) cria sessão Stripe
			\Stripe\Stripe::setApiKey(config('services.stripe.secret'));

			$lineItems = [];
			foreach ($encomenda->livros as $livro) {
				$name  = $livro->pivot->titulo_livro ?: ($livro->nome ?? $livro->titulo ?? 'Livro');
				$price = (int) round(((float)$livro->pivot->preco_unitario) * 100);
				$lineItems[] = [
					'price_data' => [
						'currency' => 'eur',
						'product_data' => ['name' => $name],
						'unit_amount' => max(0, $price),
					],
					'quantity' => (int) $livro->pivot->quantidade,
				];
			}

			$session = \Stripe\Checkout\Session::create([
				'mode' => 'payment',
				'payment_method_types' => ['card'],
				'line_items' => $lineItems,
				'success_url' => route('checkout.sucesso') . '?session_id={CHECKOUT_SESSION_ID}',
				'cancel_url'  => route('checkout.cancelado'),
				'customer_email' => $encomenda->user->email ?? null,
				'metadata' => [
					'encomenda_id' => (string) $encomenda->id,
					'user_id'      => (string) $encomenda->user_id,
				],
			]);

			$encomenda->stripe_session_id = $session->id;
			$encomenda->save();

			// 3) AGORA sim limpa o carrinho
			$carrinho->limpar();

			return redirect()->away($session->url);
		} catch (\Stripe\Exception\ApiErrorException $e) {
			\Log::error('Stripe API error', [
				'message' => $e->getMessage(),
				'type'    => optional($e->getError())->type,
				'code'    => optional($e->getError())->code,
				'param'   => optional($e->getError())->param,
			]);
			return back()->with('error', 'Stripe: ' . $e->getMessage());
		} catch (\Throwable $e) {
			report($e);
			return back()->with('error', 'Falha ao iniciar o pagamento (erro inesperado).');
		}
	}

	public function sucesso(Request $request)
	{
		// Opcionalmente podemos verificar o estado de pagamento consultando a Session:
		$sessionId = $request->query('session_id');

		if ($sessionId) {
			try {
				\Stripe\Stripe::setApiKey(config('services.stripe.secret'));
				$session = \Stripe\Checkout\Session::retrieve($sessionId);
				$encomenda = Encomenda::where('stripe_session_id', $sessionId)->first();

				// O estado definitivo é definido pelo webhook, mas podemos sinalizar ao user
				if ($encomenda && $encomenda->estado === 'paga') {
					return view('checkout.sucesso', ['encomenda' => $encomenda, 'status' => 'paga']);
				}
			} catch (\Throwable $e) {
				report($e);
			}
		}

		return view('checkout.sucesso', ['encomenda' => null, 'status' => 'pendente']);
	}

	public function cancelado(Request $request)
	{
		return view('checkout.cancelado');
	}
}
