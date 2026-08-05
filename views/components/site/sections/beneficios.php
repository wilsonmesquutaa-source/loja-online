<?php

declare(strict_types=1);

$beneficios = $beneficios ?? [];

?>

<section
    class="py-5 bg-white"
    id="beneficios"
>

    <div class="container">

        <div class="text-center mb-5">

            <p class="section-etiqueta">
                Vantagens
            </p>

            <h2 class="fw-bold">
                Por que comprar conosco?
            </h2>

        </div>

        <div class="row g-4">

            <?php foreach ($beneficios as $beneficio): ?>

                <div class="col-md-6 col-lg-3">

                    <article
                        class="beneficio-card h-100"
                    >

                        <div class="beneficio-icone">

                            <i
                                class="<?=
                                    htmlspecialchars(
                                        $beneficio['icone'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>"
                                aria-hidden="true"
                            ></i>

                        </div>

                        <h3 class="h5">
                            <?=
                                htmlspecialchars(
                                    $beneficio['titulo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ?>
                        </h3>

                        <p class="text-secondary mb-0">
                            <?=
                                htmlspecialchars(
                                    $beneficio['texto'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ?>
                        </p>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>
