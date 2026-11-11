<?php
declare(strict_types=1);
$tituloPagina = 'Detalhes do cliente';
$usarBootstrap = false;
$cssAdmin = ['clientes.css'];
$jsAdmin = ['clientes.js'];
$esc = static fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$statusNomes = ['aguardando_pagamento' => 'Aguardando pagamento', 'pago' => 'Pago', 'em_separacao' => 'Em preparação', 'enviado' => 'Saiu para entrega', 'entregue' => 'Entregue', 'cancelado' => 'Cancelado'];
require APP_ROOT . '/views/layouts/admin/header.php';
?>
<main class="admin-container clientes-admin">
    <?php require APP_ROOT . '/views/components/admin/clientes-aviso.php'; ?>
    <a class="clientes-link" href="<?= BASE_URL ?>/admin/clientes">← Todos os clientes</a>
    <?php if ($cliente === null): ?>
        <section class="clientes-painel clientes-vazio"><h1>Cliente não encontrado</h1><p>Confira o cadastro na listagem de clientes.</p></section>
    <?php else: ?>
        <header class="clientes-cabecalho"><div><span class="clientes-sobretitulo">Cadastro #<?= (int) $cliente['id'] ?></span><h1><?= $esc($cliente['nome']) ?></h1><p>Informações para atendimento e acompanhamento de pedidos.</p></div></header>
        <section class="clientes-painel clientes-dados" aria-labelledby="dados-titulo">
            <h2 id="dados-titulo">Dados do cadastro</h2>
            <dl><div><dt>E-mail de contato</dt><dd><?= $esc($cliente['email']) ?></dd></div><div><dt>Verificação do e-mail</dt><dd><span class="clientes-status <?= $cliente['email_verificado'] ? 'clientes-status--ok' : '' ?>"><?= $cliente['email_verificado'] ? 'Verificado' : 'Pendente' ?></span></dd></div><div><dt>Cliente desde</dt><dd><?= $esc(date('d/m/Y', strtotime($cliente['criado_em']))) ?></dd></div></dl>
        </section>
        <section class="clientes-painel" aria-labelledby="pedidos-titulo">
            <header class="clientes-painel-titulo"><h2 id="pedidos-titulo">Pedidos do cliente</h2><span><?= (int) $total ?> no total</span></header>
            <?php if ($pedidos === []): ?><div class="clientes-vazio"><h3>Nenhum pedido realizado</h3><p>Os pedidos deste cliente aparecerão aqui.</p></div>
            <?php else: ?>
                <div class="clientes-tabela-scroll"><table class="clientes-tabela"><caption class="clientes-sr">Pedidos deste cliente</caption><thead><tr><th scope="col">Pedido</th><th scope="col">Data</th><th scope="col">Status</th><th scope="col">Total</th><th scope="col">Ação</th></tr></thead><tbody>
                <?php foreach ($pedidos as $pedido): ?><tr><td><strong><?= $esc($pedido['codigo']) ?></strong></td><td><?= $esc(date('d/m/Y', strtotime($pedido['criado_em']))) ?></td><td><?= $esc($statusNomes[$pedido['status']] ?? $pedido['status']) ?></td><td>R$ <?= number_format((float) $pedido['total'], 2, ',', '.') ?></td><td><a class="clientes-link" href="<?= BASE_URL ?>/admin/pedidos/<?= (int) $pedido['id'] ?>">Ver pedido <span aria-hidden="true">→</span></a></td></tr><?php endforeach; ?>
                </tbody></table></div>
                <nav class="clientes-paginacao" aria-label="Paginação de pedidos"><span>Página <?= (int) $pagina ?> de <?= (int) $paginas ?></span><div><?php if ($pagina > 1): ?><a class="clientes-botao clientes-botao--secundario" href="<?= BASE_URL ?>/admin/clientes/<?= (int) $cliente['id'] ?>?pagina=<?= $pagina - 1 ?>">Anterior</a><?php endif; ?><?php if ($pagina < $paginas): ?><a class="clientes-botao clientes-botao--secundario" href="<?= BASE_URL ?>/admin/clientes/<?= (int) $cliente['id'] ?>?pagina=<?= $pagina + 1 ?>">Próxima</a><?php endif; ?></div></nav>
            <?php endif; ?>
        </section>
        <section class="clientes-painel clientes-dados clientes-acoes" aria-labelledby="acoes-titulo">
            <h2 id="acoes-titulo">Atendimento e acesso</h2>
            <?php if (!empty($cliente['possui_senha']) && !empty($cliente['email_verificado'])): ?>
                <p>Envie um link ao e-mail cadastrado. O cliente escolhe a nova senha; você não tem acesso a ela.</p>
                <form method="POST" action="<?= BASE_URL ?>/admin/clientes/<?= (int) $cliente['id'] ?>/recuperar-senha">
                    <input type="hidden" name="_token" value="<?= $esc(\App\Helpers\Csrf::gerar()) ?>">
                    <label class="clientes-confirmar"><input type="checkbox" name="solicitacao_confirmada" value="1" required> O cliente solicitou a recuperação de acesso.</label>
                    <button class="clientes-botao" type="submit">Enviar link de redefinição</button>
                    <small>Válido por 30 minutos. Até 3 envios por dia, com intervalo de 5 minutos.</small>
                </form>
            <?php else: ?><p><?= empty($cliente['possui_senha']) ? 'Esta conta não possui senha local. Oriente o cliente a recuperar o acesso pelo Google.' : 'O e-mail precisa ser confirmado pelo cliente antes da recuperação de senha.' ?></p><?php endif; ?>
        </section>
        <section class="clientes-painel clientes-dados clientes-acoes clientes-exclusao" aria-labelledby="exclusao-titulo">
            <h2 id="exclusao-titulo">Excluir cadastro</h2>
            <?php if ($total > 0): ?><p>A exclusão está bloqueada porque este cliente possui pedidos. Solicitações de encerramento precisam ser analisadas pelo atendimento, preservando os registros necessários.</p>
            <?php else: ?>
                <p>A exclusão é permanente e remove também endereços, carrinhos, dispositivos de notificação e a foto local do cadastro. Confira a solicitação antes de continuar.</p>
                <details><summary>Preparar exclusão</summary>
                    <form method="POST" action="<?= BASE_URL ?>/admin/clientes/<?= (int) $cliente['id'] ?>/excluir" data-confirmar-exclusao>
                        <input type="hidden" name="_token" value="<?= $esc(\App\Helpers\Csrf::gerar()) ?>">
                        <label>Motivo<select name="motivo" required><option value="">Selecione</option><?php foreach (\App\Services\ClienteAdminService::MOTIVOS as $valor => $nome): ?><option value="<?= $esc($valor) ?>"><?= $esc($nome) ?></option><?php endforeach; ?></select></label>
                        <label>Digite <?= (int) $cliente['id'] ?> para confirmar o cadastro<input name="confirmacao" inputmode="numeric" autocomplete="off" required pattern="<?= (int) $cliente['id'] ?>"></label>
                        <label class="clientes-confirmar"><input type="checkbox" name="ciente" value="1" required> Conferi o cadastro e estou ciente de que a exclusão não pode ser desfeita.</label>
                        <button class="clientes-botao clientes-botao--perigo" type="submit">Excluir definitivamente</button>
                    </form>
                </details>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
<?php require APP_ROOT . '/views/layouts/admin/footer.php'; ?>
