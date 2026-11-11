<?php
declare(strict_types=1);
namespace App\Helpers;

final class ImagemEscala
{
    public static function normalizar(mixed $valor): float
    {
        $numero = is_numeric($valor) ? (float) $valor : 1.0;
        return round(min(max(is_finite($numero) ? $numero : 1.0, 0.10), 2.0), 2);
    }

    public static function estilo(array $imagem, string $prefixo = 'imagem_'): string
    {
        $escala = self::normalizar($imagem[$prefixo . 'escala'] ?? 1);
        $x = min(max((float) ($imagem[$prefixo . 'posicao_x'] ?? 50), 0), 100);
        $y = min(max((float) ($imagem[$prefixo . 'posicao_y'] ?? 50), 0), 100);
        return "object-fit: contain; scale: $escala; transform-origin: $x% $y%;";
    }
}
