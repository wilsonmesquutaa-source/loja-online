<?php

declare(strict_types=1);

$iconesIndicadores = [
    'produtos' => 'bi-box-seam-fill',
    'clientes' => 'bi-people-fill',
    'pedidos' => 'bi-bag-check-fill',
    'categorias' => 'bi-grid-3x3-gap-fill',
];

$rotulosIndicadores = [
    'produtos' => 'Produtos cadastrados',
    'clientes' => 'Clientes ativos',
    'pedidos' => 'Pedidos recebidos',
    'categorias' => 'Categorias do cardápio',
];

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container dashboard-admin">

    <section class="dashboard-cabecalho">

        <div>

            <span class="dashboard-sobretitulo">
                <i class="bi bi-stars" aria-hidden="true"></i>
                Central de gestão
            </span>

            <h1>Olá, <?= htmlspecialchars(
                $usuarioAdmin['nome'] ?? 'Administrador',
                ENT_QUOTES,
                'UTF-8'
            ) ?>.</h1>

            <p>Acompanhe os principais números da sua loja em um só lugar.</p>

        </div>

        <a href="<?= BASE_URL ?>/admin/pedidos" class="dashboard-acao">
            Ver pedidos
            <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
        </a>

    </section>

    <section class="dashboard-paineis" aria-label="Indicadores da loja">

        <?php foreach ($indicadores as $nome => $valor): ?>

            <article class="dashboard-card dashboard-card--<?= htmlspecialchars(
                $nome,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

                <div class="dashboard-card-topo">

                    <span class="dashboard-card-icone" aria-hidden="true">
                        <i class="bi <?= $iconesIndicadores[$nome] ?? 'bi-bar-chart-fill' ?>"></i>
                    </span>

                    <span class="dashboard-card-status">Atualizado</span>

                </div>

                <div>

                    <span class="dashboard-card-rotulo">
                        <?= htmlspecialchars(
                            $rotulosIndicadores[$nome] ?? ucfirst($nome),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <strong><?= (int) $valor ?></strong>

                </div>

                <span class="dashboard-card-detalhe">
                    <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
                    Visão geral da operação
                </span>

            </article>

        <?php endforeach; ?>

        <article class="dashboard-card dashboard-card--mensagem">

            <div class="dashboard-mensagem-icone" aria-hidden="true">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>

            <div>
                <span class="dashboard-card-rotulo">Painel organizado</span>
                <h2>Tenha controle do cardápio, pedidos e clientes.</h2>
            </div>

            <a href="<?= BASE_URL ?>/admin/produtos">
                Gerenciar produtos
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>

        </article>

    </section>

</main>

<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
