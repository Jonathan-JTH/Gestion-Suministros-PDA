<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Reporte</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px}</style>
</head>
<body>
@php
    $titulos = [
        'consumo' => 'consumo por suministro',
        'solicitudes' => 'solicitudes por estado',
        'sucursal' => 'solicitudes por sucursal',
        'movimientos' => 'movimientos de inventario',
        'inventario_bajo' => 'inventario bajo stock mínimo',
    ];
    $col = $columna ?? 'Detalle';
@endphp
<h2>Reporte de {{ $titulos[$tipo] ?? $tipo }}</h2>
<p>Periodo: {{ $desde }} — {{ $hasta }}</p>
<table>
    <thead><tr><th>{{ $col }}</th><th>Total</th></tr></thead>
    <tbody>
        @foreach($filas as $f)
            <tr>
                <td>
                    @if($tipo === 'solicitudes')
                        {{ str_replace('_', ' ', $f->estado) }}
                    @elseif($tipo === 'movimientos')
                        {{ $f->tipo }}
                    @else
                        {{ $f->nombre }}
                    @endif
                </td>
                <td>{{ $f->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
