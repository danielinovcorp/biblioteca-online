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
						<button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</button>
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
						@auth
						@if (Auth::user()->isAdmin())
						<label for="modal-add-livro" class="btn btn-primary ml-10"><i class="fa-solid fa-circle-plus"></i> Livro</label>
						@endif
						@endauth
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
							@auth
							<th class="w-10 px-2"><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
							@endauth
							<th class="min-w-[80px]">Capa</th>
							<th class="min-w-[150px]">{!! sort_link('nome', 'Título', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[100px]">{!! sort_link('isbn', 'ISBN', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[120px]">{!! sort_link('editora_id', 'Editora', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[150px]">Autores</th>
							<th class="min-w-[80px]">{!! sort_link('preco', 'Preço', $sortField, $sortDirection) !!}</th>
							@auth
							@if (Auth::user()->isAdmin())
							<th class="w-32 text-center">Ações</th>
							@endif
							@endauth
						</tr>
					</thead>
					<tbody>
						@foreach($livros as $livro)
						@if(request('edit') == $livro->id)
						{{-- MODO DE EDIÇÃO --}}
						<tr class="bg-yellow-50" data-id="{{ $livro->id }}">
							<form method="POST" action="{{ route('livros.update', $livro->id) }}" enctype="multipart/form-data" class="contents">
								@csrf
								@method('PUT')

								@auth
								<td class="px-2"><input type="checkbox" class="checkbox checkbox-primary row-checkbox" data-id="{{ $livro->id }}" /></td>
								@endauth

								<td>
									<img src="{{ $livro->imagem_capa }}" class="w-[75px] h-[100px] object-cover shadow rounded" />
									<input type="file" name="imagem_capa" class="file-input file-input-bordered w-full mt-2 bg-white/90 border-gray-200 file:bg-gray-100 file:border-gray-300 file:text-gray-700">
								</td>

								<td><input type="text" name="nome" value="{{ $livro->nome }}" class="input input-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required></td>
								<td><input type="text" name="isbn" value="{{ $livro->isbn }}" class="input input-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required></td>

								<td>
									<select name="editora_id" class="select select-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
										@foreach ($editoras as $editora)
										<option value="{{ $editora->id }}" @selected($livro->editora_id == $editora->id)>
											{{ $editora->nome }}
										</option>
										@endforeach
									</select>
								</td>

								<td>
									<select name="autores[]" multiple class="select select-bordered w-full h-32 bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
										@foreach ($autores as $autor)
										<option value="{{ $autor->id }}" @selected($livro->autores->contains($autor->id))>
											{{ $autor->nome }}
										</option>
										@endforeach
									</select>
								</td>

								<td><input type="number" step="0.01" name="preco" value="{{ $livro->preco }}" class="input input-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required></td>

								@auth
								@if(Auth::user()->isAdmin())
								<td class="w-32 px-2">
									<div class="flex justify-center space-x-1">
										<button type="submit" class="btn btn-sm btn-ghost hover:bg-gray-100">💾</button>
										<a href="{{ route('livros.index', request()->except('edit')) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">❌</a>
									</div>
								</td>
								@endif
								@endauth
							</form>
						</tr>
						@else
						{{-- MODO VISUALIZAÇÃO (original) --}}
						<tr class="hover text-neutral odd:bg-gray-100 even:bg-white" data-id="{{ $livro->id }}">
							@auth
							<td class="px-2"><input type="checkbox" class="checkbox checkbox-primary row-checkbox" data-id="{{ $livro->id }}" /></td>
							@endauth
							<td><img src="{{ $livro->imagem_capa }}" class="w-[75px] h-[100px] object-cover shadow rounded" /></td>
							<td class="font-medium">{{ $livro->nome }}</td>
							<td>{{ $livro->isbn }}</td>
							<td>{{ $livro->editora->nome }}</td>
							<td class="max-w-[150px]">
								@foreach ($livro->autores as $autor)
								<span class="text-sm text-indigo-600">{{ $autor->nome }}</span>
								@if (!$loop->last)<br> @endif
								@endforeach
							</td>
							<td>€ {{ number_format($livro->preco, 2, ',', '.') }}</td>
							@auth
							@if(Auth::user()->isAdmin())
							<td class="w-32 px-2">
								<div class="flex justify-center space-x-1">
									<a href="{{ route('livros.index', array_merge(request()->query(), ['edit' => $livro->id])) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">✏️</a>
									<form method="POST" action="{{ route('livros.destroy', $livro->id) }}" class="inline">
										@csrf
										@method('DELETE')
										<button type="submit" onclick="return confirm('Deseja excluir este livro?')" class="btn btn-sm btn-ghost hover:bg-gray-100">🗑️</button>
									</form>
								</div>
							</td>
							@endif
							@endauth
						</tr>
						@endif
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	{{-- SCRIPT PARA CHECKBOX E EXPORTAÇÃO --}}
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const checkAll = document.getElementById('checkAll');
			const rowCheckboxes = document.querySelectorAll('.row-checkbox');
			const exportForm = document.getElementById('export-form');
			const selectedIdsInput = document.getElementById('selected-ids');

			checkAll.addEventListener('change', function() {
				rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
			});

			exportForm.addEventListener('submit', function(e) {
				const selected = Array.from(rowCheckboxes)
					.filter(cb => cb.checked)
					.map(cb => cb.closest('tr').dataset.id);

				selectedIdsInput.value = selected.join(',');
			});
		});
	</script>

	{{-- MODAL: Adicionar Novo Livro --}}
	<input type="checkbox" id="modal-add-livro" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-4xl" data-theme="light">
			<h3 class="text-lg font-bold mb-4">📚 Adicionar Livro</h3>

			<form method="POST" action="{{ route('livros.store') }}" enctype="multipart/form-data">
				@csrf
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					{{-- ISBN + botão de busca --}}
					<div class="flex gap-2 col-span-2">
						<input type="text" name="isbn" class="input input-bordered w-full" placeholder="ISBN">
						<button type="button" class="btn">🔍</button>
					</div>

					<input type="text" name="nome" class="input input-bordered w-full" placeholder="Nome do Livro">

					<select name="editora_id" class="select select-bordered">
						<option disabled selected>Selecione uma editora</option>
						@foreach ($editoras as $editora)
						<option value="{{ $editora->id }}">{{ $editora->nome }}</option>
						@endforeach
					</select>

					<input type="number" name="preco" step="0.01" class="input input-bordered" placeholder="Preço">

					<textarea name="bibliografia" class="textarea textarea-bordered col-span-2" rows="2" placeholder="Bibliografia"></textarea>

					<select name="autores[]" multiple class="select select-bordered col-span-2 h-40">
						@foreach ($autores as $autor)
						<option value="{{ $autor->id }}">{{ $autor->nome }}</option>
						@endforeach
					</select>

					<input type="file" name="imagem_capa" class="file-input file-input-bordered col-span-2">
				</div>

				<div class="modal-action">
					<label for="modal-add-livro" class="btn">Cancelar</label>
					<button type="submit" class="btn btn-primary">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</x-guest-layout>