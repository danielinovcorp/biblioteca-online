<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encomenda extends Model
{
    protected $table = 'encomendas';

    protected $fillable = [
        'user_id',
        'morada',
        'cidade',
        'codigo_postal',
        'telefone',
        'estado',                 // pendente | paga
        'total',
        'stripe_session_id',
        'stripe_payment_intent',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /** Relations */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // muitos-para-muitos com pivot (quantidade, preco_unitario, titulo_livro)
    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'encomenda_livro')
            ->withPivot(['quantidade', 'preco_unitario', 'titulo_livro'])
            ->withTimestamps();
    }

    /** Scopes */
    public function scopePagas($q)
    {
        return $q->where('estado', 'paga');
    }

    public function scopePendentes($q)
    {
        return $q->where('estado', 'pendente');
    }

    /** Helpers */
    public function totalCalculado(): float
    {
        // recalcula a partir dos itens (útil para validações)
        return (float) $this->livros->sum(function (Livro $l) {
            return (int) $l->pivot->quantidade * (float) $l->pivot->preco_unitario;
        });
    }
}
