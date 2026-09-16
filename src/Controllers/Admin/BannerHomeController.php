<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use RuntimeException;

final class BannerHomeController extends Controller
{
    /*
    =================================
    LISTAGEM
    =================================
    */

    public function index(): void
    {
        $mensagemSucesso = isset($_SESSION['admin_banner_home_sucesso'])
            ? (string) $_SESSION['admin_banner_home_sucesso']
            : null;

        unset($_SESSION['admin_banner_home_sucesso']);

        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $stmt = $pdo->query("
            SELECT
                *
            FROM banners_home
            ORDER BY
                ordem ASC,
                id ASC
        ");


        $banners = $stmt->fetchAll();


        $this->view(
            'admin/banners-home',
            [
                'tituloPagina' =>
                    'Banners da Home',

                'banners' =>
                    $banners,

                'csrfToken' =>
                    Csrf::gerar(),

                'mensagemSucesso' =>
                    $mensagemSucesso,
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
        $this->view(
            'admin/banner-home-form',
            [
                'tituloPagina' =>
                    'Novo Banner da Home',

                'banner' =>
                    null,

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


        $titulo = trim(
            (string) (
                $_POST['titulo']
                ?? ''
            )
        );


        $textoAlternativo = trim(
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


        $ordem = max(
            1,
            (int) (
                $_POST['ordem']
                ?? 1
            )
        );


        $ativo =
            isset($_POST['ativo'])
                ? (int) $_POST['ativo']
                : 1;


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
                'Selecione uma imagem para o banner.'
            );
        }


        /*
        =================================
        PROCESSA IMAGEM
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

            $stmt = $pdo->prepare("
                INSERT INTO banners_home
                (
                    titulo,
                    texto_alternativo,
                    url_imagem,
                    posicao_x,
                    posicao_y,
                    ordem,
                    ativo
                )
                VALUES
                (
                    :titulo,
                    :texto_alternativo,
                    :url_imagem,
                    :posicao_x,
                    :posicao_y,
                    :ordem,
                    :ativo
                )
            ");


            $stmt->execute([
                ':titulo' =>
                    $titulo !== ''
                        ? $titulo
                        : null,

                ':texto_alternativo' =>
                    $textoAlternativo !== ''
                        ? $textoAlternativo
                        : null,

                ':url_imagem' =>
                    $dadosImagem[
                        'url_imagem'
                    ],

                ':posicao_x' =>
                    $posicaoX,

                ':posicao_y' =>
                    $posicaoY,

                ':ordem' =>
                    $ordem,

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

        $this->definirMensagemSucesso('Banner cadastrado com sucesso.');

        $this->redirecionar(
            '/admin/banners-home'
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

        $pdo = require APP_ROOT
            . '/database/conexao.php';


        $stmt = $pdo->prepare("
            SELECT
                *
            FROM banners_home
            WHERE id = :id
            LIMIT 1
        ");


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $banner =
            $stmt->fetch();


        if (
            $banner === false
        ) {

            $this->redirecionar(
                '/admin/banners-home'
            );
        }


        $this->view(
            'admin/banner-home-form',
            [
                'tituloPagina' =>
                    'Editar Banner da Home',

                'banner' =>
                    $banner,

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


        /*
        =================================
        BUSCA BANNER
        =================================
        */

        $stmtExistente = $pdo->prepare("
            SELECT
                *
            FROM banners_home
            WHERE id = :id
            LIMIT 1
        ");


        $stmtExistente->execute([
            ':id' =>
                $id,
        ]);


        $banner =
            $stmtExistente->fetch();


        if (
            $banner === false
        ) {

            $this->redirecionar(
                '/admin/banners-home'
            );
        }


        $titulo = trim(
            (string) (
                $_POST['titulo']
                ?? ''
            )
        );


        $textoAlternativo = trim(
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


        $ordem = max(
            1,
            (int) (
                $_POST['ordem']
                ?? 1
            )
        );


        $ativo =
            isset($_POST['ativo'])
                ? (int) $_POST['ativo']
                : 1;


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

                $stmt = $pdo->prepare("
                    UPDATE banners_home
                    SET
                        titulo =
                            :titulo,

                        texto_alternativo =
                            :texto_alternativo,

                        url_imagem =
                            :url_imagem,

                        posicao_x =
                            :posicao_x,

                        posicao_y =
                            :posicao_y,

                        ordem =
                            :ordem,

                        ativo =
                            :ativo

                    WHERE id = :id
                ");


                $stmt->execute([
                    ':titulo' =>
                        $titulo !== ''
                            ? $titulo
                            : null,

                    ':texto_alternativo' =>
                        $textoAlternativo !== ''
                            ? $textoAlternativo
                            : null,

                    ':url_imagem' =>
                        $dadosImagem[
                            'url_imagem'
                        ],

                    ':posicao_x' =>
                        $posicaoX,

                    ':posicao_y' =>
                        $posicaoY,

                    ':ordem' =>
                        $ordem,

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
                . $banner[
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

        } elseif (
            isset(
                $_POST['excluir_imagem']
            )
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

            $arquivoAntigo =
                APP_ROOT
                . '/public'
                . $banner[
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


            $stmt = $pdo->prepare("
                UPDATE banners_home
                SET
                    titulo =
                        :titulo,

                    texto_alternativo =
                        :texto_alternativo,

                    url_imagem =
                        '',

                    posicao_x =
                        :posicao_x,

                    posicao_y =
                        :posicao_y,

                    ordem =
                        :ordem,

                    ativo =
                        :ativo

                WHERE id = :id
            ");


            $stmt->execute([
                ':titulo' =>
                    $titulo !== ''
                        ? $titulo
                        : null,

                ':texto_alternativo' =>
                    $textoAlternativo !== ''
                        ? $textoAlternativo
                        : null,

                ':posicao_x' =>
                    $posicaoX,

                ':posicao_y' =>
                    $posicaoY,

                ':ordem' =>
                    $ordem,

                ':ativo' =>
                    $ativo === 1
                        ? 1
                        : 0,

                ':id' =>
                    $id,
            ]);

        } else {

            /*
            -----------------------------
            APENAS DADOS
            -----------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE banners_home
                SET
                    titulo =
                        :titulo,

                    texto_alternativo =
                        :texto_alternativo,

                    posicao_x =
                        :posicao_x,

                    posicao_y =
                        :posicao_y,

                    ordem =
                        :ordem,

                    ativo =
                        :ativo

                WHERE id = :id
            ");


            $stmt->execute([
                ':titulo' =>
                    $titulo !== ''
                        ? $titulo
                        : null,

                ':texto_alternativo' =>
                    $textoAlternativo !== ''
                        ? $textoAlternativo
                        : null,

                ':posicao_x' =>
                    $posicaoX,

                ':posicao_y' =>
                    $posicaoY,

                ':ordem' =>
                    $ordem,

                ':ativo' =>
                    $ativo === 1
                        ? 1
                        : 0,

                ':id' =>
                    $id,
            ]);
        }

        $this->definirMensagemSucesso('Alterações do banner salvas com sucesso.');

        $this->redirecionar(
            '/admin/banners-home'
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


        $stmt = $pdo->prepare("
            SELECT
                url_imagem
            FROM banners_home
            WHERE id = :id
            LIMIT 1
        ");


        $stmt->execute([
            ':id' =>
                $id,
        ]);


        $banner =
            $stmt->fetch();


        if (
            $banner === false
        ) {

            $this->redirecionar(
                '/admin/banners-home'
            );
        }


        $arquivo =
            APP_ROOT
            . '/public'
            . $banner[
                'url_imagem'
            ];


        $stmtExcluir = $pdo->prepare("
            DELETE FROM banners_home
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

        $this->definirMensagemSucesso('Banner removido com sucesso.');

        $this->redirecionar(
            '/admin/banners-home'
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


        $stmt = $pdo->prepare("
            UPDATE banners_home
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

        $this->definirMensagemSucesso('Visibilidade do banner atualizada.');

        $this->redirecionar(
            '/admin/banners-home'
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

    private function definirMensagemSucesso(string $mensagem): void
    {
        $_SESSION['admin_banner_home_sucesso'] = $mensagem;
    }

    private function processarUpload(
        int $bannerId
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


        $largura =
            (int) (
                $dimensoes[0]
                ?? 0
            );


        $altura =
            (int) (
                $dimensoes[1]
                ?? 0
            );


        if (
            $largura <= 0
            ||
            $altura <= 0
        ) {

            throw new RuntimeException(
                'Não foi possível identificar as dimensões da imagem.'
            );
        }


        /*
        =================================
        PASTA
        =================================
        */

        $pasta =
            APP_ROOT
            . '/public/assets/uploads/banners-home';


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
                'Não foi possível criar a pasta dos banners da Home.'
            );
        }


        /*
        =================================
        NOME DO ARQUIVO
        =================================
        */

        $prefixo =
            $bannerId > 0
                ? 'banner_home_' . $bannerId
                : 'banner_home_novo';


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
                '/assets/uploads/banners-home/'
                . $nomeArquivo,

            'caminho' =>
                $destino,
        ];
    }
}
