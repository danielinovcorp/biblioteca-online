<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LogsModelEvents;



class AlertaDisponibilidade extends Model
{
	use HasFactory, LogsModelEvents;

	protected static string $MODULE_NAME = 'Alertas';

	protected $fillable = ['user_id', 'livro_id'];
	protected $table = 'alertas_disponibilidade';

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function livro()
	{
		return $this->belongsTo(Livro::class);
	}
}
