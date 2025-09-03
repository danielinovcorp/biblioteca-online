@component('mail::message')
# Carrinho pendente

Olá {{ $carrinho->user->name ?? '' }},

Reparamos que adicionaste livros ao carrinho há algum tempo e não concluíste a compra. Precisas de ajuda?

@component('mail::panel')
**Itens no carrinho**
@foreach($carrinho->livros as $l)
- {{ $l->nome ?? $l->titulo }} × {{ (int)$l->pivot->quantidade }}
@endforeach
@endcomponent

@component('mail::button', ['url' => route('carrinho.index')])
Ir para o carrinho
@endcomponent

Se tiveres qualquer dúvida, responde a este email.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
