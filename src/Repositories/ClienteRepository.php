<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;
use RuntimeException;

final class ClienteRepository
{
    private PDO $pdo;


    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }


    /*
    =================================
    VERIFICA E-MAIL
    =================================
    */

    public function emailExiste(
        string $email
    ): bool {

        $sql = "
            SELECT id
            FROM clientes
            WHERE email = :email
            LIMIT 1
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':email' =>
                $email,
        ]);

        return
            $stmt->fetch(
                PDO::FETCH_ASSOC
            ) !== false;
    }


    /*
    =================================
    BUSCA POR E-MAIL
    =================================
    */

    public function buscarPorEmail(
        string $email
    ): ?array {

        $sql = "
            SELECT
                id,
                google_sub,
                nome,
                email,
                senha_hash,
                foto_url,
                email_verificado,
                ultimo_acesso,
                criado_em,
                atualizado_em
            FROM clientes
            WHERE email = :email
            LIMIT 1
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':email' =>
                $email,
        ]);

        $cliente =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        if (
            $cliente === false
        ) {
            return null;
        }

        return $cliente;
    }


    /*
    =================================
    BUSCA POR GOOGLE SUB
    =================================
    */

    public function buscarPorGoogleSub(
        string $googleSub
    ): ?array {

        $sql = "
            SELECT
                id,
                google_sub,
                nome,
                email,
                senha_hash,
                foto_url,
                email_verificado,
                ultimo_acesso,
                criado_em,
                atualizado_em
            FROM clientes
            WHERE google_sub = :google_sub
            LIMIT 1
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':google_sub' =>
                $googleSub,
        ]);

        $cliente =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        if (
            $cliente === false
        ) {
            return null;
        }

        return $cliente;
    }


    /*
    =================================
    BUSCA POR ID
    =================================
    */

    public function buscarPorId(
        int $id
    ): ?array {

        $sql = "
            SELECT
                id,
                google_sub,
                nome,
                email,
                senha_hash,
                foto_url,
                email_verificado,
                ultimo_acesso,
                criado_em,
                atualizado_em
            FROM clientes
            WHERE id = :id
            LIMIT 1
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':id' =>
                $id,
        ]);

        $cliente =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        if (
            $cliente === false
        ) {
            return null;
        }

        return $cliente;
    }


    /*
    =================================
    CRIA CLIENTE TRADICIONAL
    =================================
    */

    public function criar(
        string $nome,
        string $email,
        string $senhaHash
    ): int {

        $sql = "
            INSERT INTO clientes (
                google_sub,
                nome,
                email,
                senha_hash,
                foto_url,
                email_verificado,
                ultimo_acesso
            )
            VALUES (
                NULL,
                :nome,
                :email,
                :senha_hash,
                NULL,
                0,
                NULL
            )
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':nome' =>
                $nome,

            ':email' =>
                $email,

            ':senha_hash' =>
                $senhaHash,
        ]);

        return
            (int)
            $this->pdo->lastInsertId();
    }


    /*
    =================================
    CRIA CLIENTE GOOGLE
    =================================
    */

    public function criarComGoogle(
        string $googleSub,
        string $nome,
        string $email,
        ?string $fotoUrl,
        bool $emailVerificado
    ): int {

        $sql = "
            INSERT INTO clientes (
                google_sub,
                nome,
                email,
                senha_hash,
                foto_url,
                email_verificado,
                ultimo_acesso
            )
            VALUES (
                :google_sub,
                :nome,
                :email,
                NULL,
                :foto_url,
                :email_verificado,
                NOW()
            )
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        try {

            $stmt->execute([
                ':google_sub' =>
                    $googleSub,

                ':nome' =>
                    $nome,

                ':email' =>
                    $email,

                ':foto_url' =>
                    $fotoUrl,

                ':email_verificado' =>
                    $emailVerificado
                        ? 1
                        : 0,
            ]);

        } catch (
            PDOException $erro
        ) {

            throw new RuntimeException(
                'Não foi possível criar a conta Google.',
                0,
                $erro
            );
        }

        return
            (int)
            $this->pdo->lastInsertId();
    }


    /*
    =================================
    VINCULA GOOGLE À CONTA EXISTENTE
    =================================
    */

    public function vincularGoogle(
        int $clienteId,
        string $googleSub,
        ?string $fotoUrl,
        bool $emailVerificado
    ): void {

        $sql = "
            UPDATE clientes
            SET
                google_sub = :google_sub,
                foto_url = :foto_url,
                email_verificado = :email_verificado,
                ultimo_acesso = NOW()
            WHERE id = :id
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        try {

            $stmt->execute([
                ':google_sub' =>
                    $googleSub,

                ':foto_url' =>
                    $fotoUrl,

                ':email_verificado' =>
                    $emailVerificado
                        ? 1
                        : 0,

                ':id' =>
                    $clienteId,
            ]);

        } catch (
            PDOException $erro
        ) {

            throw new RuntimeException(
                'Não foi possível vincular a conta Google.',
                0,
                $erro
            );
        }
    }


    /*
    =================================
    ATUALIZA ÚLTIMO ACESSO
    =================================
    */

    public function atualizarUltimoAcesso(
        int $clienteId
    ): void {

        $sql = "
            UPDATE clientes
            SET ultimo_acesso = NOW()
            WHERE id = :id
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':id' =>
                $clienteId,
        ]);
    }
}