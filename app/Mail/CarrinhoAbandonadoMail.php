<?php

namespace App\Mail;

use App\Models\Carrinho;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CarrinhoAbandonadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Carrinho $carrinho;

    public function __construct(Carrinho $carrinho)
    {
        $this->carrinho = $carrinho->load('livros');
    }

    public function build()
    {
        return $this->subject('👋 Precisas de ajuda com a tua compra?')
            ->markdown('emails.carrinho.abandonado', ['carrinho' => $this->carrinho]);
    }
}
