<x-guest-layout>
	<x-slot name="heading">
		📚 Importar Livros da Google Books
	</x-slot>

	<div class="max-w-4xl mx-auto px-8 md:px-24 py-6 bg-white shadow rounded">
		{{-- FORMULÁRIO DE PESQUISA --}}
		<form method="POST" action="{{ route('livros.pesquisar') }}" class="flex flex-col md:flex-row gap-4 items-center justify-center m-4">
			@csrf
			<input type="text" name="termo"
				placeholder="Digite um termo (ex: Laravel, PHP, etc)"
				value="{{ $termo ?? '' }}"
				class="input input-bordered bg-gray-200 px-4 py-2 text-base w-full md:w-[420px]" required>
			<button type="submit" class="btn btn-primary">🔍 Buscar</button>
		</form>

		{{-- RESULTADOS DA PESQUISA --}}
		@isset($livros)
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			@forelse ($livros as $livro)
			<div class="bg-gray-50 p-4 shadow rounded flex gap-4">
				<img src="{{ $livro['imagem_capa'] }}" alt="Capa" class="w-24 h-32 object-cover rounded shadow">
				<div class="flex-1">
					<h3 class="font-semibold text-indigo-700">{{ $livro['nome'] }}</h3>
					<p class="text-sm text-gray-700">ISBN: <strong>{{ $livro['isbn'] }}</strong></p>
					<p class="text-sm">Autor(es): {{ implode(', ', $livro['autores']) }}</p>
					<p class="text-sm">Editora: {{ $livro['editora'] }}</p>
					<p class="text-sm">Preço: € {{ number_format($livro['preco'], 2, ',', '.') }}</p>

					{{-- BOTÃO IMPORTAR --}}
					<form method="POST" action="{{ route('livros.importar.salvar') }}" class="mt-3">
						@csrf
						<input type="hidden" name="dados" value="{{ base64_encode(json_encode($livro)) }}">
						<button type="submit" class="btn btn-success btn-sm"><i class="fa fa-download"></i> Importar</button>
					</form>
				</div>
			</div>
			@empty
			<p class="text-center text-gray-500 col-span-2">Nenhum livro encontrado.</p>
			@endforelse
		</div>
		@endisset
		{{-- NAVEGAÇÃO ENTRE PÁGINAS --}}
		@if (isset($termo))
		<div class="flex justify-center mt-6 gap-4">
			@if ($startIndex > 0)
			<form method="POST" action="{{ route('livros.pesquisar') }}">
				@csrf
				<input type="hidden" name="termo" value="{{ $termo }}">
				<input type="hidden" name="startIndex" value="{{ max(0, $startIndex - $maxResults) }}">
				<button class="btn btn-outline">⬅ Anterior</button>
			</form>
			@endif

			<form method="POST" action="{{ route('livros.pesquisar') }}">
				@csrf
				<input type="hidden" name="termo" value="{{ $termo }}">
				<input type="hidden" name="startIndex" value="{{ $startIndex + $maxResults }}">
				<button class="btn btn-outline">Próximo ➡</button>
			</form>
		</div>
		@endif
	</div>
</x-guest-layout>