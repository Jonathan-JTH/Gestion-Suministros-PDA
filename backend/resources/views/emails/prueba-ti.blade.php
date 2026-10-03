<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de notificación TI</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h2>Prueba de correo — Informática</h2>

    <p>Este mensaje confirma que el sistema <strong>Gestión de Suministros</strong> puede enviar correos a
        <strong>{{ $destino }}</strong> usando SMTP.</p>

    <p>En producción, aquí recibirán avisos como:</p>
    <ul>
        <li><strong>Nueva solicitud de suministro</strong> (cuando una sucursal registra un pedido).</li>
        <li><strong>Copia TI: solicitud atendida</strong> (cuando soporte despacha materiales).</li>
    </ul>

    <p style="background: #f5f5f5; padding: 12px; border-left: 4px solid #0d6efd;">
        <strong>Ejemplo de asunto real:</strong> Nueva solicitud de suministro — Sucursal Centro (#1)
    </p>

    <p style="color: #666; font-size: 12px;">
        Prueba enviada el {{ $fecha }} · Remitente configurado en MAIL_FROM · Gestión de Suministros (Grupo Fabrigas).
    </p>
</body>
</html>
