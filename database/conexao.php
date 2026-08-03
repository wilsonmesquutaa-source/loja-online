<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$raizProjeto = dirname(__DIR__);

require_once $raizProjeto . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(
    $raizProjeto
);

$dotenv->safeLoad();

$host = (string) (
    $_ENV['DB_HOST'] ?? 'localhost'
);

$porta = (string) (
    $_ENV['DB_PORT'] ?? '3306'
);

$banco = (string) (
    $_ENV['DB_DATABASE'] ?? ''
);

$usuario = (string) (
    $_ENV['DB_USERNAME'] ?? 'root'
);

$senha = (string) (
    $_ENV['DB_PASSWORD'] ?? ''
);

if ($banco === '') {
    throw new RuntimeException(
        'A variável DB_DATABASE não foi configurada.'
    );
}

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $host,
    $porta,
    $banco
);

try {
    return new PDO(
        $dsn,
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE =>
                PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES =>
                false,
        ]
    );
} catch (PDOException $erro) {
    error_log(
        '[CONEXÃO COM O BANCO] '
        . $erro->getMessage()
    );

    throw new RuntimeException(
        'Não foi possível conectar ao banco de dados.',
        0,
        $erro
    );
}
