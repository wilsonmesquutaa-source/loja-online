<?php

declare(strict_types=1);

$categoria = $categoria ?? [];
$produtos = $produtos ?? [];

$nomeCategoria = $categoria['nome'] ?? 'Categoria';


?>

<div
    class="categoria-selecao"
    data-limite="<?= $limiteOpcoes ?>">

    <div
        class="mb-4"
        data-limite-opcoes="<?= (int) $limiteOpcoes ?>">

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

                    <strong>
                        Escolha até
                        <?= $limiteOpcoes ?>
                        sabores.
                    </strong>

                    <br>

                    <span class="small">

                        Opções escolhidas:
                        <strong data-contador-opcoes>
                            0
                        </strong>
                        /
                        <?= $limiteOpcoes ?>

                    </span>

                </div>

            </div>

        </div>

    </div>


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
                                    class="bi bi-image fs-1 text-secondary"></i>

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

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>