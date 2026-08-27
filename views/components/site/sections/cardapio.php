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


            <?php if (
                $categorias === []
            ): ?>

                <div class="col-12">

                    <div class="alert alert-info">

                        Nenhuma categoria encontrada.

                    </div>

                </div>

            <?php endif; ?>


            <?php foreach (
                $categorias
                as $categoria
            ): ?>

                <div class="col-md-6 col-lg-3">


                    <article
                        class="
                            produto-card
                            card
                            border-0
                            shadow-sm
                            h-100
                            overflow-hidden
                        "
                    >


                        <!-- =================================
                             IMAGEM DA CATEGORIA
                        ================================== -->

                        <div
                            class="cardapio-categoria-imagem"
                        >

                            <?php if (
                                !empty(
                                    $categoria['imagem_url']
                                )
                            ): ?>

                                <img
                                    src="<?= BASE_URL . $categoria['imagem_url'] ?>"
                                    alt="<?= htmlspecialchars(
                                        'Imagem de ' . $categoria['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    style="
                                        object-position:
                                            <?= isset(
                                                $categoria['imagem_posicao_x']
                                            )
                                                ? (float)
                                                    $categoria['imagem_posicao_x']
                                                : 50
                                            ?>%
                                            <?= isset(
                                                $categoria['imagem_posicao_y']
                                            )
                                                ? (float)
                                                    $categoria['imagem_posicao_y']
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

                        <div
                            class="
                                card-body
                                d-flex
                                flex-column
                            "
                        >


                            <!-- ETIQUETA -->

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


                            <!-- NOME -->

                            <h3
                                class="
                                    h5
                                    fw-bold
                                "
                            >

                                <?= htmlspecialchars(
                                    $categoria['nome'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </h3>


                            <!-- DESCRIÇÃO -->

                            <p
                                class="
                                    text-secondary
                                "
                            >

                                <?= htmlspecialchars(
                                    $categoria['descricao']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </p>


                            <!-- PREÇO E BOTÃO -->

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


<style>

/*
=================================
IMAGEM DA CATEGORIA
=================================
*/

.cardapio-categoria-imagem {

    width:
        100%;

    height:
        190px;

    overflow:
        hidden;

    background:
        #f8f5f0;
}


/*
=================================
IMAGEM REAL
=================================
*/

.cardapio-categoria-imagem img {

    width:
        100%;

    height:
        100%;

    display:
        block;

    object-fit:
        cover;

    transition:
        transform 0.4s ease;
}


/*
=================================
EFEITO HOVER
=================================
*/

.produto-card:hover
.cardapio-categoria-imagem img {

    transform:
        scale(1.05);
}


/*
=================================
PLACEHOLDER
=================================
*/

.cardapio-categoria-imagem-placeholder {

    width:
        100%;

    height:
        100%;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        #f8f5f0;

    color:
        #9ca3af;

    font-size:
        3rem;
}

</style>
