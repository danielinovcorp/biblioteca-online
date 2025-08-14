<?php

namespace App\Mail;

use App\Models\Livro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LivroDisponivelMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Livro $livro;

    public function __construct(Livro $livro)
    {
        $this->livro = $livro;
    }

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: '📚 Livro disponível para requisição'
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            markdown: 'emails.livros.disponivel',
            with: [
                'livro' => $this->livro,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
