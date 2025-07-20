<x-guest-layout>
	<x-slot name="heading">
		🏢 Editoras
	</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8">
			
			{{-- FORMULÁRIO DE EXPORTAÇÃO --}}
			<form method="GET" action="{{ route('editoras.export') }}" id="export-form" class="flex flex-col items-center gap-4 mb-6">
				<input type="hidden" name="ids" id="selected-ids" />
				<input type="hidden" name="q" value="{{ request('q') }}">
				<input type="hidden" name="sort" value="{{ request('sort') }}">
				<input type="hidden" name="direction" value="{{ request('direction') }}">

				<button type="submit" class="btn btn-success">📥 Exportar Excel</button>
			</form>

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
			<table class="table w-full">
				<thead class="bg-gray-700 text-white font-semibold">
					<tr>
						<th><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
						<th>Logótipo</th>
						<th>{!! sort_link_editora('nome', 'Nome', $sortField, $sortDirection) !!}</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($editoras as $editora)
						<tr class="hover text-neutral odd:bg-gray-200 even:bg-white" data-id="{{ $editora->id }}">
							<td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" /></td>
							<td>
								<img src="{{ $editora->logotipo }}"
									alt="Logótipo de {{ $editora->nome }}"
									class="w-[40px] h-[40px] rounded object-contain shadow" />
							</td>
							<td class="font-medium">{{ $editora->nome }}</td>
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
