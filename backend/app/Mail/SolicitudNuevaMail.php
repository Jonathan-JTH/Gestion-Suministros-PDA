<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudNuevaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Solicitud $solicitud) {}

    public function envelope(): Envelope
    {
        $sucursal = $this->solicitud->sucursal?->nombre ?? 'Sucursal';

        return new Envelope(
            subject: 'Nueva solicitud de suministro — ' . $sucursal,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-nueva',
        );
    }
}
