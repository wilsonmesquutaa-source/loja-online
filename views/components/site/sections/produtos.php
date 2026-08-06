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
                    Nosso cardápio
                </p>


                <h2 class="fw-bold mb-0">
                    Salgados artesanais
                </h2>


            </div>


            <a
                class="btn btn-outline-warning"
                href="<?= BASE_URL ?>/produtos"
            >
                Ver cardápio completo
            </a>


        </div>





        <div class="row g-4">


            <?php if ($produtos === []): ?>


                <div class="col-12">


                    <div class="alert alert-info">

                        Nenhum produto encontrado.

                    </div>


                </div>


            <?php endif; ?>





            <?php foreach ($produtos as $produto): ?>


                <div class="col-md-6 col-lg-4">


                    <article
                        class="produto-card card
                               border-0 shadow-sm h-100"
                    >



                        <div
                            class="card-body d-flex flex-column"
                        >



                            <span
                                class="badge bg-warning text-dark
                                       align-self-start mb-3"
                            >

                                Destaque

                            </span>





                            <h3 class="h5 fw-bold">

                                <?= htmlspecialchars(
                                    $produto['nome'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>


                            </h3>





                            <p class="text-secondary">


                                <?= htmlspecialchars(
                                    $produto['descricao'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>


                            </p>





                            <div class="mt-auto">



                                <p
                                    class="fs-4 fw-bold
                                           text-warning mb-2"
                                >

                                    R$

                                    <?= number_format(
                                        (float)
                                        $produto['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>


                                </p>





                                <?php if (
                                    isset($produto['preco_revenda'])
                                ): ?>


                                    <div
                                        class="alert alert-light
                                               border"
                                    >


                                        <strong>
                                            Revenda:
                                        </strong>


                                        R$

                                        <?= number_format(
                                            (float)
                                            $produto['preco_revenda'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>


                                        por unidade


                                        <?php if (
                                            isset(
                                                $produto['quantidade_minima']
                                            )
                                        ): ?>


                                            <br>


                                            <small>

                                                A partir de

                                                <?= $produto['quantidade_minima'] ?>

                                                unidades

                                            </small>


                                        <?php endif; ?>


                                    </div>


                                <?php endif; ?>





                                <a
                                    class="btn btn-warning text-white w-100"
                                    href="<?= BASE_URL ?>/produtos"
                                >

                                    Ver detalhes

                                </a>



                            </div>


                        </div>


                    </article>


                </div>


            <?php endforeach; ?>


        </div>


    </div>


</section>