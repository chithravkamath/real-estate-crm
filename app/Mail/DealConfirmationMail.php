<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $deal;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\Deal $deal, string $type)
    {
        $this->deal = $deal;
        $this->type = $type; // 'booking' or 'sale'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $action = $this->type === 'booking' ? 'Booking Confirmed' : 'Sale Confirmed';
        return new Envelope(
            subject: 'Deal Confirmation: ' . $action . ' for ' . $this->deal->property_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.deal-confirmed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
