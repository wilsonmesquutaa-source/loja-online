<?php
// loginadm.php (Raiz do projeto)
session_start();

// Redireciona se já estiver logado
if (isset($_SESSION['admin_id'])) {
    header("Location: views/admin/index.php");
    exit;
}

require_once 'conexao/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        try {
            $stmt = $pdo->prepare("SELECT id, nome, senha_hash, status FROM usuarios_admin WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica o hash da senha gerado pelo password_hash() do PHP
            if ($admin && password_verify($senha, $admin['senha_hash'])) {
                if ($admin['status'] === 'ativo') {
                    // Regenera o ID da sessão para prevenir Session Fixation
                    session_regenerate_id(true);
                    
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_nome'] = $admin['nome'];
                    
                    // Atualiza o timestamp do último acesso
                    $update = $pdo->prepare("UPDATE usuarios_admin SET ultimo_acesso = NOW() WHERE id = :id");
                    $update->execute(['id' => $admin['id']]);

                    header("Location: views/admin/index.php");
                    exit;
                } else {
                    $erro = "Usuário inativo. Contate a administração.";
                }
            } else {
                $erro = "Credenciais inválidas.";
            }
        } catch (PDOException $e) {
            $erro = "Erro de conexão. Tente novamente mais tarde.";
            // No ambiente de produção, registre $e->getMessage() em storage/logs
        }
    } else {
        $erro = "Por favor, preencha o e-mail e a senha.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Loja Virtual</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f7f6; }
        .login-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-container h2 { text-align: center; margin-bottom: 24px; color: #333; }
        .form-group { margin-bottom: 16px; display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 6px; font-size: 14px; color: #555; }
        .form-group input { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #007bff; }
        .btn-submit { background-color: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; font-size: 16px; cursor: pointer; width: 100%; margin-top: 10px; transition: background 0.3s; }
        .btn-submit:hover { background-color: #0056b3; }
        .alert { background-color: #ffeaea; color: #dc3545; padding: 10px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <main class="login-container">
        <h2>Acesso Restrito</h2>
        <?php if ($erro): ?>
            <div class="alert"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required>
            </div>
            <button type="submit" class="btn-submit">Entrar no Painel</button>
        </form>
    </main>
</body>
</html>