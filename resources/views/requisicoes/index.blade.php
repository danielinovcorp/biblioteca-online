<x-guest-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">📚 Minhas Requisições</h2>
    </x-slot>

    <div class="p-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="mb-4">
            <a href="{{ route('requisicoes.create') }}" class="btn btn-primary">+ Nova Requisição</a>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Livro</th>
                        <th>Data Início</th>
                        <th>Previsão Fim</th>
                        <th>Status</th>
                        @if(auth()->user()->isAdmin())
                            <th>Cidadão</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisicoes as $req)
                        <tr>
                            <td>{{ $req->numero ?? 'REQ-'.$req->id }}</td>
                            <td>{{ $req->livro->nome ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($req->data_inicio)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($req->data_fim_prevista)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-{{ $req->status === 'ativa' ? 'success' : 'neutral' }}">{{ ucfirst($req->status) }}</span></td>
                            @if(auth()->user()->isAdmin())
                                <td>{{ $req->user->name ?? '-' }}</td>
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
</x-guest-layout>
