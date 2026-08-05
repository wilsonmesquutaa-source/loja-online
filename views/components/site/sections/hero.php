<?php

declare(strict_types=1);

$tituloHero = $tituloHero
    ?? 'Encontre tudo o que precisa';

$textoHero = $textoHero
    ?? 'Produtos selecionados para você.';

?>

<section class="hero-loja py-5">

    <div class="container py-lg-5">

        <div class="row align-items-center g-5">

            <div class="col-lg-7">

                <p class="hero-etiqueta mb-3">
                    Loja Online
                </p>

                <h1 class="display-4 fw-bold">
                    <?=
                        htmlspecialchars(
                            $tituloHero,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>
                </h1>

                <p class="lead mt-3 mb-4">
                    <?=
                        htmlspecialchars(
                            $textoHero,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>
                </p>

                <div class="d-flex flex-wrap gap-3">

                    <a
                        class="btn btn-primary btn-lg"
                        href="<?= BASE_URL ?>/produtos"
                    >
                        Ver produtos
                    </a>

                    <a
                        class="btn btn-outline-dark btn-lg"
                        href="#beneficios"
                    >
                        Conhecer benefícios
                    </a>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="hero-card shadow-lg">

                    <i
                        class="bi bi-cart-check"
                        aria-hidden="true"
                    ></i>

                    <h2 class="h4 mt-3">
                        Compra simples e segura
                    </h2>

                    <p class="mb-0">
                        Produtos, pedidos, pagamentos e entregas
                        organizados em uma única plataforma.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>
