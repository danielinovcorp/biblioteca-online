<x-guest-layout>
	<x-slot name="heading">🛒 Carrinho</x-slot>


	<div class="bg-white max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		{{-- FLASH AUTO-DESAPARECENDO (Alpine já vem no Jetstream) --}}
		@foreach (['success' => 'alert-success', 'error' => 'alert-error'] as $key => $style)
		@if (session($key))
		<div x-data="{ show: true }"
			x-show="show"
			x-transition.opacity.duration.300
			x-init="setTimeout(() => show = false, 3000)"
			class="alert {{ $style }} shadow mb-6 rounded-xl">
			<span>{{ session($key) }}</span>
		</div>
		@endif
		@endforeach

		@if($itens->isEmpty())
		<div class="card bg-white border border-gray-100 rounded-2xl shadow-lg">
			<div class="card-body">
				<h2 class="card-title">O carrinho está vazio.</h2>
				<p>Adiciona livros a partir da listagem ou do detalhe.</p>
				<a href="{{ route('livros.index') }}" class="btn btn-primary mt-2">Ver livros</a>
			</div>
		</div>
		@else
		{{-- BOX MAIOR --}}
		<div class="overflow-x-auto bg-gray-200 rounded-2xl shadow-lg border border-gray-100">
			<table class="table">
				<thead>
					<tr class="text-gray-600">
						<th>Livro</th>
						<th class="text-right">Preço</th>
						<th class="text-center">Qtd</th>
						<th class="text-right">Subtotal</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach($itens as $livro)
					@php
					$preco = (float) ($livro->preco ?? 0);
					$qtd = (int) $livro->pivot->quantidade;
					$sub = $preco * $qtd;
					@endphp
					<tr class="align-top">
						<td class="py-5">
							<div class="flex items-center gap-4">
								<img src="{{ $livro->imagem_capa }}" alt="{{ $livro->nome ?? $livro->titulo }}"
									class="w-20 h-24 object-cover rounded-lg" />
								<div>
									<div class="font-semibold">{{ $livro->nome ?? $livro->titulo }}</div>
									<div class="text-sm text-gray-500">ISBN: {{ $livro->isbn }}</div>
								</div>
							</div>
						</td>
						<td class="text-right py-5">{{ number_format($preco,2,',','.') }} €</td>
						<td class="text-center py-5">
							<form method="POST" action="{{ route('carrinho.update', $livro) }}" class="flex items-center justify-center gap-2">
								@csrf
								@method('PATCH')
								<input type="number" name="quantidade" min="0" max="99" value="{{ $qtd }}"
									class="bg-gray-100 input input-bordered w-20 text-center" />
								<button class="btn btn-sm">Atualizar</button>
							</form>
						</td>
						<td class="text-right py-5 font-semibold">{{ number_format($sub,2,',','.') }} €</td>
						<td class="text-right py-5">
							<form method="POST" action="{{ route('carrinho.remove', $livro) }}">
								@csrf
								@method('DELETE')
								<button class="btn btn-outline btn-sm">X</button>
							</form>
						</td>
					</tr>
					@endforeach
					<tr>
						<td colspan="3" class="text-right font-semibold">Total</td>
						<td class="text-right font-extrabold text-lg">{{ number_format($total,2,',','.') }} €</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="mt-6 flex flex-col md:flex-row gap-3 justify-between">
			<form method="POST" action="{{ route('carrinho.clear') }}">
				@csrf @method('DELETE')
				<button class="btn btn-outline">Limpar carrinho</button>
			</form>

			<div class="flex gap-3">
				<a href="{{ route('livros.index') }}" class="btn">Continuar a comprar</a>
				<a href="{{ route('checkout.morada', ['c' => $carrinho->id]) }}" class="btn btn-primary">Finalizar compra</a>
			</div>
		</div>
		@endif
	</div>
</x-guest-layout>