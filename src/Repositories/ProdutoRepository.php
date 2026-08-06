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
}