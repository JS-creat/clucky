<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReactivacionClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario; // Aquí guardaremos al usuario

    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Te extrañamos! Mira lo que tenemos para ti 🎁',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reactivacion',
        );
    }
}
