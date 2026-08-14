<?php

declare(strict_types=1);

$categoria =
    $categoria ?? [];

$produtos =
    $produtos ?? [];

$tipoCategoria =
    $tipoCategoria ?? 'unica';


$ehSalgadosGrandes =
    $tipoCategoria ===
    'salgados_grandes';


$ehEmpadao =
    $tipoCategoria ===
    'empadao';


$ehCento =
    $tipoCategoria ===
    'cento_tradicionais'
    ||
    $tipoCategoria ===
    'cento_folhados';


$ehTradicionais =
    $tipoCategoria ===
    'cento_tradicionais';


$ehFolhados =
    $tipoCategoria ===
    'cento_folhados';


$limiteOpcoes =
    $limiteOpcoes ?? 1;


$quantidadesIniciais =
    $quantidadesIniciais ?? [];


$editarCategoriaId =
    $editarCategoriaId ?? null;


$estaEditando =
    isset($_GET['editar'])
    &&
    $_GET['editar'] === '1';


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


$totalInicialUnidades = 0;


if (
    $ehSalgadosGrandes
    ||
    $ehEmpadao
) {

    foreach (
        $quantidadesIniciais
        as $quantidadeInicial
    ) {

        $totalInicialUnidades +=
            (int) $quantidadeInicial;
    }
}

?>


<div
    class="
        categoria-selecao
        <?= (
            !$ehCento
            &&
            (
                $ehSalgadosGrandes
                ||
                $ehEmpadao
            )
        )
            ? 'categoria-com-acao-lateral'
            : ''
        ?>
    "
    data-tipo-categoria="<?= htmlspecialchars(
                                $tipoCategoria,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
    data-limite="<?= (int) $limiteOpcoes ?>"
    data-tipo-contador="<?= (
                            $ehSalgadosGrandes
                            ||
                            $ehEmpadao
                        )
                            ? 'quantidade'
                            : 'opcoes'
                        ?>">


    <!-- =================================
         CABEÇALHO DA CATEGORIA
    ================================== -->

    <div
        class="categoria-cabecalho"
        data-limite-opcoes="<?= (int) $limiteOpcoes ?>">


        <p class="categoria-subtitulo">

            Escolha os sabores

        </p>


        <h3 class="categoria-titulo">

            <?= htmlspecialchars(
                $nomeCategoria,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </h3>


        <?php if (
            !empty($categoria['descricao'])
        ): ?>

            <p class="categoria-descricao">

                <?= htmlspecialchars(
                    $categoria['descricao'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </p>

        <?php endif; ?>


    </div>


    <!-- =================================
         ALERTA DA CATEGORIA
    ================================== -->

    <div class="alert alert-warning categoria-alerta">


        <div class="
            d-flex
            align-items-center
            gap-2">


            <i class="bi bi-info-circle"></i>


            <div>


                <?php if ($ehSalgadosGrandes): ?>

                    <strong>

                        Escolha a quantidade
                        desejada de cada salgado.

                    </strong>


                    <br>


                    <span class="small">

                        Cada unidade custa
                        R$ 5,00.

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

                        <strong data-contador-opcoes>

                            0

                        </strong>

                        /
                        <?= (int) $limiteOpcoes ?>

                    </span>


                <?php endif; ?>


            </div>


        </div>


    </div>


    <!-- =================================
         FORMULÁRIO
    ================================== -->

    <form
        action="<?= BASE_URL ?>/carrinho/adicionar"
        method="POST"
        data-form-carrinho>


        <input
            type="hidden"
            name="categoria_id"
            value="<?= (int) $categoria['id'] ?>">


        <?php if (
            $editarCategoriaId !== null
        ): ?>

            <input
                type="hidden"
                name="editar_categoria_id"
                value="<?= (int) $editarCategoriaId ?>">

        <?php endif; ?>


        <!-- =================================
             CENTO
        ================================== -->

        <?php if ($ehCento): ?>


            <div class="categoria-cento-layout">


                <!-- =================================
                     ÁREA DOS PRODUTOS
                ================================== -->

                <div class="categoria-produtos-area">


                    <?php if (
                        $produtos === []
                    ): ?>


                        <div class="alert alert-info">

                            Nenhum sabor disponível
                            nesta categoria.

                        </div>


                    <?php else: ?>


                        <div class="categoria-produtos-grid">


                            <?php foreach (
                                $produtos
                                as $produto
                            ): ?>


                                <?php

                                $produtoId =
                                    (int) $produto['id'];


                                $quantidadeInicial =
                                    (int) (
                                        $quantidadesIniciais[$produtoId] ?? 0
                                    );

                                ?>


                                <div
                                    class="produto-wrapper"
                                    data-produto-wrapper>


                                    <div
                                        class="produto-selecao-card"
                                        data-produto-id="<?= $produtoId ?>">


                                        <div
                                            class="produto-card-conteudo">


                                            <!-- IMAGEM -->

                                            <div
                                                class="
                                                produto-imagem-placeholder">


                                                <i
                                                    class="
                                                    bi
                                                    bi-image">
                                                </i>


                                            </div>


                                            <!-- NOME -->

                                            <h4
                                                class="
                                                produto-card-titulo">


                                                <?= htmlspecialchars(
                                                    $produto['nome'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>


                                            </h4>


                                            <!-- DESCRIÇÃO -->

                                            <div
                                                class="
                                                produto-card-descricao">


                                                <?php if (
                                                    !empty($produto['descricao'])
                                                ): ?>


                                                    <?= htmlspecialchars(
                                                        $produto['descricao'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>


                                                <?php endif; ?>


                                            </div>


                                            <!-- CONTADOR -->

                                            <div
                                                class="
                                                contador-produto">


                                                <!-- DIMINUIR -->

                                                <button
                                                    type="button"
                                                    class="
                                                    contador-botao
                                                    contador-botao-menos"
                                                    data-diminuir
                                                    <?= (
                                                        $quantidadeInicial <= 0
                                                    )
                                                        ? 'disabled'
                                                        : ''
                                                    ?>>


                                                    <i
                                                        class="
                                                        bi
                                                        bi-dash">
                                                    </i>


                                                </button>


                                                <!-- QUANTIDADE -->

                                                <span
                                                    class="
                                                    contador-quantidade"
                                                    data-quantidade>


                                                    <?= $quantidadeInicial ?>


                                                </span>


                                                <!-- AUMENTAR -->

                                                <button
                                                    type="button"
                                                    class="
                                                    contador-botao
                                                    contador-botao-mais"
                                                    data-aumentar>


                                                    <i
                                                        class="
                                                        bi
                                                        bi-plus">
                                                    </i>


                                                </button>


                                            </div>


                                            <!-- INPUT OCULTO -->

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


                </div>


                <!-- =================================
                     PAINEL DO CENTO
                ================================== -->

                <div class="cento-preview-area">


                    <div class="cento-preview-sticky">


                        <div class="cento-preview">


                            <!-- TÍTULO -->

                            <div class="cento-preview-header">


                                <h4
                                    class="
                                    cento-preview-titulo">


                                    <?= $ehTradicionais
                                        ? 'Cento Tradicional'
                                        : 'Cento Folhado'
                                    ?>


                                </h4>


                            </div>


                            <!-- QUADRADO DO CENTO -->

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
                                            class="
                                            cento-setor-texto"
                                            data-cento-texto>
                                        </span>


                                    </div>


                                <?php endfor; ?>


                                <?php if (
                                    $ehTradicionais
                                ): ?>


                                    <div
                                        class="
                                        cento-divisao
                                        cento-divisao-vertical">
                                    </div>


                                    <div
                                        class="
                                        cento-divisao
                                        cento-divisao-horizontal">
                                    </div>


                                <?php else: ?>


                                    <div
                                        class="
                                        cento-divisao
                                        cento-divisao-vertical">
                                    </div>


                                <?php endif; ?>


                                <!-- CENTRO -->

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


                            <!-- STATUS -->

                            <div
                                class="cento-preview-status"
                                data-cento-status>


                                <small>

                                    Monte seu cento.

                                </small>


                            </div>


                            <!-- LIMPAR -->

                            <button
                                type="button"
                                class="
                                cento-botao-limpar"
                                data-limpar-cento>


                                <i
                                    class="
                                    bi
                                    bi-arrow-counterclockwise">
                                </i>


                                Limpar seleção


                            </button>


                            <!-- ADICIONAR -->

                            <button
                                type="submit"
                                class="
                                cento-botao-carrinho"
                                data-adicionar-carrinho>


                                <i
                                    class="
                                    bi
                                    bi-cart-plus">
                                </i>


                                <?= $estaEditando
                                    ? 'Atualizar carrinho'
                                    : 'Adicionar ao carrinho'
                                ?>


                            </button>


                        </div>


                    </div>


                </div>


            </div>


        <?php else: ?>


            <!-- =================================
                 CATEGORIAS NORMAIS
            ================================== -->

            <div class="categoria-produtos-area">


                <?php if (
                    $produtos === []
                ): ?>


                    <div class="alert alert-info">

                        Nenhum sabor disponível
                        nesta categoria.

                    </div>


                <?php else: ?>


                    <div class="categoria-produtos-grid">


                        <?php foreach (
                            $produtos
                            as $produto
                        ): ?>


                            <?php

                            $produtoId =
                                (int) $produto['id'];


                            $quantidadeInicial =
                                (int) (
                                    $quantidadesIniciais[$produtoId] ?? 0
                                );

                            ?>


                            <div
                                class="produto-wrapper"
                                data-produto-wrapper>


                                <div
                                    class="produto-selecao-card"
                                    data-produto-id="<?= $produtoId ?>">


                                    <div
                                        class="produto-card-conteudo">


                                        <div
                                            class="
                                            produto-imagem-placeholder">


                                            <i
                                                class="
                                                bi
                                                bi-image">
                                            </i>


                                        </div>


                                        <h4
                                            class="
                                            produto-card-titulo">


                                            <?= htmlspecialchars(
                                                $produto['nome'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>


                                        </h4>


                                        <div
                                            class="
                                            produto-card-descricao">


                                            <?php if (
                                                !empty($produto['descricao'])
                                            ): ?>


                                                <?= htmlspecialchars(
                                                    $produto['descricao'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>


                                            <?php endif; ?>


                                        </div>


                                        <div
                                            class="
                                            contador-produto">


                                            <button
                                                type="button"
                                                class="
                                                contador-botao
                                                contador-botao-menos"
                                                data-diminuir
                                                <?= (
                                                    $quantidadeInicial <= 0
                                                )
                                                    ? 'disabled'
                                                    : ''
                                                ?>>


                                                <i
                                                    class="
                                                    bi
                                                    bi-dash">
                                                </i>


                                            </button>


                                            <span
                                                class="
                                                contador-quantidade"
                                                data-quantidade>


                                                <?= $quantidadeInicial ?>


                                            </span>


                                            <button
                                                type="button"
                                                class="
                                                contador-botao
                                                contador-botao-mais"
                                                data-aumentar>


                                                <i
                                                    class="
                                                    bi
                                                    bi-plus">
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


            </div>


        <?php endif; ?>


        <!-- =================================
             PAINEL SALGADOS GRANDES / EMPADÃO
        ================================== -->

        <?php if (!$ehCento): ?>


            <div class="categoria-acao-sticky">


                <div class="categoria-acao-card">


                    <h4
                        class="
                        categoria-acao-titulo">


                        <?= $ehSalgadosGrandes
                            ? 'Salgados Grandes'
                            : 'Empadão de Frango'
                        ?>


                    </h4>


                    <p
                        class="
                        categoria-acao-texto">


                        <?= $ehSalgadosGrandes
                            ? 'Escolha as quantidades desejadas.'
                            : 'Escolha a quantidade desejada.'
                        ?>


                    </p>


                    <div
                        class="
                        categoria-acao-contador">


                        <span
                            class="
                            categoria-acao-contador-label">


                            Quantidade selecionada


                        </span>


                        <strong
                            class="
                            categoria-acao-contador-valor"
                            data-contador-quantidade>


                            <?= $totalInicialUnidades ?>


                        </strong>


                        <span
                            class="
                            categoria-acao-contador-unidade">


                            <?= (
                                $totalInicialUnidades === 1
                            )
                                ? 'unidade'
                                : 'unidades'
                            ?>


                        </span>


                    </div>


                    <button
                        type="submit"
                        class="
                        categoria-acao-botao"
                        data-adicionar-carrinho>


                        <i
                            class="
                            bi
                            bi-cart-plus">
                        </i>


                        <?= $estaEditando
                            ? 'Atualizar carrinho'
                            : 'Adicionar ao carrinho'
                        ?>


                    </button>


                </div>


            </div>


        <?php endif; ?>


    </form>


</div>