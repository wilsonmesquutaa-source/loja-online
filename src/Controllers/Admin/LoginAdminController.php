<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\UsuarioAdminRepository;
use PDO;

final class LoginAdminController
    extends Controller
{
    private UsuarioAdminRepository
        $usuarios;

    public function __construct()
    {
        $pdo = require APP_ROOT
            . '/database/conexao.php';

        if (!$pdo instanceof PDO) {
            throw new \RuntimeException(
                'A conexão não retornou um objeto PDO.'
            );
        }

        $this->usuarios =
            new UsuarioAdminRepository(
                $pdo
            );
    }

    public function formulario(): void
    {
        if (
            !empty(
                $_SESSION[
                    'usuario_admin'
                ]['id']
            )
        ) {
            $this->redirecionar(
                '/admin'
            );
        }

        $erro = $_SESSION[
            'login_admin_erro'
        ] ?? null;

        $email = $_SESSION[
            'login_admin_email'
        ] ?? '';

        unset(
            $_SESSION[
                'login_admin_erro'
            ],
            $_SESSION[
                'login_admin_email'
            ]
        );

        $this->view(
            'site/loginadmin',
            [
                'tituloPagina' =>
                    'Login administrativo',

                'erro' =>
                    $erro,

                'email' =>
                    $email,

                'csrfToken' =>
                    Csrf::gerar(),
            ]
        );
    }

    public function autenticar(): void
    {
        $token = isset(
            $_POST['_token']
        )
            ? (string)
                $_POST['_token']
            : null;

        if (!Csrf::validar($token)) {
            $this->falhar(
                'O formulário expirou. '
                . 'Atualize a página '
                . 'e tente novamente.'
            );
        }

        $email = strtolower(
            trim(
                (string) (
                    $_POST['email']
                        ?? ''
                )
            )
        );

        $senha = (string) (
            $_POST['senha']
                ?? ''
        );

        if (
            filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ) === false
            || $senha === ''
        ) {
            $this->falhar(
                'Informe um e-mail '
                . 'e uma senha válidos.',
                $email
            );
        }

        $usuario = $this
            ->usuarios
            ->buscarAtivoPorEmail(
                $email
            );

        $senhaCorreta =
            $usuario !== null
            && password_verify(
                $senha,
                (string)
                    $usuario[
                        'senha_hash'
                    ]
            );

        if (!$senhaCorreta) {
            $this->falhar(
                'E-mail ou senha inválidos.',
                $email
            );
        }

        if (
            password_needs_rehash(
                (string)
                    $usuario[
                        'senha_hash'
                    ],
                PASSWORD_DEFAULT
            )
        ) {
            $novoHash =
                password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

            $this
                ->usuarios
                ->atualizarHashSenha(
                    (int)
                        $usuario['id'],
                    $novoHash
                );
        }

        session_regenerate_id(
            true
        );

        $_SESSION[
            'usuario_admin'
        ] = [
            'id' =>
                (int) $usuario['id'],

            'nome' =>
                (string) $usuario['nome'],

            'email' =>
                (string) $usuario['email'],

            'autenticado_em' =>
                time(),
        ];

        $this
            ->usuarios
            ->registrarUltimoAcesso(
                (int) $usuario['id']
            );

        Csrf::renovar();

        $this->redirecionar(
            '/admin'
        );
    }

    public function sair(): void
    {
        $token = isset(
            $_POST['_token']
        )
            ? (string)
                $_POST['_token']
            : null;

        if (!Csrf::validar($token)) {
            http_response_code(403);

            exit(
                'Solicitação de logout inválida.'
            );
        }

        $_SESSION = [];

        if (
            ini_get(
                'session.use_cookies'
            )
        ) {
            $parametros =
                session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires' =>
                        time() - 42000,

                    'path' =>
                        $parametros[
                            'path'
                        ],

                    'domain' =>
                        $parametros[
                            'domain'
                        ],

                    'secure' =>
                        $parametros[
                            'secure'
                        ],

                    'httponly' =>
                        $parametros[
                            'httponly'
                        ],

                    'samesite' =>
                        'Lax',
                ]
            );
        }

        session_destroy();

        $this->redirecionar(
            '/login-admin'
        );
    }

    private function falhar(
        string $mensagem,
        string $email = ''
    ): never {
        $_SESSION[
            'login_admin_erro'
        ] = $mensagem;

        $_SESSION[
            'login_admin_email'
        ] = $email;

        $this->redirecionar(
            '/login-admin'
        );
    }
}
