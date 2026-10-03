<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Solicitud $solicitud,
        public string $titulo,
        public string $mensaje,
        public ?string $notaAdicional = null,
    ) {}

    public function envelope(): Envelope
    {
        $sucursal = $this->solicitud->sucursal?->nombre ?? 'Sucursal';

        return new Envelope(
            subject: $this->titulo . ' — ' . $sucursal . ' (#' . $this->solicitud->id . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-estado',
        );
    }
}
