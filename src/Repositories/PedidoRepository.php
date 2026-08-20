<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PedidoRepository
{
    private PDO $pdo;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }


    /*
    =================================
    PEDIDOS DO CLIENTE
    =================================
    */

    public function buscarPorCliente(
        int $clienteId
    ): array {
        $sql = "
            SELECT
                id,
                codigo,
                status,
                subtotal,
                frete,
                desconto,
                total,
                observacao,
                criado_em,
                atualizado_em
            FROM pedidos
            WHERE cliente_id = :cliente_id
            ORDER BY criado_em DESC, id DESC
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
    PEDIDO POR ID DO CLIENTE
    =================================
    */

    public function buscarPorIdDoCliente(
        int $pedidoId,
        int $clienteId
    ): ?array {
        $sql = "
            SELECT
                id,
                codigo,
                cliente_id,
                status,
                subtotal,
                frete,
                desconto,
                total,
                observacao,
                criado_em,
                atualizado_em
            FROM pedidos
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
                $pedidoId,

            ':cliente_id' =>
                $clienteId,
        ]);

        $pedido =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return
            $pedido !== false
                ? $pedido
                : null;
    }


    /*
    =================================
    ITENS DO PEDIDO
    =================================
    */

    public function buscarItens(
        int $pedidoId
    ): array {
        $sql = "
            SELECT
                id,
                produto_id,
                nome_produto,
                quantidade,
                preco_unitario,
                subtotal
            FROM pedido_itens
            WHERE pedido_id = :pedido_id
            ORDER BY id ASC
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':pedido_id' =>
                $pedidoId,
        ]);

        return
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );
    }


    /*
    =================================
    ENDEREÇO DO PEDIDO
    =================================
    */

    public function buscarEndereco(
        int $pedidoId
    ): ?array {
        $sql = "
            SELECT
                id,
                destinatario,
                cep,
                logradouro,
                numero,
                complemento,
                bairro,
                cidade,
                estado
            FROM pedido_enderecos
            WHERE pedido_id = :pedido_id
            LIMIT 1
        ";

        $stmt =
            $this->pdo->prepare(
                $sql
            );

        $stmt->execute([
            ':pedido_id' =>
                $pedidoId,
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
}