<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use RuntimeException;

final class DestaqueController extends Controller
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
                categoria_banners.*,

                categorias.nome AS categoria,
                categorias.slug AS categoria_slug

            FROM categoria_banners

            INNER JOIN categorias
                ON categorias.id =
                    categoria_banners.categoria_id

            ORDER BY
                categorias.nome ASC,
                categoria_banners.id ASC
        ";


        $destaques =
            $pdo
            ->query($sql)
            ->fetchAll();


        $this->view(
            'admin/destaques',
            [
                'tituloPagina' =>
                    'Destaques',

                'destaques' =>
                    $destaques,

                'csrfToken' =>
                    Csrf::gerar(),
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
                SELECT
                    id,
                    nome,
                    slug
                FROM categorias
                WHERE ativo = 1
                ORDER BY nome ASC
            ")
            ->fetchAll();


        $this->view(
            'admin/destaque-form',
            [
                'tituloPagina' =>
                    'Novo Destaque',

                'destaque' =>
                    null,

                'categorias' =>
                    $categorias,

                'csrfToken' =>
                    Csrf::gerar(),
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
        $token =
            isset($_POST['_token'])
                ? (string) $_POST['_token']
                : null;


        if (!Csrf::validar($token)) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $categoriaId =
            (int) (
                $_POST['categoria_id']
                ?? 0
            );


        $textoAlternativo =
            trim(
                (string) (
                    $_POST['texto_alternativo']
                    ?? ''
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


        $ativo =
            isset($_POST['ativo'])
                ? (int) $_POST['ativo']
                : 1;


        if ($categoriaId <= 0) {

            exit(
                'Selecione uma categoria.'
            );
        }


        /*
        =================================
        VERIFICA SE JÁ EXISTE DESTAQUE
        =================================
        */

        $stmtExiste =
            $pdo->prepare("
                SELECT
                    id
                FROM categoria_banners
                WHERE categoria_id =
                    :categoria_id
                LIMIT 1
            ");


        $stmtExiste->execute([
            ':categoria_id' =>
                $categoriaId,
        ]);


        if ($stmtExiste->fetch()) {

            exit(
                'Esta categoria já possui uma imagem de destaque.'
            );
        }


        /*
        =================================
        IMAGEM OBRIGATÓRIA
        =================================
        */

        if (
            !$this->possuiUpload(
                'imagem'
            )
        ) {

            exit(
                'Selecione uma imagem de destaque.'
            );
        }


        /*
        =================================
        SALVA IMAGEM
        =================================
        */

        $dadosImagem =
            $this->processarUpload(
                0
            );


        /*
        =================================
        INSERE REGISTRO
        =================================
        */

        try {

            $stmt =
                $pdo->prepare("
                    INSERT INTO categoria_banners
                    (
                        categoria_id,
                        url_imagem,
                        texto_alternativo,
                        posicao_x,
                        posicao_y,
                        ativo
                    )
                    VALUES
                    (
                        :categoria_id,
                        :url_imagem,
                        :texto_alternativo,
                        :posicao_x,
                        :posicao_y,
                        :ativo
                    )
                ");


            $stmt->execute([
                ':categoria_id' =>
                    $categoriaId,

                ':url_imagem' =>
                    $dadosImagem['url_imagem'],

                ':texto_alternativo' =>
                    $textoAlternativo !== ''
                        ? $textoAlternativo
                        : null,

                ':posicao_x' =>
                    $posicaoX,

                ':posicao_y' =>
                    $posicaoY,

                ':ativo' =>
                    $ativo === 1
                        ? 1
                        : 0,
            ]);

        } catch (
            \Throwable $erro
        ) {

            if (
                is_file(
                    $dadosImagem['caminho']
                )
            ) {

                @unlink(
                    $dadosImagem['caminho']
                );
            }

            throw $erro;
        }


        $this->redirecionar(
            '/admin/destaques'
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
                SELECT
                    cb.*,

                    c.nome AS categoria,
                    c.slug AS categoria_slug

                FROM categoria_banners cb

                INNER JOIN categorias c
                    ON c.id =
                        cb.categoria_id

                WHERE cb.id = :id
                LIMIT 1
            ");


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $destaque =
            $stmt->fetch();


        if (
            $destaque === false
        ) {

            $this->redirecionar(
                '/admin/destaques'
            );
        }


        $categorias =
            $pdo
            ->query("
                SELECT
                    id,
                    nome,
                    slug
                FROM categorias
                WHERE ativo = 1
                ORDER BY nome ASC
            ")
            ->fetchAll();


        $this->view(
            'admin/destaque-form',
            [
                'tituloPagina' =>
                    'Editar Destaque',

                'destaque' =>
                    $destaque,

                'categorias' =>
                    $categorias,

                'csrfToken' =>
                    Csrf::gerar(),
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

        $token =
            isset($_POST['_token'])
                ? (string) $_POST['_token']
                : null;


        if (!Csrf::validar($token)) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        /*
        =================================
        BUSCA DESTAQUE
        =================================
        */

        $stmtExistente =
            $pdo->prepare("
                SELECT
                    *
                FROM categoria_banners
                WHERE id = :id
                LIMIT 1
            ");


        $stmtExistente->execute([
            ':id' =>
                $id,
        ]);


        $destaque =
            $stmtExistente->fetch();


        if (
            $destaque === false
        ) {

            $this->redirecionar(
                '/admin/destaques'
            );
        }


        $categoriaId =
            (int) (
                $_POST['categoria_id']
                ?? 0
            );


        $textoAlternativo =
            trim(
                (string) (
                    $_POST['texto_alternativo']
                    ?? ''
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


        $ativo =
            isset($_POST['ativo'])
                ? (int) $_POST['ativo']
                : 1;


        if (
            $categoriaId <= 0
        ) {

            exit(
                'Selecione uma categoria.'
            );
        }


        /*
        =================================
        VERIFICA TROCA DE CATEGORIA
        =================================
        */

        $stmtCategoria =
            $pdo->prepare("
                SELECT
                    id
                FROM categoria_banners
                WHERE categoria_id = :categoria_id
                AND id <> :id
                LIMIT 1
            ");


        $stmtCategoria->execute([
            ':categoria_id' =>
                $categoriaId,

            ':id' =>
                $id,
        ]);


        if (
            $stmtCategoria->fetch()
        ) {

            exit(
                'Esta categoria já possui uma imagem de destaque.'
            );
        }


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

            $dadosImagem =
                $this->processarUpload(
                    $id
                );


            try {

                $stmt =
                    $pdo->prepare("
                        UPDATE categoria_banners
                        SET
                            categoria_id =
                                :categoria_id,

                            url_imagem =
                                :url_imagem,

                            texto_alternativo =
                                :texto_alternativo,

                            posicao_x =
                                :posicao_x,

                            posicao_y =
                                :posicao_y,

                            ativo =
                                :ativo

                        WHERE id = :id
                    ");


                $stmt->execute([
                    ':categoria_id' =>
                        $categoriaId,

                    ':url_imagem' =>
                        $dadosImagem['url_imagem'],

                    ':texto_alternativo' =>
                        $textoAlternativo !== ''
                            ? $textoAlternativo
                            : null,

                    ':posicao_x' =>
                        $posicaoX,

                    ':posicao_y' =>
                        $posicaoY,

                    ':ativo' =>
                        $ativo === 1
                            ? 1
                            : 0,

                    ':id' =>
                        $id,
                ]);

            } catch (
                \Throwable $erro
            ) {

                if (
                    is_file(
                        $dadosImagem['caminho']
                    )
                ) {

                    @unlink(
                        $dadosImagem['caminho']
                    );
                }

                throw $erro;
            }


            /*
            -----------------------------
            REMOVE IMAGEM ANTIGA
            -----------------------------
            */

            $arquivoAntigo =
                APP_ROOT
                . '/public'
                . $destaque[
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

        } else {

            /*
            =================================
            APENAS DADOS / POSIÇÃO
            =================================
            */

            $stmt =
                $pdo->prepare("
                    UPDATE categoria_banners
                    SET
                        categoria_id =
                            :categoria_id,

                        texto_alternativo =
                            :texto_alternativo,

                        posicao_x =
                            :posicao_x,

                        posicao_y =
                            :posicao_y,

                        ativo =
                            :ativo

                    WHERE id = :id
                ");


            $stmt->execute([
                ':categoria_id' =>
                    $categoriaId,

                ':texto_alternativo' =>
                    $textoAlternativo !== ''
                        ? $textoAlternativo
                        : null,

                ':posicao_x' =>
                    $posicaoX,

                ':posicao_y' =>
                    $posicaoY,

                ':ativo' =>
                    $ativo === 1
                        ? 1
                        : 0,

                ':id' =>
                    $id,
            ]);
        }


        $this->redirecionar(
            '/admin/destaques'
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

        $token =
            isset($_POST['_token'])
                ? (string) $_POST['_token']
                : null;


        if (!Csrf::validar($token)) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                SELECT
                    url_imagem
                FROM categoria_banners
                WHERE id = :id
                LIMIT 1
            ");


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $destaque =
            $stmt->fetch();


        if (
            $destaque === false
        ) {

            $this->redirecionar(
                '/admin/destaques'
            );
        }


        $arquivo =
            APP_ROOT
            . '/public'
            . $destaque[
                'url_imagem'
            ];


        $stmtExcluir =
            $pdo->prepare("
                DELETE FROM categoria_banners
                WHERE id = :id
            ");


        $stmtExcluir->execute([
            ':id' =>
                $id,
        ]);


        if (
            is_file(
                $arquivo
            )
        ) {

            @unlink(
                $arquivo
            );
        }


        $this->redirecionar(
            '/admin/destaques'
        );
    }


    /*
    =================================
    ATIVA / DESATIVA
    =================================
    */

    public function alternarAtivo(
        int $id
    ): void {

        $token =
            isset($_POST['_token'])
                ? (string) $_POST['_token']
                : null;


        if (!Csrf::validar($token)) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                UPDATE categoria_banners
                SET
                    ativo =
                        CASE
                            WHEN ativo = 1
                            THEN 0
                            ELSE 1
                        END
                WHERE id = :id
            ");


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $this->redirecionar(
            '/admin/destaques'
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
    PROCESSA UPLOAD
    =================================
    */

    private function processarUpload(
        int $destaqueId
    ): array {

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


        /*
        =================================
        PASTA
        =================================
        */

        $pasta =
            APP_ROOT
            . '/public/assets/uploads/destaques';


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
                'Não foi possível criar a pasta de imagens de destaque.'
            );
        }


        /*
        =================================
        NOME DO ARQUIVO
        =================================
        */

        $prefixo =
            $destaqueId > 0
                ? 'destaque_' . $destaqueId
                : 'destaque_novo';


        $nomeArquivo =
            $prefixo
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


        return [
            'url_imagem' =>
                '/assets/uploads/destaques/'
                . $nomeArquivo,

            'caminho' =>
                $destino,
        ];
    }
}