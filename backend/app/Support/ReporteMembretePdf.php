<?php

namespace App\Support;

class ReporteMembretePdf
{
    /** Ancho total hoja A4 en puntos (DomPDF). */
    public const PAGE_WIDTH = 595;

    /** @return array{header: string, footer: string, footerHeight: int, notaHeight: int} */
    public static function render(): array
    {
        $footerPath = public_path('images/pie-fabrigas-pda.png');
        $footerHeight = is_file($footerPath)
            ? self::heightForWidth(self::PAGE_WIDTH, $footerPath)
            : 0;

        return [
            'header' => self::renderImage('images/membrete-fabrigas-pda.png'),
            'footer' => self::renderImage('images/pie-fabrigas-pda.png'),
            'footerHeight' => $footerHeight,
            'notaHeight' => 14,
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
