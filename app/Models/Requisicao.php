<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LogsModelEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisicao extends Model
{
	use HasFactory, LogsModelEvents;
	protected static string $MODULE_NAME = 'Requisicoes';
	protected $table = 'requisicoes';
	
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
