<!doctype html>
<html>
  <body style="font-family:Arial,Helvetica,sans-serif;line-height:1.5">
    <h2>Nova review aguardando moderação</h2>

    <p><strong>Livro:</strong> {{ $review->livro->nome ?? '—' }}</p>
    <p><strong>Cidadão:</strong> {{ $review->user->name ?? '—' }}</p>
    <p><strong>Requisição:</strong> {{ $review->requisicao->numero ?? ('ID '.$review->requisicao_id) }}</p>
    <p><strong>Avaliação:</strong> {{ $review->rating }} / 5</p>

    @if(!empty($review->comentario))
      <p><strong>Comentário:</strong><br>{{ $review->comentario }}</p>
    @endif

    <p style="margin-top:16px">
      <a href="{{ $urlModeracao }}" style="background:#3b82f6;color:#fff;padding:10px 14px;text-decoration:none;border-radius:6px">
        Rever e Moderar
      </a>
    </p>

    <p style="margin-top:24px;color:#555">— {{ config('app.name') }}</p>
  </body>
</html>
