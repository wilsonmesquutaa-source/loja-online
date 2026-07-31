<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;


final class ProdutoController extends Controller
{


    public function index(): void
    {

        require APP_ROOT . '/conexao/conexao.php';


        $sql = "
            SELECT 
                produtos.*,
                categorias.nome AS categoria

            FROM produtos

            INNER JOIN categorias
            ON categorias.id = produtos.categoria_id

            ORDER BY produtos.id DESC
        ";


        $produtos = $pdo
            ->query($sql)
            ->fetchAll();



        $this->view(
            'admin/produtos',
            [
                'tituloPagina' => 'Produtos',
                'produtos' => $produtos
            ]
        );
    }





    public function novo(): void
    {

        require APP_ROOT . '/conexao/conexao.php';


        $categorias = $pdo
            ->query("
                SELECT *
                FROM categorias
                WHERE ativo = 1
            ")
            ->fetchAll();



        $this->view(
            'admin/produto-form',
            [
                'tituloPagina' => 'Novo Produto',
                'categorias' => $categorias
            ]
        );
    }






    public function salvar(): void
    {

        require APP_ROOT . '/conexao/conexao.php';


        $sql = "
        INSERT INTO produtos
        (
            categoria_id,
            nome,
            slug,
            descricao,
            estoque
        )
        VALUES
        (
            :categoria,
            :nome,
            :slug,
            :descricao,
            :estoque
        )
        ";



        $stmt = $pdo->prepare($sql);


        $stmt->execute([

            ':categoria' => $_POST['categoria_id'],
            ':nome' => $_POST['nome'],
            ':slug' => $_POST['slug'],
            ':descricao' => $_POST['descricao'],
            ':estoque' => $_POST['estoque']

        ]);



        header(
            'Location: ' . BASE_URL . '/admin/produtos'
        );

        exit;
    }
    public function editar(int $id): void
    {
        require APP_ROOT . '/conexao/conexao.php';


        $stmt = $pdo->prepare("
        SELECT *
        FROM produtos
        WHERE id = ?
    ");

        $stmt->execute([$id]);

        $produto = $stmt->fetch();


        if (!$produto) {

            header(
                'Location: ' . BASE_URL . '/admin/produtos'
            );

            exit;
        }


        $categorias = $pdo
            ->query("
            SELECT *
            FROM categorias
            WHERE ativo = 1
        ")
            ->fetchAll();


        $this->view(
            'admin/produto-form',
            [
                'tituloPagina' => 'Editar Produto',
                'produto' => $produto,
                'categorias' => $categorias
            ]
        );
    }

    public function atualizar(int $id): void
    {
        require APP_ROOT . '/conexao/conexao.php';


        $sql = "
        UPDATE produtos
        SET
            categoria_id = :categoria,
            nome = :nome,
            slug = :slug,
            descricao = :descricao,
            estoque = :estoque

        WHERE id = :id
    ";


        $stmt = $pdo->prepare($sql);


        $stmt->execute([

            ':categoria' => $_POST['categoria_id'],
            ':nome' => $_POST['nome'],
            ':slug' => $_POST['slug'],
            ':descricao' => $_POST['descricao'],
            ':estoque' => $_POST['estoque'],
            ':id' => $id

        ]);


        header(
            'Location: ' . BASE_URL . '/admin/produtos'
        );

        exit;
    }




    public function excluir(int $id): void
    {

        require APP_ROOT . '/conexao/conexao.php';


        $stmt = $pdo->prepare(
            "DELETE FROM produtos WHERE id = ?"
        );


        $stmt->execute([$id]);



        header(
            'Location: ' . BASE_URL . '/admin/produtos'
        );

        exit;
    }
}
