<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PruebaNotificacionTiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $destino,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prueba TI — Gestión de Suministros',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prueba-ti',
            with: [
                'fecha' => now()->format('d/m/Y H:i:s'),
            ],
        );
    }
}
