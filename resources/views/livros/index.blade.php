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
							<th class="min-w-[150px]">Autores</th>
							<th class="min-w-[120px]">{!! sort_link('editora_id', 'Editora', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[80px]">{!! sort_link('preco', 'Preço', $sortField, $sortDirection) !!}</th>
							<th>Disponível?</th>
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
									<input type="file" name="imagem_capa" class="file-input input-sm file-input-bordered w-64 mt-2 bg-white/90 border-gray-200 file:bg-gray-100 file:border-gray-300 file:text-gray-700">
								</td>

								<td><input type="text" name="nome" value="{{ $livro->nome }}" class="input input-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required></td>
								<td><input type="text" name="isbn" value="{{ $livro->isbn }}" class="input input-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required></td>

								<td>
									<select name="autores[]" multiple class="select select-bordered w-full h-32 bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
										@foreach ($autores as $autor)
										<option value="{{ $autor->id }}" @selected($livro->autores->contains($autor->id))>
											{{ $autor->nome }}
										</option>
										@endforeach
									</select>
								</td>

								<td>
									<select name="editora_id" class="select select-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
										@foreach ($editoras as $editora)
										<option value="{{ $editora->id }}" @selected($livro->editora_id == $editora->id)>
											{{ $editora->nome }}
										</option>
										@endforeach
									</select>
								</td>

								<td><input type="number" step="0.01" name="preco" value="{{ $livro->preco }}" class="input input-bordered w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required></td>

								<td>
									@if($livro->disponivel)
									<span class="badge badge-success">✔</span>
									@else
									<span class="badge badge-error">✘</span>
									@endif
								</td>

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
							<td onclick='mostrarLivro(@json($livro))' class="cursor-pointer">
								<img src="{{ $livro->imagem_capa }}" class="w-[75px] h-[100px] object-cover shadow rounded" />
							</td>

							<td onclick='mostrarLivro(@json($livro))' class="cursor-pointer hover:underline text-blue-600 font-medium">
								{{ $livro->nome }}
							</td>
							<td>{{ $livro->isbn }}</td>
							<td class="max-w-[150px]">
								@foreach ($livro->autores as $autor)
								<span class="text-sm">{{ $autor->nome }}</span>
								@if (!$loop->last)<br> @endif
								@endforeach
							</td>
							<td>{{ $livro->editora->nome }}</td>
							<td>€ {{ number_format($livro->preco, 2, ',', '.') }}</td>
							<td>
								@if($livro->disponivel)
								<span class="badge badge-success">Sim</span>
								@else
								<span class="badge badge-error">Não</span>
								@endif
							</td>
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

	{{-- MODAL: Detalhes do Livro --}}
	<input type="checkbox" id="modal-detalhes-livro" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-4xl text-left" data-theme="light">

			<div class="flex flex-col md:flex-row gap-6">
				<img id="modal-livro-capa" src="" class="w-[200px] h-[240px] rounded shadow" />

				<div class="flex flex-col gap-2 text-sm">
					<h3 id="modal-livro-nome" class="text-2xl font-bold text-indigo-700 mb-1"></h3>

					<p><strong class="text-gray-700">ISBN:</strong> <span id="modal-livro-isbn"></span></p>
					<p><strong class="text-gray-700">Editora:</strong> <span id="modal-livro-editora"></span></p>
					<p><strong class="text-gray-700">Autores:</strong> <span id="modal-livro-autores"></span></p>
					<p><strong class="text-gray-700">Preço:</strong> €<span id="modal-livro-preco"></span></p>
					<p><strong>Disponível:</strong> <span id="modal-livro-disponivel"></span></p>

					<p><strong class="text-gray-700">Bibliografia:</strong></p>
					<p id="modal-livro-bibliografia" class="whitespace-pre-wrap text-justify text-gray-800"></p>
				</div>
			</div>

			<div class="modal-action mt-6 flex flex-col md:flex-row items-center justify-end gap-4">
				<div class="mt-6">
					<h4 class="text-lg font-semibold mb-2">📖 Histórico de Requisições</h4>
					<div id="historico-requisicoes" class="overflow-x-auto">
						<p class="text-gray-500 italic">Carregando histórico...</p>
					</div>
				</div>

				<form id="form-requisitar-livro" method="GET" action="{{ route('requisicoes.create') }}">
					<input type="hidden" name="livro_id" id="input-livro-id" />
					<button type="submit" class="btn btn-primary">
						📚 Requisitar este livro
					</button>
				</form>

				<label for="modal-detalhes-livro" class="btn">Fechar</label>
			</div>

		</div>
	</div>

	{{-- SCRIPT PARA MODAL DE DETALHES DO LIVRO --}}
	<script>
		function mostrarLivro(livro) {
			const autores = Array.isArray(livro.autores) ?
				livro.autores.map(a => a.nome).join(', ') :
				livro.autores;

			document.getElementById('modal-livro-nome').innerText = livro.nome;
			document.getElementById('modal-livro-isbn').innerText = livro.isbn ?? 'N/A';
			document.getElementById('modal-livro-editora').innerText = livro.editora?.nome ?? 'N/A';
			document.getElementById('modal-livro-autores').innerText = autores;
			document.getElementById('modal-livro-preco').innerText = livro.preco ?? '0.00';
			document.getElementById('modal-livro-disponivel').innerText = livro.disponivel ? 'Sim' : 'Não';
			document.getElementById('modal-livro-bibliografia').innerText = livro.bibliografia ?? '';
			document.getElementById('modal-livro-capa').src = livro.imagem_capa ?? '';

			// Preenche o campo hidden com o ID do livro
			const inputLivroId = document.getElementById('input-livro-id');
			if (inputLivroId) {
				inputLivroId.value = livro.id;
			}

			// Mostrar ou esconder o botão de requisição com base na disponibilidade
			const formRequisitar = document.getElementById('form-requisitar-livro');
			if (formRequisitar) {
				if (livro.disponivel) {
					formRequisitar.classList.remove('hidden');
				} else {
					formRequisitar.classList.add('hidden');
				}
			}

			// Requisições
			const historicoContainer = document.getElementById('historico-requisicoes');
			if (livro.requisicoes && livro.requisicoes.length > 0) {
				const rows = livro.requisicoes.map(req => `
					<tr>
						<td>${req.numero}</td>
						<td>${req.user?.name ?? 'Desconhecido'}</td>
						<td>${req.data_inicio}</td>
						<td>${req.data_fim_prevista}</td>
						<td>${req.data_fim_real ?? '<span class="italic text-gray-400">Pendente</span>'}</td>
						<td>
							<span class="badge badge-${req.status === 'ativa' ? 'success' : (req.status === 'devolvida' ? 'info' : 'warning')}">
								${req.status.charAt(0).toUpperCase() + req.status.slice(1)}
							</span>
						</td>
					</tr>
				`).join('');

				historicoContainer.innerHTML = `
					<table class="table table-zebra w-full text-sm">
						<thead class="bg-gray-100">
							<tr>
								<th>#</th>
								<th>Cidadão</th>
								<th>Início</th>
								<th>Previsão Fim</th>
								<th>Fim Real</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>${rows}</tbody>
					</table>
	`;
			} else {
				historicoContainer.innerHTML = `<p class="text-gray-500 italic">Nenhuma requisição registrada.</p>`;
			}


			// Armazenar o livro_id na sessionStorage antes de abrir o modal
			sessionStorage.setItem('livro_id_requisicao', livro.id);

			// Abrir o modal
			document.getElementById('modal-detalhes-livro').checked = true;

			document.getElementById('input-livro-id').value = livro.id;

		}
	</script>

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

</x-guest-layout>