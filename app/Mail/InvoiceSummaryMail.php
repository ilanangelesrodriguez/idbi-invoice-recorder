<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $successfullyProcessed;
    public $failed;

    /**
     * Create a new message instance.
     *
     * @param $successfullyProcessed
     * @param $failed
     */
    public function __construct($successfullyProcessed, $failed)
    {
        $this->successfullyProcessed = $successfullyProcessed;
        $this->failed = $failed;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->subject('Resumen de Procesamiento de Comprobantes')
            ->markdown('emails.invoice-summary')
            ->with([
                'successfullyProcessed' => $this->successfullyProcessed,
                'failed' => $this->failed,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Summary Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-summary',
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
