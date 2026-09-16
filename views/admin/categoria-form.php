<?php

declare(strict_types=1);

$categoria = $categoria ?? null;
$imagemCategoria = $imagemCategoria ?? null;
$posicaoX = $imagemCategoria !== null && isset($imagemCategoria['posicao_x']) ? (float) $imagemCategoria['posicao_x'] : 50.0;
$posicaoY = $imagemCategoria !== null && isset($imagemCategoria['posicao_y']) ? (float) $imagemCategoria['posicao_y'] : 50.0;
$escalaImagem = $imagemCategoria !== null && isset($imagemCategoria['escala']) ? (float) $imagemCategoria['escala'] : 1.20;

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container categoria-editor-admin">

    <section class="categoria-editor-cabecalho">
        <div>
            <a href="<?= BASE_URL ?>/admin/categorias" class="detalhe-voltar"><i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar ao cardápio</a>
            <span class="categoria-editor-sobretitulo"><?= $categoria ? 'Edição de categoria' : 'Nova categoria' ?></span>
            <h1><?= $categoria ? 'Editar categoria' : 'Criar categoria' ?></h1>
            <p><?= $categoria ? 'Atualize a apresentação e as regras desta categoria.' : 'Defina como essa categoria aparecerá no seu cardápio.' ?></p>
        </div>
        <span class="categoria-editor-etapa"><i class="bi bi-journal-check" aria-hidden="true"></i> Cardápio</span>
    </section>

    <form class="categoria-editor-formulario" method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?><?= $categoria ? '/admin/categorias/atualizar/' . $categoria['id'] : '/admin/categorias/salvar' ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="posicao_x" id="posicao_x" value="<?= htmlspecialchars((string) $posicaoX, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="posicao_y" id="posicao_y" value="<?= htmlspecialchars((string) $posicaoY, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="escala" id="escala" value="<?= htmlspecialchars((string) $escalaImagem, ENT_QUOTES, 'UTF-8') ?>">

        <section class="categoria-editor-painel categoria-editor-dados">
            <header><span><i class="bi bi-pencil-square"></i></span><div><h2>Informações da categoria</h2><p>Dados que serão exibidos no cardápio.</p></div></header>
            <div class="categoria-editor-campos">
                <label class="categoria-campo categoria-campo--amplo"><span>Nome</span><input type="text" id="nome" name="nome" required maxlength="100" value="<?= htmlspecialchars($categoria['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
                <label class="categoria-campo categoria-campo--amplo"><span>Slug</span><input type="text" id="slug" name="slug" required maxlength="120" value="<?= htmlspecialchars($categoria['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
                <label class="categoria-campo categoria-campo--total"><span>Descrição</span><textarea id="descricao" name="descricao" rows="4" maxlength="255"><?= htmlspecialchars($categoria['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></label>
                <label class="categoria-campo"><span>Preço</span><input type="number" id="preco" name="preco" min="0" step="0.01" required value="<?= htmlspecialchars((string) ($categoria['preco'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>"></label>
                <label class="categoria-campo"><span>Preço de revenda</span><input type="number" id="preco_revenda" name="preco_revenda" min="0" step="0.01" value="<?= htmlspecialchars((string) ($categoria['preco_revenda'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                <label class="categoria-campo"><span>Mínimo para revenda</span><input type="number" id="quantidade_minima_revenda" name="quantidade_minima_revenda" min="1" value="<?= htmlspecialchars((string) ($categoria['quantidade_minima_revenda'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            </div>
        </section>

        <section class="categoria-editor-painel categoria-editor-config">
            <header><span><i class="bi bi-sliders"></i></span><div><h2>Disponibilidade e destaque</h2><p>Controle como a categoria será apresentada.</p></div></header>
            <div class="categoria-editor-campos categoria-editor-campos--config">
                <label class="categoria-campo"><span>Status</span><select id="ativo" name="ativo"><option value="1"<?= !isset($categoria['ativo']) || (int) $categoria['ativo'] === 1 ? ' selected' : '' ?>>Ativa</option><option value="0"<?= isset($categoria['ativo']) && (int) $categoria['ativo'] === 0 ? ' selected' : '' ?>>Inativa</option></select></label>
                <label class="categoria-campo"><span>Destaque</span><select id="destaque" name="destaque"><option value="0"<?= !isset($categoria['destaque']) || (int) $categoria['destaque'] === 0 ? ' selected' : '' ?>>Normal</option><option value="1"<?= isset($categoria['destaque']) && (int) $categoria['destaque'] === 1 ? ' selected' : '' ?>>Em destaque</option></select></label>
                <label class="categoria-campo"><span>Ordem do destaque</span><input type="number" id="ordem_destaque" name="ordem_destaque" min="0" value="<?= htmlspecialchars((string) ($categoria['ordem_destaque'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"></label>
            </div>
        </section>

        <section class="categoria-editor-painel categoria-editor-imagem-painel">
            <header><span><i class="bi bi-image-fill"></i></span><div><h2>Imagem da categoria</h2><p>Arraste, mova e ajuste o tamanho para definir exatamente o enquadramento do card.</p></div></header>
            <div class="categoria-imagem-editor">
                <div class="categoria-imagem-palco" id="imagem-palco">
                    <?php if ($imagemCategoria !== null && !empty($imagemCategoria['url_imagem'])): ?>
                        <img id="imagem-guia" class="categoria-imagem-guia" src="<?= BASE_URL . $imagemCategoria['url_imagem'] ?>" alt="" aria-hidden="true">
                    <?php endif; ?>
                    <div id="imagem-preview-container" class="categoria-imagem-preview">
                        <?php if ($imagemCategoria !== null && !empty($imagemCategoria['url_imagem'])): ?>
                            <img id="imagem-preview" src="<?= BASE_URL . $imagemCategoria['url_imagem'] ?>" alt="Pré-visualização da imagem" draggable="false">
                        <?php else: ?>
                            <div id="imagem-preview-placeholder" class="categoria-imagem-placeholder"><i class="bi bi-image"></i><span>Selecione uma imagem</span></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="categoria-imagem-controles" aria-label="Ajustar enquadramento da imagem">
                    <button type="button" data-mover-imagem="cima" aria-label="Mover para cima"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" data-mover-imagem="esquerda" aria-label="Mover para esquerda"><i class="bi bi-arrow-left"></i></button>
                    <button type="button" id="imagem-centralizar" class="categoria-imagem-centralizar" aria-label="Centralizar imagem"><i class="bi bi-bullseye"></i></button>
                    <button type="button" data-mover-imagem="direita" aria-label="Mover para direita"><i class="bi bi-arrow-right"></i></button>
                    <button type="button" data-mover-imagem="baixo" aria-label="Mover para baixo"><i class="bi bi-arrow-down"></i></button>
                </div>
                <label class="categoria-zoom"><span><i class="bi bi-zoom-in"></i> Tamanho da imagem</span><output id="imagem-zoom-valor"><?= (int) round($escalaImagem * 100) ?>%</output><input type="range" id="imagem-zoom" min="105" max="200" step="1" value="<?= (int) round($escalaImagem * 100) ?>"><small>A área translúcida mostra a parte da imagem que ficará fora do card.</small></label>
                <label class="categoria-upload"><i class="bi bi-cloud-arrow-up"></i><span><strong>Escolher imagem</strong><small>JPG, PNG ou WebP · até 5 MB · será salva em WebP</small></span><input type="file" id="imagem" name="imagem" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
                <?php if ($imagemCategoria !== null): ?><label class="categoria-remover-imagem"><input type="checkbox" name="excluir_imagem" value="1" id="excluir_imagem"><span>Remover a imagem atual</span></label><?php endif; ?>
            </div>
        </section>

        <footer class="categoria-editor-acoes"><a href="<?= BASE_URL ?>/admin/categorias" class="categoria-editor-cancelar">Cancelar</a><button type="submit" class="categoria-editor-salvar"><i class="bi bi-check2"></i><?= $categoria ? 'Salvar alterações' : 'Criar categoria' ?></button></footer>
    </form>

</main>

<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
