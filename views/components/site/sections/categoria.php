<?php

declare(strict_types=1);

$categoria =
    $categoria ?? [];

$produtos =
    $produtos ?? [];

$tipoCategoria =
    $tipoCategoria ?? 'unica';

$ehSalgadosGrandes =
    $tipoCategoria === 'salgados_grandes';

$ehEmpadao =
    $tipoCategoria === 'empadao';

$ehCento =
    $tipoCategoria === 'cento_tradicionais'
    ||
    $tipoCategoria === 'cento_folhados';

$ehTradicionais =
    $tipoCategoria === 'cento_tradicionais';

$ehFolhados =
    $tipoCategoria === 'cento_folhados';

$limiteOpcoes =
    $limiteOpcoes ?? 1;

$quantidadesIniciais =
    $quantidadesIniciais ?? [];

$editarIndice =
    $editarIndice ?? null;

$nomeCategoria =
    $categoria['nome']
    ?? 'Categoria';

$partesCento =
    $ehTradicionais
    ? 4
    : (
        $ehFolhados
        ? 2
        : 0
    );

$unidadesPorParte =
    $ehTradicionais
    ? 25
    : (
        $ehFolhados
        ? 50
        : 0
    );

?>

<style>
    .categoria-cento-layout {
        align-items: flex-start;
    }

    /*
    =================================
    PAINEL FIXO DO CENTO
    =================================
    */

    .cento-preview-sticky {
        position: fixed;
        top: 90px;
        right: 30px;
        width: 350px;
        z-index: 100;
    }

    .cento-preview {
        background: #ffffff;
        border: 1px solid rgba(93, 64, 55, 0.12);
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow:
            0 0.5rem 1.5rem rgba(0, 0, 0, 0.12);
    }

    .cento-preview-titulo {
        color: var(--marrom, #5D4037);
    }

    /*
    =================================
    CÍRCULO
    =================================
    */

    .cento-circulo {
        position: relative;
        width: 300px;
        height: 300px;
        margin: 1.5rem auto;
        border-radius: 50%;
        overflow: hidden;
        background: var(--creme, #FFF7E8);
        border: 5px solid var(--marrom, #5D4037);
    }


    /*
    =================================
    SETORES
    =================================
    */

    .cento-setor {
        position: absolute;
        inset: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        text-align: center;

        background: transparent;
        color: var(--marrom, #5D4037);

        transition:
            background-color 0.2s ease,
            color 0.2s ease;

        z-index: 2;
    }

    .cento-setor.ativo {
        background: var(--laranja, #F57C00);
        color: #ffffff;
    }


    /*
    =================================
    4 PARTES
    =================================
    */

    .cento-circulo[data-partes="4"] .cento-setor[data-parte="1"] {

        clip-path:
            polygon(0 0,
                50% 0,
                50% 50%,
                0 50%);
    }

    .cento-circulo[data-partes="4"] .cento-setor[data-parte="2"] {

        clip-path:
            polygon(50% 0,
                100% 0,
                100% 50%,
                50% 50%);
    }

    .cento-circulo[data-partes="4"] .cento-setor[data-parte="3"] {

        clip-path:
            polygon(50% 50%,
                100% 50%,
                100% 100%,
                50% 100%);
    }

    .cento-circulo[data-partes="4"] .cento-setor[data-parte="4"] {

        clip-path:
            polygon(0 50%,
                50% 50%,
                50% 100%,
                0 100%);
    }


    /*
    =================================
    2 PARTES
    =================================
    */

    .cento-circulo[data-partes="2"] .cento-setor[data-parte="1"] {

        clip-path:
            polygon(0 0,
                50% 0,
                50% 100%,
                0 100%);
    }

    .cento-circulo[data-partes="2"] .cento-setor[data-parte="2"] {

        clip-path:
            polygon(50% 0,
                100% 0,
                100% 100%,
                50% 100%);
    }


    /*
    =================================
    DIVISÕES
    =================================
    */

    .cento-divisao {
        position: absolute;
        z-index: 10;
        background: rgba(93, 64, 55, 0.35);
        pointer-events: none;
    }

    .cento-divisao-vertical {

        width: 3px;
        height: 100%;

        top: 0;
        left: 50%;

        transform:
            translateX(-50%);
    }

    .cento-divisao-horizontal {

        width: 100%;
        height: 3px;

        top: 50%;
        left: 0;

        transform:
            translateY(-50%);
    }


    /*
    =================================
    CENTRO
    =================================
    */

    .cento-centro {

        position: absolute;
        z-index: 40;

        top: 50%;
        left: 50%;

        transform:
            translate(-50%,
                -50%);

        width: 76px;
        height: 76px;

        border-radius: 50%;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        background: #ffffff;
        color: var(--marrom, #5D4037);

        border:
            4px solid var(--marrom, #5D4037);

        text-align: center;
        line-height: 1;

        box-shadow:
            0 0.25rem 0.75rem rgba(0, 0, 0, 0.14);

        pointer-events: none;
    }

    .cento-centro strong {
        font-size: 1rem;
    }

    .cento-centro span {
        font-size: 0.7rem;
        margin-top: 0.2rem;
    }


    /*
    =================================
    BOTÃO FIXO DO CENTO
    =================================
    */

    .cento-botao-carrinho {
        width: 100%;
        margin-top: 1rem;
    }


    /*
    =================================
    MOBILE
    =================================
    */

    @media (max-width: 1199.98px) {

        .cento-preview-sticky {

            position: static;

            width: auto;

        }

        .cento-circulo {

            width:
                min(100%,
                    280px);

            height: auto;

            aspect-ratio: 1;

        }

    }
</style>


<div
    class="categoria-selecao"
    data-tipo-categoria="<?= htmlspecialchars(
                                $tipoCategoria,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
    data-limite="<?= $limiteOpcoes !== null
                        ? (int) $limiteOpcoes
                        : ''
                    ?>">

    <div
        class="mb-4"
        data-limite-opcoes="<?= $limiteOpcoes !== null
                                ? (int) $limiteOpcoes
                                : ''
                            ?>">

        <p class="text-secondary mb-1">
            Escolha os sabores
        </p>


        <h3 class="fw-bold mb-2">

            <?= htmlspecialchars(
                $nomeCategoria,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </h3>


        <?php if (!empty($categoria['descricao'])): ?>

            <p class="text-secondary small mb-3">

                <?= htmlspecialchars(
                    $categoria['descricao'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </p>

        <?php endif; ?>


        <div class="alert alert-warning">

            <div class="d-flex align-items-center gap-2">

                <i class="bi bi-info-circle"></i>

                <div>

                    <?php if ($ehSalgadosGrandes): ?>

                        <strong>
                            Escolha a quantidade desejada
                            de cada salgado.
                        </strong>

                        <br>

                        <span class="small">

                            Cada unidade custa R$ 5,00.

                            Para revenda, a unidade
                            custa R$ 3,00,
                            com mínimo de 10 unidades.

                        </span>


                    <?php elseif ($ehEmpadao): ?>

                        <strong>
                            Escolha a quantidade desejada.
                        </strong>

                        <br>

                        <span class="small">

                            Cada unidade custa R$ 100,00.

                        </span>


                    <?php else: ?>

                        <strong>

                            Monte seu cento
                            escolhendo até
                            <?= (int) $limiteOpcoes ?>
                            sabores.

                        </strong>

                        <br>

                        <span class="small">

                            Partes escolhidas:

                            <strong
                                data-contador-opcoes>

                                0

                            </strong>

                            /
                            <?= (int) $limiteOpcoes ?>

                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <?php if ($ehCento): ?>

        <div
            class="row
                   g-4
                   categoria-cento-layout">

        <?php endif; ?>


        <!--
    =================================
    FORMULÁRIO
    =================================

    O formulário envolve tanto a lista
    quanto o painel do cento.

    Assim o botão fixo continua sendo
    o mesmo botão de envio do formulário.
    -->

        <form
            action="<?= BASE_URL ?>/carrinho/adicionar"
            method="POST"
            data-form-carrinho>


            <input
                type="hidden"
                name="categoria_id"
                value="<?= (int) $categoria['id'] ?>">


            <?php if ($editarIndice !== null): ?>

                <input
                    type="hidden"
                    name="editar_indice"
                    value="<?= (int) $editarIndice ?>">

            <?php endif; ?>


            <?php if ($ehCento): ?>

                <div class="col-lg-8">

                <?php endif; ?>


                <?php if ($produtos === []): ?>

                    <div class="alert alert-info">

                        Nenhum sabor disponível
                        nesta categoria.

                    </div>


                <?php else: ?>

                    <div class="row g-3">

                        <?php foreach ($produtos as $produto): ?>

                            <?php

                            $produtoId =
                                (int) $produto['id'];

                            $quantidadeInicial =
                                (int) (
                                    $quantidadesIniciais[$produtoId] ?? 0
                                );

                            ?>


                            <div
                                class="col-6 col-md-4"
                                data-produto-wrapper>


                                <div
                                    class="card
                                   h-100
                                   border-0
                                   shadow-sm
                                   produto-selecao-card"
                                    data-produto-id="<?= $produtoId ?>">


                                    <div
                                        class="card-body
                                       text-center">


                                        <div
                                            class="produto-imagem-placeholder
                                           mb-3">

                                            <i
                                                class="bi
                                               bi-image
                                               fs-1
                                               text-secondary">
                                            </i>

                                        </div>


                                        <h4
                                            class="h6 fw-bold"
                                            data-produto-nome>

                                            <?= htmlspecialchars(
                                                $produto['nome'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </h4>


                                        <?php if (
                                            !empty($produto['descricao'])
                                        ): ?>

                                            <p
                                                class="small
                                               text-secondary">

                                                <?= htmlspecialchars(
                                                    $produto['descricao'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </p>

                                        <?php endif; ?>


                                        <div
                                            class="contador-produto
                                           d-flex
                                           justify-content-center
                                           align-items-center
                                           gap-2
                                           mt-3">


                                            <button
                                                type="button"
                                                class="btn
                                               btn-outline-secondary
                                               btn-sm"
                                                data-diminuir
                                                <?= $quantidadeInicial <= 0
                                                    ? 'disabled'
                                                    : ''
                                                ?>>

                                                <i
                                                    class="bi bi-dash">
                                                </i>

                                            </button>


                                            <span
                                                class="fw-bold px-2"
                                                data-quantidade>

                                                <?= $quantidadeInicial ?>

                                            </span>


                                            <button
                                                type="button"
                                                class="btn
                                               btn-warning
                                               btn-sm"
                                                data-aumentar>

                                                <i
                                                    class="bi bi-plus">
                                                </i>

                                            </button>


                                        </div>


                                        <input
                                            type="hidden"
                                            name="quantidades[<?= $produtoId ?>]"
                                            value="<?= $quantidadeInicial ?>"
                                            data-input-quantidade>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>


                <?php if (!$ehCento): ?>

                    <div
                        class="mt-5
                       d-flex
                       justify-content-end">

                        <button
                            type="submit"
                            class="btn
                           btn-warning
                           text-white
                           btn-lg"
                            data-adicionar-carrinho>

                            <i
                                class="bi
                               bi-cart-plus
                               me-2">
                            </i>

                            <?= $editarIndice !== null
                                ? 'Atualizar carrinho'
                                : 'Adicionar ao carrinho'
                            ?>

                        </button>

                    </div>

                <?php endif; ?>


                <?php if ($ehCento): ?>

                </div>


                <div
                    class="col-lg-4">


                    <div
                        class="cento-preview-sticky">


                        <div
                            class="cento-preview">


                            <div
                                class="text-center">

                                <h4
                                    class="cento-preview-titulo
                                       fw-bold">

                                    <?= $ehTradicionais
                                        ? 'Cento Tradicional'
                                        : 'Cento Folhado'
                                    ?>

                                </h4>

                            </div>


                            <div
                                class="cento-circulo"
                                data-partes="<?= $partesCento ?>">


                                <?php for (
                                    $parte = 1;
                                    $parte <= $partesCento;
                                    $parte++
                                ): ?>

                                    <div
                                        class="cento-setor"
                                        data-cento-setor="<?= $parte ?>"
                                        data-parte="<?= $parte ?>">

                                        <span
                                            class="cento-setor-texto">

                                            <?= $unidadesPorParte ?>
                                            und.

                                        </span>

                                    </div>

                                <?php endfor; ?>


                                <?php if ($ehTradicionais): ?>

                                    <div
                                        class="cento-divisao
                                           cento-divisao-vertical">
                                    </div>


                                    <div
                                        class="cento-divisao
                                           cento-divisao-horizontal">
                                    </div>

                                <?php else: ?>

                                    <div
                                        class="cento-divisao
                                           cento-divisao-vertical">
                                    </div>

                                <?php endif; ?>


                                <div
                                    class="cento-centro">

                                    <strong>
                                        100
                                    </strong>

                                    <span>
                                        unidades
                                    </span>

                                </div>

                            </div>


                            <div
                                class="cento-preview-status"
                                data-cento-status>

                                <small>
                                    Monte seu cento.
                                </small>

                            </div>


                            <!--
                        =================================
                        BOTÃO FIXO
                        =================================
                        -->

                            <button
                                type="submit"
                                class="btn
                                   btn-warning
                                   text-white
                                   btn-lg
                                   cento-botao-carrinho"
                                data-adicionar-carrinho>

                                <i
                                    class="bi
                                       bi-cart-plus
                                       me-2">
                                </i>

                                <?= $editarIndice !== null
                                    ? 'Atualizar carrinho'
                                    : 'Adicionar ao carrinho'
                                ?>

                            </button>


                        </div>

                    </div>

                </div>


            <?php endif; ?>


        </form>


        <?php if ($ehCento): ?>

        </div>

    <?php endif; ?>


</div>