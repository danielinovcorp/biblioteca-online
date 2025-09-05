<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
	protected $fillable = [
		'data_hora','user_id','modulo','loggable_type','loggable_id',
		'alteracao','detalhes','ip','browser'
	];

	protected $casts = [
		'data_hora' => 'datetime',
	];

	public function user() { return $this->belongsTo(\App\Models\User::class); }
	public function loggable() { return $this->morphTo(); }
}
