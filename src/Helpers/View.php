<?php

declare(strict_types=1);

namespace App\Helpers;

use RuntimeException;

final class View
{
    public static function componente(
        string $nome,
        array $dados = []
    ): void {
        $nome = trim(
            $nome,
            '/'
        );

        if (
            $nome === ''
            || str_contains(
                $nome,
                '..'
            )
        ) {
            throw new RuntimeException(
                'Nome de componente inválido.'
            );
        }

        $arquivo = APP_ROOT
            . '/views/components/'
            . $nome
            . '.php';

        if (!is_file($arquivo)) {
            throw new RuntimeException(
                "Componente não encontrado: {$nome}"
            );
        }

        extract(
            $dados,
            EXTR_SKIP
        );

        require $arquivo;
    }
}
