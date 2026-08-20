<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CardapioRepository
{
    private PDO $pdo;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }

    /*
    =================================
    TODAS AS CATEGORIAS DO CARDÁPIO
    =================================
    */

    public function buscarCategorias(): array
    {
        $sql = '
            SELECT
                id,
                nome,
                descricao,
                preco,
                preco_revenda,
                quantidade_minima_revenda
            FROM categorias
            WHERE ativo = 1
            ORDER BY id ASC
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->execute();

        return
            $consulta->fetchAll();
    }

    /*
    =================================
    CATEGORIAS EM DESTAQUE
    =================================
    */

    public function buscarCategoriasDestaque(): array
    {
        $sql = '
            SELECT
                id,
                nome,
                descricao,
                preco,
                preco_revenda,
                quantidade_minima_revenda
            FROM categorias
            WHERE ativo = 1
            AND destaque = 1
            ORDER BY ordem_destaque ASC, id ASC
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->execute();

        return
            $consulta->fetchAll();
    }

    /*
    =================================
    CATEGORIA POR ID
    =================================
    */

    public function buscarCategoriaPorId(
        int $categoriaId
    ): ?array {
        $sql = '
            SELECT
                id,
                nome,
                descricao,
                preco,
                preco_revenda,
                quantidade_minima_revenda
            FROM categorias
            WHERE id = :id
            AND ativo = 1
            LIMIT 1
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':id',
            $categoriaId,
            PDO::PARAM_INT
        );

        $consulta->execute();

        $categoria =
            $consulta->fetch();

        return
            $categoria !== false
                ? $categoria
                : null;
    }

    /*
    =================================
    PRODUTOS POR CATEGORIA
    =================================
    */

    public function buscarProdutosPorCategoria(
        int $categoriaId
    ): array {
        $sql = '
            SELECT
                id,
                categoria_id,
                nome,
                descricao,
                estoque
            FROM produtos
            WHERE categoria_id = :categoria_id
            AND status = :status
            ORDER BY nome
        ';

        $consulta =
            $this->pdo->prepare(
                $sql
            );

        $consulta->bindValue(
            ':categoria_id',
            $categoriaId,
            PDO::PARAM_INT
        );

        $consulta->bindValue(
            ':status',
            'ativo',
            PDO::PARAM_STR
        );

        $consulta->execute();

        return
            $consulta->fetchAll();
    }
}