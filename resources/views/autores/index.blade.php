<x-guest-layout>
	<x-slot name="heading">👤 Autores</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8">

			{{-- BARRA DE AÇÕES --}}
			<div class="flex justify-center gap-4 mb-6 px-4">
				@auth
				@if(Auth::user()->isAdmin())
				<form method="GET" action="{{ route('autores.export') }}" id="export-form" class="flex items-center gap-4">
					<input type="hidden" name="ids" id="selected-ids" />
					<button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</button>
				</form>
				<label for="modal-add-autor" class="btn btn-primary">
					<i class="fa-solid fa-circle-plus"></i>Autor
				</label>
				@endif
				@endauth
			</div>

			{{-- TABELA DE AUTORES --}}
			<div class="overflow-x-auto">
				<table class="table w-full">
					<thead>
						<tr class="bg-gray-700 text-white">
							@auth
							@if(Auth::user()->isAdmin())
							<th class="w-10"><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
							@endif
							@endauth
							<th>Foto</th>
							<th>Nome</th>
							@auth
							@if(Auth::user()->isAdmin())
							<th class="text-center">Ações</th>
							@endif
							@endauth
						</tr>
					</thead>
					<tbody>
						@foreach ($autores as $autor)
						@if (request('edit') == $autor->id && Auth::user()?->isAdmin())
						{{-- MODO EDIÇÃO --}}
						<tr class="bg-yellow-50">
							<form method="POST" action="{{ route('autores.update', $autor->id) }}" enctype="multipart/form-data" class="contents">
								@csrf
								@method('PUT')

								<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" /></td>

								<td>
									<img src="{{ $autor->foto ?? asset('images/default-user.png') }}"
										class="w-12 h-12 rounded-full object-cover border-2 border-white" />
									<input type="file" name="foto" class="file-input file-input-bordered file-input-sm w-full mt-1 bg-white/90 border-gray-200 file:bg-gray-100 file:border-gray-300 file:text-gray-700" />
								</td>

								<td>
									<input type="text" name="nome" value="{{ $autor->nome }}"
										class="input input-bordered input-sm w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
								</td>

								<td class="whitespace-nowrap text-center space-x-1">
									<button type="submit" class="btn btn-sm btn-ghost hover:bg-gray-100">💾</button>
									<a href="{{ route('autores.index') }}" class="btn btn-sm btn-ghost hover:bg-gray-100">❌</a>
								</td>
							</form>
						</tr>
						@else
						{{-- MODO VISUAL --}}
						<tr class="hover {{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}">
							@auth
							@if(Auth::user()->isAdmin())
							<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" data-id="{{ $autor->id }}" /></td>
							@endif
							@endauth

							<td>
								<img src="{{ $autor->foto ?? asset('images/default-user.png') }}"
									class="w-12 h-12 rounded-full object-cover border-2 border-white" />
							</td>

							<td class="font-medium">{{ $autor->nome }}</td>

							@auth
							@if(Auth::user()->isAdmin())
							<td class="whitespace-nowrap text-center space-x-1">
								<a href="{{ route('autores.index', ['edit' => $autor->id]) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">✏️</a>
								<form method="POST" action="{{ route('autores.destroy', $autor->id) }}" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" onclick="return confirm('Deseja excluir este autor?')" class="btn btn-sm btn-ghost hover:bg-gray-100">🗑️</button>
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

	{{-- MODAL ADICIONAR AUTOR --}}
	@auth
	@if(Auth::user()->isAdmin())
	<input type="checkbox" id="modal-add-autor" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-2xl" data-theme="light">
			<h3 class="text-lg font-bold mb-4 flex items-center gap-2">
				<i class="fas fa-user-plus"></i> Adicionar Autor
			</h3>

			<form method="POST" action="{{ route('autores.store') }}" enctype="multipart/form-data">
				@csrf

				<div class="grid grid-cols-1 gap-4 text-left">
					{{-- NOME --}}
					<div>
						<label class="label">
							<span class="label-text">Nome do Autor</span>
						</label>
						<input type="text" name="nome" class="input input-bordered w-full" required>
					</div>

					{{-- FOTO --}}
					<div>
						<label class="label">
							<span class="label-text">Foto</span>
						</label>
						<input type="file" name="foto" class="file-input file-input-bordered w-full">
					</div>

					{{-- BOTÕES --}}
					<div class="modal-action justify-end">
						<label for="modal-add-autor" class="btn btn-ghost">Cancelar</label>
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
</x-guest-layout>