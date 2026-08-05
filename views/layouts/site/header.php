<?php

declare(strict_types=1);

$tituloPagina = $tituloPagina
    ?? 'Loja Online';

$descricaoPagina = $descricaoPagina
    ?? 'Loja Online';

$baseUrl = defined('BASE_URL')
    ? BASE_URL
    : '';

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="description"
        content="<?=
            htmlspecialchars(
                $descricaoPagina,
                ENT_QUOTES,
                'UTF-8'
            )
        ?>"
    >

    <title>
        <?=
            htmlspecialchars(
                $tituloPagina,
                ENT_QUOTES,
                'UTF-8'
            )
        ?>
        — Loja Online
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="<?=
            htmlspecialchars(
                $baseUrl
                    . '/assets/css/site.css',
                ENT_QUOTES,
                'UTF-8'
            )
        ?>"
    >
</head>

<body class="bg-body-tertiary">
