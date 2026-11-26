<?php
declare(strict_types=1);

/**
 * Generador PDF mínimo (PDF 1.4) sin dependencias: texto con Helvetica / Helvetica-Bold
 * (WinAnsiEncoding, soporta acentos y ñ), líneas y rectángulos. Origen arriba-izquierda, unidades en puntos.
 * Suficiente para cotizaciones; evita instalar Dompdf/TCPDF (decenas de MB y Composer).
 */
final class Pdf
{
    public const A4_W = 595.28;
    public const A4_H = 841.89;

    /** @var string[] */
    private array $pages = [];
    private int $current = -1;

    private const WIDTHS = [
        32 => 278, 278, 355, 556, 556, 889, 667, 191, 333, 333, 389, 584, 278, 333, 278, 278,
        556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 278, 278, 584, 584, 584, 556,
        1015, 667, 667, 722, 722, 667, 611, 778, 722, 278, 500, 667, 556, 833, 722, 778,
        667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 278, 278, 278, 469, 556,
        333, 556, 556, 500, 556, 556, 278, 556, 556, 222, 222, 500, 222, 833, 556, 556,
        556, 556, 333, 500, 278, 556, 500, 722, 500, 500, 500, 334, 260, 334, 584,
    ];
    private const EXTRA = [0xC1 => 667, 0xC9 => 667, 0xCD => 278, 0xD3 => 778, 0xDA => 722, 0xD1 => 722, 0xE1 => 556, 0xE9 => 556,
        0xED => 278, 0xF3 => 556, 0xFA => 556, 0xF1 => 556, 0xFC => 556, 0xBF => 611, 0xA1 => 333, 0xB0 => 400, 0xB7 => 278];

    public function addPage(): void
    {
        $this->pages[] = '';
        $this->current = count($this->pages) - 1;
    }

    private static function encode(string $text): string
    {
        $converted = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        return strtr($converted, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)', "\r" => '', "\n" => ' ']);
    }

    private static function color(array $rgb): string
    {
        return sprintf('%.3F %.3F %.3F', $rgb[0] / 255, $rgb[1] / 255, $rgb[2] / 255);
    }

    public function textWidth(string $text, float $size, bool $bold = false): float
    {
        $bytes = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        $w = 0;
        for ($i = 0, $n = strlen($bytes); $i < $n; $i++) {
            $c = ord($bytes[$i]);
            $w += self::WIDTHS[$c] ?? self::EXTRA[$c] ?? 556;
        }
        return $w * $size / 1000 * ($bold ? 1.06 : 1.0);
    }

    public function text(float $x, float $y, string $text, float $size = 10, bool $bold = false, array $rgb = [33, 37, 41], string $align = 'left'): void
    {
        if ($align === 'right') {
            $x -= $this->textWidth($text, $size, $bold);
        } elseif ($align === 'center') {
            $x -= $this->textWidth($text, $size, $bold) / 2;
        }
        $this->pages[$this->current] .= sprintf(
            "BT %s rg /%s %.2F Tf %.2F %.2F Td (%s) Tj ET\n",
            self::color($rgb), $bold ? 'F2' : 'F1', $size, $x, self::A4_H - $y, self::encode($text)
        );
    }

    public function line(float $x1, float $y1, float $x2, float $y2, float $width = 0.5, array $rgb = [200, 200, 200]): void
    {
        $this->pages[$this->current] .= sprintf("%s RG %.2F w %.2F %.2F m %.2F %.2F l S\n", self::color($rgb), $width, $x1, self::A4_H - $y1, $x2, self::A4_H - $y2);
    }

    public function rect(float $x, float $y, float $w, float $h, array $rgb): void
    {
        $this->pages[$this->current] .= sprintf("%s rg %.2F %.2F %.2F %.2F re f\n", self::color($rgb), $x, self::A4_H - $y - $h, $w, $h);
    }

    /** Divide un texto en líneas que caben en $width. */
    public function wrap(string $text, float $width, float $size, bool $bold = false): array
    {
        $lines = [];
        foreach (preg_split('/\R/u', $text) ?: [] as $paragraph) {
            $current = '';
            foreach (preg_split('/\s+/u', trim($paragraph)) ?: [] as $word) {
                $try = $current === '' ? $word : $current . ' ' . $word;
                if ($current !== '' && $this->textWidth($try, $size, $bold) > $width) {
                    $lines[] = $current;
                    $current = $word;
                } else {
                    $current = $try;
                }
            }
            $lines[] = $current;
        }
        return $lines;
    }

    public function output(): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $kids = [];
        $n = 5;
        foreach ($this->pages as $content) {
            $pageId = $n++;
            $contentId = $n++;
            $kids[] = $pageId . ' 0 R';
            $objects[$pageId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::A4_W, self::A4_H, $contentId
            );
            $stream = gzcompress($content);
            $objects[$contentId] = '<< /Length ' . strlen($stream) . " /Filter /FlateDecode >>\nstream\n" . $stream . "\nendstream";
        }
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        ksort($objects);

        $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($out);
            $out .= $id . " 0 obj\n" . $body . "\nendobj\n";
        }
        $xref = strlen($out);
        $size = max(array_keys($objects)) + 1;
        $out .= "xref\n0 {$size}\n0000000000 65535 f \n";
        for ($i = 1; $i < $size; $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }
        $out .= "trailer\n<< /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
        return $out;
    }
}
