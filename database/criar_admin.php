<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);

    exit(
        'Este arquivo deve ser executado somente pelo terminal.'
    );
}

$pdo = require __DIR__ . '/conexao.php';

$nome = trim(
    (string) readline(
        'Nome do administrador: '
    )
);

$email = strtolower(
    trim(
        (string) readline(
            'E-mail do administrador: '
        )
    )
);

$senha = (string) readline(
    'Senha com pelo menos 8 caracteres: '
);

if ($nome === '') {
    exit("O nome é obrigatório.\n");
}

if (
    filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    ) === false
) {
    exit("Informe um e-mail válido.\n");
}

if (strlen($senha) < 8) {
    exit(
        "A senha deve possuir pelo menos 8 caracteres.\n"
    );
}

$senhaHash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);

$sql = '
    INSERT INTO usuarios_admin (
        nome,
        email,
        senha_hash,
        status
    ) VALUES (
        :nome,
        :email,
        :senha_hash,
        :status
    )
';

$consulta = $pdo->prepare($sql);

try {
    $consulta->execute([
        'nome' => $nome,
        'email' => $email,
        'senha_hash' => $senhaHash,
        'status' => 'ativo',
    ]);

    echo "Administrador criado com sucesso.\n";
} catch (PDOException $erro) {
    error_log(
        '[CRIAR ADMIN] '
        . $erro->getMessage()
    );

    echo "Não foi possível criar o administrador.\n";
}
