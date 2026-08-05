<?php

declare(strict_types=1);

$produtos = $produtos ?? [];

?>

<section class="py-5">

    <div class="container">

        <div
            class="d-flex flex-column flex-md-row
                   justify-content-between
                   align-items-md-end gap-3 mb-5"
        >

            <div>

                <p class="section-etiqueta">
                    Destaques
                </p>

                <h2 class="fw-bold mb-0">
                    Produtos selecionados
                </h2>

            </div>

            <a
                class="btn btn-outline-primary"
                href="<?= BASE_URL ?>/produtos"
            >
                Ver todos
            </a>

        </div>

        <div class="row g-4">

            <?php if ($produtos === []): ?>

                <div class="col-12">

                    <div class="alert alert-info">
                        Nenhum produto em destaque foi encontrado.
                    </div>

                </div>

            <?php endif; ?>

            <?php foreach ($produtos as $produto): ?>

                <div class="col-md-6 col-lg-4">

                    <article
                        class="produto-card card
                               border-0 shadow-sm h-100"
                    >

                        <div class="card-body d-flex flex-column">

                            <span
                                class="badge text-bg-primary
                                       align-self-start mb-3"
                            >
                                Destaque
                            </span>

                            <h3 class="h5">
                                <?=
                                    htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>
                            </h3>

                            <p class="text-secondary">
                                <?=
                                    htmlspecialchars(
                                        $produto['descricao'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>
                            </p>

                            <p
                                class="fs-4 fw-bold
                                       text-primary mt-auto"
                            >
                                R$
                                <?=
                                    number_format(
                                        (float)
                                            $produto['preco'],
                                        2,
                                        ',',
                                        '.'
                                    )
                                ?>
                            </p>

                            <a
                                class="btn btn-primary"
                                href="<?=
                                    BASE_URL
                                ?>/produtos"
                            >
                                Ver produto
                            </a>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>
