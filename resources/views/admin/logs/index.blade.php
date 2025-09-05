@php
    use Illuminate\Support\Str;
	use App\Services\Ua;
@endphp

<x-guest-layout>
	<x-slot name="heading">🧾 Logs do Sistema</x-slot>

	<div class="w-full mx-auto px-6">
		<div class="mb-4">
			<form method="GET" class="flex flex-wrap gap-2 items-end">
				<input type="date" name="dia" value="{{ request('dia') }}" class="input input-bordered bg-gray-700 text-white" />
				<input type="text" name="modulo" value="{{ request('modulo') }}" placeholder="Módulo" class="input input-bordered bg-white" />
				<input type="text" name="alteracao" value="{{ request('alteracao') }}" placeholder="Alteração" class="input input-bordered bg-white" />
				<input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="User ID" class="input input-bordered w-32 bg-white" />
				<button class="btn btn-primary">Filtrar</button>
				<a href="{{ route('admin.logs.index') }}" class="btn btn-ghost">Limpar</a>
			</form>
		</div>

		<div class="overflow-x-auto bg-base-100 rounded-xl shadow">
			<table class="table w-full">
				<thead class="bg-gray-700 text-white font-semibold">
					<tr>
						<th>Data/Hora</th>
						<th>User</th>
						<th>Módulo</th>
						<th>Objeto</th>
						<th>Alteração</th>
						<th>IP</th>
						<th>Browser</th>
					</tr>
				</thead>
				<tbody class="bg-white">
					@forelse($logs as $log)
						<tr class="hover text-neutral odd:bg-gray-100 even:bg-white">
							<td>{{ $log->data_hora?->timezone(config('app.timezone'))->format('d/m/Y - H:i:s') ?? '—' }}</td>
							<td>{{ $log->user?->name }} (#{{ $log->user_id ?? '—' }})</td>
							<td>{{ $log->modulo }}</td>
							<td>{{ class_basename($log->loggable_type) }} #{{ $log->loggable_id }}</td>
							<td>
								<div class="badge badge-outline badge-primary">{{ $log->alteracao }}</div>
								@if($log->detalhes)
									<details class="mt-1">
										<summary class="cursor-pointer text-sm link">detalhes</summary>
										<pre class="text-xs whitespace-pre-wrap">{{ Str::limit($log->detalhes, 1000) }}</pre>
									</details>
								@endif
							</td>
							<td>{{ $log->ip }}</td>
							<td class="max-w-[280px] truncate" title="{{ $log->browser }}">{{ Ua::label($log->browser) }}</td>
						</tr>
					@empty
						<tr><td colspan="7" class="text-center py-8">Sem logs.</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="mt-4">{{ $logs->links() }}</div>
	</div>
</x-guest-layout>
