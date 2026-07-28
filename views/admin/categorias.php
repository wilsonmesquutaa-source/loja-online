<?php
// views/admin/categorias.php

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
    die("<strong>Erro:</strong> Arquivo de conexão não encontrado.");
}
require_once $caminhoConexao;

$mensagem = '';
$tipoMensagem = '';

// --- PROCESSAMENTO DO FORMULÁRIO (CRIAR E EXCLUIR) ---

// 1. Excluir Categoria
if (isset($_GET['excluir'])) {
    $idExcluir = (int)$_GET['excluir'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$idExcluir]);
        $mensagem = "Categoria excluída com sucesso!";
        $tipoMensagem = "success";
    } catch (PDOException $e) {
        // Se houver produtos vinculados, o banco pode bloquear a exclusão (dependendo da chave estrangeira)
        $mensagem = "Erro ao excluir: Verifique se existem salgados vinculados a esta categoria.";
        $tipoMensagem = "danger";
    }
}

// 2. Adicionar Nova Categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (nome, ativo) VALUES (?, ?)");
            $stmt->execute([$nome, $ativo]);
            $mensagem = "Categoria '{$nome}' adicionada ao cardápio!";
            $tipoMensagem = "success";
        } catch (PDOException $e) {
            $mensagem = "Erro ao salvar categoria no banco: " . $e->getMessage();
            $tipoMensagem = "danger";
        }
    } else {
        $mensagem = "O nome da categoria não pode ficar vazio.";
        $tipoMensagem = "warning";
    }
}

// --- BUSCAR DADOS PARA A TELA ---
try {
    // Busca todas as categorias
    $stmtCategorias = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
    $categorias = $stmtCategorias->fetchAll();
    
    // Contadores para manter a Sidebar atualizada
    $totPedidos = $pdo->query("SELECT COUNT(id) FROM pedidos WHERE status != 'cancelado'")->fetchColumn();
    $totProdutos = $pdo->query("SELECT COUNT(id) FROM produtos WHERE status = 'ativo'")->fetchColumn();
    $totCategorias = count($categorias);
    $totClientes = $pdo->query("SELECT COUNT(id) FROM clientes")->fetchColumn();
    $totAdmins = $pdo->query("SELECT COUNT(id) FROM usuarios_admin WHERE status = 'ativo'")->fetchColumn();

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantim do Lanche - Categorias</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #fefce8; color: #451a03; }
        
        /* Sidebar (Mesma do Dashboard) */
        .sidebar { width: 270px; background-color: #291e12; color: #fef3c7; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 20px 15px; text-align: center; border-bottom: 1px solid #451a03; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .sidebar-brand img { width: 90px; height: 90px; object-fit: cover; border-radius: 50%; border: 3px solid #f59e0b; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.3s; }
        .sidebar-brand img:hover { transform: scale(1.08) rotate(3deg); }
        .sidebar-brand span { font-size: 18px; font-weight: 700; color: #fbbf24; }
        
        .sidebar nav { display: flex; flex-direction: column; padding: 15px 0; flex-grow: 1; }
        .sidebar nav a { color: #d97706; text-decoration: none; padding: 14px 24px; font-size: 15px; display: flex; align-items: center; transition: all 0.3s; border-left: 4px solid transparent; font-weight: 500; }
        .sidebar nav a:hover { background-color: #451a03; color: #fef3c7; border-left-color: #f59e0b; padding-left: 30px; }
        .sidebar nav a.active { background-color: #451a03; color: #ffffff; border-left-color: #f59e0b; font-weight: 700; }
        .sidebar nav a.logout { margin-top: auto; background-color: #dc2626; color: #fff; text-align: center; justify-content: center; }
        .sidebar nav a.logout:hover { background-color: #b91c1c; transform: scale(0.98); }

        /* Estrutura Principal */
        .main-content { flex-grow: 1; padding: 30px; overflow-y: auto; animation: fadeIn 0.4s ease-out; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .top-bar h1 { font-size: 26px; color: #78350f; font-weight: 800; }

        /* Notificações */
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; animation: cardAppear 0.3s ease-out; }
        .alert.success { background-color: #dcfce7; color: #15803d; border-left: 5px solid #16a34a; }
        .alert.danger { background-color: #fee2e2; color: #b91c1c; border-left: 5px solid #dc2626; }
        .alert.warning { background-color: #fef3c7; color: #b45309; border-left: 5px solid #f59e0b; }

        /* Layout Grid para Formulário e Tabela */
        .content-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start; }
        
        @media (max-width: 900px) {
            .content-grid { grid-template-columns: 1fr; }
        }

        /* Painéis Brancos */
        .panel { background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(120, 53, 15, 0.05); border: 1px solid #fef3c7; }
        .panel-header { font-size: 18px; font-weight: 800; color: #78350f; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #fef3c7; }

        /* Estilização do Formulário */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 700; color: #92400e; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #fde68a; border-radius: 8px; font-size: 15px; color: #451a03; background-color: #fffbeb; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }
        
        .checkbox-group { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .checkbox-group input { width: 18px; height: 18px; accent-color: #d97706; }

        .btn { display: inline-block; padding: 12px 20px; font-size: 15px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; text-align: center; text-decoration: none; }
        .btn-primary { background-color: #d97706; color: #fff; width: 100%; }
        .btn-primary:hover { background-color: #b45309; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(180, 83, 9, 0.2); }
        
        .btn-danger-sm { background-color: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; }
        .btn-danger-sm:hover { background-color: #dc2626; color: #fff; }

        /* Tabela */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #fef3c7; font-size: 14px; }
        th { background-color: #fffbeb; color: #78350f; font-weight: 700; }
        tbody tr:hover { background-color: #fff7ed; }

        .badge-ativo { background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
        .badge-inativo { background-color: #f3f4f6; color: #6b7280; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes cardAppear { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="../../logo.png" alt="Cantim do Lanche" onerror="this.style.display='none'">
            <span>Cantim do Lanche</span>
        </div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="pedidos.php">Pedidos (<?= $totPedidos ?>)</a>
            <a href="pagamentos.php">Pagamentos</a>
            <a href="produtos.php">Salgados & Lanches (<?= $totProdutos ?>)</a>
            <a href="categorias.php" class="active">Categorias (<?= $totCategorias ?>)</a>
            <a href="clientes.php">Clientes (<?= $totClientes ?>)</a>
            <a href="carrinhos.php">Carrinhos</a>
            <a href="usuarios.php">Administradores (<?= $totAdmins ?>)</a>
            <a href="logout.php" class="logout">Sair do Sistema</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h1>Gerenciar Categorias</h1>
        </header>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?= $tipoMensagem ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <div class="content-grid">
            
            <!-- Painel de Cadastro -->
            <section class="panel">
                <div class="panel-header">Nova Categoria</div>
                <form action="categorias.php" method="POST">
                    <input type="hidden" name="acao" value="adicionar">
                    
                    <div class="form-group">
                        <label for="nome">Nome da Categoria (Ex: Salgados Fritos)</label>
                        <input type="text" id="nome" name="nome" class="form-control" placeholder="Digite o nome..." required>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="ativo" checked>
                            Visível no cardápio do site
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                </form>
            </section>

            <!-- Painel de Listagem -->
            <section class="panel">
                <div class="panel-header">Categorias Cadastradas</div>
                <?php if (count($categorias) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td>#<?= $cat['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($cat['nome']) ?></strong></td>
                                    <td>
                                        <?php if ($cat['ativo'] == 1): ?>
                                            <span class="badge-ativo">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge-inativo">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="categorias.php?excluir=<?= $cat['id'] ?>" 
                                           class="btn-danger-sm" 
                                           onclick="return confirm('Tem certeza que deseja excluir a categoria <?= htmlspecialchars($cat['nome']) ?>?');">
                                           Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #92400e;">Nenhuma categoria cadastrada ainda. Use o formulário ao lado para começar.</p>
                <?php endif; ?>
            </section>

        </div>
    </main>

</body>
</html>