@component('mail::message')
# ⏰ Lembrete de Devolução

Olá {{ $requisicao->user->name }},

Este é um lembrete de que você deve devolver o livro **{{ $requisicao->livro->nome }}** até **{{ \Carbon\Carbon::parse($requisicao->data_fim_prevista)->format('d/m/Y') }}**.

@component('mail::panel')
<img src="{{ asset('storage/' . $requisicao->livro->imagem_capa) }}" alt="Capa do Livro" width="120">
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
