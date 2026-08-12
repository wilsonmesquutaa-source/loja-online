<?php

declare(strict_types=1);

$categoria = $categoria ?? [];
$produtos = $produtos ?? [];
$tipoCategoria = $tipoCategoria ?? 'unica';

$ehSalgadosGrandes =
    $tipoCategoria === 'salgados_grandes';

$ehEmpadao =
    $tipoCategoria === 'empadao';

$limiteOpcoes =
    $limiteOpcoes ?? 1;

$nomeCategoria =
    $categoria['nome']
    ?? 'Categoria';

?>

<div
    class="categoria-selecao"
    data-tipo-categoria="<?= htmlspecialchars(
                                $tipoCategoria,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
    data-limite="<?= $limiteOpcoes !== null
                        ? (int) $limiteOpcoes
                        : ''
                    ?>">

    <div
        class="mb-4"
        data-limite-opcoes="<?= $limiteOpcoes !== null
                                ? (int) $limiteOpcoes
                                : ''
                            ?>">

        <p class="text-secondary mb-1">
            Escolha os sabores
        </p>

        <h3 class="fw-bold mb-2">

            <?= htmlspecialchars(
                $nomeCategoria,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </h3>

        <?php if (!empty($categoria['descricao'])): ?>

            <p class="text-secondary small mb-3">

                <?= htmlspecialchars(
                    $categoria['descricao'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </p>

        <?php endif; ?>


        <div class="alert alert-warning">

            <div class="d-flex align-items-center gap-2">

                <i class="bi bi-info-circle"></i>

                <div>

                    <?php if ($ehSalgadosGrandes): ?>

                        <strong>
                            Escolha a quantidade desejada
                            de cada salgado.
                        </strong>

                        <br>

                        <span class="small">
                            Cada unidade custa R$ 5,00.
                            Para revenda, a unidade custa
                            R$ 3,00, com mínimo de 10 unidades.
                        </span>


                    <?php elseif ($ehEmpadao): ?>

                        <strong>
                            Escolha a quantidade desejada.
                        </strong>

                        <br>

                        <span class="small">
                            Cada unidade custa R$ 100,00.
                        </span>


                    <?php else: ?>

                        <strong>
                            Escolha até
                            <?= (int) $limiteOpcoes ?>
                            sabores.
                        </strong>

                        <br>

                        <span class="small">

                            Opções escolhidas:

                            <strong data-contador-opcoes>
                                0
                            </strong>

                            /
                            <?= (int) $limiteOpcoes ?>

                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <form
        action="<?= BASE_URL ?>/carrinho/adicionar"
        method="POST"
        data-form-carrinho>

        <input
            type="hidden"
            name="categoria_id"
            value="<?= (int) $categoria['id'] ?>">


        <?php if ($produtos === []): ?>

            <div class="alert alert-info">

                Nenhum sabor disponível nesta categoria.

            </div>


        <?php else: ?>

            <div class="row g-3">

                <?php foreach ($produtos as $produto): ?>

                    <div
                        class="col-6 col-md-4"
                        data-produto-wrapper>

                        <div
                            class="card h-100 border-0 shadow-sm produto-selecao-card"
                            data-produto-id="<?= (int) $produto['id'] ?>">

                            <div class="card-body text-center">

                                <div
                                    class="produto-imagem-placeholder mb-3">

                                    <i
                                        class="bi bi-image fs-1 text-secondary">
                                    </i>

                                </div>


                                <h4 class="h6 fw-bold">

                                    <?= htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </h4>


                                <?php if (!empty($produto['descricao'])): ?>

                                    <p class="small text-secondary">

                                        <?= htmlspecialchars(
                                            $produto['descricao'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </p>

                                <?php endif; ?>


                                <div
                                    class="contador-produto d-flex justify-content-center align-items-center gap-2 mt-3">

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        data-diminuir
                                        disabled>

                                        <i class="bi bi-dash"></i>

                                    </button>


                                    <span
                                        class="fw-bold px-2"
                                        data-quantidade>
                                        0
                                    </span>


                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm"
                                        data-aumentar>

                                        <i class="bi bi-plus"></i>

                                    </button>


                                </div>


                                <!--
                                Campo enviado ao PHP.

                                O JavaScript mantém este campo
                                sincronizado com o contador visual.
                                -->

                                <input
                                    type="hidden"
                                    name="quantidades[<?= (int) $produto['id'] ?>]"
                                    value="0"
                                    data-input-quantidade>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <div class="mt-5 d-flex justify-content-end">

            <button
                type="submit"
                class="btn btn-warning text-white btn-lg"
                data-adicionar-carrinho>

                <i class="bi bi-cart-plus me-2"></i>

                Adicionar ao carrinho

            </button>

        </div>

    </form>

</div>