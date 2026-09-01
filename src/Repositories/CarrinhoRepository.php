<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CarrinhoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarAbertoPorToken(
        string $tokenSessao
    ): ?array {
        $sql = '
            SELECT
                id,
                cliente_id,
                token_sessao,
                status,
                criado_em,
                atualizado_em
            FROM carrinhos
            WHERE token_sessao = :token
            AND status = :status
            LIMIT 1
        ';

        $consulta = $this->pdo->prepare($sql);

        $consulta->bindValue(
            ':token',
            $tokenSessao,
            PDO::PARAM_STR
        );

        $consulta->bindValue(
            ':status',
            'aberto',
            PDO::PARAM_STR
        );

        $consulta->execute();

        $carrinho = $consulta->fetch();

        return $carrinho !== false
            ? $carrinho
            : null;
    }


    /*
    =================================
    BUSCA CARRINHO PELO TOKEN
    =================================
    */

    private function buscarPorToken(
        string $tokenSessao
    ): ?array {
        $sql = '
            SELECT
                id,
                cliente_id,
                token_sessao,
                status,
                criado_em,
                atualizado_em
            FROM carrinhos
            WHERE token_sessao = :token
            LIMIT 1
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':token',
            $tokenSessao,
            PDO::PARAM_STR
        );

        $consulta->execute();

        $carrinho =
            $consulta->fetch();

        return $carrinho !== false
            ? $carrinho
            : null;
    }


    public function criar(
        string $tokenSessao,
        ?int $clienteId = null
    ): int {
        $sql = '
            INSERT INTO carrinhos (
                cliente_id,
                token_sessao,
                status
            )
            VALUES (
                :cliente_id,
                :token,
                :status
            )
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        if (
            $clienteId === null
        ) {

            $consulta->bindValue(
                ':cliente_id',
                null,
                PDO::PARAM_NULL
            );

        } else {

            $consulta->bindValue(
                ':cliente_id',
                $clienteId,
                PDO::PARAM_INT
            );
        }

        $consulta->bindValue(
            ':token',
            $tokenSessao,
            PDO::PARAM_STR
        );

        $consulta->bindValue(
            ':status',
            'aberto',
            PDO::PARAM_STR
        );

        $consulta->execute();

        return
            (int)
            $this->pdo->lastInsertId();
    }


    public function obterOuCriar(
        string $tokenSessao,
        ?int $clienteId = null
    ): array {
        /*
        =================================
        TENTA ENCONTRAR CARRINHO ABERTO
        =================================
        */

        $carrinho =
            $this->buscarAbertoPorToken(
                $tokenSessao
            );

        if (
            $carrinho !== null
        ) {

            if (
                $clienteId !== null
                &&
                (
                    $carrinho[
                        'cliente_id'
                    ] === null
                    ||
                    (int)
                    $carrinho[
                        'cliente_id'
                    ] !== $clienteId
                )
            ) {

                $this->associarCliente(
                    (int)
                    $carrinho['id'],
                    $clienteId
                );

                $carrinho[
                    'cliente_id'
                ] =
                    $clienteId;
            }

            return $carrinho;
        }


        /*
        =================================
        PROCURA CARRINHO EXISTENTE
        =================================

        O token_sessao é UNIQUE.

        Portanto, se já existir um carrinho
        convertido para esse token, não podemos
        tentar criar outro com o mesmo token.
        */

        $carrinhoExistente =
            $this->buscarPorToken(
                $tokenSessao
            );


        if (
            $carrinhoExistente !== null
        ) {

            /*
            ==============================
            LIMPA ITENS DO CARRINHO ANTIGO
            ==============================
            */

            $sqlLimparItens = '
                DELETE FROM carrinho_itens
                WHERE carrinho_id = :carrinho_id
            ';


            $consultaLimparItens =
                $this->pdo->prepare(
                    $sqlLimparItens
                );


            $consultaLimparItens->bindValue(
                ':carrinho_id',
                (int)
                $carrinhoExistente['id'],
                PDO::PARAM_INT
            );


            $consultaLimparItens->execute();


            /*
            ==============================
            REABRE O CARRINHO
            ==============================
            */

            $sqlReabrir = '
                UPDATE carrinhos
                SET
                    cliente_id = :cliente_id,
                    status = :status,
                    atualizado_em = CURRENT_TIMESTAMP
                WHERE id = :id
            ';


            $consultaReabrir =
                $this->pdo->prepare(
                    $sqlReabrir
                );


            if (
                $clienteId === null
            ) {

                $consultaReabrir->bindValue(
                    ':cliente_id',
                    null,
                    PDO::PARAM_NULL
                );

            } else {

                $consultaReabrir->bindValue(
                    ':cliente_id',
                    $clienteId,
                    PDO::PARAM_INT
                );
            }


            $consultaReabrir->bindValue(
                ':status',
                'aberto',
                PDO::PARAM_STR
            );


            $consultaReabrir->bindValue(
                ':id',
                (int)
                $carrinhoExistente['id'],
                PDO::PARAM_INT
            );


            $consultaReabrir->execute();


            return [

                'id' =>
                    (int)
                    $carrinhoExistente['id'],

                'cliente_id' =>
                    $clienteId,

                'token_sessao' =>
                    $tokenSessao,

                'status' =>
                    'aberto',
            ];
        }


        /*
        =================================
        NÃO EXISTE: CRIA NOVO
        =================================
        */

        $id =
            $this->criar(
                $tokenSessao,
                $clienteId
            );


        return [

            'id' =>
                $id,

            'cliente_id' =>
                $clienteId,

            'token_sessao' =>
                $tokenSessao,

            'status' =>
                'aberto',
        ];
    }


    public function buscarItens(
        int $carrinhoId
    ): array {
        $sql = '
            SELECT
                ci.id,
                ci.carrinho_id,
                ci.produto_id,
                ci.quantidade,
                ci.preco_unitario,

                p.categoria_id,
                p.nome,
                p.descricao,
                p.tipo_preparo,
                p.estoque,
                p.status,

                c.nome AS categoria_nome,
                c.slug AS categoria_slug,
                c.preco AS categoria_preco,
                c.preco_revenda,
                c.quantidade_minima_revenda

            FROM carrinho_itens ci

            INNER JOIN produtos p
                ON p.id = ci.produto_id

            INNER JOIN categorias c
                ON c.id = p.categoria_id

            WHERE ci.carrinho_id = :carrinho_id
            AND p.status = :produto_status
            AND c.ativo = 1

            ORDER BY
                ci.id
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':carrinho_id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':produto_status',
            'ativo',
            PDO::PARAM_STR
        );

        $consulta->execute();

        return
            $consulta->fetchAll();
    }


    public function adicionarItem(
        int $carrinhoId,
        int $produtoId,
        int $quantidade,
        float $precoUnitario
    ): void {
        $sql = '
            INSERT INTO carrinho_itens (
                carrinho_id,
                produto_id,
                quantidade,
                preco_unitario
            )
            VALUES (
                :carrinho_id,
                :produto_id,
                :quantidade,
                :preco_unitario
            )
            ON DUPLICATE KEY UPDATE
                quantidade =
                    quantidade + VALUES(quantidade),
                preco_unitario =
                    VALUES(preco_unitario),
                atualizado_em =
                    CURRENT_TIMESTAMP
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':carrinho_id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':produto_id',
            $produtoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':quantidade',
            $quantidade,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':preco_unitario',
            number_format(
                $precoUnitario,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );

        $consulta->execute();
    }


    public function atualizarItem(
        int $carrinhoId,
        int $produtoId,
        int $quantidade,
        float $precoUnitario
    ): void {
        $sql = '
            UPDATE carrinho_itens
            SET
                quantidade = :quantidade,
                preco_unitario = :preco_unitario
            WHERE carrinho_id = :carrinho_id
            AND produto_id = :produto_id
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':quantidade',
            $quantidade,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':preco_unitario',
            number_format(
                $precoUnitario,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );

        $consulta->bindValue(
            ':carrinho_id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':produto_id',
            $produtoId,
            PDO::PARAM_INT
        );

        $consulta->execute();
    }


    public function removerItem(
        int $carrinhoId,
        int $produtoId
    ): void {
        $sql = '
            DELETE FROM carrinho_itens
            WHERE carrinho_id = :carrinho_id
            AND produto_id = :produto_id
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':carrinho_id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':produto_id',
            $produtoId,
            PDO::PARAM_INT
        );

        $consulta->execute();
    }


    public function removerItensPorCategoria(
        int $carrinhoId,
        int $categoriaId
    ): void {
        $sql = '
            DELETE FROM carrinho_itens
            WHERE carrinho_id = :carrinho_id
            AND produto_id IN (
                SELECT id
                FROM produtos
                WHERE categoria_id = :categoria_id
            )
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':carrinho_id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':categoria_id',
            $categoriaId,
            PDO::PARAM_INT
        );

        $consulta->execute();
    }


    public function atualizarPrecoPorCategoria(
        int $carrinhoId,
        int $categoriaId,
        float $preco
    ): void {
        $sql = '
            UPDATE carrinho_itens ci
            INNER JOIN produtos p
                ON p.id = ci.produto_id

            SET ci.preco_unitario = :preco

            WHERE ci.carrinho_id = :carrinho_id
            AND p.categoria_id = :categoria_id
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':preco',
            number_format(
                $preco,
                2,
                '.',
                ''
            ),
            PDO::PARAM_STR
        );

        $consulta->bindValue(
            ':carrinho_id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':categoria_id',
            $categoriaId,
            PDO::PARAM_INT
        );

        $consulta->execute();
    }


    public function removerSeVazio(
        int $carrinhoId
    ): void {
        $sql = '
            DELETE FROM carrinhos
            WHERE id = :id
            AND status = :status
            AND NOT EXISTS (
                SELECT 1
                FROM carrinho_itens
                WHERE carrinho_id = :id_itens
            )
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':id',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':status',
            'aberto',
            PDO::PARAM_STR
        );

        $consulta->bindValue(
            ':id_itens',
            $carrinhoId,
            PDO::PARAM_INT
        );

        $consulta->execute();
    }


    /*
    =================================
    ASSOCIA CLIENTE AO CARRINHO
    =================================
    */

    public function associarCliente(
        int $carrinhoId,
        int $clienteId
    ): void {
        $sql = '
            UPDATE carrinhos
            SET
                cliente_id = :cliente_id,
                atualizado_em = CURRENT_TIMESTAMP
            WHERE id = :id
            AND status = :status
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->execute([
            ':cliente_id' =>
                $clienteId,

            ':id' =>
                $carrinhoId,

            ':status' =>
                'aberto',
        ]);
    }


    /*
    =================================
    MARCA CARRINHO COMO CONVERTIDO
    =================================
    */

    public function marcarConvertido(
        int $carrinhoId,
        int $clienteId
    ): void {
        $sql = '
            UPDATE carrinhos
            SET
                cliente_id = :cliente_id,
                status = :status,
                atualizado_em = CURRENT_TIMESTAMP
            WHERE id = :id
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->execute([
            ':cliente_id' =>
                $clienteId,

            ':status' =>
                'convertido',

            ':id' =>
                $carrinhoId,
        ]);
    }
}