<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Review;

class ReviewStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Review $review;

    public function __construct(Review $review)
    {
        // Garanta que relações necessárias estejam carregadas
        $review->loadMissing(['user', 'livro']);
        $this->review = $review;
    }

    public function build()
    {
        $assunto = $this->review->status === 'ativo'
            ? '✅ Sua review foi ativada'
            : '❌ Sua review foi recusada';

        return $this->subject($assunto)
            ->markdown('emails.reviews.status_changed');
    }
}
