<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Requisicao;
use App\Mail\RequisicaoLembrete;
use Illuminate\Support\Facades\Mail;

class EnviarReminderRequisicoes extends Command
{
    protected $signature = 'requisicoes:lembrete';
    protected $description = 'Envia lembrete aos cidadãos com livros a devolver amanhã';

    public function handle()
    {
        $amanha = now()->addDay()->toDateString();

        $requisicoes = Requisicao::where('status', 'ativa')
            ->where('data_fim_prevista', $amanha)
            ->with('user', 'livro')
            ->get();

        foreach ($requisicoes as $req) {
            Mail::to($req->user->email)->send(new RequisicaoLembrete($req));
        }

        $this->info("Lembretes enviados com sucesso.");
    }
}
