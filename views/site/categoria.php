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

        <div class="container-fluid categoria-conteudo">
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


            const containerImagem =
                document.querySelector(
                    '.img_destaque_container'
                );


            const conteudo =
                document.querySelector(
                    '.categoria-conteudo'
                );


            if (
                !pagina ||
                !imagem ||
                !containerImagem ||
                !conteudo
            ) {
                return;
            }


            /*
            =================================
            CONFIGURAÇÃO DO EFEITO
            =================================
            */

            const margemInicial = -110;


            const margemFinal = -165;


            const deslocamentoMaximo =
                260;


            const movimentoImagemMaximo =
                70;


            let animacaoPendente =
                false;


            /*
            =================================
            ATUALIZAÇÃO DO MOVIMENTO
            =================================
            */

            function atualizarEfeitoScroll() {

                const rect =
                    pagina.getBoundingClientRect();


                const deslocamento =
                    Math.max(
                        0,
                        -rect.top
                    );


                /*
                O efeito termina depois de
                aproximadamente 260px de scroll.
                */

                const progresso =
                    Math.min(
                        deslocamento /
                        deslocamentoMaximo,
                        1
                    );


                /*
                =================================
                IMAGEM / FUNDO
                =================================

                A camada superior desce
                até 70px.
                */

                const movimentoImagem =
                    progresso *
                    movimentoImagemMaximo;


                imagem.style.transform =
                    'translateY(' +
                    movimentoImagem +
                    'px)';


                /*
                =================================
                CONTAINER
                =================================

                O container aumenta sua
                sobreposição de -110px para
                -165px.

                Isso faz a área creme subir
                sobre o fundo.
                */

                const margemAtual =
                    margemInicial +
                    (
                        (
                            margemFinal -
                            margemInicial
                        ) *
                        progresso
                    );


                conteudo.style.marginTop =
                    margemAtual + 'px';

            }


            /*
            =================================
            REQUEST ANIMATION FRAME
            =================================
            */

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

                        atualizarEfeitoScroll();


                        animacaoPendente =
                            false;

                    }
                );

            }


            /*
            =================================
            EVENTOS
            =================================
            */

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


            /*
            =================================
            ESTADO INICIAL
            =================================
            */

            atualizarEfeitoScroll();

        }
    );
</script>


<?php require APP_ROOT
    . '/views/layouts/site/footer.php';
