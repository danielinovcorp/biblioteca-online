<?php

namespace App\Services;

class Ua
{
	public static function label(?string $ua): string
	{
		if (!$ua) return '—';

		// SO (bem simples; adicione os que quiser)
		$os = '';
		if (str_contains($ua, 'Windows NT 10.0')) $os = 'Windows 10';
		elseif (str_contains($ua, 'Windows NT 11.0')) $os = 'Windows 11';
		elseif (str_contains($ua, 'Mac OS X')) $os = 'macOS';
		elseif (str_contains($ua, 'Android')) $os = 'Android';
		elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';

		// Browser + versão
		$browser = 'Outro'; $ver = '';
		if (preg_match('~Edg/([\d\.]+)~', $ua, $m)) { $browser = 'Edge';    $ver = $m[1]; }
		elseif (preg_match('~OPR/([\d\.]+)~', $ua, $m)) { $browser = 'Opera';   $ver = $m[1]; }
		elseif (preg_match('~Chrome/([\d\.]+)~', $ua, $m)) { $browser = 'Chrome';  $ver = $m[1]; }
		elseif (preg_match('~Firefox/([\d\.]+)~', $ua, $m)) { $browser = 'Firefox'; $ver = $m[1]; }
		elseif (preg_match('~Version/([\d\.]+).*Safari/~', $ua, $m)) { $browser = 'Safari';  $ver = $m[1]; }

		$verShort = $ver ? explode('.', $ver)[0] : '';
		return trim($browser . ($verShort ? ' ' . $verShort : '') . ($os ? " on $os" : ''));
	}
}
