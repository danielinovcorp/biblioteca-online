<x-guest-layout>
	<x-slot name="heading">🏷️ Dados de Entrega</x-slot>

	<div class="max-w-4xl mx-auto p-4 md:p-8 grid md:grid-cols-2 gap-6">
		{{-- Resumo à direita em ecrã grande --}}
		<div class="order-2 md:order-1">
			<form method="POST" action="{{ route('checkout.morada.store') }}" class="card bg-base-100 shadow">
				@csrf
				<input type="hidden" name="carrinho_id" value="{{ $carrinho->id }}">
				<div class="bg-white card-body text-left">
					<h2 class="card-title">Morada de entrega</h2>

					<div class="form-control">
						<label class="label"><span class="label-text">Morada *</span></label>
						<input type="text" name="morada" required value="{{ old('morada') }}" class="bg-gray-100 input input-bordered">
						@error('morada') <span class="text-error text-sm">{{ $message }}</span> @enderror
					</div>

					<div class="form-control">
						<label class="label"><span class="label-text">Cidade</span></label>
						<input type="text" name="cidade" value="{{ old('cidade') }}" class="bg-gray-100 input input-bordered">
						@error('cidade') <span class="text-error text-sm">{{ $message }}</span> @enderror
					</div>
					
					<div class="form-control">
						<label class="label"><span class="label-text">Código Postal</span></label>
						<input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}" class="bg-gray-100 input input-bordered">
						@error('codigo_postal') <span class="text-error text-sm">{{ $message }}</span> @enderror
					</div>

					<div class="form-control">
						<label class="label"><span class="label-text">Telefone</span></label>
						<input type="text" name="telefone" value="{{ old('telefone') }}" class="bg-gray-100 input input-bordered">
						@error('telefone') <span class="text-error text-sm">{{ $message }}</span> @enderror
					</div>

					<div class="card-actions justify-left mt-4 gap-6">
						<a href="{{ route('carrinho.index') }}" class="btn">Voltar ao carrinho</a>
						<button class="btn btn-primary">Ir para pagamento</button>
					</div>
				</div>
			</form>
		</div>

		<div class="order-1 md:order-2">
			<div class="card bg-base-100 shadow">
				<div class="bg-white card-body">
					<h3 class="card-title">Resumo</h3>
					<ul class="space-y-2">
						@foreach($carrinho->livros as $l)
							<li class="flex justify-between">
								<span>{{ $l->nome ?? $l->titulo }} × {{ (int)$l->pivot->quantidade }}</span>
								<span>{{ number_format(($l->preco ?? 0) * (int)$l->pivot->quantidade,2,',','.') }} €</span>
							</li>
						@endforeach
					</ul>
					<div class="divider my-2"></div>
					<div class="flex justify-between font-bold">
						<span>Total</span>
						<span>{{ number_format($total,2,',','.') }} €</span>
					</div>
					<p class="text-sm opacity-70 mt-2">O pagamento será processado via Stripe em ambiente de testes (sandbox).</p>
				</div>
			</div>
		</div>
	</div>
</x-guest-layout>
