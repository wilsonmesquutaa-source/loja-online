<?php

declare(strict_types=1);

use App\Helpers\View;

$erroCategoria =
    $_GET['erro']
    ?? null;

$sucessoCategoria =
    $_GET['sucesso']
    ?? null;

$bannerCategoria =
    $bannerCategoria ?? null;

require APP_ROOT
    . '/views/layouts/site/header.php';

View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
        $rotaAtual,
    ]
);

?>
<link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/categoria.css">

<main>

    <div class="categoria-pagina">

        <!-- =================================
             IMAGEM DE DESTAQUE
        ================================== -->

        <div class="img_destaque_container">

            <div
                class="img_destaque_imagem"
                <?php if (
                    $bannerCategoria !== null
                    &&
                    !empty($bannerCategoria['url_imagem'])
                ): ?>

                style="
                        background-image:
                            url('<?= BASE_URL
                                        . $bannerCategoria['url_imagem'] ?>');

                        background-position:
                            <?= (float)
                            $bannerCategoria['posicao_x'] ?>%
                            <?= (float)
                            $bannerCategoria['posicao_y'] ?>%;
                    "

                <?php endif; ?>>
            </div>

















        </div>


        <!-- =================================
             CONTEÚDO DA CATEGORIA
        ================================== -->

        <div class="container categoria-conteudo">

            <?php if (
                $sucessoCategoria ===
                'adicionado'
            ): ?>

                <div
                    class="alert alert-success alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-check-circle me-2"></i>

                    Produto adicionado ao carrinho
                    com sucesso.

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Fechar">
                    </button>

                </div>

            <?php elseif (
                $sucessoCategoria ===
                'atualizado'
            ): ?>

                <div
                    class="alert alert-success alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-check-circle me-2"></i>

                    Carrinho atualizado
                    com sucesso.

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Fechar">
                    </button>

                </div>

            <?php endif; ?>


            <div class="mb-5">

                <a
                    href="<?= BASE_URL ?>/cardapio"
                    class="btn btn-voltar-cardapio">

                    <i class="bi bi-arrow-left me-1"></i>

                    Voltar

                </a>

            </div>


            <?php

            View::componente(
                'site/sections/categoria',
                [
                    'categoria' =>
                    $categoria,

                    'produtos' =>
                    $produtos,

                    'limiteOpcoes' =>
                    $limiteOpcoes,

                    'tipoCategoria' =>
                    $tipoCategoria,

                    'quantidadesIniciais' =>
                    $quantidadesIniciais,

                    'editarCategoriaId' =>
                    $editarCategoriaId,
                ]
            );

            ?>

        </div>

    </div>

</main>

<!--
=================================
MODAL CENTO INCOMPLETO
=================================
-->

<div
    class="modal fade"
    id="modalCentoIncompleto"
    tabindex="-1"
    aria-labelledby="tituloModalCentoIncompleto"
    aria-hidden="true">

    <div
        class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h2
                    class="modal-title fs-5"
                    id="tituloModalCentoIncompleto">

                    Cento incompleto

                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fechar">
                </button>

            </div>


            <div class="modal-body text-center">

                <div class="modal-alerta-icone">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <h3 class="modal-alerta-titulo">
                    Alerta!
                </h3>

                <p
                    class="modal-alerta-mensagem"
                    data-mensagem-cento-incompleto>

                    Falta completar o cento!

                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-warning text-white"
                    data-bs-dismiss="modal">

                    Entendi

                </button>

            </div>

        </div>

    </div>

</div>


<?php if (
    $erroCategoria ===
    'cento_incompleto'
): ?>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const modalElement =
                    document.getElementById(
                        'modalCentoIncompleto'
                    );

                if (
                    modalElement &&
                    typeof bootstrap !==
                    'undefined'
                ) {

                    const modal =
                        new bootstrap.Modal(
                            modalElement
                        );

                    modal.show();
                }

            }
        );
    </script>

<?php endif; ?>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const pagina =
                document.querySelector(
                    '.categoria-pagina'
                );

            const imagem =
                document.querySelector(
                    '.img_destaque_imagem'
                );

            if (
                !pagina ||
                !imagem
            ) {
                return;
            }


            let animacaoPendente =
                false;


            function atualizarParallax() {

                const rect =
                    pagina.getBoundingClientRect();


                const alturaImagem =
                    document
                    .querySelector(
                        '.img_destaque_container'
                    )
                    ?.offsetHeight ||
                    0;


                if (
                    alturaImagem <= 0
                ) {
                    return;
                }


                /*
                =================================
                DISTÂNCIA DA PÁGINA NO SCROLL
                =================================
                */

                const deslocamento =
                    Math.max(
                        0,
                        -rect.top
                    );


                /*
                =================================
                VELOCIDADE DA IMAGEM
                =================================

                A imagem acompanha apenas 35%
                do movimento da página.

                Isso cria o efeito de profundidade.
                */

                const movimento =
                    Math.min(
                        deslocamento * 0.35,
                        alturaImagem * 0.35
                    );


                imagem.style.transform =
                    'translateY(' +
                    movimento +
                    'px)';
            }


            function solicitarAtualizacao() {

                if (
                    animacaoPendente
                ) {
                    return;
                }


                animacaoPendente =
                    true;


                requestAnimationFrame(
                    function() {

                        atualizarParallax();


                        animacaoPendente =
                            false;
                    }
                );
            }


            window.addEventListener(
                'scroll',
                solicitarAtualizacao, {
                    passive: true
                }
            );


            window.addEventListener(
                'resize',
                solicitarAtualizacao
            );


            atualizarParallax();

        }
    );
</script>


<?php require APP_ROOT
. '/views/layouts/site/footer.php';