<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require_once dirname(__DIR__)
    . '/vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Raiz do projeto
|--------------------------------------------------------------------------
*/

$raizProjeto =
    dirname(__DIR__);


define(
    'APP_ROOT',
    $raizProjeto
);


/*
|--------------------------------------------------------------------------
| Carregamento do .env
|--------------------------------------------------------------------------
*/

$dotenv =
    Dotenv::createMutable(
        $raizProjeto
    );

$dotenv->safeLoad();


/*
|--------------------------------------------------------------------------
| URL base
|--------------------------------------------------------------------------
*/

$scriptName =
    $_SERVER['SCRIPT_NAME']
    ?? '/index.php';


$caminhoBase =
    str_replace(
        '\\',
        '/',
        dirname($scriptName)
    );


if (
    $caminhoBase === '/'
    || $caminhoBase === '.'
) {

    $caminhoBase = '';

} else {

    $caminhoBase =
        rtrim(
            $caminhoBase,
            '/'
        );
}


define(
    'BASE_URL',
    $caminhoBase
);


/*
|--------------------------------------------------------------------------
| Configuração da sessão
|--------------------------------------------------------------------------
*/

ini_set(
    'session.use_strict_mode',
    '1'
);

ini_set(
    'session.use_only_cookies',
    '1'
);


$usaHttps =
    !empty(
        $_SERVER['HTTPS']
    )
    && $_SERVER['HTTPS'] !== 'off';


session_name(
    'LOJAONLINESESSID'
);


session_set_cookie_params([
    'lifetime' =>
        0,

    'path' =>
        '/',

    'secure' =>
        $usaHttps,

    'httponly' =>
        true,

    'samesite' =>
        'Lax',
]);


if (
    session_status() !==
    PHP_SESSION_ACTIVE
) {

    session_start();
}


/*
|--------------------------------------------------------------------------
| Conexão com banco
|--------------------------------------------------------------------------
*/

$pdo =
    require $raizProjeto
        . '/database/conexao.php';


/*
|--------------------------------------------------------------------------
| Carregamento dos arquivos de rotas
|--------------------------------------------------------------------------
*/

$rotas =
    array_merge(
        require $raizProjeto
            . '/routes/web.php',

        require $raizProjeto
            . '/routes/cliente.php',

        require $raizProjeto
            . '/routes/admin.php',

        require $raizProjeto
            . '/routes/logadm.php'
    );


/*
|--------------------------------------------------------------------------
| Identificação da requisição
|--------------------------------------------------------------------------
*/

$metodoHttp =
    strtoupper(
        $_SERVER['REQUEST_METHOD']
        ?? 'GET'
    );


$caminho =
    parse_url(
        $_SERVER['REQUEST_URI']
        ?? '/',
        PHP_URL_PATH
    );


$caminho =
    is_string($caminho)
    && $caminho !== ''
        ? $caminho
        : '/';


/*
|--------------------------------------------------------------------------
| Remoção do caminho-base no XAMPP
|--------------------------------------------------------------------------
*/

$estaNoCaminhoBase =
    $caminhoBase !== ''
    && (
        $caminho ===
        $caminhoBase

        ||

        str_starts_with(
            $caminho,
            $caminhoBase . '/'
        )
    );


if (
    $estaNoCaminhoBase
) {

    $caminho =
        substr(
            $caminho,
            strlen($caminhoBase)
        );
}


$caminho =
    '/'
    . trim(
        $caminho,
        '/'
    );


/*
|--------------------------------------------------------------------------
| Localização da rota
|--------------------------------------------------------------------------
*/

foreach (
    $rotas as $rota
) {

    $mesmoMetodo =
        (
            $rota['method']
            ?? ''
        )
        ===
        $metodoHttp;


    if (
        !$mesmoMetodo
    ) {

        continue;
    }


    $parametros = [];


    $padrao =
        preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '([^/]+)',
            $rota['path']
        );


    $padrao =
        '#^'
        . $padrao
        . '$#';


    if (
        !preg_match(
            $padrao,
            $caminho,
            $matches
        )
    ) {

        continue;
    }


    array_shift(
        $matches
    );


    $parametros =
        $matches;


    [
        $controller,
        $acao
    ] =
        $rota['action'];


    if (
        !class_exists(
            $controller
        )
    ) {

        throw new RuntimeException(
            "Controller não encontrado: {$controller}"
        );
    }


    $objetoController =
        new $controller(
            $pdo
        );


    if (
        !method_exists(
            $objetoController,
            $acao
        )
    ) {

        throw new RuntimeException(
            "Método não encontrado: {$controller}::{$acao}"
        );
    }


    $objetoController->{$acao}(
        ...array_map(
            'intval',
            $parametros
        )
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| Página não encontrada
|--------------------------------------------------------------------------
*/

http_response_code(
    404
);


require $raizProjeto
    . '/views/erros/404.php';