<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivroKeyword extends Model
{
    protected $fillable = ['livro_id','keyword','tf','weight'];

    public function livro() { return $this->belongsTo(Livro::class); }
}