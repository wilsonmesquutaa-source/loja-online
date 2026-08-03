<?php

declare(strict_types=1);

namespace App\Helpers;

final class Csrf
{
    private const CHAVE_SESSAO =
        '_csrf_admin';

    public static function gerar(): string
    {
        if (
            empty(
                $_SESSION[self::CHAVE_SESSAO]
            )
        ) {
            $_SESSION[self::CHAVE_SESSAO] =
                bin2hex(
                    random_bytes(32)
                );
        }

        return (string)
            $_SESSION[self::CHAVE_SESSAO];
    }

    public static function validar(
        ?string $token
    ): bool {
        if (
            $token === null
            || empty(
                $_SESSION[self::CHAVE_SESSAO]
            )
        ) {
            return false;
        }

        return hash_equals(
            (string)
                $_SESSION[self::CHAVE_SESSAO],

            $token
        );
    }

    public static function renovar(): string
    {
        $_SESSION[self::CHAVE_SESSAO] =
            bin2hex(
                random_bytes(32)
            );

        return (string)
            $_SESSION[self::CHAVE_SESSAO];
    }
}
