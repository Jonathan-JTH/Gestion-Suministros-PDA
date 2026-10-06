<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte {{ $tipo }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            width: 595pt;
        }
        .membrete-full {
            width: 595pt;
            margin: 0;
            padding: 0;
            line-height: 0;
            overflow: hidden;
        }
        .membrete-full img {
            display: block;
            margin: 0;
            padding: 0;
        }
        .contenido {
            padding: 12pt 32pt {{ ($membrete['footerHeight'] ?? 0) + ($membrete['notaHeight'] ?? 14) + 10 }}pt;
        }
        .header {
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        h1 {
            font-size: 16px;
            margin: 0;
            color: #111827;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 8px;
            text-align: center;
            line-height: 1.45;
        }
        .section { margin-top: 12px; }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 6px;
            border-left: 3px solid #2563eb;
            padding-left: 8px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th {
            background: #1f2937;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px;
            text-align: left;
        }
        td { border: 1px solid #e5e7eb; padding: 7px 8px; }
        td.num { text-align: right; font-weight: bold; width: 80px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .chart-box {
            border: 1px solid #e5e7eb;
            padding: 6px 8px 4px;
            background: #fafafa;
            text-align: center;
        }
        .chart-box img { max-width: 100%; height: auto; }
        .pie-full {
            position: fixed;
            left: 0;
            bottom: {{ $membrete['notaHeight'] ?? 14 }}pt;
            width: 595pt;
            margin: 0;
            padding: 0;
            line-height: 0;
            overflow: hidden;
        }
        .pie-full img {
            display: block;
            margin: 0;
            padding: 0;
        }
        .nota-sistema {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 595pt;
            margin: 0;
            padding: 2pt 32pt 4pt;
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.2;
            background: #fff;
        }
    </style>
</head>
<body>
@php
    $col = $columna ?? 'Detalle';
    $totalGeneral = collect($filas)->sum('total');
    $membreteHeader = $membrete['header'] ?? '';
    $membreteFooter = $membrete['footer'] ?? '';
@endphp

@if($membreteHeader !== '')
    <div class="membrete-full">{!! $membreteHeader !!}</div>
@endif

<div class="contenido">
    <div class="header">
        <h1>{{ $tituloReporte ?? 'Reporte' }}</h1>
        <div class="meta">
            Gestión de suministros · Periodo: {{ $desde }} — {{ $hasta }}
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
</div>

@if($membreteFooter !== '')
    <div class="pie-full">{!! $membreteFooter !!}</div>
@endif

<p class="nota-sistema">Documento generado por el sistema de gestión de suministros · Uso interno Fabrigas</p>
</body>
</html>
