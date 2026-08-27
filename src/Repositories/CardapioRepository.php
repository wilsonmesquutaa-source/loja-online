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
    TODAS AS CATEGORIAS
    =================================
    */

    public function buscarCategorias(): array
    {
        $sql = '
            SELECT
                categorias.id,
                categorias.nome,
                categorias.slug,
                categorias.descricao,
                categorias.preco,
                categorias.preco_revenda,
                categorias.quantidade_minima_revenda,

                (
                    SELECT
                        categoria_imagens.url_imagem
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_url,

                (
                    SELECT
                        categoria_imagens.posicao_x
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_posicao_x,

                (
                    SELECT
                        categoria_imagens.posicao_y
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_posicao_y

            FROM categorias

            WHERE categorias.ativo = 1

            ORDER BY
                categorias.ordem_destaque ASC,
                categorias.id ASC
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
                categorias.id,
                categorias.nome,
                categorias.slug,
                categorias.descricao,
                categorias.preco,
                categorias.preco_revenda,
                categorias.quantidade_minima_revenda,

                (
                    SELECT
                        categoria_imagens.url_imagem
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_url,

                (
                    SELECT
                        categoria_imagens.posicao_x
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_posicao_x,

                (
                    SELECT
                        categoria_imagens.posicao_y
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_posicao_y

            FROM categorias

            WHERE categorias.ativo = 1
            AND categorias.destaque = 1

            ORDER BY
                categorias.ordem_destaque ASC,
                categorias.id ASC
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
                categorias.id,
                categorias.nome,
                categorias.slug,
                categorias.descricao,
                categorias.preco,
                categorias.preco_revenda,
                categorias.quantidade_minima_revenda,

                (
                    SELECT
                        categoria_imagens.url_imagem
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_url,

                (
                    SELECT
                        categoria_imagens.posicao_x
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_posicao_x,

                (
                    SELECT
                        categoria_imagens.posicao_y
                    FROM categoria_imagens
                    WHERE categoria_imagens.categoria_id =
                        categorias.id
                    AND categoria_imagens.principal = 1
                    ORDER BY
                        categoria_imagens.ordem ASC,
                        categoria_imagens.id ASC
                    LIMIT 1
                ) AS imagem_posicao_y

            FROM categorias

            WHERE categorias.id = :id
            AND categorias.ativo = 1

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
                produtos.id,
                produtos.categoria_id,
                produtos.nome,
                produtos.descricao,
                produtos.estoque,

                (
                    SELECT
                        produto_imagens.url_imagem
                    FROM produto_imagens
                    WHERE produto_imagens.produto_id =
                        produtos.id
                    AND produto_imagens.principal = 1
                    ORDER BY
                        produto_imagens.ordem ASC,
                        produto_imagens.id ASC
                    LIMIT 1
                ) AS imagem_url

            FROM produtos

            WHERE produtos.categoria_id = :categoria_id
            AND produtos.status = :status

            ORDER BY
                produtos.nome
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