<x-guest-layout>
	<x-slot name="heading">🏢 Editoras</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8">

			{{-- BARRA DE AÇÕES --}}
			<div class="flex justify-center gap-4 mb-6 px-4">
				@auth
				@if(Auth::user()->isAdmin())
				<form method="GET" action="{{ route('editoras.export') }}" id="export-form" class="flex items-center gap-4">
					<input type="hidden" name="ids" id="selected-ids" />
					<input type="hidden" name="q" value="{{ request('q') }}">
					<input type="hidden" name="sort" value="{{ request('sort') }}">
					<input type="hidden" name="direction" value="{{ request('direction') }}">
					<button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</button>
				</form>
				<label for="modal-add-editora" class="btn btn-primary">
					<i class="fa-solid fa-circle-plus"></i>Editora
				</label>
				@endif
				@endauth
			</div>

			@php
			function sort_link_editora($field, $label, $currentField, $currentDirection) {
			$newDirection = ($currentField === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
			$icon = $currentField === $field ? ($currentDirection === 'asc' ? '▲' : '▼') : '';
			$query = request()->except(['page', 'sort', 'direction']);
			$query['sort'] = $field;
			$query['direction'] = $newDirection;
			$url = route('editoras.index', $query);
			return "<a href='{$url}' class='hover:underline font-medium'>{$label} {$icon}</a>";
			}
			@endphp

			{{-- TABELA DE EDITORAS --}}
			<div class="overflow-x-auto">
				<table class="table w-full">
					<thead>
						<tr class="bg-gray-700 text-white">
							@auth
							@if(Auth::user()->isAdmin())
							<th class="w-10"><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
							@endif
							@endauth
							<th>Logótipo</th>
							<th>{!! sort_link_editora('nome', 'Nome', $sortField, $sortDirection) !!}</th>
							@auth
							@if(Auth::user()->isAdmin())
							<th class="text-center">Ações</th>
							@endif
							@endauth
						</tr>
					</thead>
					<tbody>
						@foreach ($editoras as $editora)
						@if (request('edit') == $editora->id && Auth::user()?->isAdmin())
						{{-- MODO EDIÇÃO --}}
						<tr class="bg-yellow-50">
							<form method="POST" action="{{ route('editoras.update', $editora->id) }}" enctype="multipart/form-data" class="contents">
								@csrf
								@method('PUT')

								<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" /></td>

								<td>
									<img src="{{ $editora->logotipo ?? asset('images/default-publisher.png') }}"
										class="w-[60px] h-[60px] rounded object-contain shadow" />
									<input type="file" name="logotipo" class="file-input file-input-bordered file-input-sm w-full mt-1 bg-white/90 border-gray-200 file:bg-gray-100 file:border-gray-300 file:text-gray-700" />
								</td>

								<td>
									<input type="text" name="nome" value="{{ $editora->nome }}"
										class="input input-bordered input-sm w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
								</td>

								<td class="whitespace-nowrap text-center space-x-1">
									<button type="submit" class="btn btn-sm btn-ghost hover:bg-gray-100">💾</button>
									<a href="{{ route('editoras.index') }}" class="btn btn-sm btn-ghost hover:bg-gray-100">❌</a>
								</td>
							</form>
						</tr>
						@else
						{{-- MODO VISUAL --}}
						<tr class="hover {{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}" data-id="{{ $editora->id }}">
							@auth
							@if(Auth::user()->isAdmin())
							<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" data-id="{{ $editora->id }}" /></td>
							@endif
							@endauth

							<td onclick='mostrarEditora(@json($editora))' class="cursor-pointer">
								<img src="{{ $editora->logotipo ?? asset('images/default-editora.png') }}" class="w-12 h-12 object-cover rounded" />
							</td>
							<td onclick='mostrarEditora(@json($editora))' class="cursor-pointer text-indigo-600 hover:underline font-medium">
								{{ $editora->nome }}
							</td>


							@auth
							@if(Auth::user()->isAdmin())
							<td class="whitespace-nowrap text-center space-x-1">
								<a href="{{ route('editoras.index', ['edit' => $editora->id]) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">✏️</a>
								<form method="POST" action="{{ route('editoras.destroy', $editora->id) }}" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" onclick="return confirm('Deseja excluir esta editora?')" class="btn btn-sm btn-ghost hover:bg-gray-100">🗑️</button>
								</form>
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

	{{-- MODAL ADICIONAR EDITORA --}}
	@auth
	@if(Auth::user()->isAdmin())
	<input type="checkbox" id="modal-add-editora" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-2xl" data-theme="light">
			<h3 class="text-lg font-bold mb-4 flex items-center gap-2">
				<i class="fas fa-building-circle-arrow-right"></i> Adicionar Editora
			</h3>

			<form method="POST" action="{{ route('editoras.store') }}" enctype="multipart/form-data">
				@csrf

				<div class="grid grid-cols-1 gap-4 text-left">
					{{-- NOME --}}
					<div>
						<label class="label">
							<span class="label-text">Nome da Editora</span>
						</label>
						<input type="text" name="nome" class="input input-bordered w-full" required>
					</div>

					{{-- LOGOTIPO --}}
					<div>
						<label class="label">
							<span class="label-text">Logótipo</span>
						</label>
						<input type="file" name="logotipo" class="file-input file-input-bordered w-full">
					</div>

					{{-- BOTÕES --}}
					<div class="modal-action justify-end">
						<label for="modal-add-editora" class="btn btn-ghost">Cancelar</label>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save mr-1"></i> Salvar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	@endif
	@endauth

	{{-- MODAL: Detalhes da Editora --}}
	<input type="checkbox" id="modal-detalhes-editora" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-2xl text-left" data-theme="light">
			<div class="flex flex-col md:flex-row gap-6">
				<img id="modal-editora-logo" src="" class="w-32 h-32 object-contain rounded shadow" />

				<div class="flex flex-col gap-2 text-sm w-full">
					<h3 id="modal-editora-nome" class="text-xl font-bold text-indigo-700 mb-2"></h3>

					<div>
						<p class="font-semibold text-gray-700 mb-1">Livros publicados:</p>
						<ul id="modal-editora-livros" class="list-disc pl-5 text-gray-800">
							{{-- Preenchido via JS --}}
						</ul>
					</div>
				</div>
			</div>

			<div class="modal-action mt-6">
				<label for="modal-detalhes-editora" class="btn">Fechar</label>
			</div>
		</div>
	</div>

	{{-- MODAL: Detalhes do Livro --}}
	<input type="checkbox" id="modal-detalhes-livro" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-4xl text-left" data-theme="light">
			<div class="flex flex-col md:flex-row gap-6">
				<img id="modal-livro-capa" src="" class="w-40 h-auto rounded shadow" />

				<div class="flex flex-col gap-2 text-sm">
					<h3 id="modal-livro-nome" class="text-2xl font-bold text-indigo-700 mb-1"></h3>

					<p><strong class="text-gray-700">ISBN:</strong> <span id="modal-livro-isbn"></span></p>
					<p><strong class="text-gray-700">Editora:</strong> <span id="modal-livro-editora"></span></p>
					<p><strong class="text-gray-700">Autores:</strong> <span id="modal-livro-autores"></span></p>
					<p><strong class="text-gray-700">Preço:</strong> €<span id="modal-livro-preco"></span></p>

					<p><strong class="text-gray-700">Bibliografia:</strong></p>
					<p id="modal-livro-bibliografia" class="whitespace-pre-wrap text-justify text-gray-800"></p>
				</div>
			</div>

			<div class="modal-action mt-6">
				<label for="modal-detalhes-livro" class="btn">Fechar</label>
			</div>
		</div>
	</div>


	{{-- SCRIPT PARA CHECKBOX --}}
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const checkAll = document.getElementById('checkAll');
			const rowCheckboxes = document.querySelectorAll('.row-checkbox');
			const exportForm = document.getElementById('export-form');
			const selectedIdsInput = document.getElementById('selected-ids');

			if (checkAll) {
				checkAll.addEventListener('change', function() {
					rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
				});
			}

			if (exportForm) {
				exportForm.addEventListener('submit', function(e) {
					const selected = Array.from(rowCheckboxes)
						.filter(cb => cb.checked)
						.map(cb => cb.closest('tr').dataset.id);
					selectedIdsInput.value = selected.join(',');
				});
			}
		});
	</script>

	<script>
		function mostrarEditora(editora) {
			document.getElementById('modal-editora-nome').innerText = editora.nome;
			document.getElementById('modal-editora-logo').src = editora.logotipo ?? '/images/default-editora.png';

			const lista = document.getElementById('modal-editora-livros');
			lista.innerHTML = '';

			if (editora.livros && editora.livros.length > 0) {
				editora.livros.forEach(livro => {
					const li = document.createElement('li');
					const link = document.createElement('a');
					link.href = 'javascript:void(0)';
					link.className = 'text-blue-600 hover:underline';
					link.textContent = livro.nome;
					link.onclick = () => mostrarLivro(livro);
					li.appendChild(link);
					lista.appendChild(li);
				});
			} else {
				const li = document.createElement('li');
				li.textContent = 'Nenhum livro registrado.';
				lista.appendChild(li);
			}

			document.getElementById('modal-detalhes-editora').checked = true;
		}
	</script>

	<script>
		function mostrarLivro(livro) {
			const autores = Array.isArray(livro.autores) ?
				livro.autores.map(a => a.nome).join(', ') :
				(livro.autores?.nome ?? 'N/A');

			document.getElementById('modal-livro-nome').innerText = livro.nome;
			document.getElementById('modal-livro-isbn').innerText = livro.isbn ?? 'N/A';
			document.getElementById('modal-livro-editora').innerText = livro.editora?.nome ?? 'N/A';
			document.getElementById('modal-livro-autores').innerText = autores;
			document.getElementById('modal-livro-preco').innerText = livro.preco ?? '0.00';
			document.getElementById('modal-livro-bibliografia').innerText = livro.bibliografia ?? '';
			document.getElementById('modal-livro-capa').src = livro.imagem_capa ?? '';

			document.getElementById('modal-detalhes-livro').checked = true;
		}
	</script>

</x-guest-layout>