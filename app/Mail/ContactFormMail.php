<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $data
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@carlosortega.dev'),
                config('mail.from.name', 'Portfolio Contact Form')
            ),
            replyTo: [
                new Address($this->data['email'], $this->data['name']),
            ],
            subject: '[Portfolio] ' . $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-form',
            with: [
                'name' => $this->data['name'],
                'email' => $this->data['email'],
                'subject' => $this->data['subject'],
                'messageContent' => $this->data['message'],
                'ipAddress' => $this->data['ip_address'] ?? 'N/A',
                'userAgent' => $this->data['user_agent'] ?? 'N/A',
                'submittedAt' => $this->data['submitted_at'] ?? now()->toDateTimeString(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
