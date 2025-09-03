<?php

namespace App\Console\Commands;

use App\Mail\CarrinhoAbandonadoMail;
use App\Models\Carrinho;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotificarCarrinhosAbandonados extends Command
{
    protected $signature = 'carrinhos:lembrar-abandono';
    protected $description = 'Envia email 1h após inatividade do carrinho, uma vez por carrinho.';

    public function handle(): int
    {
        $limite = Carbon::now()->subHour();

        Carrinho::whereNull('abandoned_notified_at')
            ->where('updated_at', '<=', $limite)
            ->whereHas('livros') // só se ainda tem itens no carrinho
            ->with(['user','livros'])
            ->chunkById(100, function ($carrinhos) {
                foreach ($carrinhos as $c) {
                    if (!$c->user || !$c->user->email) continue;

                    Mail::to($c->user->email)->send(new CarrinhoAbandonadoMail($c));
                    $c->abandoned_notified_at = now();
                    $c->save();
                    $this->info("Enviado lembrete para carrinho #{$c->id}");
                }
            });

        return Command::SUCCESS;
    }
}
