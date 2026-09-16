<?php

declare(strict_types=1);

$categorias = $categorias ?? [];
$mensagemSucesso = $mensagemSucesso ?? null;
$ativas = count(array_filter($categorias, static function (array $categoria): bool {
    return (int) ($categoria['ativo'] ?? 0) === 1;
}));
$destaques = count(array_filter($categorias, static function (array $categoria): bool {
    return (int) ($categoria['destaque'] ?? 0) === 1;
}));

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container cardapio-admin">

    <?php if ($mensagemSucesso !== null): ?>
        <div class="admin-sucesso" role="status" data-admin-sucesso>
            <span><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
            <p><?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?></p>
            <button type="button" aria-label="Fechar mensagem" data-fechar-sucesso><i class="bi bi-x-lg" aria-hidden="true"></i></button>
        </div>
    <?php endif; ?>

    <section class="cardapio-cabecalho">
        <div>
            <span class="cardapio-sobretitulo"><i class="bi bi-journal-richtext" aria-hidden="true"></i> Organização da loja</span>
            <h1>Cardápio</h1>
            <p>Gerencie as categorias exibidas para seus clientes.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/categorias/novo" class="cardapio-novo-botao"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nova categoria</a>
    </section>

    <section class="cardapio-resumo" aria-label="Resumo do cardápio">
        <article><span><i class="bi bi-grid-3x3-gap-fill"></i></span><div><small>Categorias</small><strong><?= count($categorias) ?></strong></div></article>
        <article><span><i class="bi bi-check2-circle"></i></span><div><small>Ativas</small><strong><?= $ativas ?></strong></div></article>
        <article><span><i class="bi bi-star-fill"></i></span><div><small>Em destaque</small><strong><?= $destaques ?></strong></div></article>
    </section>

    <section class="cardapio-painel">
        <div class="cardapio-painel-topo"><div><strong>Categorias cadastradas</strong><span><?= count($categorias) ?> registro<?= count($categorias) === 1 ? '' : 's' ?></span></div><label class="cardapio-busca"><i class="bi bi-search" aria-hidden="true"></i><input id="buscaCategorias" type="search" placeholder="Buscar categoria..." autocomplete="off"></label></div>

        <?php if ($categorias === []): ?>
            <div class="cardapio-vazio"><i class="bi bi-journal-plus" aria-hidden="true"></i><strong>Seu cardápio ainda está vazio</strong><span>Crie a primeira categoria para começar.</span></div>
        <?php else: ?>
            <div class="cardapio-grade" id="listaCategorias">
                <?php foreach ($categorias as $categoria): ?>
                    <?php
                    $ativa = (int) $categoria['ativo'] === 1;
                    $destaque = (int) $categoria['destaque'] === 1;
                    $escalaImagem = min(max((float) ($categoria['imagem_escala'] ?? 1.20), 1.05), 2.00);
                    $posicaoX = min(max((float) ($categoria['imagem_posicao_x'] ?? 50), 0), 100);
                    $posicaoY = min(max((float) ($categoria['imagem_posicao_y'] ?? 50), 0), 100);
                    $excedente = ($escalaImagem - 1) * 100;
                    $estiloImagem = sprintf(
                        'width:%.2f%%;height:%.2f%%;left:%.2f%%;top:%.2f%%;',
                        $escalaImagem * 100,
                        $escalaImagem * 100,
                        -($posicaoX / 100) * $excedente,
                        -($posicaoY / 100) * $excedente
                    );
                    ?>
                    <article class="categoria-card" data-categoria="<?= htmlspecialchars(mb_strtolower((string) $categoria['nome'], 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="categoria-card-imagem">
                            <?php if (!empty($categoria['imagem_url'])): ?>
                                <img src="<?= BASE_URL . $categoria['imagem_url'] ?>" alt="<?= htmlspecialchars('Imagem de ' . $categoria['nome'], ENT_QUOTES, 'UTF-8') ?>" style="<?= $estiloImagem ?>">
                            <?php else: ?>
                                <span><i class="bi bi-image"></i></span>
                            <?php endif; ?>
                            <?php if ($destaque): ?><em><i class="bi bi-star-fill"></i> Destaque</em><?php endif; ?>
                        </div>
                        <div class="categoria-card-corpo">
                            <div class="categoria-card-titulo"><div><h2><?= htmlspecialchars((string) $categoria['nome'], ENT_QUOTES, 'UTF-8') ?></h2><span>Ordem <?= (int) $categoria['ordem_destaque'] ?></span></div><span class="categoria-status categoria-status--<?= $ativa ? 'ativa' : 'inativa' ?>"><i class="bi bi-circle-fill"></i><?= $ativa ? 'Ativa' : 'Inativa' ?></span></div>
                            <p><?= htmlspecialchars((string) ($categoria['descricao'] ?? 'Sem descrição cadastrada.'), ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="categoria-card-valores"><div><span>Preço</span><strong>R$ <?= number_format((float) $categoria['preco'], 2, ',', '.') ?></strong></div><div><span>Revenda</span><strong><?= $categoria['preco_revenda'] !== null ? 'R$ ' . number_format((float) $categoria['preco_revenda'], 2, ',', '.') : 'Não definido' ?></strong></div></div>
                            <a href="<?= BASE_URL ?>/admin/categorias/editar/<?= (int) $categoria['id'] ?>" class="categoria-editar"><span>Editar categoria</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div id="categoriasVazio" class="cardapio-vazio" hidden><i class="bi bi-search-heart" aria-hidden="true"></i><strong>Nenhuma categoria encontrada</strong><span>Tente buscar por outro nome.</span></div>
        <?php endif; ?>
    </section>

</main>

<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
