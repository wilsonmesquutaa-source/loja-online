<?php

declare(strict_types=1);

use Dotenv\Dotenv;


/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
*/

$raizProjeto =
    dirname(__DIR__);


require_once $raizProjeto
    . '/vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Constante: raiz do projeto
|--------------------------------------------------------------------------
*/

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
| URL BASE
|--------------------------------------------------------------------------
|
| Projeto:
|
| C:\xampp\htdocs\loja-online
|
| URL:
|
| http://localhost/loja-online
|
*/

$diretorioPublico =
    str_replace(
        '\\',
        '/',
        dirname(
            $_SERVER['SCRIPT_NAME']
                ?? '/index.php'
        )
    );


/*
|--------------------------------------------------------------------------
| Remove /public da URL
|--------------------------------------------------------------------------
*/

if (
    str_ends_with(
        $diretorioPublico,
        '/public'
    )
) {

    $diretorioPublico =
        substr(
            $diretorioPublico,
            0,
            -strlen('/public')
        );
}


$caminhoBase =
    rtrim(
        $diretorioPublico,
        '/'
    );


if (
    $caminhoBase === '.'
    || $caminhoBase === '/'
) {

    $caminhoBase = '';
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
| Carregamento das rotas
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
| Método HTTP
|--------------------------------------------------------------------------
*/

$metodoHttp =
    strtoupper(
        $_SERVER['REQUEST_METHOD']
            ?? 'GET'
    );


/*
|--------------------------------------------------------------------------
| URI solicitada
|--------------------------------------------------------------------------
*/

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
| Remove a BASE_URL da URI
|--------------------------------------------------------------------------
|
| Exemplo:
|
| /loja-online/cardapio
|
| vira:
|
| /cardapio
|
*/

if (
    $caminhoBase !== ''
    && (
        $caminho === $caminhoBase
        ||
        str_starts_with(
            $caminho,
            $caminhoBase . '/'
        )
    )
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

    /*
    |--------------------------------------------------------------------------
    | Verifica método HTTP
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Parâmetros da rota
    |--------------------------------------------------------------------------
    */

    $parametros = [];


    /*
    |--------------------------------------------------------------------------
    | Converte parâmetros da rota
    |--------------------------------------------------------------------------
    |
    | Exemplo:
    |
    | /admin/produtos/editar/{id}
    |
    | vira:
    |
    | /admin/produtos/editar/([^/]+)
    |
    */

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


    /*
    |--------------------------------------------------------------------------
    | Verifica se esta é realmente a rota solicitada
    |--------------------------------------------------------------------------
    */

    if (
        !preg_match(
            $padrao,
            $caminho,
            $matches
        )
    ) {

        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Proteção das rotas administrativas
    |--------------------------------------------------------------------------
    |
    | A proteção acontece somente depois
    | de confirmar que a rota corresponde
    | à URL solicitada.
    |
    */

    if (
        str_starts_with(
            $rota['path'],
            '/admin'
        )
        && empty(
            $_SESSION[
                'usuario_admin'
            ]['id']
        )
    ) {

        header(
            'Location: '
            . BASE_URL
            . '/login-admin'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Remove a correspondência completa
    |--------------------------------------------------------------------------
    */

    array_shift(
        $matches
    );


    $parametros =
        $matches;


    /*
    |--------------------------------------------------------------------------
    | Controller e ação
    |--------------------------------------------------------------------------
    */

    [
        $controller,
        $acao
    ] =
        $rota['action'];


    /*
    |--------------------------------------------------------------------------
    | Verifica Controller
    |--------------------------------------------------------------------------
    */

    if (
        !class_exists(
            $controller
        )
    ) {

        throw new RuntimeException(
            "Controller não encontrado: {$controller}"
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Instancia Controller
    |--------------------------------------------------------------------------
    |
    | O LoginAdminController possui
    | seu próprio construtor e cria
    | internamente o repository.
    |
    | Os demais controllers utilizam
    | o construtor do Controller base,
    | que recebe a conexão PDO.
    |
    */

    if (
        $controller ===
        \App\Controllers\Admin\LoginAdminController::class
    ) {

        $objetoController =
            new $controller();

    } else {

        $objetoController =
            new $controller(
                $pdo
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Verifica ação
    |--------------------------------------------------------------------------
    */

    if (
        !method_exists(
            $objetoController,
            $acao
        )
    ) {

        throw new RuntimeException(
            "Método não encontrado: "
            . "{$controller}::{$acao}"
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Executa ação
    |--------------------------------------------------------------------------
    */

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