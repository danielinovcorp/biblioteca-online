<x-guest-layout>
	<x-slot name="heading">📝 Review #{{ $review->id }}</x-slot>

	@if (session('success'))
		<div class="alert alert-success mb-4">{{ session('success') }}</div>
	@endif

	{{-- Bloco de informações --}}
	<div class="bg-white p-6 rounded shadow space-y-2">
		<div><b>Livro:</b> {{ $review->livro->nome }}</div>
		<div><b>Cidadão:</b> {{ $review->user->name }} ({{ $review->user->email }})</div>
		<div><b>Rating:</b> {{ $review->rating }} ⭐</div>
		<div><b>Status atual:</b> {{ ucfirst($review->status) }}</div>
		@if($review->comentario)
			<div><b>Comentário:</b> {{ $review->comentario }}</div>
		@endif
		@if($review->justificativa)
			<div><b>Justificativa:</b> {{ $review->justificativa }}</div>
		@endif
		<div><b>Submetido em:</b> {{ $review->created_at->format('d/m/Y H:i') }}</div>
	</div>

	{{-- Form de moderação --}}
	<div class="mt-6">
		<form method="POST" action="{{ route('admin.reviews.updateStatus', $review) }}" class="space-y-3">
			@csrf
			@method('PATCH')

			<div class="max-w-sm">
				<label class="label">Alterar status</label>
				<select name="status" class="select select-bordered w-full bg-white" required>
					<option value="">Selecione</option>
					<option value="ativo">Ativar</option>
					<option value="recusado">Recusar</option>
				</select>
			</div>

			<div>
				<label class="label">Justificativa (apenas se recusar)</label>
				<textarea name="justificativa" class="textarea textarea-bordered w-full bg-white" rows="4"
						placeholder="Explique ao cidadão por que o review foi recusado"></textarea>
			</div>

			<div class="flex gap-2">
				<button class="btn btn-primary">Salvar</button>
				<a href="{{ route('admin.reviews.index') }}" class="btn">Voltar</a>
			</div>
		</form>
	</div>
</x-guest-layout>
