@component('mail::message')
# Confirmação de Requisição

Olá {{ $requisicao->user->name }},

Sua requisição do livro **{{ $requisicao->livro->nome }}** foi registrada com sucesso.

📅 **Início:** {{ \Carbon\Carbon::parse($requisicao->data_inicio)->format('d/m/Y') }}  
📅 **Devolução prevista:** {{ \Carbon\Carbon::parse($requisicao->data_fim_prevista)->format('d/m/Y') }}

@component('mail::panel')
<img src="{{ asset('storage/' . $requisicao->livro->imagem_capa) }}" alt="Capa do Livro" width="120">
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
