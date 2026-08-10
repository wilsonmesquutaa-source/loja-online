<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProdutoRepository
{
    private PDO $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }



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
            ORDER BY id
        ';


        $consulta = $this->pdo->prepare($sql);


        $consulta->execute();


        return $consulta->fetchAll();
    }

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

        $consulta = $this->pdo->prepare($sql);

        $consulta->bindValue(
            ':id',
            $categoriaId,
            PDO::PARAM_INT
        );

        $consulta->execute();

        $categoria = $consulta->fetch();

        return $categoria !== false
            ? $categoria
            : null;
    }




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

        $consulta = $this->pdo->prepare($sql);

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

        return $consulta->fetchAll();
    }
}
