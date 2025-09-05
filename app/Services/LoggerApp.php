<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LoggerApp
{
	public static function add(string $modulo, string $alteracao, $loggable = null, $detalhes = null): void
	{
		$req = request();

		Log::create([
			'data_hora'     => now(),
			'user_id'       => Auth::id(),
			'modulo'        => $modulo,
			'loggable_type' => $loggable ? get_class($loggable) : null,
			'loggable_id'   => $loggable?->id,
			'alteracao'     => $alteracao,
			'detalhes'      => is_array($detalhes) ? json_encode($detalhes, JSON_UNESCAPED_UNICODE) : $detalhes,
			'ip'            => $req?->ip(),
			'browser'       => $req?->userAgent(),
		]);
	}
}
