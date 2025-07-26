<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;


class Editora extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'logotipo'];

    public function livros()
    {
        return $this->hasMany(Livro::class);
    }
}
