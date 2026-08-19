<?php

declare(strict_types=1);

$categoriasDestaques =
    $categoriasDestaques
    ?? [];

?>

<section class="destaques-section py-5">

    <div class="container">

        <div class="text-center mb-5">

            <p class="section-etiqueta">
                Destaques
            </p>

            <h2 class="fw-bold mb-0">
                Nossos destaques
            </h2>

            <p class="text-secondary mt-3 mb-0">
                Confira alguns dos sabores que fazem sucesso no Cantim do Lanche.
            </p>

        </div>


        <div class="row g-4">

            <?php if ($categoriasDestaques === []): ?>

                <div class="col-12">

                    <div class="alert alert-info text-center">
                        Nenhum destaque disponível no momento.
                    </div>

                </div>

            <?php endif; ?>


            <?php foreach ($categoriasDestaques as $categoria): ?>

                <div class="col-md-6 col-lg-4">

                    <article class="destaque-card card border-0 shadow-sm h-100">

                        <div class="card-body d-flex flex-column">

                            <span class="destaque-etiqueta mb-3">
                                Destaque
                            </span>


                            <h3 class="h5 fw-bold">

                                <?= htmlspecialchars(
                                    $categoria['nome'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </h3>


                            <p class="text-secondary">

                                <?= htmlspecialchars(
                                    $categoria['descricao'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </p>


                            <div class="mt-auto">

                                <p class="destaque-preco mb-3">

                                    R$

                                    <?= number_format(
                                        (float) $categoria['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </p>


                                <a
                                    class="btn btn-marca w-100"
                                    href="<?= BASE_URL ?>/cardapio/categoria/<?= (int) $categoria['id'] ?>"
                                >
                                    Ver sabores
                                </a>

                            </div>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>