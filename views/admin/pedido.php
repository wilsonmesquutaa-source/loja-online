<?php

declare(strict_types=1);

$pedido = $pedido ?? null;
$itens = $itens ?? [];
$endereco = $endereco ?? null;
$pagamento = $pagamento ?? null;
$csrfToken = $csrfToken ?? \App\Helpers\Csrf::gerar();

if ($pedido === null) {
    http_response_code(404);
    exit('Pedido não encontrado.');
}

$nomesStatus = [
    'aguardando_pagamento' => 'Aguardando pagamento',
    'pago' => 'Pago',
    'em_separacao' => 'Em preparação',
    'enviado' => 'Saiu para entrega',
    'entregue' => 'Entregue',
    'cancelado' => 'Cancelado',
];
$nomesPagamento = ['pix' => 'Pix', 'cartao' => 'Cartão'];
$nomesStatusPagamento = ['pendente' => 'Pendente', 'aprovado' => 'Aprovado', 'recusado' => 'Recusado', 'cancelado' => 'Cancelado', 'reembolsado' => 'Reembolsado'];

$status = (string) $pedido['status'];
$nomeStatus = $nomesStatus[$status] ?? $status;
$entrega = ($pedido['modalidade_recebimento'] ?? '') === 'entrega';
$modalidade = $entrega ? 'Entrega' : 'Retirada';
$dataPedido = !empty($pedido['criado_em']) ? date('d/m/Y · H:i', strtotime((string) $pedido['criado_em'])) : '-';
$dataAgendada = !empty($pedido['data_hora_agendada']) ? date('d/m/Y · H:i', strtotime((string) $pedido['data_hora_agendada'])) : 'Não informado';
$inicioPreparo = !empty($pedido['inicio_preparo']) ? date('d/m/Y · H:i', strtotime((string) $pedido['inicio_preparo'])) : 'Não iniciado';
$fimPreparo = !empty($pedido['fim_preparo_previsto']) ? date('d/m/Y · H:i', strtotime((string) $pedido['fim_preparo_previsto'])) : 'Não informado';

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container detalhe-pedido-admin">

    <section class="detalhe-pedido-cabecalho">
        <div>
            <a href="<?= BASE_URL ?>/admin/pedidos" class="detalhe-voltar"><i class="bi bi-arrow-left" aria-hidden="true"></i> Todos os pedidos</a>
            <span class="detalhe-sobretitulo">Detalhamento do pedido</span>
            <h1>#<?= htmlspecialchars((string) $pedido['codigo'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Realizado em <?= htmlspecialchars($dataPedido, ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div class="detalhe-status-destaque detalhe-status-destaque--<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
            <i class="bi bi-circle-fill" aria-hidden="true"></i>
            <span><?= htmlspecialchars($nomeStatus, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </section>

    <section class="detalhe-pedido-grid">

        <div class="detalhe-pedido-principal">
            <article class="detalhe-painel detalhe-itens-painel">
                <header class="detalhe-painel-cabecalho">
                    <div><span class="detalhe-painel-icone"><i class="bi bi-bag-heart-fill"></i></span><div><h2>Itens do pedido</h2><p><?= count($itens) ?> item<?= count($itens) === 1 ? '' : 'ns' ?> selecionado<?= count($itens) === 1 ? '' : 's' ?></p></div></div>
                </header>

                <?php if ($itens === []): ?>
                    <div class="detalhe-sem-itens"><i class="bi bi-inbox"></i><span>Nenhum item encontrado.</span></div>
                <?php else: ?>
                    <div class="detalhe-itens-lista">
                        <?php foreach ($itens as $item): ?>
                            <div class="detalhe-item">
                                <div class="detalhe-item-quantidade"><?= (int) $item['quantidade'] ?>×</div>
                                <div class="detalhe-item-nome"><strong><?= htmlspecialchars((string) $item['nome_produto'], ENT_QUOTES, 'UTF-8') ?></strong><span>R$ <?= number_format((float) $item['preco_unitario'], 2, ',', '.') ?> por unidade</span></div>
                                <strong class="detalhe-item-subtotal">R$ <?= number_format((float) $item['subtotal'], 2, ',', '.') ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>

            <?php if (!empty($pedido['observacao'])): ?>
                <article class="detalhe-painel detalhe-observacao">
                    <header class="detalhe-painel-cabecalho"><div><span class="detalhe-painel-icone"><i class="bi bi-chat-left-text-fill"></i></span><div><h2>Observação do cliente</h2><p>Mensagem enviada junto ao pedido</p></div></div></header>
                    <p><?= nl2br(htmlspecialchars((string) $pedido['observacao'], ENT_QUOTES, 'UTF-8')) ?></p>
                </article>
            <?php endif; ?>

            <?php if ($entrega && $endereco !== null): ?>
                <article class="detalhe-painel detalhe-endereco">
                    <header class="detalhe-painel-cabecalho"><div><span class="detalhe-painel-icone"><i class="bi bi-geo-alt-fill"></i></span><div><h2>Endereço de entrega</h2><p>Local informado pelo cliente</p></div></div></header>
                    <div class="detalhe-endereco-corpo">
                        <strong><?= htmlspecialchars((string) $endereco['destinatario'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <p><?= htmlspecialchars((string) $endereco['logradouro'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) $endereco['numero'], ENT_QUOTES, 'UTF-8') ?><?= !empty($endereco['complemento']) ? ' · ' . htmlspecialchars((string) $endereco['complemento'], ENT_QUOTES, 'UTF-8') : '' ?></p>
                        <p><?= htmlspecialchars((string) $endereco['bairro'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) $endereco['cidade'], ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars((string) $endereco['estado'], ENT_QUOTES, 'UTF-8') ?> · CEP <?= htmlspecialchars((string) $endereco['cep'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </article>
            <?php endif; ?>
        </div>

        <aside class="detalhe-pedido-lateral">
            <article class="detalhe-painel detalhe-atualizar-status">
                <header><span class="detalhe-painel-icone"><i class="bi bi-arrow-repeat"></i></span><div><h2>Atualizar pedido</h2><p>Altere o andamento quando necessário.</p></div></header>
                <form method="POST" action="<?= BASE_URL ?>/admin/pedidos/status/<?= (int) $pedido['id'] ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="statusPedidoDetalhe">Novo status</label>
                    <select id="statusPedidoDetalhe" name="status" required>
                        <?php foreach ($nomesStatus as $valor => $nome): ?>
                            <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"<?= $status === $valor ? ' selected' : '' ?>><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit"><i class="bi bi-check2" aria-hidden="true"></i> Salvar atualização</button>
                </form>
            </article>

            <article class="detalhe-painel detalhe-resumo-financeiro">
                <header><span class="detalhe-painel-icone"><i class="bi bi-wallet2"></i></span><div><h2>Resumo financeiro</h2><p>Valores registrados no pedido</p></div></header>
                <div class="detalhe-valores"><div><span>Subtotal</span><strong>R$ <?= number_format((float) $pedido['subtotal'], 2, ',', '.') ?></strong></div><div><span>Frete</span><strong>R$ <?= number_format((float) $pedido['frete'], 2, ',', '.') ?></strong></div><div><span>Desconto</span><strong>R$ <?= number_format((float) $pedido['desconto'], 2, ',', '.') ?></strong></div><div class="detalhe-total"><span>Total</span><strong>R$ <?= number_format((float) $pedido['total'], 2, ',', '.') ?></strong></div></div>
            </article>

            <article class="detalhe-painel detalhe-info-pedido">
                <header><span class="detalhe-painel-icone"><i class="bi bi-person-vcard-fill"></i></span><div><h2>Cliente</h2><p>Dados para contato</p></div></header>
                <div class="detalhe-info-linhas"><div><span>Nome</span><strong><?= htmlspecialchars((string) $pedido['nome_cliente'], ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>E-mail</span><strong><?= htmlspecialchars((string) $pedido['email_cliente'], ENT_QUOTES, 'UTF-8') ?></strong></div></div>
            </article>

            <article class="detalhe-painel detalhe-info-pedido">
                <header><span class="detalhe-painel-icone"><i class="bi bi-<?= $entrega ? 'truck' : 'shop' ?>"></i></span><div><h2>Recebimento</h2><p><?= $modalidade ?></p></div></header>
                <div class="detalhe-info-linhas"><div><span>Agendado para</span><strong><?= htmlspecialchars($dataAgendada, ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>Início do preparo</span><strong><?= htmlspecialchars($inicioPreparo, ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>Previsão</span><strong><?= htmlspecialchars($fimPreparo, ENT_QUOTES, 'UTF-8') ?></strong></div></div>
            </article>

            <article class="detalhe-painel detalhe-info-pedido">
                <header><span class="detalhe-painel-icone"><i class="bi bi-credit-card-2-front-fill"></i></span><div><h2>Pagamento</h2><p><?= $pagamento === null ? 'Sem registro' : 'Informações da cobrança' ?></p></div></header>
                <?php if ($pagamento === null): ?>
                    <div class="detalhe-sem-pagamento">Nenhum pagamento registrado.</div>
                <?php else: ?>
                    <div class="detalhe-info-linhas"><div><span>Método</span><strong><?= htmlspecialchars($nomesPagamento[$pagamento['metodo']] ?? (string) $pagamento['metodo'], ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>Status</span><strong><?= htmlspecialchars($nomesStatusPagamento[$pagamento['status']] ?? (string) $pagamento['status'], ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>Valor</span><strong>R$ <?= number_format((float) $pagamento['valor'], 2, ',', '.') ?></strong></div></div>
                <?php endif; ?>
            </article>
        </aside>

    </section>

</main>

<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
