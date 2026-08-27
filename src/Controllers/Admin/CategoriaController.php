<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use RuntimeException;

final class CategoriaController extends Controller
{
    public function index(): void
    {
        $pdo = require APP_ROOT
            . '/database/conexao.php';

        $sql = "
            SELECT
                categorias.*,

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
                ) AS imagem_url

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

                'imagemCategoria' =>
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

            exit('Formulário expirado.');
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
            isset($_POST['preco_revenda'])
            && $_POST['preco_revenda'] !== ''
        )
            ? (float) $_POST['preco_revenda']
            : null;


        $quantidadeMinima = (
            isset(
                $_POST['quantidade_minima_revenda']
            )
            && $_POST['quantidade_minima_revenda'] !== ''
        )
            ? (int) $_POST['quantidade_minima_revenda']
            : null;


        $ativo = (int) (
            $_POST['ativo']
            ?? 0
        );


        $destaque = (int) (
            $_POST['destaque']
            ?? 0
        );


        $ordemDestaque = (int) (
            $_POST['ordem_destaque']
            ?? 0
        );


        if (
            $nome === ''
            || $slug === ''
        ) {

            exit('Nome e slug são obrigatórios.');
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


        $stmt =
            $pdo->prepare($sql);


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


        $categoriaId =
            (int) $pdo->lastInsertId();


        if (
            $this->possuiUpload(
                'imagem'
            )
        ) {

            $this->salvarImagemCategoria(
                $pdo,
                $categoriaId,
                $nome,
                50.00,
                50.00
            );
        }


        $this->redirecionar(
            '/admin/categorias'
        );
    }


    public function editar(
        int $id
    ): void {

        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                SELECT *
                FROM categorias
                WHERE id = :id
                LIMIT 1
            ");


        $stmt->execute([
            ':id' =>
            $id,
        ]);


        $categoria =
            $stmt->fetch();


        if ($categoria === false) {

            $this->redirecionar(
                '/admin/categorias'
            );
        }


        $stmtImagem =
            $pdo->prepare("
                SELECT
                    id,
                    url_imagem,
                    texto_alternativo,
                    principal,
                    ordem,
                    posicao_x,
                    posicao_y
                FROM categoria_imagens
                WHERE categoria_id = :categoria_id
                AND principal = 1
                ORDER BY
                    ordem ASC,
                    id ASC
                LIMIT 1
            ");


        $stmtImagem->execute([
            ':categoria_id' =>
            $id,
        ]);


        $imagemCategoria =
            $stmtImagem->fetch();


        if ($imagemCategoria === false) {

            $imagemCategoria =
                null;
        }


        $this->view(
            'admin/categoria-form',
            [
                'tituloPagina' =>
                'Editar Categoria',

                'categoria' =>
                $categoria,

                'imagemCategoria' =>
                $imagemCategoria,

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

            exit('Formulário expirado.');
        }


        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $stmtCategoria =
            $pdo->prepare("
                SELECT
                    id,
                    nome
                FROM categorias
                WHERE id = :id
                LIMIT 1
            ");


        $stmtCategoria->execute([
            ':id' =>
            $id,
        ]);


        $categoria =
            $stmtCategoria->fetch();


        if (
            $categoria === false
        ) {

            $this->redirecionar(
                '/admin/categorias'
            );
        }


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
            isset($_POST['preco_revenda'])
            && $_POST['preco_revenda'] !== ''
        )
            ? (float) $_POST['preco_revenda']
            : null;


        $quantidadeMinima = (
            isset(
                $_POST['quantidade_minima_revenda']
            )
            && $_POST['quantidade_minima_revenda'] !== ''
        )
            ? (int) $_POST['quantidade_minima_revenda']
            : null;


        $ativo = (int) (
            $_POST['ativo']
            ?? 0
        );


        $destaque = (int) (
            $_POST['destaque']
            ?? 0
        );


        $ordemDestaque = (int) (
            $_POST['ordem_destaque']
            ?? 0
        );


        $posicaoX =
            $this->normalizarPosicao(
                $_POST['posicao_x']
                    ?? 50
            );


        $posicaoY =
            $this->normalizarPosicao(
                $_POST['posicao_y']
                    ?? 50
            );


        if (
            $nome === ''
            || $slug === ''
        ) {

            exit('Nome e slug são obrigatórios.');
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


        $stmt =
            $pdo->prepare($sql);


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


        /*
        =================================
        NOVA IMAGEM
        =================================
        */

        if (
            $this->possuiUpload(
                'imagem'
            )
        ) {

            $this->salvarImagemCategoria(
                $pdo,
                $id,
                $nome,
                $posicaoX,
                $posicaoY
            );
        } elseif (
            isset($_POST['excluir_imagem'])
            &&
            (string)
            $_POST['excluir_imagem']
            === '1'
        ) {

            /*
            -----------------------------
            EXCLUI IMAGEM
            -----------------------------
            */

            $this->excluirImagemPrincipal(
                $pdo,
                $id
            );
        } else {

            /*
            -----------------------------
            ATUALIZA APENAS POSIÇÃO
            -----------------------------
            */

            $stmtImagem =
                $pdo->prepare("
                    UPDATE categoria_imagens
                    SET
                        posicao_x = :posicao_x,
                        posicao_y = :posicao_y
                    WHERE categoria_id =
                        :categoria_id
                    AND principal = 1
                ");


            $stmtImagem->execute([
                ':posicao_x' =>
                $posicaoX,

                ':posicao_y' =>
                $posicaoY,

                ':categoria_id' =>
                $id,
            ]);
        }


        $this->redirecionar(
            '/admin/categorias'
        );
    }


    /*
    =================================
    NORMALIZA POSIÇÃO
    =================================
    */

    private function normalizarPosicao(
        $valor
    ): float {

        $valor =
            is_numeric($valor)
            ? (float) $valor
            : 50.00;


        if ($valor < 0) {
            return 0.00;
        }


        if ($valor > 100) {
            return 100.00;
        }


        return round(
            $valor,
            2
        );
    }

    /*
    =================================
    VERIFICA UPLOAD
    =================================
    */

    private function possuiUpload(
        string $campo
    ): bool {

        return isset(
            $_FILES[$campo]
        )
            &&
            is_array(
                $_FILES[$campo]
            )
            &&
            (
                (int)
                (
                    $_FILES[$campo]['error']
                    ?? UPLOAD_ERR_NO_FILE
                )
            )
            !== UPLOAD_ERR_NO_FILE;
    }


    /*
    =================================
    SALVA IMAGEM
    =================================
    */

    private function salvarImagemCategoria(
        \PDO $pdo,
        int $categoriaId,
        string $nomeCategoria,
        float $posicaoX,
        float $posicaoY
    ): void {

        if (
            !isset(
                $_FILES['imagem']
            )
        ) {

            throw new RuntimeException(
                'Nenhuma imagem foi recebida.'
            );
        }


        $arquivo =
            $_FILES['imagem'];


        $erro =
            (int) (
                $arquivo['error']
                ?? UPLOAD_ERR_NO_FILE
            );


        if (
            $erro !== UPLOAD_ERR_OK
        ) {

            throw new RuntimeException(
                'Não foi possível enviar a imagem.'
            );
        }


        $tamanho =
            (int) (
                $arquivo['size']
                ?? 0
            );


        if (
            $tamanho <= 0
            ||
            $tamanho > 5 * 1024 * 1024
        ) {

            throw new RuntimeException(
                'A imagem deve possuir no máximo 5 MB.'
            );
        }


        $arquivoTemporario =
            (string) (
                $arquivo['tmp_name']
                ?? ''
            );


        if (
            $arquivoTemporario === ''
            ||
            !is_uploaded_file(
                $arquivoTemporario
            )
        ) {

            throw new RuntimeException(
                'Upload de imagem inválido.'
            );
        }


        $finfo =
            new \finfo(
                FILEINFO_MIME_TYPE
            );


        $mime =
            $finfo->file(
                $arquivoTemporario
            );


        $extensoesPermitidas = [
            'image/jpeg' =>
            'jpg',

            'image/png' =>
            'png',

            'image/webp' =>
            'webp',
        ];


        if (
            !isset(
                $extensoesPermitidas[$mime]
            )
        ) {

            throw new RuntimeException(
                'Formato de imagem não permitido. Use JPG, PNG ou WEBP.'
            );
        }


        $dimensoes =
            @getimagesize(
                $arquivoTemporario
            );


        if (
            $dimensoes === false
        ) {

            throw new RuntimeException(
                'O arquivo enviado não é uma imagem válida.'
            );
        }


        $pasta =
            APP_ROOT
            . '/public/assets/uploads/categorias';


        if (
            !is_dir($pasta)
            &&
            !mkdir(
                $pasta,
                0755,
                true
            )
            &&
            !is_dir($pasta)
        ) {

            throw new RuntimeException(
                'Não foi possível criar a pasta de imagens das categorias.'
            );
        }


        $nomeArquivo =
            'categoria_'
            . $categoriaId
            . '_'
            . bin2hex(
                random_bytes(8)
            )
            . '.'
            . $extensoesPermitidas[$mime];


        $destino =
            $pasta
            . '/'
            . $nomeArquivo;


        if (
            !move_uploaded_file(
                $arquivoTemporario,
                $destino
            )
        ) {

            throw new RuntimeException(
                'Não foi possível salvar a imagem.'
            );
        }


        $urlImagem =
            '/assets/uploads/categorias/'
            . $nomeArquivo;


        $posicaoX =
            $this->normalizarPosicao(
                $posicaoX
            );


        $posicaoY =
            $this->normalizarPosicao(
                $posicaoY
            );


        try {

            /*
            -----------------------------
            BUSCA IMAGENS PRINCIPAIS
            -----------------------------
            */

            $stmtAntigas =
                $pdo->prepare("
                    SELECT
                        id,
                        url_imagem
                    FROM categoria_imagens
                    WHERE categoria_id =
                        :categoria_id
                    AND principal = 1
                ");


            $stmtAntigas->execute([
                ':categoria_id' =>
                $categoriaId,
            ]);


            $imagensAntigas =
                $stmtAntigas->fetchAll();


            /*
            -----------------------------
            APAGA ARQUIVOS ANTIGOS
            -----------------------------
            */

            foreach (
                $imagensAntigas
                as $imagemAntiga
            ) {

                $arquivoAntigo =
                    APP_ROOT
                    . '/public'
                    . $imagemAntiga['url_imagem'];


                if (
                    is_file(
                        $arquivoAntigo
                    )
                ) {

                    @unlink(
                        $arquivoAntigo
                    );
                }
            }


            /*
            -----------------------------
            REMOVE REGISTROS ANTIGOS
            -----------------------------
            */

            $stmtExcluir =
                $pdo->prepare("
                    DELETE FROM categoria_imagens
                    WHERE categoria_id =
                        :categoria_id
                    AND principal = 1
                ");


            $stmtExcluir->execute([
                ':categoria_id' =>
                $categoriaId,
            ]);


            /*
            -----------------------------
            INSERE NOVA IMAGEM
            -----------------------------
            */

            $stmtImagem =
                $pdo->prepare("
                    INSERT INTO categoria_imagens
                    (
                        categoria_id,
                        url_imagem,
                        texto_alternativo,
                        principal,
                        ordem,
                        posicao_x,
                        posicao_y
                    )
                    VALUES
                    (
                        :categoria_id,
                        :url_imagem,
                        :texto_alternativo,
                        1,
                        1,
                        :posicao_x,
                        :posicao_y
                    )
                ");


            $stmtImagem->execute([
                ':categoria_id' =>
                $categoriaId,

                ':url_imagem' =>
                $urlImagem,

                ':texto_alternativo' =>
                'Imagem de '
                    . $nomeCategoria,

                ':posicao_x' =>
                $posicaoX,

                ':posicao_y' =>
                $posicaoY,
            ]);
        } catch (
            \Throwable $erro
        ) {

            if (
                is_file(
                    $destino
                )
            ) {

                @unlink(
                    $destino
                );
            }

            throw $erro;
        }
    }


    /*
    =================================
    EXCLUI IMAGEM PRINCIPAL
    =================================
    */

    private function excluirImagemPrincipal(
        \PDO $pdo,
        int $categoriaId
    ): void {

        $stmt =
            $pdo->prepare("
                SELECT
                    id,
                    url_imagem
                FROM categoria_imagens
                WHERE categoria_id =
                    :categoria_id
                AND principal = 1
                LIMIT 1
            ");


        $stmt->execute([
            ':categoria_id' =>
            $categoriaId,
        ]);


        $imagem =
            $stmt->fetch();


        if (
            $imagem === false
        ) {

            return;
        }


        $arquivo =
            APP_ROOT
            . '/public'
            . $imagem['url_imagem'];


        if (
            is_file(
                $arquivo
            )
        ) {

            @unlink(
                $arquivo
            );
        }


        $stmtExcluir =
            $pdo->prepare("
                DELETE FROM categoria_imagens
                WHERE id = :id
            ");


        $stmtExcluir->execute([
            ':id' =>
            (int) $imagem['id'],
        ]);
    }
}
