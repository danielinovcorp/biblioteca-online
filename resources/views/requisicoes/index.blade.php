<x-guest-layout>
	<style>
		.table-zebra tbody tr:nth-child(even) {
			background-color: rgb(220 220 220 / 1);
		}
	</style>

	<x-slot name="header">
		<h2 class="text-xl font-bold">📚 Minhas Requisições</h2>
	</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto">
			<div class="pt-6">
				@if (session('success'))
				<div id="success-message" class="alert alert-success">{{ session('success') }}</div>
				<script>
					setTimeout(() => {
						const alert = document.getElementById('success-message');
						if (alert) alert.style.display = 'none';
					}, 5000);
				</script>
				@endif

				@if (session('error'))
				<div id="error-message" class="alert alert-error">{{ session('error') }}</div>
				<script>
					setTimeout(() => {
						const alert = document.getElementById('error-message');
						if (alert) alert.style.display = 'none';
					}, 5000);
				</script>
				@endif


				<div class="mb-4">
					<a href="{{ route('requisicoes.create') }}" class="btn btn-primary">+ Nova Requisição</a>
				</div>

				<div class="flex flex-col sm:flex-row justify-around gap-4 mb-6 text-center">
					<div class="bg-blue-100 text-blue-800 rounded p-4 shadow w-full sm:w-1/3">
						<h4 class="font-semibold text-lg">📘 Ativas</h4>
						<p class="text-2xl font-bold">{{ $requisicoesAtivas }}</p>
					</div>

					<div class="bg-yellow-100 text-yellow-800 rounded p-4 shadow w-full sm:w-1/3">
						<h4 class="font-semibold text-lg">📅 Últimos 30 dias</h4>
						<p class="text-2xl font-bold">{{ $requisicoesUltimos30 }}</p>
					</div>

					<div class="bg-green-100 text-green-800 rounded p-4 shadow w-full sm:w-1/3">
						<h4 class="font-semibold text-lg">📦 Entregues Hoje</h4>
						<p class="text-2xl font-bold">{{ $livrosEntreguesHoje }}</p>
					</div>
				</div>

				<div class="overflow-x-auto w-full">
					<table class="table table-zebra w-full">
						<thead class="bg-gray-700 text-white font-semibold">
							<tr>
								<th>Requisição</th>
								@if(auth()->user()->isAdmin())
								<th>Cidadão</th>
								@endif
								<th>Livro</th>
								<th>Status</th>
								<th>Foto</th>
								<th>Data Início</th>
								<th>Previsão Fim</th>
								<th>Recebido em</th>
								<th>Dias Decorridos</th>
								@if(auth()->user()->isAdmin())
								<th>Ações</th>
								@endif
							</tr>
						</thead>
						<tbody>
							@forelse($requisicoes as $req)
							<tr>
								<td>{{ $req->numero ?? 'REQ-'.$req->id }}</td>
								@if(auth()->user()->isAdmin())
								<td>{{ $req->user->name ?? '-' }}</td>
								@endif
								<td>{{ $req->livro->nome ?? '-' }}</td>
								<td><span class="badge badge-{{ $req->status === 'ativa' ? 'success' : 'error' }}">{{ ucfirst($req->status) }}</span></td>
								<td>
									@if ($req->foto_cidadao)
									<a href="{{ asset('storage/' . $req->foto_cidadao) }}" target="_blank">
										<img src="{{ asset('storage/' . $req->foto_cidadao) }}"
											alt="Foto"
											class="w-14 h-14 object-cover rounded border shadow">
									</a>
									@else
									<span class="text-sm italic text-gray-400">Sem foto</span>
									@endif
								</td>
								<td>{{ \Carbon\Carbon::parse($req->data_inicio)->format('d/m/Y') }}</td>
								<td>{{ \Carbon\Carbon::parse($req->data_fim_prevista)->format('d/m/Y') }}</td>
								<td>
									{{ $req->data_fim_real ? \Carbon\Carbon::parse($req->data_fim_real)->format('d/m/Y') : '—' }}
								</td>
								<td>
									{{ $req->dias_decorridos ?? '—' }}
								</td>
								@if(auth()->user()->isAdmin())
								<td>
									@if($req->status === 'ativa')
									<form method="POST" action="{{ route('requisicoes.confirmar-devolucao', $req) }}">
										@csrf
										<button type="submit" class="btn btn-xs btn-primary"
											onclick="return confirm('Confirmar devolução do livro?')">
											Devolução
										</button>
									</form>
									@else
									<span class="text-sm text-gray-500">Devolvido</span>
									@endif
								</td>
								@endif
							</tr>
							@empty
							<tr>
								<td colspan="6">Nenhuma requisição encontrada.</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</x-guest-layout>