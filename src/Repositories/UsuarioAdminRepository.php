<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class UsuarioAdminRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarAtivoPorEmail(string $email): ?array
    {
        $sql = '
            SELECT
                id,
                nome,
                email,
                senha_hash,
                status,
                ultimo_acesso
            FROM usuarios_admin
            WHERE email = :email
              AND status = :status
            LIMIT 1
        ';

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            'email' => strtolower(trim($email)),
            'status' => 'ativo',
        ]);

        $usuario = $consulta->fetch();

        return is_array($usuario)
            ? $usuario
            : null;
    }

    public function registrarUltimoAcesso(int $usuarioId): void
    {
        $sql = '
            UPDATE usuarios_admin
            SET ultimo_acesso = NOW()
            WHERE id = :id
        ';

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            'id' => $usuarioId,
        ]);
    }

    public function atualizarHashSenha(
        int $usuarioId,
        string $novoHash
    ): void {
        $sql = '
            UPDATE usuarios_admin
            SET senha_hash = :senha_hash
            WHERE id = :id
        ';

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            'senha_hash' => $novoHash,
            'id' => $usuarioId,
        ]);
    }
}

