<?php
require_once __DIR__ . '/conexao/conexao.php';

$nome = "Administrador";
$email = "admin@loja.com";
$senha = "admin123";

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios_admin (nome, email, senha_hash, status)
        VALUES (:nome, :email, :senha_hash, 'ativo')";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'nome' => $nome,
    'email' => $email,
    'senha_hash' => $senha_hash
]);

echo "Administrador criado com sucesso!<br>";
echo "E-mail: " . $email . "<br>";
echo "Senha: " . $senha;