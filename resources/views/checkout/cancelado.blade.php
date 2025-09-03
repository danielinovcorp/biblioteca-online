<x-guest-layout>
    <x-slot name="heading">❌ Pagamento cancelado</x-slot>

    <div class="max-w-3xl mx-auto p-4 md:p-8">
        <div class="alert alert-warning mb-6">
            <span>Cancelaste o pagamento no Stripe. Podes tentar novamente quando quiseres.</span>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="card-actions justify-end">
                    <a href="{{ route('carrinho.index') }}" class="btn">Voltar ao carrinho</a>
                    <a href="{{ route('checkout.morada') }}" class="btn btn-primary">Tentar novamente</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
