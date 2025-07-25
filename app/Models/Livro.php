<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;


class Livro extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
    ];

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class);
    }

    public function requisicoes()
    {
        return $this->hasMany(Requisicao::class);
    }

    public function setBibliografiaAttribute($value)
    {
        $this->attributes['bibliografia'] = Crypt::encryptString($value);
    }

    public function getBibliografiaAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}
