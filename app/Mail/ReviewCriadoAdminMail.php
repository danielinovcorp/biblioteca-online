<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewCriadoAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review->loadMissing([
            'livro:id,nome',
            'user:id,name',
            'requisicao:id,numero',
        ]);
    }

    public function build()
    {
        $urlModeracao = url('/admin/reviews?status=suspenso');

        return $this->subject('⭐ Nova review aguardando moderação')
            ->view('emails.reviews.criado_admin')
            ->with([
                'review'       => $this->review,
                'urlModeracao' => $urlModeracao,
            ]);
    }
}
