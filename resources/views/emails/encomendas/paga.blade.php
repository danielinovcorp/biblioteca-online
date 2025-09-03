@component('mail::message')
# Pagamento confirmado

Olá {{ $encomenda->user->name ?? '' }},

Recebemos o teu pagamento com sucesso. A tua encomenda **#{{ $encomenda->id }}** foi marcada como **paga**.

**Total:** {{ number_format($encomenda->total,2,',','.') }} €  
**Data:** {{ $encomenda->created_at->format('d/m/Y H:i') }}

@component('mail::panel')
**Morada de entrega**  
{{ $encomenda->morada }}  
{{ $encomenda->codigo_postal }} {{ $encomenda->cidade }}
@endcomponent

@component('mail::table')
| Livro | Qtd | Preço | Subtotal |
|:----- |:--:| -----:| -------:|
@foreach($encomenda->livros as $l)
| {{ $l->pivot->titulo_livro ?: ($l->nome ?? $l->titulo) }} | {{ (int)$l->pivot->quantidade }} | {{ number_format($l->pivot->preco_unitario,2,',','.') }} € | {{ number_format($l->pivot->preco_unitario * (int)$l->pivot->quantidade,2,',','.') }} € |
@endforeach
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
