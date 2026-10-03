<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva solicitud</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h2>Nueva solicitud de suministro</h2>

    <p>Se registró una solicitud en el sistema <strong>Gestión de Suministros</strong>.</p>

    <ul>
        <li><strong>Número:</strong> #{{ $solicitud->id }}</li>
        <li><strong>Sucursal:</strong> {{ $solicitud->sucursal?->nombre ?? '—' }}</li>
        <li><strong>Solicitante:</strong> {{ $solicitud->nombreSolicitanteMostrar() }}</li>
        <li><strong>Correo solicitante:</strong> {{ $solicitud->correoSolicitanteMostrar() ?? '—' }}</li>
        @if($solicitud->tipo_movimiento)
            <li><strong>Tipo:</strong> {{ $solicitud->tipo_movimiento }}</li>
        @endif
        @if($solicitud->ubicacion)
            <li><strong>Ubicación:</strong> {{ $solicitud->ubicacion }}</li>
        @endif
        @if($solicitud->modelo_equipo)
            <li><strong>Modelo equipo:</strong> {{ $solicitud->modelo_equipo }}</li>
        @endif
        <li><strong>Fecha:</strong> {{ $solicitud->created_at?->format('d/m/Y H:i') }}</li>
        <li><strong>Estado:</strong> {{ str_replace('_', ' ', $solicitud->estado) }}</li>
    </ul>

    <h3>Detalle</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Suministro</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($solicitud->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->suministro?->nombre ?? '—' }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($solicitud->observacion)
        <p><strong>Observaciones:</strong> {{ $solicitud->observacion }}</p>
    @endif

    <p style="color: #666; font-size: 12px;">Correo automático — no responder.</p>
</body>
</html>
