<?php

declare(strict_types=1);
$temaLoja=$GLOBALS['temaLoja']??[];


$tituloHero =
    $tituloHero
    ?? 'Salgados artesanais feitos com muito amor';


$textoHero =
    $textoHero
    ?? 'Salgados fresquinhos para festas, eventos ou aquele lanche especial.';


$bannersHome =
    $bannersHome
    ?? [];

if (isset($temaLoja['assets']['banner_home'])) {
    $assetBanner = $temaLoja['assets']['banner_home'];
    $assetBanner['url_imagem'] = $assetBanner['url_arquivo'];
    $bannersHome = [$assetBanner];
}

?>

<section class="hero-loja py-5">
    <?php \App\Helpers\View::componente('site/enfeite-home', ['tipoEnfeite' => 'home_pendente']); ?>

    <div class="container py-lg-5">

        <div class="row align-items-center g-5">


            <!-- =========================================================
                 TEXTO PRINCIPAL
            ========================================================== -->

            <div class="col-lg-7">


                <p class="hero-etiqueta mb-3">

                    Cantim do Lanche

                </p>


                <h1 class="display-4 fw-bold">

                    <?= htmlspecialchars(
                        $tituloHero,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </h1>


                <p class="lead mt-3 mb-4">

                    <?= htmlspecialchars(
                        $textoHero,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </p>


                <div class="d-flex flex-wrap gap-3">

                    <a
                        class="
                            btn
                            btn-warning
                            btn-lg
                            text-white
                        "
                        href="<?= BASE_URL ?>/cardapio"
                    >

                        <i
                            class="bi bi-basket me-2"
                            aria-hidden="true"
                        ></i>

                        Ver cardápio

                    </a>

                </div>


            </div>


            <!-- =========================================================
                 BANNER DA HOME
            ========================================================== -->

            <div class="col-lg-5 tema-home-banner-area">
                <?php \App\Helpers\View::componente('site/enfeite-home', ['tipoEnfeite' => 'home_moldura']); ?>

                <div
                    class="
                        hero-card
                        hero-banner-home
                        shadow-lg
                    "
                    data-hero-galeria
                >

                    <?php if (
                        $bannersHome !== []
                    ): ?>


                        <div
                            class="
                                hero-banner-slides
                            "
                        >


                            <?php foreach (
                                $bannersHome
                                as $indice => $banner
                            ): ?>


                                <?php

                                $imagem =
                                    (string) (
                                        $banner[
                                            'url_imagem'
                                        ]
                                        ?? ''
                                    );


                                $titulo =
                                    (string) (
                                        $banner[
                                            'titulo'
                                        ]
                                        ?? ''
                                    );


                                $textoAlternativo =
                                    (string) (
                                        $banner[
                                            'texto_alternativo'
                                        ]
                                        ?? ''
                                    );


                                $posicaoX =
                                    isset(
                                        $banner[
                                            'posicao_x'
                                        ]
                                    )
                                        ? (float)
                                            $banner[
                                                'posicao_x'
                                            ]
                                        : 50.00;


                                $posicaoY =
                                    isset(
                                        $banner[
                                            'posicao_y'
                                        ]
                                    )
                                        ? (float)
                                            $banner[
                                                'posicao_y'
                                            ]
                                        : 50.00;

                                ?>


                                <figure
                                    class="
                                        hero-banner-slide
                                        <?= $indice === 0
                                            ? 'ativo'
                                            : ''
                                        ?>
                                    "
                                    data-hero-slide
                                    aria-hidden="<?= $indice === 0
                                        ? 'false'
                                        : 'true'
                                    ?>"
                                >


                                    <img
                                        src="<?= BASE_URL
                                            . htmlspecialchars(
                                                $imagem,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $textoAlternativo !== ''
                                                ? $textoAlternativo
                                                : (
                                                    $titulo !== ''
                                                        ? $titulo
                                                        : 'Banner do Cantim do Lanche'
                                                ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        loading="<?= $indice === 0
                                            ? 'eager'
                                            : 'lazy'
                                        ?>"
                                        style="
                                            opacity: <?= max(0, min(1, (float) ($banner['opacidade'] ?? 1))) ?>;
                                            <?= \App\Helpers\ImagemEscala::estilo($banner, '') ?> object-position:
                                                <?= $posicaoX ?>%
                                                <?= $posicaoY ?>%;
                                        "
                                    >


                                    <?php if (
                                        $titulo !== ''
                                    ): ?>

                                        <figcaption
                                            class="
                                                hero-banner-titulo
                                            "
                                        >

                                            <?= htmlspecialchars(
                                                $titulo,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </figcaption>

                                    <?php endif; ?>


                                </figure>


                            <?php endforeach; ?>


                        </div>


                        <?php if (
                            count($bannersHome) > 1
                        ): ?>

                            <div
                                class="
                                    hero-banner-indicadores
                                "
                                data-hero-indicadores
                                aria-label="Banners da Home"
                            ></div>

                        <?php endif; ?>


                    <?php else: ?>


                        <!-- =================================================
                             FALLBACK
                        ================================================== -->

                        <div
                            class="
                                hero-banner-vazio
                            "
                        >

                            <i
                                class="
                                    bi
                                    bi-image
                                "
                                aria-hidden="true"
                            ></i>


                            <h2 class="h4 mt-3">

                                Salgados feitos à mão

                            </h2>


                            <p class="mb-0">

                                Cadastre os banners da Home
                                pelo painel administrativo.

                            </p>

                        </div>


                    <?php endif; ?>


                </div>

            </div>


        </div>

    </div>

</section>
