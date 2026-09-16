<?php

declare(strict_types=1);

$pedidos = $pedidos ?? [];
$statusAtual = $statusAtual ?? null;

$nomesStatus = [
    'aguardando_pagamento' => 'Aguardando pagamento',
    'pago' => 'Pago',
    'em_separacao' => 'Em preparação',
    'enviado' => 'Saiu para entrega',
    'entregue' => 'Entregue',
    'cancelado' => 'Cancelado',
];

$porStatus = array_fill_keys(array_keys($nomesStatus), 0);
$faturamento = 0.0;

foreach ($pedidos as $pedido) {
    $statusPedido = (string) ($pedido['status'] ?? '');

    if (array_key_exists($statusPedido, $porStatus)) {
        $porStatus[$statusPedido] += 1;
    }

    if ($statusPedido !== 'cancelado') {
        $faturamento += (float) ($pedido['total'] ?? 0);
    }
}

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container pedidos-admin">

    <section class="pedidos-cabecalho">
        <div>
            <span class="pedidos-sobretitulo"><i class="bi bi-receipt-cutoff" aria-hidden="true"></i> Operação da loja</span>
            <h1>Pedidos</h1>
            <p>Visualize, acompanhe e atualize cada pedido recebido.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin" class="pedidos-voltar"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i> Painel inicial</a>
    </section>

    <section class="pedidos-resumo" aria-label="Resumo dos pedidos">
        <article class="pedido-resumo-card pedido-resumo-card--total">
            <span><i class="bi bi-bag-check-fill"></i></span>
            <div><small>Pedidos exibidos</small><strong><?= count($pedidos) ?></strong></div>
        </article>
        <article class="pedido-resumo-card pedido-resumo-card--preparo">
            <span><i class="bi bi-fire"></i></span>
            <div><small>Em preparação</small><strong><?= $porStatus['em_separacao'] ?></strong></div>
        </article>
        <article class="pedido-resumo-card pedido-resumo-card--entrega">
            <span><i class="bi bi-truck"></i></span>
            <div><small>Em entrega</small><strong><?= $porStatus['enviado'] ?></strong></div>
        </article>
        <article class="pedido-resumo-card pedido-resumo-card--vendas">
            <span><i class="bi bi-cash-stack"></i></span>
            <div><small>Valor dos pedidos</small><strong>R$ <?= number_format($faturamento, 2, ',', '.') ?></strong></div>
        </article>
    </section>

    <section class="pedidos-painel">
        <div class="pedidos-ferramentas">
            <div>
                <strong>Lista de pedidos</strong>
                <span><?= count($pedidos) ?> registro<?= count($pedidos) === 1 ? '' : 's' ?></span>
            </div>

            <form method="GET" action="<?= BASE_URL ?>/admin/pedidos" class="pedidos-filtro-form">
                <label for="statusPedido"><i class="bi bi-funnel" aria-hidden="true"></i><span class="sr-only">Filtrar por status</span></label>
                <select id="statusPedido" name="status">
                    <option value="">Todos os status</option>
                    <?php foreach ($nomesStatus as $valor => $nome): ?>
                        <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"<?= $statusAtual === $valor ? ' selected' : '' ?>><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Aplicar</button>
                <?php if ($statusAtual !== null): ?>
                    <a href="<?= BASE_URL ?>/admin/pedidos" title="Limpar filtro"><i class="bi bi-x-lg" aria-hidden="true"></i><span class="sr-only">Limpar filtro</span></a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($pedidos === []): ?>
            <div class="pedidos-vazio">
                <i class="bi bi-inbox" aria-hidden="true"></i>
                <strong>Nenhum pedido encontrado</strong>
                <span>Quando houver novos pedidos, eles aparecerão aqui.</span>
            </div>
        <?php else: ?>
            <div class="pedidos-tabela-area">
                <table class="pedidos-tabela">
                    <thead>
                        <tr><th>Pedido</th><th>Cliente</th><th>Recebimento</th><th>Agendamento</th><th>Total</th><th>Status</th><th><span class="sr-only">Ações</span></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <?php
                            $status = (string) $pedido['status'];
                            $nomeStatus = $nomesStatus[$status] ?? $status;
                            $dataPedido = !empty($pedido['criado_em']) ? date('d/m/Y · H:i', strtotime((string) $pedido['criado_em'])) : '-';
                            $agendamento = !empty($pedido['data_hora_agendada']) ? date('d/m/Y · H:i', strtotime((string) $pedido['data_hora_agendada'])) : 'Não agendado';
                            $entrega = ($pedido['modalidade_recebimento'] ?? '') === 'entrega';
                            ?>
                            <tr>
                                <td data-label="Pedido">
                                    <div class="pedido-codigo"><strong>#<?= htmlspecialchars((string) $pedido['codigo'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($dataPedido, ENT_QUOTES, 'UTF-8') ?></span></div>
                                </td>
                                <td data-label="Cliente">
                                    <div class="pedido-cliente"><span class="pedido-cliente-avatar"><i class="bi bi-person-fill"></i></span><div><strong><?= htmlspecialchars((string) $pedido['nome_cliente'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars((string) $pedido['email_cliente'], ENT_QUOTES, 'UTF-8') ?></span></div></div>
                                </td>
                                <td data-label="Recebimento"><span class="pedido-recebimento pedido-recebimento--<?= $entrega ? 'entrega' : 'retirada' ?>"><i class="bi bi-<?= $entrega ? 'truck' : 'shop' ?>" aria-hidden="true"></i><?= $entrega ? 'Entrega' : 'Retirada' ?></span></td>
                                <td data-label="Agendamento"><span class="pedido-agendamento"><i class="bi bi-calendar3" aria-hidden="true"></i><?= htmlspecialchars($agendamento, ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td data-label="Total"><strong class="pedido-total">R$ <?= number_format((float) $pedido['total'], 2, ',', '.') ?></strong></td>
                                <td data-label="Status"><span class="pedido-status pedido-status--<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-circle-fill" aria-hidden="true"></i><?= htmlspecialchars($nomeStatus, ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td class="pedido-acao" data-label="Ações"><a href="<?= BASE_URL ?>/admin/pedidos/<?= (int) $pedido['id'] ?>"><i class="bi bi-arrow-up-right" aria-hidden="true"></i><span>Detalhes</span></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
