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
                cliente_id,
                modalidade_recebimento,
                data_hora_agendada,
                inicio_preparo,
                fim_preparo_previsto,
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

            ORDER BY
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
                modalidade_recebimento,
                data_hora_agendada,
                inicio_preparo,
                fim_preparo_previsto,
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
    CRIA PEDIDO
    =================================
    */

    public function criarPedido(
        string $codigo,
        int $clienteId,
        string $modalidadeRecebimento,
        string $dataHoraAgendada,
        string $inicioPreparo,
        string $fimPreparoPrevisto,
        float $subtotal,
        float $frete,
        float $desconto,
        float $total,
        ?string $observacao
    ): int {

        $sql = "
            INSERT INTO pedidos (

                codigo,
                cliente_id,
                modalidade_recebimento,
                data_hora_agendada,
                inicio_preparo,
                fim_preparo_previsto,
                status,
                subtotal,
                frete,
                desconto,
                total,
                observacao

            )

            VALUES (

                :codigo,
                :cliente_id,
                :modalidade_recebimento,
                :data_hora_agendada,
                :inicio_preparo,
                :fim_preparo_previsto,
                :status,
                :subtotal,
                :frete,
                :desconto,
                :total,
                :observacao

            )
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->bindValue(
            ':codigo',
            $codigo,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':cliente_id',
            $clienteId,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':modalidade_recebimento',
            $modalidadeRecebimento,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':data_hora_agendada',
            $dataHoraAgendada,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':inicio_preparo',
            $inicioPreparo,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':fim_preparo_previsto',
            $fimPreparoPrevisto,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':status',
            'aguardando_pagamento',
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':subtotal',
            number_format(
                $subtotal,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':frete',
            number_format(
                $frete,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':desconto',
            number_format(
                $desconto,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':total',
            number_format(
                $total,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        if (
            $observacao === null
        ) {

            $stmt->bindValue(
                ':observacao',
                null,
                PDO::PARAM_NULL
            );

        } else {

            $stmt->bindValue(
                ':observacao',
                $observacao,
                PDO::PARAM_STR
            );
        }


        $stmt->execute();


        return
            (int)
            $this->pdo->lastInsertId();
    }


    /*
    =================================
    ITENS DO PEDIDO
    =================================
    */

    public function adicionarItem(
        int $pedidoId,
        int $produtoId,
        string $nomeProduto,
        int $quantidade,
        float $precoUnitario,
        float $subtotal
    ): void {

        $sql = "
            INSERT INTO pedido_itens (
                pedido_id,
                produto_id,
                nome_produto,
                quantidade,
                preco_unitario,
                subtotal
            )

            VALUES (
                :pedido_id,
                :produto_id,
                :nome_produto,
                :quantidade,
                :preco_unitario,
                :subtotal
            )
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->bindValue(
            ':pedido_id',
            $pedidoId,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':produto_id',
            $produtoId,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':nome_produto',
            $nomeProduto,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':quantidade',
            $quantidade,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':preco_unitario',
            number_format(
                $precoUnitario,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':subtotal',
            number_format(
                $subtotal,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        $stmt->execute();
    }


    /*
    =================================
    ENDEREÇO DO PEDIDO
    =================================
    */

    public function adicionarEndereco(
        int $pedidoId,
        array $endereco
    ): void {

        $sql = "
            INSERT INTO pedido_enderecos (
                pedido_id,
                destinatario,
                cep,
                logradouro,
                numero,
                complemento,
                bairro,
                cidade,
                estado
            )

            VALUES (
                :pedido_id,
                :destinatario,
                :cep,
                :logradouro,
                :numero,
                :complemento,
                :bairro,
                :cidade,
                :estado
            )
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->bindValue(
            ':pedido_id',
            $pedidoId,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':destinatario',
            (string)
            $endereco['destinatario'],
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':cep',
            (string)
            $endereco['cep'],
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':logradouro',
            (string)
            $endereco['logradouro'],
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':numero',
            (string)
            $endereco['numero'],
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':complemento',
            (
                $endereco['complemento']
                ?? ''
            ),
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':bairro',
            (string)
            $endereco['bairro'],
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':cidade',
            (string)
            $endereco['cidade'],
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':estado',
            (string)
            $endereco['estado'],
            PDO::PARAM_STR
        );


        $stmt->execute();
    }


    /*
    =================================
    PAGAMENTO
    =================================
    */

    public function criarPagamento(
        int $pedidoId,
        string $metodo,
        float $valor
    ): int {

        $sql = "
            INSERT INTO pagamentos (
                pedido_id,
                provedor,
                metodo,
                status,
                valor
            )

            VALUES (
                :pedido_id,
                :provedor,
                :metodo,
                :status,
                :valor
            )
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->bindValue(
            ':pedido_id',
            $pedidoId,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':provedor',
            'mercadopago',
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':metodo',
            $metodo,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':status',
            'pendente',
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':valor',
            number_format(
                $valor,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );


        $stmt->execute();


        return
            (int)
            $this->pdo->lastInsertId();
    }


    /*
    =================================
    ATUALIZA DADOS DO PAGAMENTO
    =================================
    */

    public function atualizarDadosPagamento(
        int $pagamentoId,
        ?string $pagamentoExternoId,
        ?string $pixCopiaCola,
        ?string $expiraEm,
        string $status = 'pendente'
    ): bool {

        $sql = "
            UPDATE pagamentos

            SET
                pagamento_externo_id =
                    :pagamento_externo_id,

                pix_copia_cola =
                    :pix_copia_cola,

                expira_em =
                    :expira_em,

                status =
                    :status

            WHERE id = :id
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        if (
            $pagamentoExternoId === null
        ) {

            $stmt->bindValue(
                ':pagamento_externo_id',
                null,
                PDO::PARAM_NULL
            );

        } else {

            $stmt->bindValue(
                ':pagamento_externo_id',
                $pagamentoExternoId,
                PDO::PARAM_STR
            );
        }


        if (
            $pixCopiaCola === null
        ) {

            $stmt->bindValue(
                ':pix_copia_cola',
                null,
                PDO::PARAM_NULL
            );

        } else {

            $stmt->bindValue(
                ':pix_copia_cola',
                $pixCopiaCola,
                PDO::PARAM_STR
            );
        }


        if (
            $expiraEm === null
        ) {

            $stmt->bindValue(
                ':expira_em',
                null,
                PDO::PARAM_NULL
            );

        } else {

            $stmt->bindValue(
                ':expira_em',
                $expiraEm,
                PDO::PARAM_STR
            );
        }


        $stmt->bindValue(
            ':status',
            $status,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':id',
            $pagamentoId,
            PDO::PARAM_INT
        );


        return
            $stmt->execute();
    }


    /*
    =================================
    ATUALIZA STATUS DO PAGAMENTO
    =================================
    */

    public function atualizarStatusPagamento(
        int $pagamentoId,
        string $status,
        ?string $aprovadoEm = null
    ): bool {

        $sql = "
            UPDATE pagamentos

            SET
                status = :status,

                aprovado_em = :aprovado_em

            WHERE id = :id
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->bindValue(
            ':status',
            $status,
            PDO::PARAM_STR
        );


        if (
            $aprovadoEm === null
        ) {

            $stmt->bindValue(
                ':aprovado_em',
                null,
                PDO::PARAM_NULL
            );

        } else {

            $stmt->bindValue(
                ':aprovado_em',
                $aprovadoEm,
                PDO::PARAM_STR
            );
        }


        $stmt->bindValue(
            ':id',
            $pagamentoId,
            PDO::PARAM_INT
        );


        return
            $stmt->execute();
    }


    /*
    =================================
    BUSCA PAGAMENTO POR ID
    =================================
    */

    public function buscarPagamentoPorId(
        int $pagamentoId
    ): ?array {

        $sql = "
            SELECT
                id,
                pedido_id,
                provedor,
                pagamento_externo_id,
                metodo,
                status,
                valor,
                pix_copia_cola,
                expira_em,
                aprovado_em,
                criado_em,
                atualizado_em

            FROM pagamentos

            WHERE id = :id

            LIMIT 1
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->execute([
            ':id' =>
                $pagamentoId,
        ]);


        $pagamento =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return
            $pagamento !== false
                ? $pagamento
                : null;
    }


    /*
    =================================
    BUSCA PAGAMENTO PELO ID EXTERNO
    =================================
    */

    public function buscarPagamentoPorExterno(
        string $pagamentoExternoId
    ): ?array {

        $sql = "
            SELECT
                id,
                pedido_id,
                provedor,
                pagamento_externo_id,
                metodo,
                status,
                valor,
                pix_copia_cola,
                expira_em,
                aprovado_em,
                criado_em,
                atualizado_em

            FROM pagamentos

            WHERE pagamento_externo_id =
                :pagamento_externo_id

            LIMIT 1
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->execute([
            ':pagamento_externo_id' =>
                $pagamentoExternoId,
        ]);


        $pagamento =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return
            $pagamento !== false
                ? $pagamento
                : null;
    }


    /*
    =================================
    ATUALIZA STATUS DO PEDIDO
    =================================
    */

    public function atualizarStatus(
        int $pedidoId,
        string $status
    ): bool {

        $sql = "
            UPDATE pedidos

            SET
                status = :status

            WHERE id = :id
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->bindValue(
            ':status',
            $status,
            PDO::PARAM_STR
        );


        $stmt->bindValue(
            ':id',
            $pedidoId,
            PDO::PARAM_INT
        );


        return
            $stmt->execute();
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

            ORDER BY
                id ASC
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


    /*
    =================================
    ADMINISTRATIVO
    LISTAGEM DE PEDIDOS
    =================================
    */

    public function buscarTodosAdministrativo(
        ?string $status = null
    ): array {

        $sql = "
            SELECT
                pedidos.id,
                pedidos.codigo,
                pedidos.cliente_id,
                pedidos.modalidade_recebimento,
                pedidos.data_hora_agendada,
                pedidos.inicio_preparo,
                pedidos.fim_preparo_previsto,
                pedidos.status,
                pedidos.subtotal,
                pedidos.frete,
                pedidos.desconto,
                pedidos.total,
                pedidos.observacao,
                pedidos.criado_em,
                pedidos.atualizado_em,

                clientes.nome AS nome_cliente,
                clientes.email AS email_cliente

            FROM pedidos

            INNER JOIN clientes
                ON clientes.id =
                    pedidos.cliente_id
        ";


        $parametros = [];


        if (
            $status !== null
        ) {

            $sql .= "
                WHERE pedidos.status =
                    :status
            ";


            $parametros[
                ':status'
            ] =
                $status;
        }


        $sql .= "
            ORDER BY
                pedidos.criado_em DESC,
                pedidos.id DESC
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->execute(
            $parametros
        );


        return
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );
    }


    /*
    =================================
    ADMINISTRATIVO
    PEDIDO POR ID
    =================================
    */

    public function buscarPorIdAdministrativo(
        int $pedidoId
    ): ?array {

        $sql = "
            SELECT
                pedidos.id,
                pedidos.codigo,
                pedidos.cliente_id,
                pedidos.modalidade_recebimento,
                pedidos.data_hora_agendada,
                pedidos.inicio_preparo,
                pedidos.fim_preparo_previsto,
                pedidos.status,
                pedidos.subtotal,
                pedidos.frete,
                pedidos.desconto,
                pedidos.total,
                pedidos.observacao,
                pedidos.criado_em,
                pedidos.atualizado_em,

                clientes.nome AS nome_cliente,
                clientes.email AS email_cliente

            FROM pedidos

            INNER JOIN clientes
                ON clientes.id =
                    pedidos.cliente_id

            WHERE pedidos.id = :id

            LIMIT 1
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->execute([
            ':id' =>
                $pedidoId,
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
    ADMINISTRATIVO
    PAGAMENTO DO PEDIDO
    =================================
    */

    public function buscarPagamento(
        int $pedidoId
    ): ?array {

        $sql = "
            SELECT
                id,
                pedido_id,
                provedor,
                pagamento_externo_id,
                metodo,
                status,
                valor,
                pix_copia_cola,
                expira_em,
                aprovado_em,
                criado_em,
                atualizado_em

            FROM pagamentos

            WHERE pedido_id = :pedido_id

            ORDER BY
                id DESC

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


        $pagamento =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return
            $pagamento !== false
                ? $pagamento
                : null;
    }
}