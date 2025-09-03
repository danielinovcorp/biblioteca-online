<x-guest-layout>
    <x-slot name="heading">🎉 Pagamento</x-slot>

    <div class="max-w-3xl mx-auto p-4 md:p-8">
        @if($status === 'paga')
            <div class="alert alert-success mb-6">
                <span>Pagamento confirmado! A tua encomenda foi marcada como <b>paga</b>.</span>
            </div>
        @else
            <div class="alert alert-info mb-6">
                <span>Recebemos o retorno do Stripe. Se o pagamento foi concluído, a encomenda será marcada como <b>paga</b> nos próximos instantes.</span>
            </div>
        @endif

        @if($encomenda)
            <div class="card bg-base-100 shadow">
                <div class="card-body bg-white">
                    <h2 class="card-title">Encomenda #{{ $encomenda->id }}</h2>
                    <p>Estado: <span class="badge {{ $encomenda->estado === 'paga' ? 'badge-success' : 'badge-ghost' }}">{{ $encomenda->estado }}</span></p>
                    <p>Total: <b>{{ number_format($encomenda->total,2,',','.') }} €</b></p>

                    <div class="card-actions justify-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">Voltar à página inicial</a>
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Obrigado!</h2>
                    <p>Irás receber confirmação por email assim que a transação for validada.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('home') }}" class="btn btn-primary">Voltar à página inicial</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-guest-layout>
