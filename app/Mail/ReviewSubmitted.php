<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReviewSubmitted extends Mailable
{
    public function __construct(public Review $review)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New review to moderate: '.$this->review->getTranslation('name', 'en'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.review');
    }
}
