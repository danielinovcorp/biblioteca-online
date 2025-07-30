<x-guest-layout>
	<x-slot name="header">
		<h2 class="text-xl font-bold">📥 Nova Requisição</h2>
	</x-slot>

	<div class="p-4 max-w-2xl mx-auto">
		@if ($errors->any())
		<div class="alert alert-error">
			<ul class="list-disc list-inside text-sm">
				@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif

		<form method="POST" action="{{ route('requisicoes.store') }}" enctype="multipart/form-data" class="space-y-4">
			@csrf

			<div>
				<label class="label">
					<span class="label-text">Livro</span>
				</label>
				<select name="livro_id" class="select select-bordered w-full bg-white" required>
					<option value="">Selecione um livro</option>
					@foreach ($livrosDisponiveis as $livro)
					<option value="{{ $livro->id }}"
						@if (old('livro_id', $livroSelecionadoId)==$livro->id) selected @endif>
						{{ $livro->nome }}
					</option>
					@endforeach
				</select>
			</div>

			<div>
				<label class="label">
					<span class="label-text">Foto do cidadão (obrigatória)</span>
				</label>
				<input type="file" name="foto_cidadao" class="file-input file-input-bordered w-full bg-white" required />
			</div>

			<div class="text-right">
				<a href="{{ route('requisicoes.index') }}" class="btn btn-ghost">Cancelar</a>
				<button type="submit" class="btn btn-primary">Salvar Requisição</button>
			</div>
		</form>
	</div>
</x-guest-layout>