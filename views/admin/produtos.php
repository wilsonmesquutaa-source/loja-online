<?php

declare(strict_types=1);

$totalProdutos = count($produtos);
$totalAtivos = count(array_filter($produtos, static function (array $produto): bool {
    return ($produto['status'] ?? '') === 'ativo';
}));
$estoqueBaixo = count(array_filter($produtos, static function (array $produto): bool {
    return (int) ($produto['estoque'] ?? 0) <= 5;
}));

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container produtos-admin">

    <section class="produtos-cabecalho">

        <div>
            <span class="produtos-sobretitulo">
                <i class="bi bi-boxes" aria-hidden="true"></i>
                Catálogo da loja
            </span>
            <h1>Produtos</h1>
            <p>Organize o cardápio, disponibilidade e estoque em um só painel.</p>
        </div>

        <a href="<?= BASE_URL ?>/admin/produtos/novo" class="produto-novo-botao">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            Novo produto
        </a>

    </section>

    <section class="produtos-resumo" aria-label="Resumo dos produtos">

        <article class="produto-resumo-card produto-resumo-card--total">
            <span class="produto-resumo-icone"><i class="bi bi-box-seam-fill"></i></span>
            <div><small>Total no catálogo</small><strong><?= $totalProdutos ?></strong></div>
        </article>

        <article class="produto-resumo-card produto-resumo-card--ativo">
            <span class="produto-resumo-icone"><i class="bi bi-check2-circle"></i></span>
            <div><small>Produtos ativos</small><strong><?= $totalAtivos ?></strong></div>
        </article>

        <article class="produto-resumo-card produto-resumo-card--alerta">
            <span class="produto-resumo-icone"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <div><small>Estoque baixo</small><strong><?= $estoqueBaixo ?></strong></div>
        </article>

    </section>

    <section class="produtos-painel">

        <div class="produtos-ferramentas">

            <label class="produtos-busca">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input
                    id="buscaProdutos"
                    type="search"
                    placeholder="Buscar produto ou categoria..."
                    autocomplete="off"
                >
            </label>

            <div class="produtos-filtros" aria-label="Filtrar produtos">
                <button type="button" class="produto-filtro ativo" data-filtro="todos">Todos</button>
                <button type="button" class="produto-filtro" data-filtro="ativo">Ativos</button>
                <button type="button" class="produto-filtro" data-filtro="inativo">Inativos</button>
                <button type="button" class="produto-filtro" data-filtro="baixo">Estoque baixo</button>
            </div>

        </div>

        <div class="produtos-tabela-area">

            <table class="produtos-tabela">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th><span class="sr-only">Ações</span></th>
                    </tr>
                </thead>
                <tbody id="listaProdutos">

                    <?php foreach ($produtos as $produto): ?>

                        <?php
                        $estoque = (int) $produto['estoque'];
                        $status = (string) $produto['status'];
                        $estoqueClasse = $estoque <= 5 ? 'baixo' : ($estoque <= 15 ? 'medio' : 'alto');
                        ?>

                        <tr
                            data-produto="<?= htmlspecialchars(mb_strtolower(
                                $produto['nome'] . ' ' . $produto['categoria'],
                                'UTF-8'
                            ), ENT_QUOTES, 'UTF-8') ?>"
                            data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"
                            data-estoque="<?= $estoque ?>"
                        >
                            <td data-label="Produto">
                                <div class="produto-item">
                                    <?php if (!empty($produto['imagem_url'])): ?>
                                        <img
                                            src="<?= BASE_URL . $produto['imagem_url'] ?>"
                                            alt="<?= htmlspecialchars('Imagem de ' . $produto['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                    <?php else: ?>
                                        <span class="produto-sem-imagem"><i class="bi bi-image"></i></span>
                                    <?php endif; ?>

                                    <div>
                                        <strong><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span>#<?= (int) $produto['id'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Categoria"><span class="produto-categoria"><?= htmlspecialchars($produto['categoria'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td data-label="Estoque">
                                <span class="produto-estoque produto-estoque--<?= $estoqueClasse ?>">
                                    <i class="bi bi-box2-heart" aria-hidden="true"></i>
                                    <?= $estoque ?> un.
                                </span>
                            </td>
                            <td data-label="Status"><span class="produto-status produto-status--<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-circle-fill" aria-hidden="true"></i><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="produto-acoes" data-label="Ações">
                                <a href="<?= BASE_URL ?>/admin/produtos/editar/<?= (int) $produto['id'] ?>" class="produto-acao produto-acao--editar" title="Editar <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i><span>Editar</span>
                                </a>
                                <form action="<?= BASE_URL ?>/admin/produtos/excluir/<?= (int) $produto['id'] ?>" method="POST" class="produto-excluir-form" data-produto-nome="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="produto-acao produto-acao--excluir" title="Excluir <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="bi bi-trash3" aria-hidden="true"></i><span>Excluir</span>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

            <div id="produtosVazio" class="produtos-vazio" hidden>
                <i class="bi bi-search-heart" aria-hidden="true"></i>
                <strong>Nenhum produto encontrado</strong>
                <span>Tente buscar por outro nome ou ajuste o filtro.</span>
            </div>

        </div>

    </section>

</main>

<div id="modalExcluirProduto" class="produto-modal" aria-hidden="true">
    <div class="produto-modal-caixa" role="dialog" aria-modal="true" aria-labelledby="modalExcluirTitulo">
        <span class="produto-modal-icone"><i class="bi bi-trash3-fill"></i></span>
        <h2 id="modalExcluirTitulo">Excluir produto?</h2>
        <p>Você está prestes a excluir <strong id="modalExcluirNome"></strong>. Esta ação não poderá ser desfeita.</p>
        <div class="produto-modal-acoes">
            <button type="button" class="produto-modal-cancelar" data-fechar-modal>Cancelar</button>
            <button type="button" class="produto-modal-confirmar" id="confirmarExcluirProduto">Sim, excluir</button>
        </div>
    </div>
</div>

<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
