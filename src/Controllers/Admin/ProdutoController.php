<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use RuntimeException;

final class ProdutoController extends Controller
{
    /*
    =================================
    LISTAGEM
    =================================
    */

    public function index(): void
    {
        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $sql = "
            SELECT
                produtos.*,

                categorias.nome AS categoria,

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

            INNER JOIN categorias
                ON categorias.id =
                    produtos.categoria_id

            ORDER BY
                produtos.id DESC
        ";


        $produtos =
            $pdo
            ->query($sql)
            ->fetchAll();


        $this->view(
            'admin/produtos',
            [
                'tituloPagina' =>
                    'Produtos',

                'produtos' =>
                    $produtos,
            ]
        );
    }


    /*
    =================================
    NOVO
    =================================
    */

    public function novo(): void
    {
        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $categorias =
            $pdo
            ->query("
                SELECT *
                FROM categorias
                WHERE ativo = 1
                ORDER BY nome ASC
            ")
            ->fetchAll();


        $this->view(
            'admin/produto-form',
            [
                'tituloPagina' =>
                    'Novo Produto',

                'produto' =>
                    null,

                'categorias' =>
                    $categorias,

                'imagemProduto' =>
                    null,
            ]
        );
    }


    /*
    =================================
    SALVAR
    =================================
    */

    public function salvar(): void
    {
        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $categoriaId =
            (int) (
                $_POST['categoria_id']
                ?? 0
            );


        $nome =
            trim(
                (string) (
                    $_POST['nome']
                    ?? ''
                )
            );


        $slug =
            trim(
                (string) (
                    $_POST['slug']
                    ?? ''
                )
            );


        $descricao =
            trim(
                (string) (
                    $_POST['descricao']
                    ?? ''
                )
            );


        $estoque =
            max(
                0,
                (int) (
                    $_POST['estoque']
                    ?? 0
                )
            );


        if (
            $categoriaId <= 0
        ) {

            exit(
                'Selecione uma categoria.'
            );
        }


        if (
            $nome === ''
        ) {

            exit(
                'Informe o nome do produto.'
            );
        }


        if (
            $slug === ''
        ) {

            exit(
                'Informe o slug do produto.'
            );
        }


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
                :categoria_id,
                :nome,
                :slug,
                :descricao,
                :estoque
            )
        ";


        $stmt =
            $pdo->prepare(
                $sql
            );


        $stmt->execute([
            ':categoria_id' =>
                $categoriaId,

            ':nome' =>
                $nome,

            ':slug' =>
                $slug,

            ':descricao' =>
                $descricao !== ''
                    ? $descricao
                    : null,

            ':estoque' =>
                $estoque,
        ]);


        $produtoId =
            (int)
            $pdo->lastInsertId();


        /*
        =================================
        IMAGEM
        =================================
        */

        if (
            $this->possuiUpload(
                'imagem'
            )
        ) {

            $this->salvarImagemProduto(
                $pdo,
                $produtoId,
                $nome,
                50.00,
                50.00
            );
        }


        $this->redirecionar(
            '/admin/produtos'
        );
    }


    /*
    =================================
    EDITAR
    =================================
    */

    public function editar(
        int $id
    ): void {

        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                SELECT *
                FROM produtos
                WHERE id = :id
                LIMIT 1
            ");


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $produto =
            $stmt->fetch();


        if (
            $produto === false
        ) {

            $this->redirecionar(
                '/admin/produtos'
            );
        }


        $categorias =
            $pdo
            ->query("
                SELECT *
                FROM categorias
                WHERE ativo = 1
                ORDER BY nome ASC
            ")
            ->fetchAll();


        $stmtImagem =
            $pdo->prepare("
                SELECT
                    id,
                    produto_id,
                    url_imagem,
                    texto_alternativo,
                    principal,
                    ordem,
                    posicao_x,
                    posicao_y
                FROM produto_imagens
                WHERE produto_id = :produto_id
                AND principal = 1
                ORDER BY
                    ordem ASC,
                    id ASC
                LIMIT 1
            ");


        $stmtImagem->execute([
            ':produto_id' =>
                $id,
        ]);


        $imagemProduto =
            $stmtImagem->fetch();


        if (
            $imagemProduto === false
        ) {

            $imagemProduto =
                null;
        }


        $this->view(
            'admin/produto-form',
            [
                'tituloPagina' =>
                    'Editar Produto',

                'produto' =>
                    $produto,

                'categorias' =>
                    $categorias,

                'imagemProduto' =>
                    $imagemProduto,
            ]
        );
    }


    /*
    =================================
    ATUALIZAR
    =================================
    */

    public function atualizar(
        int $id
    ): void {

        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmtExiste =
            $pdo->prepare("
                SELECT
                    id,
                    nome
                FROM produtos
                WHERE id = :id
                LIMIT 1
            ");


        $stmtExiste->execute([
            ':id' =>
                $id,
        ]);


        $produtoExistente =
            $stmtExiste->fetch();


        if (
            $produtoExistente === false
        ) {

            $this->redirecionar(
                '/admin/produtos'
            );
        }


        $categoriaId =
            (int) (
                $_POST['categoria_id']
                ?? 0
            );


        $nome =
            trim(
                (string) (
                    $_POST['nome']
                    ?? ''
                )
            );


        $slug =
            trim(
                (string) (
                    $_POST['slug']
                    ?? ''
                )
            );


        $descricao =
            trim(
                (string) (
                    $_POST['descricao']
                    ?? ''
                )
            );


        $estoque =
            max(
                0,
                (int) (
                    $_POST['estoque']
                    ?? 0
                )
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
            $categoriaId <= 0
        ) {

            exit(
                'Selecione uma categoria.'
            );
        }


        if (
            $nome === ''
        ) {

            exit(
                'Informe o nome do produto.'
            );
        }


        if (
            $slug === ''
        ) {

            exit(
                'Informe o slug do produto.'
            );
        }


        $sql = "
            UPDATE produtos
            SET
                categoria_id = :categoria_id,
                nome = :nome,
                slug = :slug,
                descricao = :descricao,
                estoque = :estoque
            WHERE id = :id
        ";


        $stmt =
            $pdo->prepare(
                $sql
            );


        $stmt->execute([
            ':categoria_id' =>
                $categoriaId,

            ':nome' =>
                $nome,

            ':slug' =>
                $slug,

            ':descricao' =>
                $descricao !== ''
                    ? $descricao
                    : null,

            ':estoque' =>
                $estoque,

            ':id' =>
                $id,
        ]);


        /*
        =================================
        EXCLUI IMAGEM
        =================================
        */

        if (
            isset(
                $_POST['excluir_imagem']
            )
            &&
            (string)
                $_POST['excluir_imagem']
                === '1'
        ) {

            $this->excluirImagemPrincipal(
                $pdo,
                $id
            );

        } elseif (
            $this->possuiUpload(
                'imagem'
            )
        ) {

            /*
            -----------------------------
            NOVA IMAGEM
            -----------------------------
            */

            $this->salvarImagemProduto(
                $pdo,
                $id,
                $nome,
                $posicaoX,
                $posicaoY
            );

        } else {

            /*
            -----------------------------
            APENAS POSICIONAMENTO
            -----------------------------
            */

            $stmtImagem =
                $pdo->prepare("
                    UPDATE produto_imagens
                    SET
                        posicao_x = :posicao_x,
                        posicao_y = :posicao_y
                    WHERE produto_id =
                        :produto_id
                    AND principal = 1
                ");


            $stmtImagem->execute([
                ':posicao_x' =>
                    $posicaoX,

                ':posicao_y' =>
                    $posicaoY,

                ':produto_id' =>
                    $id,
            ]);
        }


        $this->redirecionar(
            '/admin/produtos'
        );
    }


    /*
    =================================
    EXCLUIR
    =================================
    */

    public function excluir(
        int $id
    ): void {

        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare(
                "
                DELETE FROM produtos
                WHERE id = :id
                "
            );


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $this->redirecionar(
            '/admin/produtos'
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


        if (
            $valor < 0
        ) {

            return 0.00;
        }


        if (
            $valor > 100
        ) {

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

    private function salvarImagemProduto(
        \PDO $pdo,
        int $produtoId,
        string $nomeProduto,
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
                $extensoesPermitidas[
                    $mime
                ]
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
            . '/public/assets/uploads/produtos';


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
                'Não foi possível criar a pasta de imagens dos produtos.'
            );
        }


        $nomeArquivo =
            'produto_'
            . $produtoId
            . '_'
            . bin2hex(
                random_bytes(8)
            )
            . '.'
            . $extensoesPermitidas[
                $mime
            ];


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
            '/assets/uploads/produtos/'
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
                    FROM produto_imagens
                    WHERE produto_id =
                        :produto_id
                    AND principal = 1
                ");


            $stmtAntigas->execute([
                ':produto_id' =>
                    $produtoId,
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
                    . $imagemAntiga[
                        'url_imagem'
                    ];


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
                    DELETE FROM produto_imagens
                    WHERE produto_id =
                        :produto_id
                    AND principal = 1
                ");


            $stmtExcluir->execute([
                ':produto_id' =>
                    $produtoId,
            ]);


            /*
            -----------------------------
            INSERE NOVA IMAGEM
            -----------------------------
            */

            $stmtImagem =
                $pdo->prepare("
                    INSERT INTO produto_imagens
                    (
                        produto_id,
                        url_imagem,
                        texto_alternativo,
                        principal,
                        ordem,
                        posicao_x,
                        posicao_y
                    )
                    VALUES
                    (
                        :produto_id,
                        :url_imagem,
                        :texto_alternativo,
                        1,
                        1,
                        :posicao_x,
                        :posicao_y
                    )
                ");


            $stmtImagem->execute([
                ':produto_id' =>
                    $produtoId,

                ':url_imagem' =>
                    $urlImagem,

                ':texto_alternativo' =>
                    'Imagem de '
                    . $nomeProduto,

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
        int $produtoId
    ): void {

        $stmt =
            $pdo->prepare("
                SELECT
                    id,
                    url_imagem
                FROM produto_imagens
                WHERE produto_id =
                    :produto_id
                AND principal = 1
                LIMIT 1
            ");


        $stmt->execute([
            ':produto_id' =>
                $produtoId,
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
                DELETE FROM produto_imagens
                WHERE id = :id
            ");


        $stmtExcluir->execute([
            ':id' =>
                (int)
                $imagem['id'],
        ]);
    }
}

