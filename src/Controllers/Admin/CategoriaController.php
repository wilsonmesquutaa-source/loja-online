<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;

final class CategoriaController extends Controller
{
    public function index(): void
    {
        $pdo = require APP_ROOT
            . '/database/conexao.php';

        $sql = "
            SELECT
                *
            FROM categorias
            ORDER BY
                ordem_destaque ASC,
                id ASC
        ";

        $categorias = $pdo
            ->query($sql)
            ->fetchAll();

        $this->view(
            'admin/categorias',
            [
                'tituloPagina' =>
                    'Categorias',

                'categorias' =>
                    $categorias,
            ]
        );
    }


    public function novo(): void
    {
        $this->view(
            'admin/categoria-form',
            [
                'tituloPagina' =>
                    'Nova Categoria',

                'categoria' =>
                    null,

                'csrfToken' =>
                    Csrf::gerar(),
            ]
        );
    }


    public function salvar(): void
    {
        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : null;

        if (!Csrf::validar($token)) {
            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $nome = trim(
            (string) (
                $_POST['nome']
                ?? ''
            )
        );

        $slug = trim(
            (string) (
                $_POST['slug']
                ?? ''
            )
        );

        $descricao = trim(
            (string) (
                $_POST['descricao']
                ?? ''
            )
        );

        $preco = (float) (
            $_POST['preco']
            ?? 0
        );

        $precoRevenda = (
            isset(
                $_POST['preco_revenda']
            )
            && $_POST['preco_revenda'] !== ''
        )
            ? (float)
                $_POST['preco_revenda']
            : null;

        $quantidadeMinima = (
            isset(
                $_POST[
                    'quantidade_minima_revenda'
                ]
            )
            && $_POST[
                'quantidade_minima_revenda'
            ] !== ''
        )
            ? (int)
                $_POST[
                    'quantidade_minima_revenda'
                ]
            : null;

        $ativo = (
            (int) (
                $_POST['ativo']
                ?? 0
            )
        );

        $destaque = (
            (int) (
                $_POST['destaque']
                ?? 0
            )
        );

        $ordemDestaque = (
            (int) (
                $_POST['ordem_destaque']
                ?? 0
            )
        );


        if (
            $nome === ''
            || $slug === ''
        ) {
            exit(
                'Nome e slug são obrigatórios.'
            );
        }


        $sql = "
            INSERT INTO categorias
            (
                nome,
                slug,
                descricao,
                preco,
                preco_revenda,
                quantidade_minima_revenda,
                ativo,
                destaque,
                ordem_destaque
            )
            VALUES
            (
                :nome,
                :slug,
                :descricao,
                :preco,
                :preco_revenda,
                :quantidade_minima_revenda,
                :ativo,
                :destaque,
                :ordem_destaque
            )
        ";


        $stmt = $pdo->prepare($sql);


        $stmt->execute([
            ':nome' =>
                $nome,

            ':slug' =>
                $slug,

            ':descricao' =>
                $descricao !== ''
                    ? $descricao
                    : null,

            ':preco' =>
                $preco,

            ':preco_revenda' =>
                $precoRevenda,

            ':quantidade_minima_revenda' =>
                $quantidadeMinima,

            ':ativo' =>
                $ativo,

            ':destaque' =>
                $destaque,

            ':ordem_destaque' =>
                $ordemDestaque,
        ]);


        $this->redirecionar(
            '/admin/categorias'
        );
    }


    public function editar(
        int $id
    ): void {
        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $stmt = $pdo->prepare("
            SELECT *
            FROM categorias
            WHERE id = :id
            LIMIT 1
        ");


        $stmt->execute([
            ':id' => $id,
        ]);


        $categoria =
            $stmt->fetch();


        if ($categoria === false) {
            $this->redirecionar(
                '/admin/categorias'
            );
        }


        $this->view(
            'admin/categoria-form',
            [
                'tituloPagina' =>
                    'Editar Categoria',

                'categoria' =>
                    $categoria,

                'csrfToken' =>
                    Csrf::gerar(),
            ]
        );
    }


    public function atualizar(
        int $id
    ): void {
        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : null;

        if (!Csrf::validar($token)) {
            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $nome = trim(
            (string) (
                $_POST['nome']
                ?? ''
            )
        );

        $slug = trim(
            (string) (
                $_POST['slug']
                ?? ''
            )
        );

        $descricao = trim(
            (string) (
                $_POST['descricao']
                ?? ''
            )
        );

        $preco = (float) (
            $_POST['preco']
            ?? 0
        );

        $precoRevenda = (
            isset(
                $_POST['preco_revenda']
            )
            && $_POST['preco_revenda'] !== ''
        )
            ? (float)
                $_POST['preco_revenda']
            : null;

        $quantidadeMinima = (
            isset(
                $_POST[
                    'quantidade_minima_revenda'
                ]
            )
            && $_POST[
                'quantidade_minima_revenda'
            ] !== ''
        )
            ? (int)
                $_POST[
                    'quantidade_minima_revenda'
                ]
            : null;

        $ativo = (
            (int) (
                $_POST['ativo']
                ?? 0
            )
        );

        $destaque = (
            (int) (
                $_POST['destaque']
                ?? 0
            )
        );

        $ordemDestaque = (
            (int) (
                $_POST['ordem_destaque']
                ?? 0
            )
        );


        if (
            $nome === ''
            || $slug === ''
        ) {
            exit(
                'Nome e slug são obrigatórios.'
            );
        }


        $sql = "
            UPDATE categorias
            SET
                nome = :nome,
                slug = :slug,
                descricao = :descricao,
                preco = :preco,
                preco_revenda = :preco_revenda,
                quantidade_minima_revenda =
                    :quantidade_minima_revenda,
                ativo = :ativo,
                destaque = :destaque,
                ordem_destaque = :ordem_destaque
            WHERE id = :id
        ";


        $stmt = $pdo->prepare($sql);


        $stmt->execute([
            ':nome' =>
                $nome,

            ':slug' =>
                $slug,

            ':descricao' =>
                $descricao !== ''
                    ? $descricao
                    : null,

            ':preco' =>
                $preco,

            ':preco_revenda' =>
                $precoRevenda,

            ':quantidade_minima_revenda' =>
                $quantidadeMinima,

            ':ativo' =>
                $ativo,

            ':destaque' =>
                $destaque,

            ':ordem_destaque' =>
                $ordemDestaque,

            ':id' =>
                $id,
        ]);


        $this->redirecionar(
            '/admin/categorias'
        );
    }
}