<x-mail::message>
# 📚 Livro disponível para requisição!

O livro **{{ $livro->nome }}** agora está disponível para requisição.

<x-mail::panel>
**ISBN:** {{ $livro->isbn ?? '—' }}  
**Editora:** {{ $livro->editora->nome ?? '—' }}
</x-mail::panel>

<x-mail::button :url="route('requisicoes.create', ['livro_id' => $livro->id])">
Requisitar agora
</x-mail::button>

Se já não tiver interesse, pode ignorar este e-mail.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
