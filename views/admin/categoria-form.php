<?php

declare(strict_types=1);

$categoria = $categoria ?? null;
$imagemCategoria = $imagemCategoria ?? null;

if (!isset($csrfToken)) {
    throw new RuntimeException(
        'Token CSRF não informado para o formulário da categoria.'
    );
}

$csrfToken = (string) $csrfToken;

$posicaoX =
    $imagemCategoria !== null
    && isset($imagemCategoria['posicao_x'])
    ? (float) $imagemCategoria['posicao_x']
    : 50.0;

$posicaoY =
    $imagemCategoria !== null
    && isset($imagemCategoria['posicao_y'])
    ? (float) $imagemCategoria['posicao_y']
    : 50.0;

$escalaImagem =
    $imagemCategoria !== null
    && isset($imagemCategoria['escala'])
    ? (float) $imagemCategoria['escala']
    : 1.00;


$bannerCategoria =
    $bannerCategoria
    ?? null;

$bannerEscala =
    max(1.0, \App\Helpers\ImagemEscala::normalizar(
        $bannerCategoria['escala']
            ?? 1
    ));

$bannerPosicaoX =
    $bannerCategoria !== null
    && isset($bannerCategoria['posicao_x'])
    ? (float) $bannerCategoria['posicao_x']
    : 50.0;

$bannerPosicaoY =
    $bannerCategoria !== null
    && isset($bannerCategoria['posicao_y'])
    ? (float) $bannerCategoria['posicao_y']
    : 50.0;


/*
|--------------------------------------------------------------------------
| CSS E JS ESPECÍFICOS DESTA TELA
|--------------------------------------------------------------------------
|
| O editor da imagem da categoria fica separado do admin.js principal.
|
| Assim não interfere:
| - no menu lateral;
| - no editor de produtos;
| - em outros componentes administrativos.
|
*/

$cssAdmin = array_merge(
    $cssAdmin ?? [],
    [
        'categoria-imagem-editor.css',
    ]
);

$jsAdmin = array_merge(
    $jsAdmin ?? [],
    [
        'categoria-imagem-editor.js',
    ]
);

$bannerPreviewStyle =
    sprintf(
        '--banner-x: %s%%; --banner-y: %s%%; --banner-escala: %s;',
        json_encode(min(100, max(0, $bannerPosicaoX))),
        json_encode(min(100, max(0, $bannerPosicaoY))),
        json_encode($bannerEscala)
    );

$bannerPreviewStyleAttr =
    'style="'
    . htmlspecialchars(
        $bannerPreviewStyle,
        ENT_QUOTES,
        'UTF-8'
    )
    . '"';


require APP_ROOT
    . '/views/layouts/admin/header.php';

?>




<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/categoria-banner.css?v=<?= filemtime(APP_ROOT . '/public/assets/css/categoria-banner.css') ?>">

<main
    class="
        admin-container
        categoria-editor-admin
    ">


    <section class="categoria-editor-cabecalho">

        <div>

            <a
                href="<?= BASE_URL ?>/admin/categorias"
                class="detalhe-voltar">
                <i
                    class="bi bi-arrow-left"
                    aria-hidden="true"></i>

                Voltar ao cardápio
            </a>


            <span class="categoria-editor-sobretitulo">

                <?= $categoria
                    ? 'Edição de categoria'
                    : 'Nova categoria'
                ?>

            </span>


            <h1>

                <?= $categoria
                    ? 'Editar categoria'
                    : 'Criar categoria'
                ?>

            </h1>


            <p>

                <?= $categoria
                    ? 'Atualize a apresentação e as regras desta categoria.'
                    : 'Defina como essa categoria aparecerá no seu cardápio.'
                ?>

            </p>

        </div>


        <span class="categoria-editor-etapa">

            <i
                class="bi bi-journal-check"
                aria-hidden="true"></i>

            Cardápio

        </span>

    </section>


    <form
        class="categoria-editor-formulario"
        method="POST"
        enctype="multipart/form-data"
        action="<?= BASE_URL ?><?= $categoria
                                    ? '/admin/categorias/atualizar/' . $categoria['id']
                                    : '/admin/categorias/salvar'
                                ?>">


        <input
            type="hidden"
            name="_token"
            value="<?= htmlspecialchars(
                        $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="posicao_x"
            id="categoria-posicao-x"
            value="<?= htmlspecialchars(
                        (string) $posicaoX,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="posicao_y"
            id="categoria-posicao-y"
            value="<?= htmlspecialchars(
                        (string) $posicaoY,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="escala"
            id="categoria-escala"
            value="<?= htmlspecialchars(
                        (string) $escalaImagem,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="banner_posicao_x"
            id="banner_posicao_x"
            value="<?= htmlspecialchars(
                        (string) $bannerPosicaoX,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="banner_posicao_y"
            id="banner_posicao_y"
            value="<?= htmlspecialchars(
                        (string) $bannerPosicaoY,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="banner_escala"
            id="banner_escala"
            value="<?= $bannerEscala ?>">


        <!-- =====================================================
             INFORMAÇÕES
        ====================================================== -->

        <section
            class="
                categoria-editor-painel
                categoria-editor-dados
            ">

            <header>

                <span>
                    <i class="bi bi-pencil-square"></i>
                </span>

                <div>

                    <h2>
                        Informações da categoria
                    </h2>

                    <p>
                        Dados que serão exibidos no cardápio.
                    </p>

                </div>

            </header>


            <div class="categoria-editor-campos">


                <label
                    class="
                        categoria-campo
                        categoria-campo--amplo
                    ">

                    <span>
                        Nome
                    </span>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        required
                        maxlength="100"
                        value="<?= htmlspecialchars(
                                    $categoria['nome']
                                        ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>


                <label
                    class="
                        categoria-campo
                        categoria-campo--amplo
                    ">

                    <span>
                        Slug
                    </span>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        required
                        maxlength="120"
                        value="<?= htmlspecialchars(
                                    $categoria['slug']
                                        ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>


                <label
                    class="
                        categoria-campo
                        categoria-campo--total
                    ">

                    <span>
                        Descrição
                    </span>

                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="4"
                        maxlength="255"><?= htmlspecialchars(
                                            $categoria['descricao']
                                                ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?></textarea>

                </label>


                <label class="categoria-campo">

                    <span>
                        Preço
                    </span>

                    <input
                        type="number"
                        id="preco"
                        name="preco"
                        min="0"
                        step="0.01"
                        required
                        value="<?= htmlspecialchars(
                                    (string) (
                                        $categoria['preco']
                                        ?? '0.00'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>


                <label class="categoria-campo">

                    <span>
                        Preço de revenda
                    </span>

                    <input
                        type="number"
                        id="preco_revenda"
                        name="preco_revenda"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                                    (string) (
                                        $categoria['preco_revenda']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>


                <label class="categoria-campo">

                    <span>
                        Mínimo para revenda
                    </span>

                    <input
                        type="number"
                        id="quantidade_minima_revenda"
                        name="quantidade_minima_revenda"
                        min="1"
                        value="<?= htmlspecialchars(
                                    (string) (
                                        $categoria['quantidade_minima_revenda']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>

            </div>

        </section>


        <!-- =====================================================
             CONFIGURAÇÃO
        ====================================================== -->

        <section
            class="
                categoria-editor-painel
                categoria-editor-config
            ">

            <header>

                <span>
                    <i class="bi bi-sliders"></i>
                </span>

                <div>

                    <h2>
                        Disponibilidade e destaque
                    </h2>

                    <p>
                        Controle como a categoria será apresentada.
                    </p>

                </div>

            </header>


            <div
                class="
                    categoria-editor-campos
                    categoria-editor-campos--config
                ">


                <label class="categoria-campo">

                    <span>
                        Status
                    </span>

                    <select
                        id="ativo"
                        name="ativo">

                        <option
                            value="1"
                            <?= !isset($categoria['ativo'])
                                || (int) $categoria['ativo'] === 1
                                ? ' selected'
                                : ''
                            ?>>
                            Ativa
                        </option>

                        <option
                            value="0"
                            <?= isset($categoria['ativo'])
                                && (int) $categoria['ativo'] === 0
                                ? ' selected'
                                : ''
                            ?>>
                            Inativa
                        </option>

                    </select>

                </label>


                <label class="categoria-campo">

                    <span>
                        Destaque
                    </span>

                    <select
                        id="destaque"
                        name="destaque">

                        <option
                            value="0"
                            <?= !isset($categoria['destaque'])
                                || (int) $categoria['destaque'] === 0
                                ? ' selected'
                                : ''
                            ?>>
                            Normal
                        </option>

                        <option
                            value="1"
                            <?= isset($categoria['destaque'])
                                && (int) $categoria['destaque'] === 1
                                ? ' selected'
                                : ''
                            ?>>
                            Em destaque
                        </option>

                    </select>

                </label>


                <label class="categoria-campo">

                    <span>
                        Ordem do destaque
                    </span>

                    <input
                        type="number"
                        id="ordem_destaque"
                        name="ordem_destaque"
                        min="0"
                        value="<?= htmlspecialchars(
                                    (string) (
                                        $categoria['ordem_destaque']
                                        ?? 0
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>

            </div>

        </section>


        <!-- =====================================================
             IMAGEM DA CATEGORIA
        ====================================================== -->

        <section
            class="
                categoria-editor-painel
                categoria-editor-imagem-painel
            ">

            <header>

                <span>
                    <i class="bi bi-image-fill"></i>
                </span>

                <div>

                    <h2>
                        Imagem da categoria
                    </h2>

                    <p>
                        A moldura mostra exatamente a proporção
                        utilizada nos cards da loja.
                    </p>

                </div>

            </header>


            <div class="categoria-imagem-editor">


                <div
                    class="categoria-imagem-palco"
                    id="categoria-imagem-palco">


                    <?php if (
                        $imagemCategoria !== null
                        &&
                        !empty($imagemCategoria['url_imagem'])
                    ): ?>

                        <img
                            id="categoria-imagem-guia"
                            class="categoria-imagem-guia"
                            src="<?= BASE_URL
                                        . $imagemCategoria['url_imagem']
                                    ?>"
                            alt=""
                            aria-hidden="true">

                    <?php endif; ?>


                    <div
                        id="categoria-imagem-preview-container"
                        class="categoria-imagem-preview">


                        <?php if (
                            $imagemCategoria !== null
                            &&
                            !empty($imagemCategoria['url_imagem'])
                        ): ?>

                            <img
                                id="categoria-imagem-preview"
                                src="<?= BASE_URL
                                            . $imagemCategoria['url_imagem']
                                        ?>"
                                alt="Pré-visualização da imagem"
                                draggable="false">

                        <?php else: ?>

                            <div
                                id="categoria-imagem-preview-placeholder"
                                class="categoria-imagem-placeholder">

                                <i class="bi bi-image"></i>

                                <span>
                                    Selecione uma imagem
                                </span>

                            </div>

                        <?php endif; ?>


                    </div>

                </div>


                <div
                    class="categoria-imagem-controles"
                    aria-label="Ajustar enquadramento da imagem">

                    <button
                        type="button"
                        data-mover-categoria-imagem="cima"
                        aria-label="Mover para cima">
                        <i class="bi bi-arrow-up"></i>
                    </button>


                    <button
                        type="button"
                        data-mover-categoria-imagem="esquerda"
                        aria-label="Mover para esquerda">
                        <i class="bi bi-arrow-left"></i>
                    </button>


                    <button
                        type="button"
                        id="categoria-imagem-centralizar"
                        class="categoria-imagem-centralizar"
                        aria-label="Centralizar imagem">
                        <i class="bi bi-bullseye"></i>
                    </button>


                    <button
                        type="button"
                        data-mover-categoria-imagem="direita"
                        aria-label="Mover para direita">
                        <i class="bi bi-arrow-right"></i>
                    </button>


                    <button
                        type="button"
                        data-mover-categoria-imagem="baixo"
                        aria-label="Mover para baixo">
                        <i class="bi bi-arrow-down"></i>
                    </button>

                </div>


                <label class="categoria-zoom">

                    <span>
                        <i class="bi bi-zoom-in"></i>
                        Tamanho da imagem
                    </span>

                    <output id="categoria-imagem-zoom-valor">
                        <?= (int) round(
                            $escalaImagem * 100
                        ) ?>%
                    </output>

                    <input
                        type="range"
                        id="categoria-imagem-zoom"
                        min="10"
                        max="200"
                        step="1"
                        value="<?= (int) round(
                                    $escalaImagem * 100
                                ) ?>">

                    <small>
                        A moldura laranja tem exatamente a mesma
                        proporção da foto exibida nos cards da loja.
                    </small>

                </label>


                <label class="categoria-upload">

                    <i class="bi bi-cloud-arrow-up"></i>

                    <span>

                        <strong>
                            Escolher imagem
                        </strong>

                        <small>
                            JPG, PNG ou WebP · até 5 MB · será salva em WebP
                        </small>

                    </span>

                    <input
                        type="file"
                        id="categoria-imagem-arquivo"
                        name="imagem"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                </label>


                <?php if (
                    $imagemCategoria !== null
                ): ?>

                    <label class="categoria-remover-imagem">

                        <input
                            type="checkbox"
                            name="excluir_imagem"
                            value="1"
                            id="excluir_imagem">

                        <span>
                            Remover a imagem atual
                        </span>

                    </label>

                <?php endif; ?>


            </div>

        </section>


        <!-- =====================================================
             BANNER DA PÁGINA
        ====================================================== -->

        <section
            class="
                categoria-editor-painel
                categoria-editor-imagem-painel
                categoria-banner-painel
            ">

            <header>

                <span>
                    <i class="bi bi-card-image"></i>
                </span>

                <div>

                    <h2>
                        Imagem de fundo da página
                    </h2>

                    <p>
                        Esta imagem aparece no topo quando o cliente
                        abre a categoria. Ela é independente da imagem
                        do card.
                    </p>

                </div>

            </header>


            <label class="categoria-banner-tela" for="banner-largura">
                Largura da página pública (px)
                <input id="banner-largura" type="number" min="280" max="3840" step="1" value="1440">
                <small>Experimente 390 no celular, 768 no tablet ou 1440 no computador.
                A faixa inferior representa o conteúdo que encobre a foto.</small>
            </label>
            <label class="categoria-banner-tela" for="banner-rolagem">
                Simular rolagem da página
                <input id="banner-rolagem" type="range" min="0" max="260" value="0">
            </label>
            <div class="categoria-banner-editor">


                <div
                    class="categoria-banner-palco"
                    id="banner-palco">

                    <div
                        id="banner-preview-container"
                        class="categoria-banner-preview categoria-banner-contexto">

                        <div class="categoria-banner-superficie">
                        <?php if (
                            $bannerCategoria !== null
                            &&
                            !empty($bannerCategoria['url_imagem'])
                        ): ?>

                            <img
                                id="banner-preview"
                                class="categoria-banner-imagem"
                                src="<?= htmlspecialchars(BASE_URL . $bannerCategoria['url_imagem'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="Pré-visualização da imagem de fundo"
                                draggable="false"
                                <?= $bannerPreviewStyleAttr ?>>

                        <?php else: ?>

                            <div
                                id="banner-preview-placeholder"
                                class="categoria-banner-placeholder">

                                <i class="bi bi-card-image"></i>

                                <strong>
                                    Imagem de fundo da categoria
                                </strong>

                                <span>
                                    Selecione uma imagem horizontal
                                    para o topo da página.
                                </span>

                            </div>

                        <?php endif; ?>

                    </div>


                    </div>
                    <span class="categoria-banner-etiqueta">
                        PRÉVIA DO SITE
                    </span>

                </div>


                <div class="categoria-banner-opcoes">


                    <label class="categoria-zoom">

                        <span>
                            Tamanho da imagem de fundo
                        </span>

                        <output id="banner-zoom-valor">
                            <?= round(
                                $bannerEscala * 100
                            ) ?>%
                        </output>

                        <input
                            type="range"
                            id="banner-zoom"
                            min="100"
                            max="200"
                            value="<?= round(
                                        $bannerEscala * 100
                                    ) ?>">

                        <small>
                            100%: preenche toda a área do banner.
                            Aumente para aproximar e arraste para ajustar o recorte.
                        </small>

                    </label>


                    <div class="categoria-imagem-instrucoes">

                        <i class="bi bi-arrows-move"></i>

                        <div>

                            <strong>
                                Ajuste o enquadramento
                            </strong>

                            <span>
                                Arraste a imagem ou use as setas para
                                escolher a parte que ficará visível.
                            </span>

                        </div>

                    </div>


                    <div
                        class="categoria-banner-direcoes"
                        aria-label="Ajustar posição da imagem de fundo">

                        <button
                            type="button"
                            data-mover-banner="cima"
                            aria-label="Mover fundo para cima">
                            <i class="bi bi-arrow-up"></i>
                        </button>


                        <button
                            type="button"
                            data-mover-banner="esquerda"
                            aria-label="Mover fundo para esquerda">
                            <i class="bi bi-arrow-left"></i>
                        </button>


                        <button
                            type="button"
                            id="banner-centralizar"
                            aria-label="Centralizar fundo">
                            <i class="bi bi-bullseye"></i>
                        </button>


                        <button
                            type="button"
                            data-mover-banner="direita"
                            aria-label="Mover fundo para direita">
                            <i class="bi bi-arrow-right"></i>
                        </button>


                        <button
                            type="button"
                            data-mover-banner="baixo"
                            aria-label="Mover fundo para baixo">
                            <i class="bi bi-arrow-down"></i>
                        </button>

                    </div>


                    <label
                        class="
                            categoria-upload
                            categoria-banner-upload
                        ">

                        <i class="bi bi-cloud-arrow-up"></i>

                        <span>

                            <strong>
                                Escolher imagem de fundo
                            </strong>

                            <small>
                                JPG, PNG ou WebP · até 8 MB · será salva em WebP
                            </small>

                        </span>

                        <input
                            type="file"
                            id="banner_imagem"
                            name="banner_imagem"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                    </label>


                    <?php if (
                        $bannerCategoria !== null
                    ): ?>

                        <label class="categoria-remover-imagem">

                            <input
                                type="checkbox"
                                name="excluir_banner"
                                value="1"
                                id="excluir_banner">

                            <span>
                                Remover a imagem de fundo atual
                            </span>

                        </label>

                    <?php endif; ?>


                </div>

            </div>

        </section>


        <footer class="categoria-editor-acoes">

            <a
                href="<?= BASE_URL ?>/admin/categorias"
                class="categoria-editor-cancelar">
                Cancelar
            </a>


            <button
                type="submit"
                class="categoria-editor-salvar">

                <i class="bi bi-check2"></i>

                <?= $categoria
                    ? 'Salvar alterações'
                    : 'Criar categoria'
                ?>

            </button>

        </footer>


    </form>

</main>


<script src="<?= BASE_URL ?>/assets/js/categoria-banner.js?v=<?= filemtime(APP_ROOT . '/public/assets/js/categoria-banner.js') ?>"></script>
<script
    src="<?= BASE_URL ?>/assets/js/admin/categoria-banner.js?v=<?= filemtime(APP_ROOT . '/public/assets/js/admin/categoria-banner.js') ?>"></script>


<?php

require APP_ROOT
    . '/views/layouts/admin/footer.php';
