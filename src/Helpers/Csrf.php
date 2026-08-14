<?php

declare(strict_types=1);

namespace App\Helpers;

final class Csrf
{
    private const CHAVE_SESSAO_ADMIN =
        '_csrf_admin';

    private const CHAVE_SESSAO_CLIENTE =
        '_csrf_cliente';


    /*
    =================================
    CSRF ADMIN
    =================================
    */

    public static function gerar(): string
    {
        if (
            empty(
                $_SESSION[self::CHAVE_SESSAO_ADMIN]
            )
        ) {
            $_SESSION[self::CHAVE_SESSAO_ADMIN] =
                bin2hex(
                    random_bytes(32)
                );
        }

        return (string)
            $_SESSION[self::CHAVE_SESSAO_ADMIN];
    }


    public static function validar(
        ?string $token
    ): bool {
        if (
            $token === null
            || empty(
                $_SESSION[self::CHAVE_SESSAO_ADMIN]
            )
        ) {
            return false;
        }

        return hash_equals(
            (string)
                $_SESSION[self::CHAVE_SESSAO_ADMIN],

            $token
        );
    }


    public static function renovar(): string
    {
        $_SESSION[self::CHAVE_SESSAO_ADMIN] =
            bin2hex(
                random_bytes(32)
            );

        return (string)
            $_SESSION[self::CHAVE_SESSAO_ADMIN];
    }


    /*
    =================================
    CSRF CLIENTE
    =================================
    */

    public static function gerarCliente(): string
    {
        if (
            empty(
                $_SESSION[self::CHAVE_SESSAO_CLIENTE]
            )
        ) {
            $_SESSION[self::CHAVE_SESSAO_CLIENTE] =
                bin2hex(
                    random_bytes(32)
                );
        }

        return (string)
            $_SESSION[self::CHAVE_SESSAO_CLIENTE];
    }


    public static function validarCliente(
        ?string $token
    ): bool {
        if (
            $token === null
            || empty(
                $_SESSION[self::CHAVE_SESSAO_CLIENTE]
            )
        ) {
            return false;
        }

        return hash_equals(
            (string)
                $_SESSION[self::CHAVE_SESSAO_CLIENTE],

            $token
        );
    }


    public static function renovarCliente(): string
    {
        $_SESSION[self::CHAVE_SESSAO_CLIENTE] =
            bin2hex(
                random_bytes(32)
            );

        return (string)
            $_SESSION[self::CHAVE_SESSAO_CLIENTE];
    }
}