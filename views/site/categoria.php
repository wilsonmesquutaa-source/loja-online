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


<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/categoria-banner.css?v=<?= filemtime(APP_ROOT . '/public/assets/css/categoria-banner.css') ?>">

<main>

    <div class="categoria-pagina categoria-banner-contexto">

        <!-- =================================
             IMAGEM DE DESTAQUE
        ================================== -->

        <div class="img_destaque_container categoria-banner-superficie">
            <?php if (!empty($bannerCategoria['url_imagem'])):
                $bannerX = min(100, max(0, (float) ($bannerCategoria['posicao_x'] ?? 50)));
                $bannerY = min(100, max(0, (float) ($bannerCategoria['posicao_y'] ?? 50)));
                $bannerZoom = max(1.0, \App\Helpers\ImagemEscala::normalizar($bannerCategoria['escala'] ?? 1));
                $bannerEstilo = sprintf('--banner-x: %s%%; --banner-y: %s%%; --banner-escala: %s;',
                    json_encode($bannerX), json_encode($bannerY), json_encode($bannerZoom));
            ?>
                <img class="categoria-banner-imagem" alt="" draggable="false"
                    src="<?= htmlspecialchars(BASE_URL . $bannerCategoria['url_imagem'], ENT_QUOTES, 'UTF-8') ?>"
                    <?= 'style="' . htmlspecialchars($bannerEstilo, ENT_QUOTES, 'UTF-8') . '"' ?>>
            <?php endif; ?>
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


<script src="<?= BASE_URL ?>/assets/js/categoria-banner.js?v=<?= filemtime(APP_ROOT . '/public/assets/js/categoria-banner.js') ?>"></script>


<?php require APP_ROOT
    . '/views/layouts/site/footer.php';
