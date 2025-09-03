<?php

namespace App\View\Components;

use App\Models\Carrinho;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class CartIndicator extends Component
{
	public int $count = 0;

	public function __construct()
	{
		if (Auth::check()) {
			$carrinho = \App\Models\Carrinho::where('user_id', Auth::id())
				->orderByDesc('updated_at')
				->first();

			if ($carrinho) {
				$this->count = (int) $carrinho->livros()->sum('carrinho_livro.quantidade');
			}
		}
	}

	public function render(): View|Closure|string
	{
		return view('components.cart-indicator');
	}
}
