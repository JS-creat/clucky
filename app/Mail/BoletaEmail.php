<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoletaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    protected $pdfBinary;

    public function __construct(Pedido $pedido, $pdfBinary)
    {
        $this->pedido    = $pedido;
        $this->pdfBinary = $pdfBinary;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprobante de Pago - Pedido #' . $this->pedido->numero_pedido,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.boleta', // La plantilla HTML del correo
        );
    }

    /**
     * Adjunta el PDF generado por APIs Perú al correo
     */
    public function attachments(): array
    {
        // Si la variable no tiene contenido, no adjunta nada
        if (empty($this->pdfBinary)) {
            return [];
        }

        // Adjuntamos el archivo binario
        return [
            Attachment::fromData(
                fn() => $this->pdfBinary,
                "Boleta-B001-{$this->pedido->id_pedido}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
