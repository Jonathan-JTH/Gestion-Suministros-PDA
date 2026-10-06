<?php

namespace App\Support;

class ReporteGraficoSvg
{
    /** @param  array<int, string>  $labels
     * @param  array<int, int|float>  $values
     */
    public static function render(string $estilo, array $labels, array $values, int $width = 520, int $height = 240): string
    {
        if ($values === [] || array_sum($values) <= 0) {
            return '<p style="color:#64748b;font-size:11px;">Sin datos suficientes para la gráfica.</p>';
        }

        return match ($estilo) {
            'line' => self::lineChart($labels, $values, $width, $height),
            'pie' => self::pieChart($labels, $values, $width, $height, false),
            'doughnut' => self::pieChart($labels, $values, $width, $height, true),
            'horizontal' => self::horizontalBarChart($labels, $values, $width, $height),
            default => self::barChart($labels, $values, $width, $height),
        };
    }

    /** DomPDF renderiza mejor la gráfica como imagen embebida que SVG inline. */
    public static function renderForPdf(string $estilo, array $labels, array $values, int $width = 520, int $height = 240): string
    {
        $svg = self::render($estilo, $labels, $values, $width, $height);
        if (! str_contains($svg, '<svg')) {
            return $svg;
        }

        $encoded = base64_encode($svg);

        return sprintf(
            '<img src="data:image/svg+xml;base64,%s" width="%d" alt="Gráfica del reporte" style="display:block;margin:0 auto;max-width:100%%;height:auto;" />',
            $encoded,
            $width
        );
    }

    /** @param  array<int, string>  $labels
     * @param  array<int, int|float>  $values
     */
    private static function barChart(array $labels, array $values, int $width, int $height): string
    {
        $max = max($values) ?: 1;
        $count = count($values);
        $pad = 36;
        $chartW = $width - $pad * 2;
        $chartH = $height - 50;
        $gap = 8;
        $barW = max(12, ($chartW - ($count - 1) * $gap) / max(1, $count));
        $bars = '';
        foreach ($values as $i => $v) {
            $h = (float) $v / $max * ($chartH - 10);
            $x = $pad + $i * ($barW + $gap);
            $y = $pad + ($chartH - $h);
            $label = self::truncate($labels[$i] ?? '', 12);
            $bars .= sprintf(
                '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="#2563eb" rx="2"/>',
                $x,
                $y,
                $barW,
                $h
            );
            $bars .= sprintf(
                '<text x="%.1f" y="%d" font-size="8" fill="#475569" text-anchor="middle">%s</text>',
                $x + $barW / 2,
                $height - 8,
                htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            );
            $bars .= sprintf(
                '<text x="%.1f" y="%.1f" font-size="8" fill="#111827" text-anchor="middle">%s</text>',
                $x + $barW / 2,
                max($y - 4, $pad),
                (int) $v
            );
        }

        return self::wrap($width, $height, $bars.sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#cbd5e1"/>', $pad, $pad + $chartH, $width - $pad, $pad + $chartH));
    }

    /** @param  array<int, string>  $labels
     * @param  array<int, int|float>  $values
     */
    private static function horizontalBarChart(array $labels, array $values, int $width, int $height): string
    {
        $max = max($values) ?: 1;
        $rowH = min(28, max(18, (int) (($height - 20) / max(1, count($values)))));
        $labelW = 140;
        $barMaxW = $width - $labelW - 50;
        $content = '';
        foreach ($values as $i => $v) {
            $y = 10 + $i * $rowH;
            $bw = (float) $v / $max * $barMaxW;
            $content .= sprintf(
                '<text x="4" y="%.1f" font-size="9" fill="#334155">%s</text>',
                $y + $rowH * 0.65,
                htmlspecialchars(self::truncate($labels[$i] ?? '', 22), ENT_QUOTES, 'UTF-8')
            );
            $content .= sprintf(
                '<rect x="%d" y="%.1f" width="%.1f" height="%.1f" fill="#059669" rx="2"/>',
                $labelW,
                $y + 4,
                $bw,
                $rowH - 8
            );
            $content .= sprintf(
                '<text x="%.1f" y="%.1f" font-size="9" fill="#111827">%s</text>',
                $labelW + $bw + 6,
                $y + $rowH * 0.65,
                (int) $v
            );
        }
        $h = max($height, 10 + count($values) * $rowH);

        return self::wrap($width, $h, $content);
    }

    /** @param  array<int, string>  $labels
     * @param  array<int, int|float>  $values
     */
    private static function lineChart(array $labels, array $values, int $width, int $height): string
    {
        $max = max($values) ?: 1;
        $count = count($values);
        $pad = 36;
        $chartW = $width - $pad * 2;
        $chartH = $height - 50;
        $points = [];
        foreach ($values as $i => $v) {
            $x = $count === 1 ? $pad + $chartW / 2 : $pad + ($i / max(1, $count - 1)) * $chartW;
            $y = $pad + $chartH - (float) $v / $max * ($chartH - 10);
            $points[] = sprintf('%.1f,%.1f', $x, $y);
        }
        $polyline = '<polyline points="'.implode(' ', $points).'" fill="none" stroke="#2563eb" stroke-width="2"/>';
        foreach ($points as $i => $pt) {
            [$x, $y] = explode(',', $pt);
            $polyline .= sprintf('<circle cx="%s" cy="%s" r="3" fill="#1d4ed8"/>', $x, $y);
            $polyline .= sprintf(
                '<text x="%s" y="%d" font-size="8" fill="#475569" text-anchor="middle">%s</text>',
                $x,
                $height - 8,
                htmlspecialchars(self::truncate($labels[$i] ?? '', 10), ENT_QUOTES, 'UTF-8')
            );
        }

        return self::wrap($width, $height, $polyline.sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#cbd5e1"/>', $pad, $pad + $chartH, $width - $pad, $pad + $chartH));
    }

    /** @param  array<int, string>  $labels
     * @param  array<int, int|float>  $values
     */
    private static function pieChart(array $labels, array $values, int $width, int $height, bool $doughnut): string
    {
        $total = array_sum($values);
        $cx = $width / 2;
        $cy = $height / 2 - 10;
        $r = min($width, $height) / 2 - 40;
        $colors = ['#2563eb', '#059669', '#64748b', '#d97706', '#7c3aed', '#dc2626', '#0891b2'];
        $start = -90;
        $slices = '';
        $legendY = 12;
        if (count($values) === 1) {
            $slices .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>', $cx, $cy, $r, $colors[0]);
        }
        foreach ($values as $i => $v) {
            if (count($values) === 1) {
                $legendY += 14;
                $slices .= sprintf(
                    '<rect x="8" y="%.1f" width="10" height="10" fill="%s"/>',
                    $legendY - 10,
                    $colors[0]
                );
                $slices .= sprintf(
                    '<text x="22" y="%.1f" font-size="9" fill="#334155">%s (%s)</text>',
                    $legendY - 1,
                    htmlspecialchars(self::truncate($labels[$i] ?? '', 28), ENT_QUOTES, 'UTF-8'),
                    (int) $v
                );
                break;
            }
            $angle = (float) $v / $total * 360;
            $end = $start + $angle;
            $slices .= self::arcSlice($cx, $cy, $r, $start, $end, $colors[$i % count($colors)]);
            $legendY += 14;
            $slices .= sprintf(
                '<rect x="8" y="%.1f" width="10" height="10" fill="%s"/>',
                $legendY - 10,
                $colors[$i % count($colors)]
            );
            $slices .= sprintf(
                '<text x="22" y="%.1f" font-size="9" fill="#334155">%s (%s)</text>',
                $legendY - 1,
                htmlspecialchars(self::truncate($labels[$i] ?? '', 28), ENT_QUOTES, 'UTF-8'),
                (int) $v
            );
            $start = $end;
        }
        if ($doughnut) {
            $slices .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="#ffffff"/>', $cx, $cy, $r * 0.55);
        }

        return self::wrap($width, $height, $slices);
    }

    private static function arcSlice(float $cx, float $cy, float $r, float $startDeg, float $endDeg, string $fill): string
    {
        $start = deg2rad($startDeg);
        $end = deg2rad($endDeg);
        $x1 = $cx + $r * cos($start);
        $y1 = $cy + $r * sin($start);
        $x2 = $cx + $r * cos($end);
        $y2 = $cy + $r * sin($end);
        $large = ($endDeg - $startDeg) > 180 ? 1 : 0;

        return sprintf(
            '<path d="M %.2f %.2f L %.2f %.2f A %.2f %.2f 0 %d 1 %.2f %.2f Z" fill="%s"/>',
            $cx,
            $cy,
            $x1,
            $y1,
            $r,
            $r,
            $large,
            $x2,
            $y2,
            $fill
        );
    }

    private static function wrap(int $width, int $height, string $inner): string
    {
        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">%s</svg>',
            $width,
            $height,
            $width,
            $height,
            $inner
        );
    }

    private static function truncate(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1).'…' : $text;
    }
}
