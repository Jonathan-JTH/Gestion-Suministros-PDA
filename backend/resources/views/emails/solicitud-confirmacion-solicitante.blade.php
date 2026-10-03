<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de solicitud</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h2>Solicitud registrada</h2>
    <p>Hola {{ $solicitud->nombre_solicitante ?? 'estimado usuario' }},</p>
    <p>Su solicitud de suministro fue registrada correctamente.</p>
    <ul>
        <li><strong>Número:</strong> #{{ $solicitud->id }}</li>
        <li><strong>Tipo:</strong> {{ $solicitud->tipo_movimiento }}</li>
        <li><strong>Sucursal:</strong> {{ $solicitud->sucursal?->nombre ?? '—' }}</li>
        <li><strong>Estado:</strong> {{ str_replace('_', ' ', $solicitud->estado) }}</li>
    </ul>
    <p>Para consultar el avance, use el portal con su número de solicitud y este correo.</p>
    <p style="color: #666; font-size: 12px;">Correo automático — no responder.</p>
</body>
</html>
