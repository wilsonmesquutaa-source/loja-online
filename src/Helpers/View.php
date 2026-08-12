<?php

declare(strict_types=1);

namespace App\Helpers;

use PDO;
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
            ||
            str_contains(
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

        /*
        =================================
        DISPONIBILIZA O PDO
        =================================

        O public/index.php cria o PDO
        na variável global $pdo.

        Disponibilizamos essa conexão
        automaticamente para todos os
        componentes.
        */

        if (
            !isset($dados['pdo'])
            &&
            isset($GLOBALS['pdo'])
            &&
            $GLOBALS['pdo'] instanceof PDO
        ) {
            $dados['pdo'] =
                $GLOBALS['pdo'];
        }

        extract(
            $dados,
            EXTR_SKIP
        );

        require $arquivo;
    }
}