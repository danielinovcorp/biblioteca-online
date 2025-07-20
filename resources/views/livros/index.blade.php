<x-guest-layout>
	<x-slot name="heading">
		📚 Livros
	</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8">

			{{-- FORMULÁRIO DE FILTRO E EXPORTAÇÃO --}}
			<form method="GET" action="{{ route('livros.export') }}" id="export-form" class="flex flex-col items-center gap-4 mb-6">
				{{-- INPUT HIDDEN PARA IDS SELECIONADOS --}}
				<input type="hidden" name="ids" id="selected-ids" />

				{{-- FILTROS E BOTÕES LADO A LADO --}}
				<div class="flex flex-col sm:flex-row gap-4 w-full justify-center items-end">
					{{-- BOTÃO DE EXPORTAR --}}
					<div class="self-end">
						<button type="submit" class="btn btn-success">
							📥 Exportar Excel
						</button>
					</div>

					{{-- FILTRO POR EDITORA --}}
					<div class="form-control w-full max-w-xs">
						<select name="editora_id" class="select select-neutral select-bordered w-full bg-white text-gray-900">
							<option value="">Todas as Editoras</option>
							@foreach ($editoras as $editora)
								<option value="{{ $editora->id }}" @selected(request('editora_id')==$editora->id)>
									{{ $editora->nome }}
								</option>
							@endforeach
						</select>
					</div>

					{{-- FILTRO POR AUTOR --}}
					<div class="form-control w-full max-w-xs">
						<select name="autor_id" class="select select-neutral select-bordered w-full bg-white text-gray-900">
							<option value="">Todos os Autores</option>
							@foreach ($autores as $autor)
								<option value="{{ $autor->id }}" @selected(request('autor_id')==$autor->id)>
									{{ $autor->nome }}
								</option>
							@endforeach
						</select>
					</div>

					{{-- BOTÕES DE FILTRAR / LIMPAR --}}
					<div class="flex gap-2 self-end">
						<button type="submit" formaction="{{ route('livros.index') }}" class="btn btn-outline btn-info">Filtrar</button>
						<a href="{{ route('livros.index') }}" class="btn btn-outline btn-error">Limpar</a>
					</div>
				</div>
			</form>

			{{-- TABELA DE LIVROS --}}
			@php
			function sort_link($field, $label, $currentField, $currentDirection) {
				$newDirection = ($currentField === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
				$icon = $currentField === $field ? ($currentDirection === 'asc' ? '▲' : '▼') : '';
				$query = request()->except(['page', 'sort', 'direction']);
				$query['sort'] = $field;
				$query['direction'] = $newDirection;
				$url = route('livros.index', $query);
				return "<a href='{$url}' class='hover:underline font-medium'>{$label} {$icon}</a>";
			}
			@endphp

			<div class="overflow-x-auto w-full">
				<table class="table w-full">
					<thead class="bg-gray-700 text-white font-semibold">
						<tr>
							<th><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
							<th>{!! sort_link('nome', 'Título', $sortField, $sortDirection) !!}</th>
							<th>{!! sort_link('isbn', 'ISBN', $sortField, $sortDirection) !!}</th>
							<th>{!! sort_link('editora_id', 'Editora', $sortField, $sortDirection) !!}</th>
							<th>Autores</th>
							<th class="text-left">{!! sort_link('preco', 'Preço', $sortField, $sortDirection) !!}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($livros as $livro)
							<tr class="hover text-neutral odd:bg-gray-200 even:bg-white" data-id="{{ $livro->id }}">
								<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" /></td>
								<td>
									<div class="flex items-center gap-3">
										<div class="avatar">
											<div class="mask mask-squircle h-12 w-9">
												<img src="{{ $livro->imagem_capa }}" alt="Capa de {{ $livro->nome }}" />
											</div>
										</div>
										<div class="font-medium">{{ $livro->nome }}</div>
									</div>
								</td>
								<td>{{ $livro->isbn }}</td>
								<td>{{ $livro->editora->nome }}</td>
								<td>
									@foreach ($livro->autores as $autor)
										<span class="text-indigo-600 hover:underline text-sm">
											{{ $autor->nome }}
										</span>
										@if (!$loop->last), @endif
									@endforeach
								</td>
								<td class="text-left font-semibold whitespace-nowrap">
									€ {{ number_format($livro->preco, 2, ',', '.') }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	{{-- SCRIPT PARA CHECKBOX E EXPORTAÇÃO --}}
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const checkAll = document.getElementById('checkAll');
			const rowCheckboxes = document.querySelectorAll('.row-checkbox');
			const exportForm = document.getElementById('export-form');
			const selectedIdsInput = document.getElementById('selected-ids');

			checkAll.addEventListener('change', function () {
				rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
			});

			exportForm.addEventListener('submit', function (e) {
				const selected = Array.from(rowCheckboxes)
					.filter(cb => cb.checked)
					.map(cb => cb.closest('tr').dataset.id);

				selectedIdsInput.value = selected.join(',');
			});
		});
	</script>
</x-guest-layout>
