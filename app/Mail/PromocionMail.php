<?php

namespace App\Mail;

use App\Models\Producto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromocionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Producto $producto) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '🔥 ¡Nueva oferta en B-EDEN!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.promocion');
    }
}
