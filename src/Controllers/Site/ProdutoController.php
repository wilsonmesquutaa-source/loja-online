<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use PDO;

final class ProdutoController extends Controller
{
    public function index(): void
    {
        require_once __DIR__ . '/../../../database/conexao.php';

        $sql = "
            SELECT
                id,
                nome,
                descricao,
                preco
            FROM categorias
            WHERE ativo = 1
            ORDER BY id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view(
            'site/produtos',
            [
                'tituloPagina' => 'Produtos',
                'categorias' => $categorias,
            ]
        );
    }


    public function categoria(int $id): void
    {
        require_once __DIR__ . '/../../../conexao/conexao.php';

        $sql = "
            SELECT
                id,
                nome,
                descricao
            FROM produtos
            WHERE categoria_id = :categoria_id
            AND status = 'ativo'
            ORDER BY nome
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(
            ':categoria_id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');

        echo json_encode($produtos);
    }
}
