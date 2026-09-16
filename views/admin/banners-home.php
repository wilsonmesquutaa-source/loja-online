<?php

declare(strict_types=1);

$banners = $banners ?? [];
$csrfToken = $csrfToken ?? \App\Helpers\Csrf::gerar();
$mensagemSucesso = $mensagemSucesso ?? null;
$ativos = count(array_filter($banners, static fn (array $banner): bool => (int) ($banner['ativo'] ?? 0) === 1));

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container banners-home-admin">
    <?php if ($mensagemSucesso !== null): ?>
        <div class="admin-sucesso" role="status" data-admin-sucesso><span><i class="bi bi-check2-circle"></i></span><p><?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?></p><button type="button" data-fechar-sucesso aria-label="Fechar"><i class="bi bi-x-lg"></i></button></div>
    <?php endif; ?>

    <section class="banners-cabecalho">
        <div><span><i class="bi bi-images"></i> Vitrine da loja</span><h1>Banners da Home</h1><p>Controle as imagens de maior destaque na entrada do seu site.</p></div>
        <a href="<?= BASE_URL ?>/admin/banners-home/novo" class="banners-novo"><i class="bi bi-plus-lg"></i> Novo banner</a>
    </section>

    <section class="banners-resumo"><article><span><i class="bi bi-images"></i></span><div><small>Banners</small><strong><?= count($banners) ?></strong></div></article><article><span><i class="bi bi-eye-fill"></i></span><div><small>Ativos</small><strong><?= $ativos ?></strong></div></article><article><span><i class="bi bi-sliders"></i></span><div><small>Controle</small><strong>Manual</strong></div></article></section>

    <?php if ($banners === []): ?>
        <section class="banners-vazio"><span><i class="bi bi-image"></i></span><h2>Crie o primeiro banner</h2><p>Use imagens atrativas para apresentar novidades, campanhas ou produtos especiais.</p><a href="<?= BASE_URL ?>/admin/banners-home/novo"><i class="bi bi-plus-lg"></i> Criar banner</a></section>
    <?php else: ?>
        <section class="banners-painel"><header><div><strong>Banners cadastrados</strong><span>Clique em editar para ajustar imagem, posição e conteúdo.</span></div></header><div class="banners-lista">
            <?php foreach ($banners as $banner): ?>
                <?php $id = (int) $banner['id']; $ativo = (int) ($banner['ativo'] ?? 0) === 1; $imagem = (string) ($banner['url_imagem'] ?? ''); $x = (float) ($banner['posicao_x'] ?? 50); $y = (float) ($banner['posicao_y'] ?? 50); ?>
                <article class="banner-item">
                    <div class="banner-item-imagem"><?php if ($imagem !== ''): ?><img src="<?= BASE_URL . $imagem ?>" alt="<?= htmlspecialchars((string) ($banner['texto_alternativo'] ?? 'Banner da Home'), ENT_QUOTES, 'UTF-8') ?>" style="object-position:<?= $x ?>% <?= $y ?>%;"><?php else: ?><i class="bi bi-image"></i><?php endif; ?><span class="banner-item-ordem">#<?= (int) ($banner['ordem'] ?? 1) ?></span></div>
                    <div class="banner-item-corpo"><div class="banner-item-meta"><span class="banner-status banner-status--<?= $ativo ? 'ativo' : 'inativo' ?>"><i class="bi bi-circle-fill"></i><?= $ativo ? 'Ativo' : 'Inativo' ?></span><span>Posição <?= number_format($x, 0) ?>% · <?= number_format($y, 0) ?>%</span></div><h2><?= htmlspecialchars((string) ($banner['titulo'] ?: 'Banner sem título'), ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars((string) ($banner['texto_alternativo'] ?: 'Sem texto alternativo cadastrado.'), ENT_QUOTES, 'UTF-8') ?></p></div>
                    <div class="banner-item-acoes"><a href="<?= BASE_URL ?>/admin/banners-home/editar/<?= $id ?>" class="banner-acao banner-acao--editar"><i class="bi bi-pencil-square"></i><span>Editar</span></a><form method="POST" action="<?= BASE_URL ?>/admin/banners-home/alternar/<?= $id ?>"><input type="hidden" name="_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>"><button class="banner-acao" type="submit"><i class="bi bi-<?= $ativo ? 'eye-slash' : 'eye' ?>"></i><span><?= $ativo ? 'Ocultar' : 'Ativar' ?></span></button></form><form method="POST" action="<?= BASE_URL ?>/admin/banners-home/excluir/<?= $id ?>" onsubmit="return confirm('Remover este banner?');"><input type="hidden" name="_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>"><button class="banner-acao banner-acao--remover" type="submit"><i class="bi bi-trash3"></i><span>Remover</span></button></form></div>
                </article>
            <?php endforeach; ?>
        </div></section>
    <?php endif; ?>
</main>

<?php require APP_ROOT . '/views/layouts/admin-footer.php'; ?>
