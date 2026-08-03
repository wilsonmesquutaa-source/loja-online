<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$raizProjeto = dirname(__DIR__);

define('APP_ROOT', $raizProjeto);

/*
|---------------------------------------------------------------------------
| Carregamento dos arquivos de rotas
|---------------------------------------------------------------------------
*/

$rotas = array_merge(
    require $raizProjeto . '/routes/web.php',
    require $raizProjeto . '/routes/cliente.php',
    require $raizProjeto . '/routes/admin.php'
);

/*
|---------------------------------------------------------------------------
| Identificação da requisição
|---------------------------------------------------------------------------
*/

$metodoHttp = strtoupper(
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);

$caminho = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$caminho = is_string($caminho)
    && $caminho !== ''
    ? $caminho
    : '/';

/*
|---------------------------------------------------------------------------
| Remoção do caminho-base no XAMPP
|---------------------------------------------------------------------------
*/

$caminhoBase = str_replace(
    '\\',
    '/',
    dirname($_SERVER['SCRIPT_NAME'] ?? '')
);

$caminhoBase = $caminhoBase === '/'
    ? ''
    : rtrim($caminhoBase, '/');

define('BASE_URL', $caminhoBase);

if (
    $caminhoBase !== ''
    && str_starts_with(
        $caminho,
        $caminhoBase
    )
) {
    $caminho = substr(
        $caminho,
        strlen($caminhoBase)
    );
}

$caminho = '/' . trim($caminho, '/');

/*
|---------------------------------------------------------------------------
| Localização da rota
|---------------------------------------------------------------------------
*/

foreach ($rotas as $rota) {
    $mesmoMetodo =
        ($rota['method'] ?? '') === $metodoHttp;

    $padraoRota = $rota['path'] ?? '';

    $parametros = [];

    $mesmoCaminho = preg_match(
        '#^' . preg_replace(
            '#\{[^}]+\}#',
            '([^/]+)',
            $padraoRota
        ) . '$#',
        $caminho,
        $parametros
    );

    if (!$mesmoMetodo || !$mesmoCaminho) {
        continue;
    }

    try {
        [$controller, $acao] = $rota['action'];

        if (!class_exists($controller)) {
            throw new RuntimeException(
                "Controller não encontrado: {$controller}"
            );
        }

        $objetoController = new $controller();

        if (!method_exists($objetoController, $acao)) {
            throw new RuntimeException(
                "Método não encontrado: {$controller}::{$acao}"
            );
        }

        if (isset($parametros[1])) {

            $objetoController->{$acao}(
                (int) $parametros[1]
            );
        } else {

            $objetoController->{$acao}();
        }
        exit;
    } catch (Throwable $erro) {
        echo '<pre>';
        echo $erro->getMessage();
        echo "\n\n";
        echo $erro->getFile();
        echo "\n";
        echo $erro->getLine();
        echo '</pre>';

        exit;
    }
}

/*
|---------------------------------------------------------------------------
| Página não encontrada
|---------------------------------------------------------------------------
*/

http_response_code(404);

require APP_ROOT . '/views/erros/404.php';
