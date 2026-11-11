<?php
declare(strict_types=1);
namespace App\Helpers;
use DomainException;
final class Decimal
{
    public static function valor(mixed $v, string $min = '0', string $max = '999999999', int $casas = 8): string
    {
        if (!is_scalar($v)) throw new DomainException('Valor numérico inválido.');
        $v = str_replace(',', '.', trim((string) $v));
        if (!preg_match('/^\d{1,12}(\.\d{1,8})?$/D', $v) || bccomp($v, $min, 8) < 0 || bccomp($v, $max, 8) > 0) throw new DomainException('Valor fora do intervalo permitido.');
        return bcadd($v, '0', $casas);
    }
    public static function somar(array $valores): string { return array_reduce($valores, fn($a,$b) => bcadd($a,(string)$b,8), '0'); }
    public static function dinheiro(string $v): string { return bcadd($v, bccomp($v,'0',8) < 0 ? '-0.005' : '0.005',2); }
}
