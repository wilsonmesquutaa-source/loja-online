<?php

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;

abstract class Controller
{
    protected function view(
        string $view,
        array $dados = []
    ): void {
        $view = trim($view, '/');

        if ($view === '' || str_contains($view, '..')) {
            throw new RuntimeException(
                'Nome de view inválido.'
            );
        }

        $arquivo = APP_ROOT
            . '/views/'
            . $view
            . '.php';

        if (!is_file($arquivo)) {
            throw new RuntimeException(
                "View não encontrada: {$view}"
            );
        }

        extract($dados, EXTR_SKIP);

        require $arquivo;
    }

    protected function redirecionar(
        string $caminho
    ): void {
        $destino = BASE_URL
            . '/'
            . ltrim($caminho, '/');

        header('Location: ' . $destino);

        exit;
    }
}
