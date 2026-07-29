<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$raizProjeto = dirname(__DIR__);

$pagina = $_GET['pagina'] ?? 'home';

$rotas = [
    'home' => $raizProjeto . '/views/site/home.php',
    'admin' => $raizProjeto . '/views/admin/dashboard.php',
];

if (!array_key_exists($pagina, $rotas)) {
    http_response_code(404);

    require $raizProjeto . '/views/erros/404.php';

    exit;
}

require $rotas[$pagina];

$rotas = [
    'home' => $raizProjeto . '/views/site/home.php',

    'produtos' =>
        $raizProjeto . '/views/site/produtos.php',

    'contato' =>
        $raizProjeto . '/views/site/contato.php',

    'login' =>
        $raizProjeto . '/views/site/login.php',

    'admin' =>
        $raizProjeto . '/views/admin/dashboard.php',
];
