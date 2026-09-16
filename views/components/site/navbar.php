<?php

declare(strict_types=1);

use App\Repositories\CarrinhoRepository;
use App\Helpers\Csrf;

$rotaAtual =
    $rotaAtual ?? '';


if (
    session_status() !==
    PHP_SESSION_ACTIVE
) {
    session_start();
}


/*
=================================
CARRINHO
=================================
*/

if (
    !isset($pdo)
    ||
    !($pdo instanceof PDO)
) {
    $quantidadeCarrinho = 999;

} else {

    $tokenSessao =
        $_SESSION['carrinho_token']
        ?? null;

    $quantidadeCarrinho = 0;


    if ($tokenSessao !== null) {

        $repository =
            new CarrinhoRepository(
                $pdo
            );

        $carrinho =
            $repository
                ->buscarAbertoPorToken(
                    $tokenSessao
                );


        if ($carrinho !== null) {

            $itens =
                $repository
                    ->buscarItens(
                        (int) $carrinho['id']
                    );

            $grupos = [];


            foreach (
                $itens as $item
            ) {

                $categoriaId =
                    (int)
                    $item['categoria_id'];


                if (
                    !isset(
                        $grupos[$categoriaId]
                    )
                ) {

                    $grupos[$categoriaId] = [
                        'tipo' =>
                            'unica',

                        'quantidade' =>
                            0,
                    ];
                }


                $nomeCategoria =
                    mb_strtolower(
                        trim(
                            $item['categoria_nome']
                        ),
                        'UTF-8'
                    );


                $tipo =
                    'unica';


                if (
                    str_contains(
                        $nomeCategoria,
                        'tradicionais'
                    )
                ) {

                    $tipo =
                        'cento_tradicionais';

                } elseif (
                    str_contains(
                        $nomeCategoria,
                        'folhados'
                    )
                ) {

                    $tipo =
                        'cento_folhados';

                } elseif (
                    str_contains(
                        $nomeCategoria,
                        'grandes'
                    )
                ) {

                    $tipo =
                        'salgados_grandes';

                } elseif (
                    str_contains(
                        $nomeCategoria,
                        'empadão'
                    )
                    ||
                    str_contains(
                        $nomeCategoria,
                        'empadões'
                    )
                    ||
                    str_contains(
                        $nomeCategoria,
                        'empadao'
                    )
                    ||
                    str_contains(
                        $nomeCategoria,
                        'empadoes'
                    )
                ) {

                    $tipo =
                        'empadao';
                }


                $grupos[$categoriaId]['tipo'] =
                    $tipo;


                $grupos[$categoriaId]['quantidade'] +=
                    (int)
                    $item['quantidade'];
            }


            foreach (
                $grupos as $grupo
            ) {

                if (
                    $grupo['tipo'] ===
                    'cento_tradicionais'
                ) {

                    $quantidadeCarrinho +=
                        (int) ceil(
                            $grupo['quantidade']
                            / 4
                        );

                } elseif (
                    $grupo['tipo'] ===
                    'cento_folhados'
                ) {

                    $quantidadeCarrinho +=
                        (int) ceil(
                            $grupo['quantidade']
                            / 2
                        );

                } else {

                    $quantidadeCarrinho +=
                        $grupo['quantidade'];
                }
            }
        }
    }
}


/*
=================================
CLIENTE LOGADO
=================================
*/

$clienteLogado =
    !empty(
        $_SESSION['cliente_id']
    );


$clienteNomeCompleto =
    (string) (
        $_SESSION['cliente_nome']
        ?? ''
    );


$clienteNome =
    trim(
        (string) (
            preg_split(
                '/\s+/',
                $clienteNomeCompleto
            )[0]
            ?? ''
        )
    );


$clienteFoto =
    $_SESSION['cliente_foto_url']
    ?? null;

?>

<nav
    class="navbar navbar-expand-lg navbar-site shadow-sm sticky-top"
    aria-label="Navegação principal">

    <div
        class="
            container-fluid
            px-4
            navbar-container-marca
        ">


        <!-- =================================
             LOGO
        ================================== -->

        <a
            class="
                navbar-brand-logo
                d-flex
                align-items-center
                gap-2
            "
            href="<?= BASE_URL ?>/">

            <picture>

                <source
                    media="(max-width: 991.98px)"
                    srcset="<?= BASE_URL ?>/assets/images/logo2.webp">

                <img
                    src="<?= BASE_URL ?>/assets/images/logo.webp"
                    alt="Cantim do Lanche"
                    height="55">

            </picture>

        </a>


        <!-- =================================
             AÇÕES MOBILE
        ================================== -->

        <div
            class="
                navbar-acoes-mobile
                d-flex
                align-items-center
                gap-1
            ">


            <?php if ($clienteLogado): ?>

                <!-- CLIENTE LOGADO -->

                <div
                    class="dropdown-cliente-custom">

                    <div
                        class="
                            btn
                            btn-navbar-cliente
                            d-flex
                            align-items-center
                            gap-1
                            p-0
                        "
                        role="button"
                        tabindex="0"
                        aria-expanded="false"
                        aria-controls="menuClienteMobile">

                        <span
                            class="
                                navbar-cliente-boas-vindas
                            ">

                            Bem-vindo,

                            <strong>
                                <?= htmlspecialchars(
                                    $clienteNome,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                        </span>


                        <span
                            class="
                                navbar-cliente-avatar
                            ">

                            <?php if (
                                !empty(
                                    $clienteFoto
                                )
                            ): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        (string)
                                        $clienteFoto,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="Foto de perfil">

                            <?php else: ?>

                                <i
                                    class="
                                        bi
                                        bi-person
                                    "
                                    aria-hidden="true"></i>

                            <?php endif; ?>

                        </span>


                        <i
                            class="
                                bi
                                bi-chevron-down
                            "
                            aria-hidden="true"></i>

                    </div>


                    <ul
                        id="menuClienteMobile"
                        class="
                            dropdown-menu-cliente-custom
                        ">

                        <li>

                            <a
                                href="<?= BASE_URL ?>/cliente/perfil"
                                class="dropdown-item">

                                <i
                                    class="
                                        bi
                                        bi-pencil
                                        me-2
                                    "></i>

                                Editar perfil

                            </a>

                        </li>


                        <li>

                            <a
                                href="<?= BASE_URL ?>/cliente/pedidos"
                                class="dropdown-item">

                                <i
                                    class="
                                        bi
                                        bi-box-seam
                                        me-2
                                    "></i>

                                Meus pedidos

                            </a>

                        </li>


                        <li>

                            <a
                                href="<?= BASE_URL ?>/cliente/enderecos"
                                class="dropdown-item">

                                <i
                                    class="
                                        bi
                                        bi-geo-alt
                                        me-2
                                    "></i>

                                Meus endereços

                            </a>

                        </li>


                        <li>

                            <a
                                href="<?= BASE_URL ?>/cliente/seguranca"
                                class="dropdown-item">

                                <i
                                    class="
                                        bi
                                        bi-shield-lock
                                        me-2
                                    "></i>

                                Segurança

                            </a>

                        </li>


                        <li>

                            <hr
                                class="dropdown-divider">

                        </li>


                        <li>

                            <form
                                method="POST"
                                action="<?= BASE_URL ?>/logout">

                                <input
                                    type="hidden"
                                    name="_csrf"
                                    value="<?= htmlspecialchars(
                                        Csrf::gerarCliente(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                <button
                                    type="submit"
                                    class="
                                        dropdown-item
                                        text-danger
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-box-arrow-right
                                            me-2
                                        "
                                        aria-hidden="true"></i>

                                    Sair

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>


            <?php endif; ?>


            <!-- =================================
                 CARRINHO MOBILE
            ================================== -->

            <a
                href="<?= BASE_URL ?>/carrinho"
                class="
                    btn
                    btn-carrinho
                    position-relative
                    navbar-carrinho-mobile
                "
                title="Carrinho"
                aria-label="Carrinho">

                <i
                    class="
                        bi
                        bi-cart3
                    "
                    aria-hidden="true"></i>


                <span
                    class="
                        position-absolute
                        top-0
                        start-100
                        translate-middle
                        badge
                        rounded-pill
                        bg-danger
                    ">

                    <?= $quantidadeCarrinho ?>

                </span>

            </a>


            <?php if (!$clienteLogado): ?>


                <!-- =================================
                     ENTRAR MOBILE
                ================================== -->

                <div
                    class="dropdown">

                    <button
                        type="button"
                        class="
                            btn
                            btn-navbar-menor
                            navbar-botao-mobile
                            dropdown-toggle
                        "
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        title="Entrar"
                        aria-label="Entrar">

                        <i
                            class="
                                bi
                                bi-box-arrow-in-right
                            "
                            aria-hidden="true"></i>

                    </button>


                    <ul
                        class="
                            dropdown-menu
                            dropdown-menu-end
                        ">

                        <li>

                            <a
                                href="<?= BASE_URL ?>/login"
                                class="dropdown-item">

                                <i
                                    class="
                                        bi
                                        bi-box-arrow-in-right
                                        me-2
                                    "></i>

                                Login

                            </a>

                        </li>


                        <li>

                            <a
                                href="<?= BASE_URL ?>/cadastro"
                                class="dropdown-item">

                                <i
                                    class="
                                        bi
                                        bi-person-plus
                                        me-2
                                    "></i>

                                Criar conta

                            </a>

                        </li>

                    </ul>

                </div>


                <!-- =================================
                     ADMIN MOBILE
                ================================== -->

                <a
                    href="<?= BASE_URL ?>/login-admin"
                    class="
                        btn
                        btn-dark
                        btn-navbar-menor
                        navbar-botao-mobile
                    "
                    title="Admin"
                    aria-label="Admin">

                    <i
                        class="
                            bi
                            bi-shield-lock
                        "
                        aria-hidden="true"></i>

                </a>


            <?php endif; ?>


        </div>


        <!-- =================================
             MENU HAMBURGER
        ================================== -->

        <button
            class="
                navbar-toggler
                navbar-toggler-marca
            "
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Abrir menu">

            <i
                class="bi bi-list"
                aria-hidden="true"></i>

        </button>


        <div
            class="
                collapse
                navbar-collapse
            "
            id="menuPrincipal">


            <!-- =================================
                 MENU PRINCIPAL
            ================================== -->

            <ul
                class="
                    navbar-nav
                    mx-auto
                    align-items-lg-center
                ">

                <li class="nav-item">

                    <a
                        class="
                            nav-link
                            nav-link-marca
                            <?= $rotaAtual === 'home'
                                ? 'active'
                                : '' ?>
                        "
                        href="<?= BASE_URL ?>/">

                        Início

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="
                            nav-link
                            nav-link-marca
                            <?= $rotaAtual === 'cardapio'
                                ? 'active'
                                : '' ?>
                        "
                        href="<?= BASE_URL ?>/cardapio">

                        Cardápio

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="
                            nav-link
                            nav-link-marca
                            <?= $rotaAtual === 'quemsomos'
                                ? 'active'
                                : '' ?>
                        "
                        href="<?= BASE_URL ?>/quemsomos">

                        Quem Somos

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="
                            nav-link
                            nav-link-marca
                            <?= $rotaAtual === 'contato'
                                ? 'active'
                                : '' ?>
                        "
                        href="<?= BASE_URL ?>/contato">

                        Contato

                    </a>

                </li>

            </ul>


            <!-- =================================
                 AÇÕES DESKTOP
            ================================== -->

            <div
                class="
                    navbar-acoes-desktop
                    d-flex
                    flex-column
                    flex-lg-row
                    align-items-lg-center
                    gap-2
                ">


                <!-- CARRINHO -->

                <a
                    href="<?= BASE_URL ?>/carrinho"
                    class="
                        btn
                        btn-carrinho
                        position-relative
                    "
                    title="Carrinho"
                    aria-label="Carrinho">

                    <i
                        class="bi bi-cart3"
                        aria-hidden="true"></i>


                    <span
                        class="
                            position-absolute
                            top-0
                            start-100
                            translate-middle
                            badge
                            rounded-pill
                            bg-danger
                        ">

                        <?= $quantidadeCarrinho ?>

                    </span>

                </a>


                <!-- PESQUISA -->

                <form
                    class="d-flex"
                    action="<?= BASE_URL ?>/cardapio"
                    method="GET">

                    <div
                        class="input-group">

                        <input
                            type="search"
                            name="busca"
                            class="form-control"
                            placeholder="Buscar salgados...">


                        <button
                            class="
                                btn
                                btn-warning
                            "
                            type="submit">

                            <i
                                class="bi bi-search"
                                aria-hidden="true"></i>

                        </button>

                    </div>

                </form>


                <?php if (!$clienteLogado): ?>


                    <!-- ENTRAR -->

                    <div
                        class="dropdown">

                        <button
                            type="button"
                            class="
                                btn
                                btn-outline-secondary
                                btn-navbar-menor
                                dropdown-toggle
                            "
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            title="Entrar"
                            aria-label="Entrar">

                            <i
                                class="
                                    bi
                                    bi-box-arrow-in-right
                                "
                                aria-hidden="true"></i>

                        </button>


                        <ul
                            class="
                                dropdown-menu
                                dropdown-menu-end
                            ">

                            <li>

                                <a
                                    href="<?= BASE_URL ?>/login"
                                    class="dropdown-item">

                                    <i
                                        class="
                                            bi
                                            bi-box-arrow-in-right
                                            me-2
                                        "></i>

                                    Login

                                </a>

                            </li>


                            <li>

                                <a
                                    href="<?= BASE_URL ?>/cadastro"
                                    class="dropdown-item">

                                    <i
                                        class="
                                            bi
                                            bi-person-plus
                                            me-2
                                        "></i>

                                    Criar conta

                                </a>

                            </li>

                        </ul>

                    </div>


                    <!-- ADMIN -->

                    <a
                        href="<?= BASE_URL ?>/login-admin"
                        class="
                            btn
                            btn-dark
                            btn-navbar-menor
                        "
                        title="Admin"
                        aria-label="Admin">

                        <i
                            class="
                                bi
                                bi-shield-lock
                            "
                            aria-hidden="true"></i>

                    </a>


                <?php else: ?>


                    <!-- CLIENTE DESKTOP -->

                    <div
                        class="dropdown-cliente-custom">

                        <div
                            class="
                                btn
                                btn-navbar-cliente
                                d-flex
                                align-items-center
                                gap-2
                            "
                            role="button"
                            tabindex="0"
                            aria-expanded="false"
                            aria-controls="menuClienteDesktop">

                            <span
                                class="
                                    navbar-cliente-boas-vindas
                                ">

                                Bem-vindo,

                                <strong>
                                    <?= htmlspecialchars(
                                        $clienteNome,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                            </span>


                            <span
                                class="
                                    navbar-cliente-avatar
                                ">

                                <?php if (
                                    !empty(
                                        $clienteFoto
                                    )
                                ): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            (string)
                                            $clienteFoto,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt="Foto de perfil">

                                <?php else: ?>

                                    <i
                                        class="
                                            bi
                                            bi-person
                                        "
                                        aria-hidden="true"></i>

                                <?php endif; ?>

                            </span>


                            <i
                                class="
                                    bi
                                    bi-chevron-down
                                "
                                aria-hidden="true"></i>

                        </div>


                        <ul
                            id="menuClienteDesktop"
                            class="
                                dropdown-menu-cliente-custom
                            ">

                            <li>

                                <a
                                    href="<?= BASE_URL ?>/cliente/perfil"
                                    class="dropdown-item">

                                    <i
                                        class="
                                            bi
                                            bi-pencil
                                            me-2
                                        "></i>

                                    Editar perfil

                                </a>

                            </li>


                            <li>

                                <a
                                    href="<?= BASE_URL ?>/cliente/pedidos"
                                    class="dropdown-item">

                                    <i
                                        class="
                                            bi
                                            bi-box-seam
                                            me-2
                                        "></i>

                                    Meus pedidos

                                </a>

                            </li>


                            <li>

                                <a
                                    href="<?= BASE_URL ?>/cliente/enderecos"
                                    class="dropdown-item">

                                    <i
                                        class="
                                            bi
                                            bi-geo-alt
                                            me-2
                                        "></i>

                                    Meus endereços

                                </a>

                            </li>


                            <li>

                                <a
                                    href="<?= BASE_URL ?>/cliente/seguranca"
                                    class="dropdown-item">

                                    <i
                                        class="
                                            bi
                                            bi-shield-lock
                                            me-2
                                        "></i>

                                    Segurança

                                </a>

                            </li>


                            <li>

                                <hr
                                    class="dropdown-divider">

                            </li>


                            <li>

                                <form
                                    method="POST"
                                    action="<?= BASE_URL ?>/logout">

                                    <input
                                        type="hidden"
                                        name="_csrf"
                                        value="<?= htmlspecialchars(
                                            Csrf::gerarCliente(),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                                    <button
                                        type="submit"
                                        class="
                                            dropdown-item
                                            text-danger
                                        ">

                                        <i
                                            class="
                                                bi
                                                bi-box-arrow-right
                                                me-2
                                            "
                                            aria-hidden="true"></i>

                                        Sair

                                    </button>

                                </form>

                            </li>

                        </ul>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </div>

</nav>


<script>

(function () {

    'use strict';


    function fecharDropdown(dropdown) {

        if (!dropdown) {
            return;
        }


        dropdown.classList.remove(
            'aberto'
        );


        const botao =
            dropdown.querySelector(
                '.btn-navbar-cliente'
            );


        if (botao) {

            botao.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    }


    function fecharTodos(dropdownIgnorar = null) {

        document
            .querySelectorAll(
                '.dropdown-cliente-custom.aberto'
            )
            .forEach(function (dropdown) {

                if (
                    dropdown !==
                    dropdownIgnorar
                ) {

                    fecharDropdown(
                        dropdown
                    );

                }

            });

    }


    function inicializarDropdownsCliente() {

        const dropdowns =
            document.querySelectorAll(
                '.dropdown-cliente-custom'
            );


        dropdowns.forEach(function (dropdown) {

            if (
                dropdown.dataset.clienteInicializado ===
                'true'
            ) {
                return;
            }


            const botao =
                dropdown.querySelector(
                    '.btn-navbar-cliente'
                );


            const menu =
                dropdown.querySelector(
                    '.dropdown-menu-cliente-custom'
                );


            if (
                !botao
                ||
                !menu
            ) {
                return;
            }


            dropdown.dataset.clienteInicializado =
                'true';


            botao.addEventListener(
                'click',
                function (evento) {

                    evento.preventDefault();

                    evento.stopPropagation();


                    const aberto =
                        dropdown.classList.contains(
                            'aberto'
                        );


                    fecharTodos(
                        dropdown
                    );


                    if (aberto) {

                        fecharDropdown(
                            dropdown
                        );

                        return;
                    }


                    dropdown.classList.add(
                        'aberto'
                    );


                    botao.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                }
            );


            botao.addEventListener(
                'keydown',
                function (evento) {

                    if (
                        evento.key ===
                        'Enter'
                        ||
                        evento.key ===
                        ' '
                    ) {

                        evento.preventDefault();

                        botao.click();

                    }


                    if (
                        evento.key ===
                        'Escape'
                    ) {

                        fecharDropdown(
                            dropdown
                        );

                    }

                }
            );


            menu.addEventListener(
                'click',
                function (evento) {

                    evento.stopPropagation();

                }
            );

        });

    }


    document.addEventListener(
        'click',
        function (evento) {

            document
                .querySelectorAll(
                    '.dropdown-cliente-custom.aberto'
                )
                .forEach(function (dropdown) {

                    if (
                        !dropdown.contains(
                            evento.target
                        )
                    ) {

                        fecharDropdown(
                            dropdown
                        );

                    }

                });

        }
    );


    document.addEventListener(
        'keydown',
        function (evento) {

            if (
                evento.key !==
                'Escape'
            ) {
                return;
            }


            document
                .querySelectorAll(
                    '.dropdown-cliente-custom.aberto'
                )
                .forEach(function (dropdown) {

                    fecharDropdown(
                        dropdown
                    );

                });

        }
    );


    if (
        document.readyState ===
        'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            inicializarDropdownsCliente
        );

    } else {

        inicializarDropdownsCliente();

    }

})();

</script>