<?php

declare(strict_types=1);

$categorias =
    $categorias
    ?? [];

$etiquetaProdutos =
    $etiquetaProdutos
    ?? 'Cardápio';

?>

<section class="produtos-section py-5">

    <div class="container">


        <div
            class="
                d-flex
                flex-column
                flex-md-row
                justify-content-between
                align-items-md-end
                gap-3
                mb-5
            "
        >

            <div>

                <p class="section-etiqueta">
                    Nosso cardápio
                </p>


                <h2 class="fw-bold mb-0">
                    Salgados artesanais
                </h2>

            </div>

        </div>


        <div class="row g-4">


            <?php if ($categorias === []): ?>

                <div class="col-12">

                    <div class="alert alert-info">
                        Nenhuma categoria encontrada.
                    </div>

                </div>

            <?php endif; ?>


            <?php foreach ($categorias as $categoria): ?>

                <div class="col-md-6 col-lg-3">

                    <article
                        class="
                            produto-card
                            card
                            border-0
                            shadow-sm
                            h-100
                        "
                    >

                        <div
                            class="
                                card-body
                                d-flex
                                flex-column
                            "
                        >

                            <span
                                class="
                                    badge
                                    bg-warning
                                    text-dark
                                    align-self-start
                                    mb-3
                                "
                            >

                                <?= htmlspecialchars(
                                    $etiquetaProdutos,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

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

                                <p
                                    class="
                                        fs-4
                                        fw-bold
                                        text-warning
                                        mb-3
                                    "
                                >

                                    R$

                                    <?= number_format(
                                        (float)
                                        $categoria['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </p>


                                <a
                                    class="
                                        btn
                                        btn-warning
                                        text-white
                                        w-100
                                    "
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