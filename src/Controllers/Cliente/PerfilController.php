<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\ClienteRepository;

final class PerfilController extends Controller
{
    /*
    =================================
    EDITAR PERFIL
    =================================
    */

    public function index(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }


        if (
            empty(
                $_SESSION['cliente_id']
            )
        ) {
            $this->redirecionar(
                '/login'
            );

            return;
        }


        $clienteId =
            (int)
            $_SESSION['cliente_id'];


        $repository =
            new ClienteRepository(
                $this->pdo
            );


        $cliente =
            $repository->buscarPorId(
                $clienteId
            );


        if (
            $cliente === null
        ) {
            $_SESSION = [];

            $this->redirecionar(
                '/login'
            );

            return;
        }


        /*
        =================================
        GARANTE FOTO NA SESSÃO
        =================================
        */

        $_SESSION[
            'cliente_foto_url'
        ] =
            !empty(
                $cliente['foto_url']
            )
                ? (string)
                    $cliente['foto_url']
                : null;


        /*
        =================================
        VIEW
        =================================
        */

        $this->view(
            'cliente/perfil',
            [
                'tituloPagina' =>
                    'Editar perfil',

                'rotaAtual' =>
                    'perfil',

                'cliente' =>
                    $cliente,

                'csrfToken' =>
                    Csrf::gerarCliente(),
            ]
        );
    }


    /*
    =================================
    ATUALIZAR PERFIL
    =================================
    */

    public function atualizar(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }


        if (
            empty(
                $_SESSION['cliente_id']
            )
        ) {
            $this->redirecionar(
                '/login'
            );

            return;
        }


        /*
        =================================
        CSRF
        =================================
        */

        $tokenCsrf =
            isset(
                $_POST['_csrf']
            )
                ? (string)
                    $_POST['_csrf']
                : null;


        if (
            !Csrf::validarCliente(
                $tokenCsrf
            )
        ) {
            $this->redirecionar(
                '/cliente/perfil?erro='
                . rawurlencode(
                    'A sessão do formulário expirou. Atualize a página e tente novamente.'
                )
            );

            return;
        }


        /*
        =================================
        CLIENTE
        =================================
        */

        $clienteId =
            (int)
            $_SESSION['cliente_id'];


        $repository =
            new ClienteRepository(
                $this->pdo
            );


        $clienteAtual =
            $repository->buscarPorId(
                $clienteId
            );


        if (
            $clienteAtual === null
        ) {
            $_SESSION = [];

            $this->redirecionar(
                '/login'
            );

            return;
        }


        /*
        =================================
        DADOS
        =================================
        */

        $nome =
            trim(
                (string) (
                    $_POST['nome']
                    ?? ''
                )
            );


        $email =
            trim(
                strtolower(
                    (string) (
                        $_POST['email']
                        ?? ''
                    )
                )
            );


        /*
        =================================
        VALIDA NOME
        =================================
        */

        if (
            $nome === ''
        ) {
            $this->redirecionar(
                '/cliente/perfil?erro='
                . rawurlencode(
                    'Informe seu nome completo.'
                )
            );

            return;
        }


        if (
            mb_strlen(
                $nome,
                'UTF-8'
            ) > 150
        ) {
            $this->redirecionar(
                '/cliente/perfil?erro='
                . rawurlencode(
                    'O nome informado é muito longo.'
                )
            );

            return;
        }


        /*
        =================================
        VALIDA E-MAIL
        =================================
        */

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->redirecionar(
                '/cliente/perfil?erro='
                . rawurlencode(
                    'Informe um e-mail válido.'
                )
            );

            return;
        }


        if (
            strlen($email) > 180
        ) {
            $this->redirecionar(
                '/cliente/perfil?erro='
                . rawurlencode(
                    'O e-mail informado é muito longo.'
                )
            );

            return;
        }


        /*
        =================================
        E-MAIL DUPLICADO
        =================================
        */

        if (
            $repository
                ->emailExisteParaOutroCliente(
                    $email,
                    $clienteId
                )
        ) {
            $this->redirecionar(
                '/cliente/perfil?erro='
                . rawurlencode(
                    'Este e-mail já está sendo usado por outra conta.'
                )
            );

            return;
        }


        /*
        =================================
        FOTO ATUAL
        =================================
        */

        $fotoUrl =
            !empty(
                $clienteAtual['foto_url']
            )
                ? (string)
                    $clienteAtual['foto_url']
                : null;


        /*
        =================================
        NOVA FOTO
        =================================
        */

        if (
            isset(
                $_FILES['foto']
            )
            &&
            $_FILES['foto']['error']
                !== UPLOAD_ERR_NO_FILE
        ) {

            $arquivo =
                $_FILES['foto'];


            /*
            ==============================
            ERRO UPLOAD
            ==============================
            */

            if (
                $arquivo['error']
                !== UPLOAD_ERR_OK
            ) {
                $this->redirecionar(
                    '/cliente/perfil?erro='
                    . rawurlencode(
                        'Não foi possível enviar a foto.'
                    )
                );

                return;
            }


            /*
            ==============================
            ARQUIVO VÁLIDO
            ==============================
            */

            if (
                !is_uploaded_file(
                    $arquivo['tmp_name']
                )
            ) {
                $this->redirecionar(
                    '/cliente/perfil?erro='
                    . rawurlencode(
                        'Arquivo de imagem inválido.'
                    )
                );

                return;
            }


            /*
            ==============================
            TAMANHO
            ==============================
            */

            if (
                $arquivo['size']
                > 5 * 1024 * 1024
            ) {
                $this->redirecionar(
                    '/cliente/perfil?erro='
                    . rawurlencode(
                        'A foto deve ter no máximo 5 MB.'
                    )
                );

                return;
            }


            /*
            ==============================
            MIME
            ==============================
            */

            $mime =
                mime_content_type(
                    $arquivo['tmp_name']
                );


            $tiposPermitidos = [
                'image/jpeg' =>
                    'jpg',

                'image/png' =>
                    'png',

                'image/webp' =>
                    'webp',
            ];


            if (
                !isset(
                    $tiposPermitidos[
                        $mime
                    ]
                )
            ) {
                $this->redirecionar(
                    '/cliente/perfil?erro='
                    . rawurlencode(
                        'Use uma imagem JPG, PNG ou WEBP.'
                    )
                );

                return;
            }


            /*
            ==============================
            DIRETÓRIO
            ==============================
            */

            $diretorio =
                APP_ROOT
                . '/public/assets/uploads/clientes';


            if (
                !is_dir(
                    $diretorio
                )
            ) {
                if (
                    !mkdir(
                        $diretorio,
                        0755,
                        true
                    )
                    &&
                    !is_dir(
                        $diretorio
                    )
                ) {
                    $this->redirecionar(
                        '/cliente/perfil?erro='
                        . rawurlencode(
                            'Não foi possível criar o diretório das fotos.'
                        )
                    );

                    return;
                }
            }


            /*
            ==============================
            NOME
            ==============================
            */

            $extensao =
                $tiposPermitidos[
                    $mime
                ];


            $nomeArquivo =
                'cliente_'
                . $clienteId
                . '_'
                . bin2hex(
                    random_bytes(8)
                )
                . '.'
                . $extensao;


            $caminhoArquivo =
                $diretorio
                . DIRECTORY_SEPARATOR
                . $nomeArquivo;


            /*
            ==============================
            MOVE ARQUIVO
            ==============================
            */

            if (
                !move_uploaded_file(
                    $arquivo['tmp_name'],
                    $caminhoArquivo
                )
            ) {
                $this->redirecionar(
                    '/cliente/perfil?erro='
                    . rawurlencode(
                        'Não foi possível salvar a foto.'
                    )
                );

                return;
            }


            /*
            ==============================
            URL PÚBLICA
            ==============================
            */

            $fotoUrl =
                BASE_URL
                . '/assets/uploads/clientes/'
                . $nomeArquivo;


            /*
            ==============================
            ATUALIZA FOTO NO BANCO
            ==============================
            */

            $repository->atualizarFoto(
                $clienteId,
                $fotoUrl
            );


            /*
            ==============================
            REMOVE FOTO ANTIGA
            ==============================
            */

            $fotoAntiga =
                $clienteAtual['foto_url']
                ?? null;


            if (
                !empty(
                    $fotoAntiga
                )
                &&
                $fotoAntiga !==
                    $fotoUrl
            ) {

                $caminhoFotoAntiga =
                    APP_ROOT
                    . '/public'
                    . $fotoAntiga;


                if (
                    is_file(
                        $caminhoFotoAntiga
                    )
                ) {
                    @unlink(
                        $caminhoFotoAntiga
                    );
                }
            }
        }


        /*
        =================================
        ATUALIZA NOME E E-MAIL
        =================================
        */

        $repository->atualizarDados(
            $clienteId,
            $nome,
            $email
        );


        /*
        =================================
        ATUALIZA SESSÃO
        =================================
        */

        $_SESSION[
            'cliente_nome'
        ] =
            $nome;


        $_SESSION[
            'cliente_email'
        ] =
            $email;


        $_SESSION[
            'cliente_foto_url'
        ] =
            $fotoUrl;


        /*
        =================================
        RENOVA CSRF
        =================================
        */

        Csrf::renovarCliente();


        /*
        =================================
        SUCESSO
        =================================
        */

        $this->redirecionar(
            '/cliente/perfil?sucesso='
            . rawurlencode(
                'Seus dados foram atualizados com sucesso.'
            )
        );
    }
}