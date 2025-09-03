<?php

namespace App\Mail;

use App\Models\Encomenda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EncomendaPagaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Encomenda $encomenda;

    public function __construct(Encomenda $encomenda)
    {
        $this->encomenda = $encomenda;
    }

    public function build()
    {
        return $this->subject('🎉 Pagamento confirmado - Encomenda #' . $this->encomenda->id)
            ->markdown('emails.encomendas.paga', ['encomenda' => $this->encomenda]);
    }
}
