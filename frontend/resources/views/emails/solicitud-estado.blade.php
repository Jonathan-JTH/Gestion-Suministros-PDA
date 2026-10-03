<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización de solicitud</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h2>{{ $titulo }}</h2>

    <p>{{ $mensaje }}</p>

    <ul>
        <li><strong>Número:</strong> #{{ $solicitud->id }}</li>
        <li><strong>Sucursal:</strong> {{ $solicitud->sucursal?->nombre ?? '—' }}</li>
        <li><strong>Solicitante:</strong> {{ $solicitud->usuario?->nombre ?? '—' }}</li>
        <li><strong>Estado actual:</strong>
            @if($solicitud->estado === 'atendida')
                Atendida (solicitud realizada)
            @else
                {{ str_replace('_', ' ', $solicitud->estado) }}
            @endif
        </li>
        <li><strong>Actualizado:</strong> {{ $solicitud->updated_at?->format('d/m/Y H:i') }}</li>
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
        <p><strong>Observaciones de la solicitud:</strong> {{ $solicitud->observacion }}</p>
    @endif

    @if(!empty($notaAdicional))
        <p><strong>Nota del área de TI:</strong> {{ $notaAdicional }}</p>
    @endif

    <p style="color: #666; font-size: 12px;">Correo automático — Gestión de Suministros (Grupo Fabrigas).</p>
</body>
</html>
