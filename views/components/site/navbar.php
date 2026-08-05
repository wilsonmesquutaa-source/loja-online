<?php

declare(strict_types=1);

$rotaAtual = $rotaAtual ?? '';

?>

<nav
    class="navbar navbar-expand-lg
           navbar-dark bg-dark sticky-top"
    aria-label="Navegação principal"
>
    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="<?= BASE_URL ?>/"
        >
            <i
                class="bi bi-bag-check me-2"
                aria-hidden="true"
            ></i>

            Loja Online
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Abrir menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="menuPrincipal"
        >

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a
                        class="nav-link
                            <?=
                                $rotaAtual === 'home'
                                    ? 'active'
                                    : ''
                            ?>"
                        href="<?= BASE_URL ?>/"
                    >
                        Início
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= BASE_URL ?>/produtos"
                    >
                        Produtos
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= BASE_URL ?>/#beneficios"
                    >
                        Benefícios
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= BASE_URL ?>/#contato"
                    >
                        Contato
                    </a>
                </li>

                <li class="nav-item ms-lg-3">
                    <a
                        class="btn btn-outline-light"
                        href="<?= BASE_URL ?>/login-admin"
                    >
                        Área administrativa
                    </a>
                </li>

            </ul>

        </div>

    </div>
</nav>
