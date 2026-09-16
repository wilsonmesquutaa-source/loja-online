<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| CONFIGURAÇÕES DA PÁGINA
|--------------------------------------------------------------------------
*/

$tituloPagina =
    $tituloPagina
    ?? 'Cantim do Lanche';


$descricaoPagina =
    $descricaoPagina
    ?? 'Cantim do Lanche - Salgados Artesanais';


$baseUrl =
    defined('BASE_URL')
    ? BASE_URL
    : '';


/*
|--------------------------------------------------------------------------
| CAMINHO DOS ARQUIVOS CSS
|--------------------------------------------------------------------------
|
| Estrutura:
|
| views/layouts/site/header.php
|
| public/assets/css/
|
*/

$cssPath =
    dirname(__DIR__, 3)
    . '/public/assets/css/';


/*
|--------------------------------------------------------------------------
| ARQUIVOS CSS
|--------------------------------------------------------------------------
*/

$siteCssPath =
    $cssPath
    . 'site.css';


$homeCssPath =
    $cssPath
    . 'home.css';


$navbarCssPath =
    $cssPath
    . 'navbar.css';


$categoriaCssPath =
    $cssPath
    . 'categoria.css';


$quemSomosCssPath =
    $cssPath
    . 'quemsomos.css';


$clienteEditarCssPath =
    $cssPath
    . 'cliente_editar.css';


$clientePedidosCssPath =
    $cssPath
    . 'cliente_pedidos.css';


$clienteEnderecosCssPath =
    $cssPath
    . 'cliente_enderecos.css';


$clienteSegurancaCssPath =
    $cssPath
    . 'cliente_seguranca.css';


$checkoutCssPath =
    $cssPath
    . 'checkout.css';


$clienteCadastroCssPath =
    $cssPath
    . 'cliente_cadastro.css';


$clienteLoginCssPath =
    $cssPath
    . 'cliente_login.css';


$carrinhoCssPath =
    $cssPath
    . 'carrinho.css';


/*
|--------------------------------------------------------------------------
| VERSIONAMENTO AUTOMÁTICO DOS CSS
|--------------------------------------------------------------------------
|
| Sempre que um arquivo CSS for alterado,
| filemtime() muda a versão da URL.
|
| Isso evita que o navegador carregue
| uma versão antiga do CSS.
|
*/

$siteCssVersion =
    file_exists($siteCssPath)
        ? filemtime($siteCssPath)
        : time();


$homeCssVersion =
    file_exists($homeCssPath)
        ? filemtime($homeCssPath)
        : time();


$navbarCssVersion =
    file_exists($navbarCssPath)
        ? filemtime($navbarCssPath)
        : time();


$categoriaCssVersion =
    file_exists($categoriaCssPath)
        ? filemtime($categoriaCssPath)
        : time();


$quemSomosCssVersion =
    file_exists($quemSomosCssPath)
        ? filemtime($quemSomosCssPath)
        : time();


$clienteEditarCssVersion =
    file_exists($clienteEditarCssPath)
        ? filemtime($clienteEditarCssPath)
        : time();


$clientePedidosCssVersion =
    file_exists($clientePedidosCssPath)
        ? filemtime($clientePedidosCssPath)
        : time();


$clienteEnderecosCssVersion =
    file_exists($clienteEnderecosCssPath)
        ? filemtime($clienteEnderecosCssPath)
        : time();


$clienteSegurancaCssVersion =
    file_exists($clienteSegurancaCssPath)
        ? filemtime($clienteSegurancaCssPath)
        : time();


$checkoutCssVersion =
    file_exists($checkoutCssPath)
        ? filemtime($checkoutCssPath)
        : time();


$clienteCadastroCssVersion =
    file_exists($clienteCadastroCssPath)
        ? filemtime($clienteCadastroCssPath)
        : time();


$clienteLoginCssVersion =
    file_exists($clienteLoginCssPath)
        ? filemtime($clienteLoginCssPath)
        : time();


$carrinhoCssVersion =
    file_exists($carrinhoCssPath)
        ? filemtime($carrinhoCssPath)
        : time();

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">


    <!-- =================================
         RESPONSIVIDADE
    ================================== -->

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <!-- =================================
         DESCRIÇÃO
    ================================== -->

    <meta
        name="description"
        content="<?= htmlspecialchars(
            $descricaoPagina,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         TÍTULO
    ================================== -->

    <title>

        <?= htmlspecialchars(
            $tituloPagina,
            ENT_QUOTES,
            'UTF-8'
        ); ?>

        | Cantim do Lanche

    </title>


    <!-- =================================
         BOOTSTRAP
    ================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =================================
         BOOTSTRAP ICONS
    ================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >


    <!-- =================================
         CSS GERAL
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/site.css?v='
            . $siteCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS HOME
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/home.css?v='
            . $homeCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS NAVBAR
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/navbar.css?v='
            . $navbarCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CATEGORIA
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/categoria.css?v='
            . $categoriaCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS QUEM SOMOS
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/quemsomos.css?v='
            . $quemSomosCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CLIENTE EDITAR
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/cliente_editar.css?v='
            . $clienteEditarCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CLIENTE PEDIDOS
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/cliente_pedidos.css?v='
            . $clientePedidosCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CLIENTE ENDEREÇOS
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/cliente_enderecos.css?v='
            . $clienteEnderecosCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CLIENTE SEGURANÇA
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/cliente_seguranca.css?v='
            . $clienteSegurancaCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CHECKOUT
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/checkout.css?v='
            . $checkoutCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CLIENTE CADASTRO
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/cliente_cadastro.css?v='
            . $clienteCadastroCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CLIENTE LOGIN
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/cliente_login.css?v='
            . $clienteLoginCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >


    <!-- =================================
         CSS CARRINHO
    ================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl
            . '/assets/css/carrinho.css?v='
            . $carrinhoCssVersion,
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >

</head>


<body>