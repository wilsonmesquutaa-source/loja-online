<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class EnderecoRepository
{
    private PDO $pdo;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }


    /*
    =================================
    ENDEREÇOS DO CLIENTE
    =================================
    */

    public function buscarPorCliente(
        int $clienteId
    ): array {
        $sql = "
            SELECT
                id,
                cliente_id,
                identificacao,
                destinatario,
                cep,
                logradouro,
                numero,
                complemento,
                bairro,
                cidade,
                estado,
                principal,
                criado_em,
                atualizado_em
            FROM enderecos
            WHERE cliente_id = :cliente_id
            ORDER BY
                principal DESC,
                criado_em DESC,
                id DESC
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':cliente_id' =>
                $clienteId,
        ]);

        return
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );
    }


    /*
    =================================
    ENDEREÇO POR ID DO CLIENTE
    =================================
    */

    public function buscarPorIdDoCliente(
        int $enderecoId,
        int $clienteId
    ): ?array {
        $sql = "
            SELECT
                id,
                cliente_id,
                identificacao,
                destinatario,
                cep,
                logradouro,
                numero,
                complemento,
                bairro,
                cidade,
                estado,
                principal,
                criado_em,
                atualizado_em
            FROM enderecos
            WHERE id = :id
            AND cliente_id = :cliente_id
            LIMIT 1
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':id' =>
                $enderecoId,

            ':cliente_id' =>
                $clienteId,
        ]);

        $endereco =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return
            $endereco !== false
                ? $endereco
                : null;
    }


    /*
    =================================
    CRIA ENDEREÇO
    =================================
    */

    public function criar(
        int $clienteId,
        string $identificacao,
        string $destinatario,
        string $cep,
        string $logradouro,
        string $numero,
        ?string $complemento,
        string $bairro,
        string $cidade,
        string $estado,
        bool $principal
    ): int {

        $stmt = $this->pdo->prepare("
            INSERT INTO enderecos (
                cliente_id,
                identificacao,
                destinatario,
                cep,
                logradouro,
                numero,
                complemento,
                bairro,
                cidade,
                estado,
                principal
            )
            VALUES (
                :cliente_id,
                :identificacao,
                :destinatario,
                :cep,
                :logradouro,
                :numero,
                :complemento,
                :bairro,
                :cidade,
                :estado,
                :principal
            )
        ");

        $stmt->execute([
            ':cliente_id' =>
                $clienteId,

            ':identificacao' =>
                $identificacao,

            ':destinatario' =>
                $destinatario,

            ':cep' =>
                $cep,

            ':logradouro' =>
                $logradouro,

            ':numero' =>
                $numero,

            ':complemento' =>
                $complemento,

            ':bairro' =>
                $bairro,

            ':cidade' =>
                $cidade,

            ':estado' =>
                strtoupper($estado),

            ':principal' =>
                $principal
                    ? 1
                    : 0,
        ]);

        return
            (int)
            $this->pdo->lastInsertId();
    }


    /*
    =================================
    ATUALIZA ENDEREÇO
    =================================
    */

    public function atualizar(
        int $enderecoId,
        int $clienteId,
        string $identificacao,
        string $destinatario,
        string $cep,
        string $logradouro,
        string $numero,
        ?string $complemento,
        string $bairro,
        string $cidade,
        string $estado
    ): void {

        $stmt = $this->pdo->prepare("
            UPDATE enderecos
            SET
                identificacao = :identificacao,
                destinatario = :destinatario,
                cep = :cep,
                logradouro = :logradouro,
                numero = :numero,
                complemento = :complemento,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado
            WHERE id = :id
            AND cliente_id = :cliente_id
        ");

        $stmt->execute([
            ':identificacao' =>
                $identificacao,

            ':destinatario' =>
                $destinatario,

            ':cep' =>
                $cep,

            ':logradouro' =>
                $logradouro,

            ':numero' =>
                $numero,

            ':complemento' =>
                $complemento,

            ':bairro' =>
                $bairro,

            ':cidade' =>
                $cidade,

            ':estado' =>
                strtoupper($estado),

            ':id' =>
                $enderecoId,

            ':cliente_id' =>
                $clienteId,
        ]);
    }


    /*
    =================================
    DEFINE ENDEREÇO PRINCIPAL
    =================================
    */

    public function definirPrincipal(
        int $enderecoId,
        int $clienteId
    ): void {

        $this->pdo->beginTransaction();

        try {

            $stmt = $this->pdo->prepare("
                UPDATE enderecos
                SET principal = 0
                WHERE cliente_id = :cliente_id
            ");

            $stmt->execute([
                ':cliente_id' =>
                    $clienteId,
            ]);


            $stmt = $this->pdo->prepare("
                UPDATE enderecos
                SET principal = 1
                WHERE id = :id
                AND cliente_id = :cliente_id
            ");

            $stmt->execute([
                ':id' =>
                    $enderecoId,

                ':cliente_id' =>
                    $clienteId,
            ]);


            $this->pdo->commit();

        } catch (\Throwable $erro) {

            $this->pdo->rollBack();

            throw $erro;
        }
    }


    /*
    =================================
    EXCLUI ENDEREÇO
    =================================
    */

    public function excluir(
        int $enderecoId,
        int $clienteId
    ): void {

        $stmt = $this->pdo->prepare("
            DELETE FROM enderecos
            WHERE id = :id
            AND cliente_id = :cliente_id
        ");

        $stmt->execute([
            ':id' =>
                $enderecoId,

            ':cliente_id' =>
                $clienteId,
        ]);
    }
}