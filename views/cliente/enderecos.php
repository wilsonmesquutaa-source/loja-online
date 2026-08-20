<?php

declare(strict_types=1);

use App\Helpers\View;

require APP_ROOT
    . '/views/layouts/site/header.php';

View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
            $rotaAtual,
    ]
);

$erro =
    $_GET['erro']
    ?? null;

$sucesso =
    $_GET['sucesso']
    ?? null;

?>

<main class="cliente-enderecos-page">

    <div class="cliente-enderecos-container">

        <header class="cliente-enderecos-header">

            <p class="cliente-enderecos-etiqueta">
                Minha conta
            </p>

            <h1>
                Meus endereços
            </h1>

            <p>
                Cadastre os locais onde deseja receber seus pedidos.
            </p>

        </header>


        <?php if ($erro !== null): ?>

            <div
                class="cliente-enderecos-alert cliente-enderecos-alert-danger"
            >
                <?= htmlspecialchars(
                    (string) $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <?php if ($sucesso !== null): ?>

            <div
                class="cliente-enderecos-alert cliente-enderecos-alert-success"
            >
                <?= htmlspecialchars(
                    (string) $sucesso,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <div class="cliente-enderecos-topo">

            <div>
                <h2>
                    Endereços cadastrados
                </h2>

                <p>
                    Você pode cadastrar vários endereços para escolher onde deseja receber cada pedido.
                </p>
            </div>


            <a
                href="<?= BASE_URL ?>/cliente/enderecos/novo"
                class="cliente-enderecos-btn-principal"
            >
                + Adicionar endereço
            </a>

        </div>


        <?php if ($enderecos === []): ?>

            <section class="cliente-enderecos-vazio">

                <div class="cliente-enderecos-vazio-icone">
                    📍
                </div>

                <h2>
                    Nenhum endereço cadastrado
                </h2>

                <p>
                    Cadastre seu primeiro endereço para facilitar suas próximas compras.
                </p>

                <a
                    href="<?= BASE_URL ?>/cliente/enderecos/novo"
                    class="cliente-enderecos-btn-principal"
                >
                    Adicionar endereço
                </a>

            </section>

        <?php else: ?>

            <section class="cliente-enderecos-lista">

                <?php foreach (
                    $enderecos
                    as $endereco
                ): ?>

                    <article class="cliente-endereco-card">

                        <div class="cliente-endereco-card-topo">

                            <div>

                                <div class="cliente-endereco-titulo">

                                    <span>
                                        📍
                                    </span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            (string)
                                            $endereco['identificacao'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                </div>

                                <?php if (
                                    (int)
                                    $endereco['principal']
                                    === 1
                                ): ?>

                                    <span
                                        class="cliente-endereco-principal"
                                    >
                                        Endereço principal
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <div class="cliente-endereco-dados">

                            <p>
                                <strong>
                                    <?= htmlspecialchars(
                                        (string)
                                        $endereco['destinatario'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>
                            </p>

                            <p>
                                <?= htmlspecialchars(
                                    (string)
                                    $endereco['logradouro'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>,
                                <?= htmlspecialchars(
                                    (string)
                                    $endereco['numero'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>


                            <?php if (
                                !empty(
                                    $endereco['complemento']
                                )
                            ): ?>

                                <p>
                                    <?= htmlspecialchars(
                                        (string)
                                        $endereco['complemento'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                            <?php endif; ?>


                            <p>
                                <?= htmlspecialchars(
                                    (string)
                                    $endereco['bairro'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                                ·

                                <?= htmlspecialchars(
                                    (string)
                                    $endereco['cidade'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                                -

                                <?= htmlspecialchars(
                                    (string)
                                    $endereco['estado'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>


                            <p>
                                CEP:
                                <?= htmlspecialchars(
                                    (string)
                                    $endereco['cep'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                        </div>


                        <div class="cliente-endereco-acoes">

                            <?php if (
                                (int)
                                $endereco['principal']
                                !== 1
                            ): ?>

                                <form
                                    method="POST"
                                    action="<?= BASE_URL ?>/cliente/enderecos/principal/<?= (int) $endereco['id'] ?>"
                                >

                                    <input
                                        type="hidden"
                                        name="_csrf"
                                        value="<?= htmlspecialchars(
                                            $csrfToken,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="cliente-endereco-btn cliente-endereco-btn-principal"
                                    >
                                        Tornar principal
                                    </button>

                                </form>

                            <?php endif; ?>


                            <a
                                href="<?= BASE_URL ?>/cliente/enderecos/editar/<?= (int) $endereco['id'] ?>"
                                class="cliente-endereco-btn cliente-endereco-btn-editar"
                            >
                                Editar
                            </a>


                            <form
                                method="POST"
                                action="<?= BASE_URL ?>/cliente/enderecos/excluir/<?= (int) $endereco['id'] ?>"
                                onsubmit="return confirm('Deseja realmente excluir este endereço?');"
                            >

                                <input
                                    type="hidden"
                                    name="_csrf"
                                    value="<?= htmlspecialchars(
                                        $csrfToken,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <button
                                    type="submit"
                                    class="cliente-endereco-btn cliente-endereco-btn-excluir"
                                >
                                    Excluir
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';