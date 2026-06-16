<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoRecibido extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $email,
        public string $asunto,
        public string $mensaje,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->email],
            subject: $this->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.contacto-recibido',
        );
    }
}
