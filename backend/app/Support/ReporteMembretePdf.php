<?php

namespace App\Support;

class ReporteMembretePdf
{
    /** Ancho total hoja A4 en puntos (DomPDF). */
    public const PAGE_WIDTH = 595;

    /** Márgenes laterales del cuerpo del reporte (pt). */
    public const H_MARGIN = 36;

    public static function contentWidth(): int
    {
        return self::PAGE_WIDTH - (self::H_MARGIN * 2);
    }

    /** @return array{header: string, footer: string, headerHeight: int, footerHeight: int, notaHeight: int, chartWidth: int, chartHeight: int} */
    public static function render(): array
    {
        $headerPath = public_path('images/membrete-fabrigas-pda.png');
        $footerPath = public_path('images/pie-fabrigas-pda.png');
        $headerHeight = is_file($headerPath)
            ? self::heightForWidth(self::PAGE_WIDTH, $headerPath)
            : 0;
        $footerHeight = is_file($footerPath)
            ? self::heightForWidth(self::PAGE_WIDTH, $footerPath)
            : 0;
        $contentWidth = self::contentWidth();

        return [
            'header' => self::renderImage('images/membrete-fabrigas-pda.png'),
            'footer' => self::renderImage('images/pie-fabrigas-pda.png'),
            'headerHeight' => $headerHeight,
            'footerHeight' => $footerHeight,
            'notaHeight' => 14,
            'contentWidth' => $contentWidth,
            'chartWidth' => $contentWidth,
            'chartHeight' => 185,
        ];
    }

    private static function renderImage(string $relativePath): string
    {
        $path = public_path($relativePath);
        if (! is_file($path) || ! extension_loaded('gd')) {
            return '';
        }

        $width = self::PAGE_WIDTH;
        $height = self::heightForWidth($width, $path);
        $src = str_replace('\\', '/', realpath($path) ?: $path);

        return sprintf(
            '<img src="%s" width="%d" height="%d" alt="" style="display:block;width:%dpt;height:%dpt;margin:0;padding:0;border:0;" />',
            $src,
            $width,
            $height,
            $width,
            $height
        );
    }

    private static function heightForWidth(int $width, string $pngPath): int
    {
        $size = @getimagesize($pngPath);
        if ($size && $size[0] > 0) {
            return max(1, (int) round($width * ($size[1] / $size[0])));
        }

        return 1;
    }
}
