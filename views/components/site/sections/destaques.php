<?php

declare(strict_types=1);

$destaquesHome =
    $destaquesHome
    ?? [];

?>

<section class="destaques-section py-5">

    <div class="container">

        <!-- =================================
             CABEÇALHO
        ================================== -->

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


        <!-- =================================
             LISTA DE DESTAQUES
        ================================== -->

        <div class="row g-4">

            <?php if ($destaquesHome === []): ?>

                <div class="col-12">

                    <div class="alert alert-info text-center">

                        Nenhum destaque disponível no momento.

                    </div>

                </div>

            <?php endif; ?>


            <?php foreach ($destaquesHome as $destaque): ?>


                <?php

                /* =================================
                   TÍTULO
                ================================== */

                $titulo =
                    $destaque['tipo'] === 'produto'
                        ? (
                            $destaque['produto_nome']
                            ?? ''
                        )
                        : (
                            $destaque['categoria_nome']
                            ?? ''
                        );


                /* =================================
                   DESCRIÇÃO
                ================================== */

                $descricao =
                    $destaque['tipo'] === 'produto'
                        ? (
                            $destaque['produto_descricao']
                            ?? ''
                        )
                        : (
                            $destaque['categoria_descricao']
                            ?? ''
                        );

                ?>


                <!-- =================================
                     CARD
                ================================== -->

                <div class="col-6 col-md-6 col-lg-4">

                    <article
                        class="
                            destaque-card
                            produto-card
                            card
                            border-0
                            shadow-sm
                            overflow-hidden
                        "
                    >


                        <!-- =================================
                             IMAGEM
                        ================================== -->

                        <div class="cardapio-categoria-imagem">

                            <?php if (
                                !empty(
                                    $destaque['imagem_url']
                                )
                            ): ?>

                                <img
                                    src="<?= BASE_URL . $destaque['imagem_url'] ?>"
                                    alt="<?= htmlspecialchars(
                                        'Imagem de ' . $titulo,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    style="
                                        object-position:
                                            <?= isset(
                                                $destaque[
                                                    'imagem_posicao_x'
                                                ]
                                            )
                                                ? (float)
                                                    $destaque[
                                                        'imagem_posicao_x'
                                                    ]
                                                : 50
                                            ?>%
                                            <?= isset(
                                                $destaque[
                                                    'imagem_posicao_y'
                                                ]
                                            )
                                                ? (float)
                                                    $destaque[
                                                        'imagem_posicao_y'
                                                    ]
                                                : 50
                                            ?>%;
                                    "
                                >

                            <?php else: ?>

                                <div
                                    class="
                                        cardapio-categoria-imagem-placeholder
                                    "
                                >

                                    <i
                                        class="
                                            bi
                                            bi-image
                                        "
                                        aria-hidden="true"
                                    ></i>

                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- =================================
                             CONTEÚDO
                        ================================== -->

                        <div class="card-body">


                            <!-- =================================
                                 ETIQUETA
                            ================================== -->

                            <span
                                class="
                                    destaque-etiqueta
                                "
                            >

                                Destaque

                            </span>


                            <!-- =================================
                                 TÍTULO
                            ================================== -->

                            <h3 class="h5 fw-bold">

                                <?= htmlspecialchars(
                                    $titulo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </h3>


                            <!-- =================================
                                 DESCRIÇÃO
                            ================================== -->

                            <?php if (
                                !empty(
                                    trim(
                                        $descricao
                                    )
                                )
                            ): ?>

                                <p
                                    class="
                                        text-secondary
                                        destaque-descricao
                                    "
                                >

                                    <?= htmlspecialchars(
                                        $descricao,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <!-- =================================
                                 AÇÕES
                            ================================== -->

                            <div class="destaque-acoes">


                                <!-- =================================
                                     PREÇO
                                ================================== -->

                                <p class="destaque-preco">

                                    R$

                                    <?= number_format(
                                        (float)
                                        $destaque['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </p>


                                <!-- =================================
                                     BOTÃO
                                ================================== -->

                                <a
                                    class="
                                        btn
                                        btn-marca
                                        w-100
                                    "
                                    href="<?= BASE_URL ?>/cardapio/categoria/<?= (int) $destaque['categoria_id'] ?>"
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