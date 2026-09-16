<?php

declare(strict_types=1);


$tituloPagina =
    $tituloPagina
    ?? 'Administração';


$baseUrl =
    defined('BASE_URL')
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


    <title>

        <?= htmlspecialchars(
            $tituloPagina,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/assets/css/admin.css"
    >

</head>


<body class="admin-body">


<!-- =====================================================
     HEADER SUPERIOR
===================================================== -->

<header class="admin-topbar">


    <div class="admin-topbar-logo">


        <div class="admin-logo-destaque">

            <img
                src="<?= $baseUrl ?>/assets/images/logo.webp"
                class="logo-admin"
                alt="Cantim do Lanche"
            >

        </div>


        <span class="admin-topbar-separador" aria-hidden="true"></span>


        <div class="admin-topbar-marca">

            <span>Painel administrativo</span>

            <strong>Cantim do Lanche</strong>

        </div>


    </div>


    <div class="admin-topbar-titulo">


        <span class="admin-topbar-legenda">Visão geral</span>


        <h4>

            <?= htmlspecialchars(
                $tituloPagina,
                ENT_QUOTES,
                'UTF-8'
            ) ?>


        </h4>


    </div>


    <div class="admin-user">


        <span class="admin-user-avatar" aria-hidden="true">

            <i class="bi bi-person-fill"></i>

        </span>


        <span class="admin-user-dados">

            <small>Administrador</small>

            <strong>

            <?= htmlspecialchars(
                $_SESSION[
                    'usuario_admin'
                ]['nome']
                ?? 'Administrador',
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            </strong>

        </span>


        <i class="bi bi-chevron-down admin-user-seta" aria-hidden="true"></i>


    </div>


</header>


<!-- =====================================================
     MENU LATERAL RETRÁTIL
===================================================== -->

<aside
    id="sidebar"
    class="sidebar"
>


    <!-- =================================================
         FAIXA VISÍVEL QUANDO FECHADO
    ================================================== -->

    <button
        id="menuAbrir"
        type="button"
        class="sidebar-faixa"
        aria-label="Abrir menu"
        aria-expanded="false"
    >


        <span
            class="sidebar-faixa-icone"
            aria-hidden="true"
        >

            <i class="bi bi-list"></i>

        </span>


        <span
            class="sidebar-faixa-texto"
        >

            MENU

        </span>


    </button>


    <!-- =================================================
         PAINEL QUE DESCE
    ================================================== -->

    <div
        class="sidebar-painel"
    >


        <!-- =================================================
             LINKS
        ================================================== -->

        <nav
            class="sidebar-menu"
        >


            <a
                href="<?= $baseUrl ?>/admin"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-house-fill"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Dashboard

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>/admin/produtos"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-box-seam-fill"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Produtos

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>/admin/clientes"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-person-fill"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Clientes

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>/admin/pedidos"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-cart-fill"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Pedidos

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>/admin/categorias"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-egg-fried"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Cardápio

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-globe2"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Loja

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>/admin/destaques-home"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-star-fill"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Destaques Home

                </span>


            </a>


            <a
                href="<?= $baseUrl ?>/admin/banners-home"
            >

                <span
                    class="menu-icone"
                    aria-hidden="true"
                >

                    <i class="bi bi-images"></i>

                </span>


                <span
                    class="menu-text"
                >

                    Banners da Home

                </span>


            </a>


            <!-- =================================================
                 SAIR
            ================================================== -->

            <form
                method="POST"
                action="<?= $baseUrl ?>/logout-admin"
                class="logout-form"
            >


                <input
                    type="hidden"
                    name="_token"
                    value="<?= htmlspecialchars(
                        \App\Helpers\Csrf::gerar(),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >


                <button
                    type="submit"
                    class="sidebar-link logout-button"
                >


                    <span
                        class="menu-icone"
                        aria-hidden="true"
                    >

                        <i class="bi bi-box-arrow-right"></i>

                    </span>


                    <span
                        class="menu-text"
                    >

                        Sair

                    </span>


                </button>


            </form>


        </nav>


        <!-- =================================================
             BOTÃO FECHAR NO FINAL
        ================================================== -->

        <button
            id="menuFechar"
            type="button"
            class="sidebar-fechar"
            aria-label="Fechar menu"
        >


            <span
                aria-hidden="true"
            >

                <i class="bi bi-dash-lg"></i>

            </span>


            <span>

                MENU

            </span>


        </button>


    </div>


</aside>


<!-- =====================================================
     CONTEÚDO
===================================================== -->

<div
    id="content"
    class="content"
>
