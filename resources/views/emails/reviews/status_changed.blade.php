@php
    $isAtivo = $review->status === 'ativo';
    $comentario = trim(strip_tags((string)($review->comentario ?? '')));
@endphp

<x-mail::message>
# Olá, {{ $review->user->name }}!

Recebemos uma atualização sobre a sua review do livro **{{ $review->livro->nome ?? $review->livro->titulo ?? '—' }}**.

@if ($isAtivo)
A sua review foi **ativada** e já aparece na página do livro para outros utilizadores.
@else
A sua review foi **recusada**.<br>
@if (isset($review->justificativa) && trim($review->justificativa) !== '')
**Motivo da Recusa:** {{ $review->justificativa }}
@endif
@endif

---

**Resumo da sua review:**
> {{ \Illuminate\Support\Str::limit($comentario, 200) ?: '—' }}

<x-mail::button :url="route('livros.index')">
Ver livros
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
