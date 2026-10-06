<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte {{ $tipo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 28px; }
        .header { border-bottom: 2px solid #1f2937; padding-bottom: 10px; margin-bottom: 18px; }
        .brand { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        h1 { font-size: 18px; margin: 4px 0 0; color: #111827; }
        .meta { font-size: 10px; color: #64748b; margin-top: 6px; }
        .section { margin-top: 16px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; margin-bottom: 8px; border-left: 3px solid #2563eb; padding-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th { background: #1f2937; color: #fff; font-size: 10px; text-transform: uppercase; padding: 8px; text-align: left; }
        td { border: 1px solid #e5e7eb; padding: 7px 8px; }
        td.num { text-align: right; font-weight: bold; width: 80px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .chart-box { border: 1px solid #e5e7eb; padding: 12px; background: #fafafa; text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
@php
    $col = $columna ?? 'Detalle';
    $totalGeneral = collect($filas)->sum('total');
@endphp

<div class="header">
    <div class="brand">Grupo Fabrigas · Gestión de Suministros</div>
    <h1>{{ $tituloReporte ?? 'Reporte' }}</h1>
    <div class="meta">
        Periodo: {{ $desde }} — {{ $hasta }}
        · Gráfica: {{ $nombreGrafico ?? 'Barras' }}
        · Generado: {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<div class="section">
    <div class="section-title">Gráfica — {{ $nombreGrafico ?? '' }}</div>
    <div class="chart-box">
        {!! $graficoSvg ?? '' !!}
    </div>
</div>

<div class="section">
    <div class="section-title">Detalle en tabla</div>
    <table>
        <thead>
            <tr>
                <th>{{ $col }}</th>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $f)
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
                    <td class="num">{{ $f->total }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Sin datos en el periodo seleccionado.</td></tr>
            @endforelse
            @if($filas->isNotEmpty())
                <tr>
                    <td style="font-weight:bold;background:#f1f5f9;">Total general</td>
                    <td class="num" style="background:#f1f5f9;">{{ $totalGeneral }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="footer">
    Documento generado por el sistema de gestión de suministros · Uso interno Fabrigas
</div>
</body>
</html>
