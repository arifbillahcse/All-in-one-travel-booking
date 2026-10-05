<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Tells the team about a new booking request or contact message. Always in English. */
class InquiryReceived extends Mailable
{
    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        $i = $this->inquiry;

        return new Envelope(
            subject: $i->type === Inquiry::TYPE_BOOKING
                ? "New booking request: {$i->name} – ".($i->destination?->getTranslation('name', 'en') ?? 'trip')
                : "New message: {$i->name} – {$i->topic}",
            replyTo: $i->email ? [$i->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inquiry');
    }
}
