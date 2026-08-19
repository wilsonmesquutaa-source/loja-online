<?php

declare(strict_types=1);

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

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="<?= htmlspecialchars(
                        $descricaoPagina,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>">

    <title>

        <?= htmlspecialchars(
            $tituloPagina,
            ENT_QUOTES,
            'UTF-8'
        ); ?>

        | Cantim do Lanche

    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <!-- CSS Geral -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
                    $baseUrl . '/assets/css/site.css',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>">


    <!-- CSS Home -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
                    $baseUrl . '/assets/css/home.css',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>">


      <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
                    $baseUrl . '/assets/css/navbar.css',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>">

        
      <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
                    $baseUrl . '/assets/css/quemsomos.css',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>">





</head>


<body>