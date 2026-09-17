<?php

declare(strict_types=1);

$produto = $produto ?? null;
$imagemProduto = $imagemProduto ?? null;
$categorias = $categorias ?? [];

$posicaoX =
    $imagemProduto !== null
    && isset($imagemProduto['posicao_x'])
    ? (float) $imagemProduto['posicao_x']
    : 50.00;

$posicaoY =
    $imagemProduto !== null
    && isset($imagemProduto['posicao_y'])
    ? (float) $imagemProduto['posicao_y']
    : 50.00;

$escala =
    $imagemProduto !== null
    && isset($imagemProduto['escala'])
    ? (float) $imagemProduto['escala']
    : 1.20;

$escala = min(
    max(
        $escala,
        1.05
    ),
    2.00
);

$excedente =
    ($escala - 1)
    * 100;

$imagemLeft =
    - ($posicaoX / 100)
        * $excedente;

$imagemTop =
    - ($posicaoY / 100)
        * $excedente;

require APP_ROOT
    . '/views/layouts/admin-header.php';

?>

<main class="admin-container produto-editor-admin">

    <section class="produto-editor-cabecalho">

        <div>

            <a
                href="<?= BASE_URL ?>/admin/produtos"
                class="detalhe-voltar">
                <i
                    class="bi bi-arrow-left"
                    aria-hidden="true"></i>

                Voltar aos produtos
            </a>

            <span class="produto-editor-sobretitulo">

                <?= $produto
                    ? 'Edição de produto'
                    : 'Novo produto'
                ?>

            </span>

            <h1>

                <?= $produto
                    ? 'Editar produto'
                    : 'Criar produto'
                ?>

            </h1>

            <p>

                <?= $produto
                    ? 'Atualize as informações, estoque e apresentação deste produto.'
                    : 'Cadastre um novo item para disponibilizar no seu cardápio.'
                ?>

            </p>

        </div>

        <span class="produto-editor-etapa">

            <i
                class="bi bi-box-seam"
                aria-hidden="true"></i>

            Produtos

        </span>

    </section>


    <form
        class="produto-editor-formulario"
        method="POST"
        enctype="multipart/form-data"
        action="<?= BASE_URL ?><?= $produto
                                    ? '/admin/produtos/atualizar/' . (int) $produto['id']
                                    : '/admin/produtos/salvar'
                                ?>">

        <?php if (isset($csrfToken)): ?>

            <input
                type="hidden"
                name="_token"
                value="<?= htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">

        <?php endif; ?>


        <input
            type="hidden"
            name="posicao_x"
            id="posicao_x"
            value="<?= htmlspecialchars(
                        (string) $posicaoX,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="posicao_y"
            id="posicao_y"
            value="<?= htmlspecialchars(
                        (string) $posicaoY,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <input
            type="hidden"
            name="escala"
            id="escala"
            value="<?= htmlspecialchars(
                        number_format(
                            $escala,
                            2,
                            '.',
                            ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


        <!-- ==================================
             INFORMAÇÕES DO PRODUTO
        =================================== -->

        <section
            class="
                produto-editor-painel
                produto-editor-dados
            ">

            <header>

                <span>
                    <i
                        class="bi bi-pencil-square"
                        aria-hidden="true"></i>
                </span>

                <div>

                    <h2>
                        Informações do produto
                    </h2>

                    <p>
                        Dados exibidos no catálogo e no cardápio.
                    </p>

                </div>

            </header>


            <div class="produto-editor-campos">

                <label
                    class="
                        produto-editor-campo
                        produto-editor-campo--total
                    ">

                    <span>
                        Categoria
                    </span>

                    <select
                        name="categoria_id"
                        id="categoria_id"
                        required>

                        <option value="">
                            Selecione uma categoria
                        </option>

                        <?php foreach ($categorias as $categoria): ?>

                            <option
                                value="<?= (int) $categoria['id'] ?>"
                                <?= $produto
                                    && (int) $produto['categoria_id']
                                    === (int) $categoria['id']
                                    ? ' selected'
                                    : ''
                                ?>>

                                <?= htmlspecialchars(
                                    $categoria['nome'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>


                <label class="produto-editor-campo">

                    <span>
                        Nome
                    </span>

                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        required
                        maxlength="150"
                        value="<?= htmlspecialchars(
                                    $produto['nome'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>


                <label class="produto-editor-campo">

                    <span>
                        Slug
                    </span>

                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        required
                        maxlength="180"
                        value="<?= htmlspecialchars(
                                    $produto['slug'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </label>


                <label
                    class="
                        produto-editor-campo
                        produto-editor-campo--total
                    ">

                    <span>
                        Descrição
                    </span>

                    <textarea
                        name="descricao"
                        id="descricao"
                        rows="4"
                        maxlength="500"><?= htmlspecialchars(
                                            $produto['descricao'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?></textarea>

                </label>

            </div>

        </section>


        <!-- ==================================
             ESTOQUE
        =================================== -->

        <section
            class="
                produto-editor-painel
                produto-editor-estoque
            ">

            <header>

                <span>
                    <i
                        class="bi bi-box2-heart"
                        aria-hidden="true"></i>
                </span>

                <div>

                    <h2>
                        Estoque
                    </h2>

                    <p>
                        Controle a quantidade disponível.
                    </p>

                </div>

            </header>


            <div
                class="
                    produto-editor-campos
                    produto-editor-campos--estoque
                ">

                <label class="produto-editor-campo">

                    <span>
                        Quantidade em estoque
                    </span>

                    <input
                        type="number"
                        name="estoque"
                        id="estoque"
                        min="0"
                        step="1"
                        value="<?= htmlspecialchars(
                                    (string) (
                                        $produto['estoque']
                                        ?? 0
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                    <small>
                        Informe quantas unidades estão disponíveis.
                    </small>

                </label>

            </div>

        </section>


        <!-- ==================================
             IMAGEM
        =================================== -->

        <section
            class="
                produto-editor-painel
                produto-editor-imagem-painel
            ">

            <header>

                <span>
                    <i
                        class="bi bi-image-fill"
                        aria-hidden="true"></i>
                </span>

                <div>

                    <h2>
                        Imagem do produto
                    </h2>

                    <p>
                        Escolha a imagem e ajuste exatamente a área que será exibida para o cliente.
                    </p>

                </div>

            </header>


            <div class="produto-imagem-editor">

                <!-- PALCO -->

                <div
                    class="produto-imagem-palco"
                    id="imagem-palco">

                    <?php if (
                        $imagemProduto !== null
                        && !empty($imagemProduto['url_imagem'])
                    ): ?>

                        <img
                            id="imagem-guia"
                            class="produto-imagem-guia" src="<?= BASE_URL
                                                                    . htmlspecialchars(
                                                                        $imagemProduto['url_imagem'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    )
                                                                ?>"
                            alt=""
                            aria-hidden="true">

                    <?php endif; ?>


                    <div
                        class="produto-imagem-preview" id="imagem-preview-container">

                        <?php if (
                            $imagemProduto !== null
                            && !empty($imagemProduto['url_imagem'])
                        ): ?>

                            <img
                                id="imagem-preview"
                                src="<?= BASE_URL
                                            . htmlspecialchars(
                                                $imagemProduto['url_imagem'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        ?>"
                                alt="Pré-visualização do produto"
                                draggable="false"
                                style="
                                    object-position:
                                        <?= (float) $posicaoX ?>%
                                        <?= (float) $posicaoY ?>%;
                                    width:
                                        <?= $escala * 100 ?>%;
                                    height:
                                        <?= $escala * 100 ?>%;
                                    left:
                                        <?= $imagemLeft ?>%;
                                    top:
                                        <?= $imagemTop ?>%;
                                ">

                        <?php else: ?>

                            <div
                                id="imagem-preview-placeholder"
                                class="produto-imagem-placeholder">

                                <i
                                    class="bi bi-image"
                                    aria-hidden="true"></i>

                                <strong>
                                    Nenhuma imagem selecionada
                                </strong>

                                <span>
                                    Escolha uma imagem para visualizar aqui.
                                </span>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- CONTROLES -->

                <div class="produto-imagem-opcoes">

                    <div class="produto-imagem-ajuste">

                        <div class="produto-imagem-instrucoes">

                            <i
                                class="bi bi-arrows-move"
                                aria-hidden="true"></i>

                            <div>

                                <strong>
                                    Ajuste o enquadramento
                                </strong>

                                <span>
                                    Arraste a imagem ou use os controles para escolher a região exibida.
                                </span>

                            </div>

                        </div>


                        <div
                            class="produto-imagem-direcoes"
                            aria-label="Controles de posição da imagem">

                            <button
                                type="button"
                                data-mover-imagem="cima"
                                aria-label="Mover imagem para cima"
                                title="Mover para cima">

                                <i
                                    class="bi bi-arrow-up"
                                    aria-hidden="true"></i>

                            </button>

                            <button
                                type="button"
                                data-mover-imagem="esquerda"
                                aria-label="Mover imagem para esquerda"
                                title="Mover para esquerda">

                                <i
                                    class="bi bi-arrow-left"
                                    aria-hidden="true"></i>

                            </button>

                            <button
                                type="button"
                                id="imagem-centralizar"
                                aria-label="Centralizar imagem"
                                title="Centralizar imagem">

                                <i
                                    class="bi bi-bullseye"
                                    aria-hidden="true"></i>

                            </button>

                            <button
                                type="button"
                                data-mover-imagem="direita"
                                aria-label="Mover imagem para direita"
                                title="Mover para direita">

                                <i
                                    class="bi bi-arrow-right"
                                    aria-hidden="true"></i>

                            </button>

                            <button
                                type="button"
                                data-mover-imagem="baixo"
                                aria-label="Mover imagem para baixo"
                                title="Mover para baixo">

                                <i
                                    class="bi bi-arrow-down"
                                    aria-hidden="true"></i>

                            </button>

                        </div>

                    </div>


                    <div class="produto-imagem-acoes-inferiores">

                        <label class="produto-imagem-zoom">

                            <span>
                                <strong>
                                    Tamanho da imagem
                                </strong>

                                <output id="imagem-zoom-valor">
                                    120%
                                </output>
                            </span>

                            <input
                                type="range"
                                id="imagem-zoom"
                                min="105"
                                max="200"
                                step="1"
                                value="120">

                        </label>


                        <label class="produto-upload">

                            <i
                                class="bi bi-cloud-arrow-up"
                                aria-hidden="true"></i>

                            <span>

                                <strong>
                                    Escolher imagem
                                </strong>

                                <small>
                                    JPG, PNG ou WebP · máximo de 5 MB
                                </small>

                            </span>

                            <input
                                type="file"
                                id="imagem"
                                name="imagem"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                        </label>

                    </div>


                    <?php if ($imagemProduto !== null): ?>

                        <p class="produto-imagem-aviso">

                            <i
                                class="bi bi-info-circle"
                                aria-hidden="true"></i>

                            Envie uma nova imagem para substituir a atual.

                        </p>


                        <label class="produto-remover-imagem">

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

            </div>

        </section>


        <!-- ==================================
             AÇÕES
        =================================== -->

        <footer class="produto-editor-acoes">

            <a
                href="<?= BASE_URL ?>/admin/produtos"
                class="produto-editor-cancelar">
                Cancelar
            </a>


            <button
                type="submit"
                class="produto-editor-salvar">

                <i
                    class="bi bi-check2"
                    aria-hidden="true"></i>

                <?= $produto
                    ? 'Salvar alterações'
                    : 'Criar produto'
                ?>

            </button>

        </footer>

    </form>

</main>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';
