<?php

declare(strict_types=1);

$tituloPagina = 'Dashboard administrativo';

require $raizProjeto . '/views/layouts/header.php';

?>

<?php
// views/admin/index.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Autenticação de segurança
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../loginadm.php");
    exit;
}

$caminhoConexao = __DIR__ . '/../../conexao/conexao.php';

if (!file_exists($caminhoConexao)) {
    die("<strong>Erro:</strong> O arquivo de conexão não foi encontrado em: <code>" . $caminhoConexao . "</code>");
}

require_once $caminhoConexao;

if (!isset($pdo)) {
    die("<strong>Erro:</strong> A variável \$pdo não foi definida em conexao.php.");
}

try {
    // 1. PEDIDOS
    $stmtPedidos = $pdo->query("SELECT COUNT(id) AS total_pedidos, COALESCE(SUM(total), 0) AS faturamento_total FROM pedidos WHERE status != 'cancelado'");
    $pedidosData = $stmtPedidos->fetch();

    // 2. PAGAMENTOS
    $stmtPagamentos = $pdo->query("SELECT COUNT(id) AS total_aprovados, COALESCE(SUM(valor), 0) AS receita_aprovada FROM pagamentos WHERE status = 'aprovado'");
    $pagamentosData = $stmtPagamentos->fetch();

    // 3. PRODUTOS (Salgados)
    $stmtProdutos = $pdo->query("SELECT COUNT(id) AS total FROM produtos WHERE status = 'ativo'");
    $totalProdutos = $stmtProdutos->fetch()['total'] ?? 0;

    // 4. CATEGORIAS (Fritos, Assados, Folhados, Bebidas, etc)
    $stmtCategorias = $pdo->query("SELECT COUNT(id) AS total FROM categorias WHERE ativo = 1");
    $totalCategorias = $stmtCategorias->fetch()['total'] ?? 0;

    // 5. CLIENTES
    $stmtClientes = $pdo->query("SELECT COUNT(id) AS total FROM clientes");
    $totalClientes = $stmtClientes->fetch()['total'] ?? 0;

    // 6. CARRINHOS
    $stmtCarrinhos = $pdo->query("SELECT 
        COALESCE(SUM(CASE WHEN status = 'aberto' THEN 1 ELSE 0 END), 0) AS abertos,
        COALESCE(SUM(CASE WHEN status = 'abandonado' THEN 1 ELSE 0 END), 0) AS abandonados
        FROM carrinhos");
    $carrinhosData = $stmtCarrinhos->fetch();

    // 7. ADMINS
    $stmtAdmins = $pdo->query("SELECT COUNT(id) AS total FROM usuarios_admin WHERE status = 'ativo'");
    $totalAdmins = $stmtAdmins->fetch()['total'] ?? 0;

    // Últimos 5 Pedidos
    $stmtUltimosPedidos = $pdo->query("
        SELECT p.id, p.codigo, p.total, p.status, p.criado_em, c.nome AS cliente_nome 
        FROM pedidos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        ORDER BY p.criado_em DESC LIMIT 5
    ");
    $ultimosPedidos = $stmtUltimosPedidos->fetchAll();

} catch (PDOException $e) {
    die("<strong>Erro no Banco de Dados:</strong> " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantim do Lanche - Painel Administrativo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #fefce8; color: #451a03; }
        
        /* Sidebar Navigation - Tom Marrom/Dourado Escuro */
        .sidebar { width: 270px; background-color: #291e12; color: #fef3c7; display: flex; flex-direction: column; flex-shrink: 0; }
        
        .sidebar-brand { 
            padding: 20px 15px; 
            text-align: center; 
            border-bottom: 1px solid #451a03; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 10px;
        }
        
        .sidebar-brand img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f59e0b;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .sidebar-brand img:hover {
            transform: scale(1.08) rotate(3deg);
        }

        .sidebar-brand span {
            font-size: 18px;
            font-weight: 700;
            color: #fbbf24;
            letter-spacing: 0.5px;
        }

        .sidebar nav { display: flex; flex-direction: column; padding: 15px 0; flex-grow: 1; }
        
        .sidebar nav a { 
            color: #d97706; 
            text-decoration: none; 
            padding: 14px 24px; 
            font-size: 15px; 
            display: flex; 
            align-items: center; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .sidebar nav a:hover { 
            background-color: #451a03; 
            color: #fef3c7; 
            border-left-color: #f59e0b; 
            padding-left: 30px; 
        }

        .sidebar nav a.active { 
            background-color: #451a03; 
            color: #ffffff; 
            border-left-color: #f59e0b; 
            font-weight: 700;
        }

        .sidebar nav a.logout { 
            margin-top: auto; 
            background-color: #dc2626; 
            color: #fff; 
            text-align: center; 
            justify-content: center; 
            border-left: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .sidebar nav a.logout:hover { 
            background-color: #b91c1c; 
            padding-left: 24px;
            transform: scale(0.98);
        }

        /* Conteúdo Principal */
        .main-content { flex-grow: 1; padding: 30px; overflow-y: auto; animation: fadeIn 0.4s ease-out; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .top-bar h1 { font-size: 26px; color: #78350f; font-weight: 800; }
        
        .admin-badge { 
            background-color: #ffffff; 
            padding: 8px 18px; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 14px; 
            color: #92400e; 
            border: 1px solid #fde68a;
            box-shadow: 0 2px 5px rgba(217, 119, 6, 0.1);
            transition: transform 0.3s ease;
        }
        .admin-badge:hover {
            transform: translateY(-2px);
        }

        /* KPI Cards Grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px; }
        
        .card { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(120, 53, 15, 0.05); 
            border-top: 4px solid #f59e0b; 
            opacity: 0;
            animation: cardAppear 0.5s ease-out forwards;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 20px rgba(180, 83, 9, 0.15);
        }

        /* Animação em cascata */
        .card:nth-child(1) { animation-delay: 0.05s; }
        .card:nth-child(2) { animation-delay: 0.10s; }
        .card:nth-child(3) { animation-delay: 0.15s; }
        .card:nth-child(4) { animation-delay: 0.20s; }
        .card:nth-child(5) { animation-delay: 0.25s; }
        .card:nth-child(6) { animation-delay: 0.30s; }
        .card:nth-child(7) { animation-delay: 0.35s; }

        .card.success { border-top-color: #16a34a; }
        .card.warning { border-top-color: #ea580c; }
        .card.purple { border-top-color: #d97706; }
        .card.danger { border-top-color: #dc2626; }
        .card.info { border-top-color: #0284c7; }
        .card.dark { border-top-color: #78350f; }
        
        .card-title { font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #b45309; font-weight: 700; margin-bottom: 8px; }
        .card-value { font-size: 24px; font-weight: 800; color: #451a03; }
        .card-subtext { font-size: 12px; color: #92400e; margin-top: 6px; }

        /* Tabela */
        .panel { 
            background: #ffffff; 
            border-radius: 12px; 
            padding: 24px; 
            box-shadow: 0 4px 12px rgba(120, 53, 15, 0.05); 
            animation: cardAppear 0.5s ease-out 0.4s forwards;
            opacity: 0;
            border: 1px solid #fef3c7;
        }
        .panel-header { font-size: 18px; font-weight: 800; color: #78350f; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #fef3c7; font-size: 14px; }
        th { background-color: #fffbeb; color: #78350f; font-weight: 700; }
        
        tbody tr { 
            transition: background-color 0.2s ease, transform 0.2s ease; 
        }
        tbody tr:hover { 
            background-color: #fff7ed; 
        }

        /* Status Badges */
        .status-badge { 
            padding: 5px 12px; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: 700; 
            display: inline-block; 
            text-transform: capitalize; 
            transition: transform 0.2s ease;
        }
        .status-badge:hover {
            transform: scale(1.05);
        }
        .status-pago, .status-entregue { background-color: #dcfce7; color: #15803d; }
        .status-aguardando_pagamento { background-color: #fef3c7; color: #b45309; }
        .status-cancelado { background-color: #fee2e2; color: #b91c1c; }
        .status-em_separacao, .status-enviado { background-color: #ffedd5; color: #c2410c; }

        /* Keyframes */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <!-- Imagem da Logo na Sidebar -->
            <img src="../../logo.png" alt="Cantim do Lanche" onerror="this.style.display='none'">
            <span>Cantim do Lanche</span>
        </div>
        <nav>
            <a href="index.php" class="active">Dashboard</a>
            <a href="pedidos.php">Pedidos (<?= $pedidosData['total_pedidos'] ?>)</a>
            <a href="pagamentos.php">Pagamentos</a>
            <a href="produtos.php">Salgados & Lanches (<?= $totalProdutos ?>)</a>
            <a href="categorias.php">Categorias (<?= $totalCategorias ?>)</a>
            <a href="clientes.php">Clientes (<?= $totalClientes ?>)</a>
            <a href="carrinhos.php">Carrinhos</a>
            <a href="usuarios.php">Administradores (<?= $totalAdmins ?>)</a>
            <a href="logout.php" class="logout">Sair do Sistema</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h1>Visão Geral do Cantim</h1>
            <div class="admin-badge">
                Atendente: <?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador') ?>
            </div>
        </header>

        <section class="kpi-grid">
            <div class="card">
                <div class="card-title">Pedidos Totais</div>
                <div class="card-value"><?= $pedidosData['total_pedidos'] ?></div>
                <div class="card-subtext">R$ <?= number_format($pedidosData['faturamento_total'], 2, ',', '.') ?> em vendas</div>
            </div>

            <div class="card success">
                <div class="card-title">Receita Aprovada</div>
                <div class="card-value">R$ <?= number_format($pagamentosData['receita_aprovada'], 2, ',', '.') ?></div>
                <div class="card-subtext"><?= $pagamentosData['total_aprovados'] ?> pagamentos PIX/Cartão</div>
            </div>

            <div class="card purple">
                <div class="card-title">Salgados Cadastrados</div>
                <div class="card-value"><?= $totalProdutos ?></div>
                <div class="card-subtext">Cardápio ativo</div>
            </div>

            <div class="card info">
                <div class="card-title">Categorias</div>
                <div class="card-value"><?= $totalCategorias ?></div>
                <div class="card-subtext">Fritos, Folhados, Assados...</div>
            </div>

            <div class="card dark">
                <div class="card-title">Clientes Fregueses</div>
                <div class="card-value"><?= $totalClientes ?></div>
                <div class="card-subtext">Cadastrados no site</div>
            </div>

            <div class="card warning">
                <div class="card-title">Carrinhos em Aberto</div>
                <div class="card-value"><?= $carrinhosData['abertos'] ?></div>
                <div class="card-subtext"><?= $carrinhosData['abandonados'] ?> não finalizados</div>
            </div>

            <div class="card danger">
                <div class="card-title">Administradores</div>
                <div class="card-value"><?= $totalAdmins ?></div>
                <div class="card-subtext">Acesso ao sistema</div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">Últimos Pedidos do Cantim</div>
            <?php if (count($ultimosPedidos) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimosPedidos as $pedido): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($pedido['codigo']) ?></strong></td>
                                <td><?= htmlspecialchars($pedido['cliente_nome'] ?? 'Cliente da Loja') ?></td>
                                <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($pedido['status']) ?>">
                                        <?= str_replace('_', ' ', htmlspecialchars($pedido['status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #92400e;">Nenhum pedido cadastrado no momento.</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>