<?php

declare(strict_types=1);

use App\Repositories\CarrinhoRepository;

$rotaAtual =
    $rotaAtual ?? '';


if (
    session_status() !==
    PHP_SESSION_ACTIVE
) {
    session_start();
}


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
                            $item[
                                'categoria_nome'
                            ]
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


                $grupos[
                    $categoriaId
                ]['tipo'] = $tipo;


                $grupos[
                    $categoriaId
                ]['quantidade'] +=
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

?>

<nav
    class="navbar navbar-expand-lg bg-white shadow-sm sticky-top"
    aria-label="Navegação principal">

    <div class="container">

        <a
            class="navbar-brand d-flex align-items-center gap-2"
            href="<?= BASE_URL ?>/">

            <img
                src="<?= BASE_URL ?>/assets/images/logo.png"
                alt="Cantim do Lanche"
                height="55">

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Abrir menu">

            <span
                class="navbar-toggler-icon">
            </span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="menuPrincipal">


            <ul
                class="navbar-nav mx-auto align-items-lg-center">

                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold
                        <?= $rotaAtual === 'home'
                            ? 'active'
                            : ''
                        ?>"
                        href="<?= BASE_URL ?>/">

                        Início

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold"
                        href="<?= BASE_URL ?>/produtos">

                        Cardápio

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold"
                        href="<?= BASE_URL ?>/quemsomos">

                        Quem Somos

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold"
                        href="<?= BASE_URL ?>/contato">

                        Contato

                    </a>

                </li>

            </ul>


            <div
                class="
                d-flex
                flex-column
                flex-lg-row
                align-items-lg-center
                gap-2
                ">


                <form
                    class="d-flex"
                    action="<?= BASE_URL ?>/produtos"
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
                                class="bi bi-search">
                            </i>

                        </button>

                    </div>

                </form>


                <a
                    href="<?= BASE_URL ?>/carrinho"
                    class="btn btn-carrinho position-relative"
                    title="Carrinho">

                    <i class="bi bi-cart3"></i>

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


                <a
                    href="<?= BASE_URL ?>/login"
                    class="btn btn-outline-secondary">

                    <i
                        class="bi bi-person">
                    </i>

                    Entrar

                </a>


                <a
                    href="<?= BASE_URL ?>/cadastro"
                    class="btn btn-warning text-white">

                    Cadastrar

                </a>


                <a
                    href="<?= BASE_URL ?>/login-admin"
                    class="btn btn-dark">

                    <i
                        class="bi bi-shield-lock">
                    </i>

                    Admin

                </a>

            </div>

        </div>

    </div>

</nav>