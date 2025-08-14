<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;


class Livro extends Model
{
	use HasFactory;

	protected $table = 'livros';

	protected $fillable = [
		'isbn',
		'nome',
		'editora_id',
		'bibliografia',
		'imagem_capa',
		'preco',
		'disponivel',
	];

	protected $appends = ['disponivel'];

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
	public function reviews()
	{ 
		return $this->hasMany(Review::class); 
	}

	public function getDisponivelAttribute()
	{
		return !$this->requisicoes()
			->where('status', 'ativa')
			->exists();
	}

	public function alertas()
	{
		return $this->hasMany(\App\Models\AlertaDisponibilidade::class);
	}
}
