<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;

class EventCertificate extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction, $pdfContent)
    {
        $this->transaction = $transaction;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Certificate: ' . $this->transaction->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event_certificate',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'E-Certificate-' . $this->transaction->name . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
