<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
	use HasFactory;

	protected $fillable = [
		'livro_id',
		'requisicao_id',
		'user_id',
		'rating',
		'comentario',
		'status',
		'justificativa',
	];

	public function livro()
	{
		return $this->belongsTo(Livro::class);
	}

		public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function requisicao()
	{
		return $this->belongsTo(Requisicao::class);
	}

	// Escopos úteis
	public function scopeAtivos($q)
	{
		return $q->where('status','ativo');
	}
	public function scopeSuspensos($q)
	{
		return $q->where('status','suspenso');
	}
}
