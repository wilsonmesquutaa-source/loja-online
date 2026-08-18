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

$carrinho =
    $carrinho ?? [];

$totalCarrinho =
    0.0;

$nomesCategorias = [
    'cento_tradicionais' =>
        'Salgados Tradicionais',

    'cento_folhados' =>
        'Salgados Folhados',

    'salgados_grandes' =>
        'Salgados Grandes',

    'empadao' =>
        'Empadão de Frango',

    'unica' =>
        'Produto',
];

?>

<main>

    <div class="container py-5">

        <h1 class="fw-bold mb-4">
            Seu carrinho
        </h1>


        <?php if (
            $carrinho === []
        ): ?>

            <div class="alert alert-info">

                Seu carrinho está vazio.

            </div>


            <a
                href="<?= BASE_URL ?>/cardapio"
                class="btn btn-voltar-cardapio">

                <i
                    class="bi bi-arrow-left me-1">
                </i>

                Voltar ao cardápio

            </a>


        <?php else: ?>


            <?php foreach (
                $carrinho
                as $item
            ): ?>

                <?php

                $tipoCategoria =
                    $item[
                        'tipo_categoria'
                    ]
                    ?? 'unica';

                $nomeCategoria =
                    $nomesCategorias[
                        $tipoCategoria
                    ]
                    ?? 'Produto';

                $totalCarrinho +=
                    (float)
                    $item['subtotal'];

                ?>


                <div
                    class="card
                           shadow-sm
                           border-0
                           mb-4">

                    <div class="card-body">


                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start
                                   gap-3
                                   mb-4">

                            <div>

                                <h2
                                    class="h5
                                           fw-bold
                                           mb-1">

                                    <?= htmlspecialchars(
                                        $nomeCategoria,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </h2>

                                <small
                                    class="text-secondary">

                                    <?= htmlspecialchars(
                                        $item[
                                            'categoria_nome'
                                        ],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </small>

                            </div>


                            <div
                                class="d-flex
                                       gap-2">


                                <a
                                    href="<?= BASE_URL ?>/carrinho/editar/<?= (int) $item['categoria_id'] ?>"
                                    class="btn btn-outline-secondary btn-sm">

                                    <i
                                        class="bi bi-pencil me-1">
                                    </i>

                                    Editar

                                </a>


                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    data-remover-carrinho
                                    data-categoria-id="<?= (int) $item['categoria_id'] ?>"
                                    data-nome="<?= htmlspecialchars(
                                        $nomeCategoria,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRemoverCarrinho">

                                    <i
                                        class="bi bi-trash me-1">
                                    </i>

                                    Remover

                                </button>

                            </div>

                        </div>


                        <div class="mb-4">

                            <?php foreach (
                                $item[
                                    'produtos'
                                ] ?? []
                                as $produto
                            ): ?>

                                <div
                                    class="d-flex
                                           justify-content-between
                                           py-2
                                           border-bottom">

                                    <span>

                                        <?= htmlspecialchars(
                                            $produto['nome'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        ×

                                        <?= (int)
                                            $produto[
                                                'quantidade'
                                            ] ?>

                                    </span>

                                </div>

                            <?php endforeach; ?>

                        </div>


                        <?php if (
                            $tipoCategoria ===
                            'cento_tradicionais'
                            ||
                            $tipoCategoria ===
                            'cento_folhados'
                        ): ?>

                            <div
                                class="d-flex
                                       justify-content-between
                                       mb-2">

                                <span>
                                    Quantidade
                                </span>

                                <strong>

                                    <?= (int)
                                        $item[
                                            'quantidade_centos'
                                        ] ?>

                                    <?= (
                                        (int)
                                        $item[
                                            'quantidade_centos'
                                        ]
                                    ) === 1
                                        ? 'cento'
                                        : 'centos'
                                    ?>

                                </strong>

                            </div>


                            <div
                                class="d-flex
                                       justify-content-between
                                       mb-2">

                                <span>
                                    Valor
                                </span>

                                <strong>

                                    R$

                                    <?= number_format(
                                        (float)
                                        $item[
                                            'preco_unitario'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                    / cento

                                </strong>

                            </div>


                        <?php elseif (
                            $tipoCategoria ===
                            'salgados_grandes'
                        ): ?>

                            <div
                                class="d-flex
                                       justify-content-between
                                       mb-2">

                                <span>
                                    Quantidade total
                                </span>

                                <strong>

                                    <?= (int)
                                        $item[
                                            'quantidade_total'
                                        ] ?>

                                    unidades

                                </strong>

                            </div>


                            <div
                                class="d-flex
                                       justify-content-between
                                       mb-2">

                                <span>
                                    Preço por unidade
                                </span>

                                <strong>

                                    R$

                                    <?= number_format(
                                        (float)
                                        (
                                            $item[
                                                'produtos'
                                            ][0][
                                                'preco_unitario'
                                            ]
                                            ?? 0
                                        ),
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </strong>

                            </div>


                        <?php elseif (
                            $tipoCategoria ===
                            'empadao'
                        ): ?>

                            <div
                                class="d-flex
                                       justify-content-between
                                       mb-2">

                                <span>
                                    Quantidade
                                </span>

                                <strong>

                                    <?= (int)
                                        $item[
                                            'quantidade_total'
                                        ] ?>

                                    unidade(s)

                                </strong>

                            </div>


                            <div
                                class="d-flex
                                       justify-content-between
                                       mb-2">

                                <span>
                                    Preço por unidade
                                </span>

                                <strong>

                                    R$

                                    <?= number_format(
                                        (float)
                                        (
                                            $item[
                                                'produtos'
                                            ][0][
                                                'preco_unitario'
                                            ]
                                            ?? 0
                                        ),
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </strong>

                            </div>

                        <?php endif; ?>


                        <div
                            class="d-flex
                                   justify-content-between
                                   mt-3
                                   pt-3
                                   border-top">

                            <strong>
                                Subtotal
                            </strong>

                            <strong
                                class="fs-5">

                                R$

                                <?= number_format(
                                    (float)
                                    $item[
                                        'subtotal'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>


            <div
                class="card
                       shadow-sm
                       border-0">

                <div class="card-body">

                    <div
                        class="d-flex
                               justify-content-between
                               align-items-center">

                        <span
                            class="fs-5
                                   fw-bold">

                            Total do carrinho

                        </span>

                        <strong
                            class="fs-4
                                   text-warning">

                            R$

                            <?= number_format(
                                $totalCarrinho,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>


                    <div
                        class="mt-4">

                        <a
                            href="<?= BASE_URL ?>/cardapio"
                            class="btn btn-outline-secondary">

                            <i
                                class="bi bi-arrow-left me-1">
                            </i>

                            Continuar comprando

                        </a>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>

</main>


<div
    class="modal fade"
    id="modalRemoverCarrinho"
    tabindex="-1"
    aria-labelledby="tituloModalRemoverCarrinho"
    aria-hidden="true">

    <div
        class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h2
                    class="modal-title fs-5"
                    id="tituloModalRemoverCarrinho">

                    Remover item

                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fechar">
                </button>

            </div>


            <div class="modal-body">

                <p class="mb-0">

                    Tem certeza de que deseja remover
                    <strong
                        data-nome-item-remover>

                        este item

                    </strong>

                    do carrinho?

                </p>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>


                <form
                    id="formRemoverCarrinho"
                    action="<?= BASE_URL ?>/carrinho/remover"
                    method="POST">

                    <input
                        type="hidden"
                        name="categoria_id"
                        value=""
                        id="categoriaRemoverCarrinho">


                    <button
                        type="submit"
                        class="btn btn-danger">

                        <i
                            class="bi bi-trash me-1">
                        </i>

                        Remover

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';