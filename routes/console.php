<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// executa o comando de lembrete a cada 10 min (timezone PT)
Schedule::command('carrinhos:lembrar-abandono')
    ->everyMinute()
    ->timezone('Europe/Lisbon');
