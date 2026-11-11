<?php
declare(strict_types=1);
$tituloPagina = 'Clientes';
$usarBootstrap = false;
$cssAdmin = ['clientes.css'];
$jsAdmin = ['clientes.js'];
$esc = static fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$urlPagina = static fn(int $numero): string => BASE_URL . '/admin/clientes?' . http_build_query([
    'busca' => $busca, 'verificacao' => $verificacao, 'pagina' => $numero,
]);
require APP_ROOT . '/views/layouts/admin/header.php';
?>
<main class="admin-container clientes-admin">
    <?php require APP_ROOT . '/views/components/admin/clientes-aviso.php'; ?>
    <header class="clientes-cabecalho">
        <div><span class="clientes-sobretitulo">Relacionamento com o cliente</span><h1>Clientes</h1><p>Encontre um cadastro e acompanhe seus pedidos.</p></div>
        <a class="clientes-botao clientes-botao--secundario" href="<?= BASE_URL ?>/admin">Voltar ao painel</a>
    </header>
    <section class="clientes-resumo" aria-label="Resumo da consulta">
        <div><span class="clientes-resumo-icone" aria-hidden="true">◎</span><div><strong><?= (int) $total ?></strong><span>cadastros encontrados</span></div></div>
        <p>Contato completo disponível nos detalhes, para atendimento.</p>
    </section>
    <section class="clientes-painel" aria-label="Cadastros de clientes">
        <form class="clientes-filtros" method="GET" action="<?= BASE_URL ?>/admin/clientes" role="search" id="clientes-filtros">
            <label class="clientes-busca"><span>Buscar cliente</span><input type="search" name="busca" maxlength="150" value="<?= $esc($busca) ?>" placeholder="Nome ou e-mail" autocomplete="off"></label>
            <label><span>Verificação do e-mail</span><select name="verificacao"><option value="">Todos</option><option value="1" <?= $verificacao === '1' ? 'selected' : '' ?>>Verificado</option><option value="0" <?= $verificacao === '0' ? 'selected' : '' ?>>Pendente</option></select></label>
            <button class="clientes-botao" type="submit">Buscar</button>
            <?php if ($busca !== '' || $verificacao !== ''): ?><a class="clientes-limpar" href="<?= BASE_URL ?>/admin/clientes">Limpar filtros</a><?php endif; ?>
        </form>
        <?php if ($clientes === []): ?>
            <div class="clientes-vazio"><span aria-hidden="true">◎</span><h2>Nenhum cliente encontrado</h2><p><?= $busca !== '' || $verificacao !== '' ? 'Tente outro nome, e-mail ou filtro.' : 'Os novos cadastros aparecerão aqui.' ?></p></div>
        <?php else: ?>
            <div class="clientes-tabela-scroll"><table class="clientes-tabela">
                <caption class="clientes-sr">Clientes encontrados na consulta</caption>
                <thead><tr><th scope="col">Cliente</th><th scope="col">E-mail</th><th scope="col">Verificação</th><th scope="col">Cadastro</th><th scope="col">Pedidos</th><th scope="col">Ação</th></tr></thead>
                <tbody><?php foreach ($clientes as $cliente): ?><tr>
                    <td><div class="clientes-identidade"><span class="clientes-avatar" aria-hidden="true"><?= $esc(mb_strtoupper(mb_substr($cliente['nome'], 0, 1))) ?></span><div><strong><?= $esc($cliente['nome']) ?></strong><small>#<?= (int) $cliente['id'] ?></small></div></div></td>
                    <td><?= $esc($cliente['email_resumido']) ?></td>
                    <td><span class="clientes-status <?= $cliente['email_verificado'] ? 'clientes-status--ok' : '' ?>"><?= $cliente['email_verificado'] ? 'Verificado' : 'Pendente' ?></span></td>
                    <td><?= $esc(date('d/m/Y', strtotime($cliente['criado_em']))) ?></td>
                    <td><?= (int) $cliente['pedidos'] ?></td>
                    <td><a class="clientes-link" href="<?= BASE_URL ?>/admin/clientes/<?= (int) $cliente['id'] ?>" aria-label="Ver cadastro de <?= $esc($cliente['nome']) ?>">Ver cadastro <span aria-hidden="true">→</span></a></td>
                </tr><?php endforeach; ?></tbody>
            </table></div>
            <nav class="clientes-paginacao" aria-label="Paginação de clientes">
                <span>Página <?= (int) $pagina ?> de <?= (int) $paginas ?> · Até 20 cadastros por página</span>
                <div><?php if ($pagina > 1): ?><a class="clientes-botao clientes-botao--secundario" href="<?= $esc($urlPagina($pagina - 1)) ?>">Anterior</a><?php endif; ?><?php if ($pagina < $paginas): ?><a class="clientes-botao clientes-botao--secundario" href="<?= $esc($urlPagina($pagina + 1)) ?>">Próxima</a><?php endif; ?></div>
            </nav>
        <?php endif; ?>
    </section>
</main>
<?php require APP_ROOT . '/views/layouts/admin/footer.php'; ?>
