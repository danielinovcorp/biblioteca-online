<x-guest-layout>
    <x-slot name="heading">📦 Encomenda #{{ $encomenda->id }}</x-slot>

    <div class="max-w-7x1 mx-auto p-4 md:p-8 space-y-6">
        <div class="stats shadow w-full bg-white">
            <div class="stat">
                <div class="stat-title text-neutral">Utilizador</div>
                <div class="stat-value text-lg">{{ $encomenda->user->name ?? '-' }}</div>
                <div class="stat-desc text-neutral">{{ $encomenda->user->email ?? '-' }}</div>
            </div>
            <div class="stat">
                <div class="stat-title text-neutral">Estado</div>
                <div class="stat-value text-lg">
                    <span class="badge {{ $encomenda->estado==='paga' ? 'badge-success' : 'badge-ghost' }}">
                        {{ ucfirst($encomenda->estado) }}
                    </span>
                </div>
                <div class="stat-desc text-neutral">{{ $encomenda->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="stat">
                <div class="stat-title text-neutral">Total</div>
                <div class="stat-value">{{ number_format($encomenda->total,2,',','.') }} €</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="card bg-base-100 shadow bg-white">
                <div class="card-body">
                    <h2 class="card-title">Morada</h2>
                    <p>{{ $encomenda->morada }}</p>
                    <p>{{ $encomenda->codigo_postal }} {{ $encomenda->cidade }}</p>
                    <p>{{ $encomenda->telefone }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow bg-white">
                <div class="card-body">
                    <h2 class="card-title">Stripe</h2>
                    <p><b>Session:</b> {{ $encomenda->stripe_session_id ?? '-' }}</p>
                    <p><b>Payment Intent:</b> {{ $encomenda->stripe_payment_intent ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-base-100 rounded-box shadow bg-white">
            <table class="table table-zebra">
                <thead class="bg-gray-700 text-white font-semibold">
                    <tr>
                        <th>Livro</th>
                        <th class="text-center">Qtd</th>
                        <th class="text-right">Preço</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($encomenda->livros as $livro)
                        @php
                            $q   = (int) $livro->pivot->quantidade;
                            $p   = (float) $livro->pivot->preco_unitario;
                            $sub = $q * $p;
                        @endphp
                        <tr class="hover text-neutral odd:bg-gray-100 even:bg-white">
                            <td>{{ $livro->pivot->titulo_livro ?: ($livro->nome ?? $livro->titulo) }}</td>
                            <td class="text-center">{{ $q }}</td>
                            <td class="text-right">{{ number_format($p,2,',','.') }} €</td>
                            <td class="text-right">{{ number_format($sub,2,',','.') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-right">
            <a href="{{ route('admin.encomendas.index') }}" class="btn">Voltar</a>
        </div>
    </div>
</x-guest-layout>
