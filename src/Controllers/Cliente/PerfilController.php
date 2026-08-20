<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\ClienteRepository;

final class PerfilController extends Controller
{
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

        $repository =
            new ClienteRepository(
                $this->pdo
            );

        $cliente =
            $repository->buscarPorId(
                (int)
                $_SESSION['cliente_id']
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

        $clienteId =
            (int)
            $_SESSION['cliente_id'];

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

        $repository =
            new ClienteRepository(
                $this->pdo
            );

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

        $repository->atualizarDados(
            $clienteId,
            $nome,
            $email
        );

        /*
        =================================
        FOTO
        =================================
        */

        $fotoAtual =
            null;

        $clienteAtual =
            $repository->buscarPorId(
                $clienteId
            );

        if (
            $clienteAtual !== null
            &&
            !empty(
                $clienteAtual['foto_url']
            )
        ) {
            $fotoAtual =
                (string)
                $clienteAtual['foto_url'];
        }

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

            $mime =
                mime_content_type(
                    $arquivo['tmp_name']
                );

            $tiposPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png' =>
                    'png',
                'image/webp' =>
                    'webp',
            ];

            if (
                !isset(
                    $tiposPermitidos[$mime]
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

            $diretorio =
                APP_ROOT
                . '/public/assets/uploads/clientes';

            if (
                !is_dir(
                    $diretorio
                )
            ) {
                mkdir(
                    $diretorio,
                    0755,
                    true
                );
            }

            $nomeArquivo =
                'cliente_'
                . $clienteId
                . '_'
                . bin2hex(
                    random_bytes(8)
                )
                . '.'
                . $tiposPermitidos[$mime];

            $caminhoArquivo =
                $diretorio
                . '/'
                . $nomeArquivo;

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

            $fotoUrl =
                BASE_URL
                . '/assets/uploads/clientes/'
                . $nomeArquivo;

            $repository->atualizarFoto(
                $clienteId,
                $fotoUrl
            );

            $_SESSION[
                'cliente_foto_url'
            ] =
                $fotoUrl;
        } else {

            $_SESSION[
                'cliente_foto_url'
            ] =
                $fotoAtual;
        }

        $_SESSION['cliente_nome'] =
            $nome;

        $_SESSION['cliente_email'] =
            $email;

        Csrf::renovarCliente();

        $this->redirecionar(
            '/cliente/perfil?sucesso='
            . rawurlencode(
                'Seus dados foram atualizados com sucesso.'
            )
        );
    }
}