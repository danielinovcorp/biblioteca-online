<x-guest-layout>
	<style>
		.table-zebra tbody tr:nth-child(even) {
			background-color: rgb(220 220 220 / 1);
		}
	</style>
	<x-slot name="heading">📝 Reviews (Moderação)</x-slot>

<div class="w-full mx-auto px-16">
	<div class="bg-white shadow rounded-lg w-full overflow-x-auto">
		<div class="pt-6">
			{{-- Filtros --}}
			<form method="GET" class="mb-4 flex flex-col md:flex-row items-center justify-center gap-3">
				<div class="flex gap-2">
					<select name="status" class="select select-bordered border border-gray-300 bg-white">
						<option value="">Todos</option>
						<option value="suspenso" @selected(($status ?? '' )==='suspenso' )>Suspensos</option>
						<option value="ativo" @selected(($status ?? '' )==='ativo' )>Ativos</option>
						<option value="recusado" @selected(($status ?? '' )==='recusado' )>Recusados</option>
					</select>
					<input type="text" name="q" value="{{ $term ?? '' }}" placeholder="Buscar por livro ou cidadão"
					class="input input-bordered border border-gray-300 bg-white w-80" />
				</div>
				<div class="flex gap-2">
					<button class="btn">Filtrar</button>
					<a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost">Limpar</a>
				</div>
			</form>
		</div>
			
		{{-- Tabela --}}
		<div class="overflow-x-auto w-full">
			<table class="table table-zebra w-full">
				<thead class="bg-gray-700 text-white font-semibold">
					<tr>
						<th>ID</th>
						<th>Livro</th>
						<th>Cidadão</th>
						<th>Rating</th>
						<th>Status</th>
						<th>Data</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@forelse ($reviews as $r)
					<tr>
						<td>{{ $r->id }}</td>
						<td class="font-medium">{{ $r->livro->nome }}</td>
						<td>{{ $r->user->name }}</td>
						<td>{{ $r->rating }} ⭐</td>
						<td>
							@php
							$cls = $r->status==='suspenso' ? 'badge-warning'
							: ($r->status==='ativo' ? 'badge-success' : 'badge-error');
							@endphp
							<span class="badge {{ $cls }}">{{ ucfirst($r->status) }}</span>
						</td>
						<td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
						<td>
							<a class="btn btn-sm" href="{{ route('admin.reviews.show',$r) }}">Abrir</a>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="7" class="text-center p-6 opacity-60">
							Nenhum review encontrado.
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		{{-- Paginação --}}
		<div class="mt-4">{{ $reviews->links() }}</div>
	</div>
</div>
</x-guest-layout>