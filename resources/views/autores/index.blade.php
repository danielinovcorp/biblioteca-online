<x-guest-layout>
	<x-slot name="heading">
		👤 Autores
	</x-slot>

	<div class="overflow-x-auto w-[1300px] mx-auto px-6 py-2">
		<div class="bg-white shadow rounded-lg p-5 w-full overflow-x-auto">
			
			{{-- FORMULÁRIO DE EXPORTAÇÃO --}}
			<form method="GET" action="{{ route('autores.export') }}" id="export-form" class="mb-6">
				{{-- Checkboxes selecionadas --}}
				<input type="hidden" name="ids" id="selected-ids" />

				{{-- Campo de pesquisa --}}
				<input type="hidden" name="q" value="{{ request('q') }}">
				<input type="hidden" name="sort" value="{{ request('sort') }}">
				<input type="hidden" name="direction" value="{{ request('direction') }}">

				<button type="submit" class="btn btn-success">📥 Exportar Excel</button>
			</form>


			@php
			function sort_link_autor($field, $label, $currentField, $currentDirection) {
				$newDirection = ($currentField === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
				$icon = $currentField === $field ? ($currentDirection === 'asc' ? '▲' : '▼') : '';
				$query = request()->except(['page', 'sort', 'direction']);
				$query['sort'] = $field;
				$query['direction'] = $newDirection;
				$url = route('autores.index', $query);
				return "<a href='{$url}' class='hover:underline font-medium'>{$label} {$icon}</a>";
			}
			@endphp

			{{-- TABELA DE AUTORES --}}
			<table class="table w-full">
				<thead class="bg-gray-700 text-white font-semibold">
					<tr>
						<th><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
						<th>{!! sort_link_autor('nome', 'Nome', $sortField, $sortDirection) !!}</th>
						<th>Foto</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($autores as $autor)
						<tr class="hover text-neutral" data-id="{{ $autor->id }}">
							<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" /></td>
							<td>
								<div class="avatar">
									<div class="mask mask-squircle h-12 w-12">
										<img src="{{ $autor->foto }}" alt="Foto de {{ $autor->nome }}" />
									</div>
								</div>
							</td>
							<td class="font-medium">{{ $autor->nome }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>

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
