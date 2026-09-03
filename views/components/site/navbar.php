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


            foreach ($itens as $item) {

                $categoriaId =
                    (int)
                    $item['categoria_id'];


                if (
                    !isset(
                        $grupos[$categoriaId]
                    )
                ) {

                    $grupos[$categoriaId] = [
                        'tipo' => 'unica',
                        'quantidade' => 0,
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
    !empty($_SESSION['cliente_id']);


$clienteNome =
    (string) (
        $_SESSION['cliente_nome']
        ?? ''
    );


$clienteFoto =
    $_SESSION['cliente_foto_url']
    ?? null;

?>

<nav
    class="navbar navbar-expand-lg navbar-site shadow-sm sticky-top"
    aria-label="Navegação principal">

    <div class="container-fluid px-4">


        <!-- =================================
             LOGO
        ================================== -->

        <a
            class="navbar-brand-logo d-flex align-items-center gap-2"
            href="<?= BASE_URL ?>/">

            <img
                src="<?= BASE_URL ?>/assets/images/logo.webp"
                alt="Cantim do Lanche"
                height="55">

        </a>


        <!-- =================================
             MENU MOBILE
        ================================== -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Abrir menu">

            <span
                class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="menuPrincipal">


            <!-- =================================
                 MENU PRINCIPAL
            ================================== -->

            <ul
                class="navbar-nav mx-auto align-items-lg-center">

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
                 AÇÕES DA NAVBAR
            ================================== -->

            <div
                class="
                    d-flex
                    flex-column
                    flex-lg-row
                    align-items-lg-center
                    gap-2
                ">


                <!-- =================================
                     CARRINHO
                ================================== -->

                <a
                    href="<?= BASE_URL ?>/carrinho"
                    class="
                        btn
                        btn-carrinho
                        position-relative
                    "
                    title="Carrinho">

                    <i
                        class="bi bi-cart3"></i>


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


                <!-- =================================
                     PESQUISA
                ================================== -->

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
                            class="btn btn-warning"
                            type="submit">

                            <i
                                class="bi bi-search"></i>

                        </button>

                    </div>

                </form>


                <?php if (!$clienteLogado): ?>


                    <!-- =================================
                         ENTRAR
                    ================================== -->

                    <div class="dropdown">

                        <button
                            type="button"
                            class="
                                btn
                                btn-outline-secondary
                                btn-navbar-menor
                                dropdown-toggle
                            "
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i
                                class="bi bi-person"></i>

                            Entrar

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
                         ADMIN
                    ================================== -->

                    <a
                        href="<?= BASE_URL ?>/login-admin"
                        class="
                            btn
                            btn-dark
                            btn-navbar-menor
                        ">

                        <i
                            class="
                                bi
                                bi-shield-lock
                            "></i>

                        Admin

                    </a>


                <?php else: ?>


                    <!-- =================================
                         CLIENTE LOGADO
                    ================================== -->

                    <div class="dropdown">


                        <!-- =================================
                             BOTÃO DO CLIENTE
                        ================================== -->

                        <div

                            class="
                                btn
                                btn-navbar-cliente
                                d-flex
                                align-items-center
                                gap-2
                            "
                            data-bs-toggle="dropdown"
                            aria-expanded="false">


                            <span
                                class="navbar-cliente-boas-vindas">

                                Bem-vindo,

                                <strong>
                                    <?= htmlspecialchars(
                                        $clienteNome,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                            </span>


                            <!-- FOTO -->

                            <span
                                class="navbar-cliente-avatar">

                                <?php if (
                                    !empty($clienteFoto)
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


                            <!-- SETA -->

                            <i
                                class="
                                    bi
                                    bi-chevron-down
                                "
                                aria-hidden="true"></i>


                        </div>


                        <!-- =================================
                             DROPDOWN DO CLIENTE
                        ================================== -->

                        <ul
                            class="
                                dropdown-menu
                                dropdown-menu-end
                            ">

                            <!-- EDITAR PERFIL -->

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


                            <!-- MEUS PEDIDOS -->

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


                            <!-- MEUS ENDEREÇOS -->

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


                            <!-- SEGURANÇA -->

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


                            <!-- SAIR -->

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
                                            "></i>

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