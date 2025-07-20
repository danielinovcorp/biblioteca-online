<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisicao extends Model
{
    protected $fillable = [
        'livro_id', 'user_id', 'numero', 'status',
        'foto_cidadao', 'data_inicio', 'data_fim_prevista', 'data_fim_real'
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
