<?php

declare(strict_types=1);

$destaques = $destaques ?? [];
$csrfToken = $csrfToken ?? \App\Helpers\Csrf::gerar();
$mensagemSucesso = $mensagemSucesso ?? null;
$ativos = count(array_filter($destaques, static fn (array $item): bool => (int) ($item['ativo'] ?? 0) === 1));
$categorias = count(array_filter($destaques, static fn (array $item): bool => ($item['tipo'] ?? '') === 'categoria'));

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container destaques-home-admin">
    <?php if ($mensagemSucesso !== null): ?>
        <div class="admin-sucesso" role="status" data-admin-sucesso><span><i class="bi bi-check2-circle"></i></span><p><?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?></p><button type="button" data-fechar-sucesso aria-label="Fechar"><i class="bi bi-x-lg"></i></button></div>
    <?php endif; ?>

    <section class="banners-cabecalho"><div><span><i class="bi bi-stars"></i> Vitrine da loja</span><h1>Destaques da Home</h1><p>Defina os produtos e categorias que merecem aparecer primeiro para seus clientes.</p></div><a href="<?= BASE_URL ?>/admin/destaques-home/novo" class="banners-novo"><i class="bi bi-plus-lg"></i> Novo destaque</a></section>
    <section class="banners-resumo"><article><span><i class="bi bi-stars"></i></span><div><small>Na vitrine</small><strong><?= count($destaques) ?></strong></div></article><article><span><i class="bi bi-eye-fill"></i></span><div><small>Ativos</small><strong><?= $ativos ?></strong></div></article><article><span><i class="bi bi-grid-3x3-gap-fill"></i></span><div><small>Categorias</small><strong><?= $categorias ?></strong></div></article></section>

    <?php if ($destaques === []): ?>
        <section class="banners-vazio"><span><i class="bi bi-stars"></i></span><h2>Sua vitrine ainda está vazia</h2><p>Adicione uma categoria ou produto para apresentar os sabores mais importantes logo na entrada da loja.</p><a href="<?= BASE_URL ?>/admin/destaques-home/novo"><i class="bi bi-plus-lg"></i> Criar destaque</a></section>
    <?php else: ?>
        <form id="form-ordem-destaques" method="POST" action="<?= BASE_URL ?>/admin/destaques-home/ordem"><input type="hidden" name="_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>"></form>
        <section class="banners-painel"><header><div><strong>Itens em destaque</strong><span>Use a posição para definir a sequência da vitrine.</span></div><button type="submit" form="form-ordem-destaques" class="banners-salvar-ordem"><i class="bi bi-arrow-down-up"></i> Salvar ordem</button></header><div class="banners-lista">
            <?php foreach ($destaques as $destaque): ?>
                <?php $id = (int) $destaque['id']; $tipo = (string) ($destaque['tipo'] ?? ''); $ehCategoria = $tipo === 'categoria'; $nome = $ehCategoria ? (string) ($destaque['categoria_nome'] ?? 'Categoria') : (string) ($destaque['produto_nome'] ?? 'Produto'); $descricao = $ehCategoria ? 'Categoria exibida na seção de destaques.' : (string) ($destaque['produto_descricao'] ?? 'Produto exibido na seção de destaques.'); $imagem = (string) ($destaque['imagem_url'] ?? ''); $ativo = (int) ($destaque['ativo'] ?? 0) === 1; ?>
                <article class="banner-item destaque-item"><div class="banner-item-imagem"><?php if ($imagem !== ''): ?><img src="<?= BASE_URL . '/' . ltrim($imagem, '/') ?>" alt="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"><?php else: ?><i class="bi bi-image"></i><?php endif; ?><span class="banner-item-ordem"><input form="form-ordem-destaques" type="number" name="ordem[<?= $id ?>]" value="<?= (int) ($destaque['ordem'] ?? 0) ?>" min="0" aria-label="Posição de <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"></span></div><div class="banner-item-corpo"><div class="banner-item-meta"><span class="destaque-tipo"><i class="bi bi-<?= $ehCategoria ? 'grid-3x3-gap-fill' : 'box-seam-fill' ?>"></i><?= $ehCategoria ? 'Categoria' : 'Produto' ?></span><span class="banner-status banner-status--<?= $ativo ? 'ativo' : 'inativo' ?>"><i class="bi bi-circle-fill"></i><?= $ativo ? 'Ativo' : 'Inativo' ?></span></div><h2><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars(mb_strimwidth($descricao, 0, 110, '...', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?></p></div><div class="banner-item-acoes"><a class="banner-acao banner-acao--editar" href="<?= BASE_URL ?>/admin/destaques-home/editar/<?= $id ?>"><i class="bi bi-pencil-square"></i><span>Editar</span></a><form method="POST" action="<?= BASE_URL ?>/admin/destaques-home/alternar/<?= $id ?>"><input type="hidden" name="_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>"><button class="banner-acao" type="submit"><i class="bi bi-<?= $ativo ? 'eye-slash' : 'eye' ?>"></i><span><?= $ativo ? 'Ocultar' : 'Ativar' ?></span></button></form><form method="POST" action="<?= BASE_URL ?>/admin/destaques-home/excluir/<?= $id ?>" onsubmit="return confirm('Remover este destaque?');"><input type="hidden" name="_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>"><button class="banner-acao banner-acao--remover" type="submit"><i class="bi bi-trash3"></i><span>Remover</span></button></form></div></article>
            <?php endforeach; ?>
        </div></section>
    <?php endif; ?>
</main>

<?php require APP_ROOT . '/views/layouts/admin-footer.php'; ?>
