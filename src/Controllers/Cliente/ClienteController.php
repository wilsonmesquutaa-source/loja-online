<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\ClienteRepository;
use App\Services\GoogleAuthService;

final class ClienteController extends Controller
{
    /*
    =================================
    LOGIN
    =================================
    */

    public function login(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }

        if (
            !empty(
                $_SESSION['cliente_id']
            )
        ) {

            /*
            =================================
            CLIENTE JÁ LOGADO
            VOLTA PARA A LOJA
            =================================
            */

            header(
                'Location: '
                . BASE_URL
                . '/'
            );

            exit;
        }


        $this->view(
            'site/cliente_login',
            [
                'tituloPagina' =>
                    'Entrar',

                'rotaAtual' =>
                    'login',

                'erro' =>
                    null,

                'email' =>
                    '',
            ]
        );
    }


    /*
    =================================
    AUTENTICAÇÃO
    =================================
    */

    public function autenticar(): void
    {
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

            $this->mostrarLoginComErro(
                'A sessão do formulário expirou. Atualize a página e tente novamente.',
                ''
            );
        }


        $email =
            trim(
                strtolower(
                    (string) (
                        $_POST['email']
                        ?? ''
                    )
                )
            );


        $senha =
            (string) (
                $_POST['senha']
                ?? ''
            );


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $this->mostrarLoginComErro(
                'Informe um e-mail válido.',
                $email
            );
        }


        if (
            $senha === ''
        ) {

            $this->mostrarLoginComErro(
                'Informe sua senha.',
                $email
            );
        }


        $repository =
            new ClienteRepository(
                $this->pdo
            );


        $cliente =
            $repository->buscarPorEmail(
                $email
            );


        /*
        =================================
        CREDENCIAIS INVÁLIDAS
        =================================
        */

        if (
            $cliente === null
            ||
            empty(
                $cliente['senha_hash']
            )
            ||
            !password_verify(
                $senha,
                $cliente['senha_hash']
            )
        ) {

            $this->mostrarLoginComErro(
                'E-mail ou senha inválidos.',
                $email
            );
        }


        /*
        =================================
        SESSÃO
        =================================
        */

        $this->iniciarSessaoCliente(
            (int)
                $cliente['id'],

            (string)
                $cliente['nome'],

            (string)
                $cliente['email'],

            !empty(
                $cliente['foto_url']
            )
                ? (string)
                    $cliente['foto_url']
                : null
        );


        /*
        =================================
        ATUALIZA ACESSO
        =================================
        */

        $repository->atualizarUltimoAcesso(
            (int)
                $cliente['id']
        );


        /*
        =================================
        RENOVA CSRF
        =================================
        */

        Csrf::renovarCliente();


        /*
        =================================
        VOLTA PARA A LOJA
        =================================
        */

        header(
            'Location: '
            . BASE_URL
            . '/'
        );

        exit;
    }


    /*
    =================================
    LOGOUT
    =================================
    */

    public function logout(): void
    {
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

            http_response_code(403);

            exit(
                'Token CSRF inválido.'
            );
        }


        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
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
                time() - 42000,
                $parametros['path'],
                $parametros['domain'] ?? '',
                (bool)
                    $parametros['secure'],
                (bool)
                    $parametros['httponly']
            );
        }


        session_destroy();


        /*
        =================================
        VOLTA PARA A LOJA
        =================================
        */

        header(
            'Location: '
            . BASE_URL
            . '/'
        );

        exit;
    }


    /*
    =================================
    ERRO DO LOGIN
    =================================
    */

    private function mostrarLoginComErro(
        string $mensagem,
        string $email
    ): never {

        $this->view(
            'site/cliente_login',
            [
                'tituloPagina' =>
                    'Entrar',

                'rotaAtual' =>
                    'login',

                'erro' =>
                    $mensagem,

                'email' =>
                    $email,
            ]
        );

        exit;
    }


    /*
    =================================
    CADASTRO
    =================================
    */

    public function cadastro(): void
    {
        $this->view(
            'site/cliente_cadastro',
            [
                'tituloPagina' =>
                    'Criar conta',

                'rotaAtual' =>
                    'cadastro',

                'erro' =>
                    null,

                'nome' =>
                    '',

                'email' =>
                    '',
            ]
        );
    }


    /*
    =================================
    CADASTRO TRADICIONAL
    =================================
    */

    public function registrar(): void
    {
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

            $this->mostrarCadastroComErro(
                'A sessão do formulário expirou. Atualize a página e tente novamente.',
                '',
                ''
            );
        }


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


        $senha =
            (string) (
                $_POST['senha']
                ?? ''
            );


        $senhaConfirmacao =
            (string) (
                $_POST['senha_confirmacao']
                ?? ''
            );


        if (
            $nome === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe seu nome completo.',
                $nome,
                $email
            );
        }


        if (
            mb_strlen(
                $nome,
                'UTF-8'
            ) > 150
        ) {

            $this->mostrarCadastroComErro(
                'O nome informado é muito longo.',
                $nome,
                $email
            );
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $this->mostrarCadastroComErro(
                'Informe um e-mail válido.',
                $nome,
                $email
            );
        }


        if (
            strlen($email) > 180
        ) {

            $this->mostrarCadastroComErro(
                'O e-mail informado é muito longo.',
                $nome,
                $email
            );
        }


        if (
            strlen($senha) < 8
        ) {

            $this->mostrarCadastroComErro(
                'A senha deve ter pelo menos 8 caracteres.',
                $nome,
                $email
            );
        }


        if (
            $senha !==
            $senhaConfirmacao
        ) {

            $this->mostrarCadastroComErro(
                'As senhas não coincidem.',
                $nome,
                $email
            );
        }


        $repository =
            new ClienteRepository(
                $this->pdo
            );


        if (
            $repository->emailExiste(
                $email
            )
        ) {

            $this->mostrarCadastroComErro(
                'Já existe uma conta cadastrada com este e-mail.',
                $nome,
                $email
            );
        }


        $senhaHash =
            password_hash(
                $senha,
                PASSWORD_DEFAULT
            );


        if (
            $senhaHash === false
        ) {

            $this->mostrarCadastroComErro(
                'Não foi possível proteger sua senha. Tente novamente.',
                $nome,
                $email
            );
        }


        $clienteId =
            $repository->criar(
                $nome,
                $email,
                $senhaHash
            );


        /*
        =================================
        CADASTRO TRADICIONAL
        SEM FOTO
        =================================
        */

        $this->iniciarSessaoCliente(
            $clienteId,
            $nome,
            $email,
            null
        );


        Csrf::renovarCliente();


        /*
        =================================
        CADASTRO → LOJA
        =================================
        */

        header(
            'Location: '
            . BASE_URL
            . '/'
        );

        exit;
    }


    /*
    =================================
    INICIA CADASTRO GOOGLE
    =================================
    */

    public function iniciarCadastroGoogle(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        $state =
            bin2hex(
                random_bytes(32)
            );


        $_SESSION['google_oauth_state'] =
            $state;


        $_SESSION['google_oauth_action'] =
            'cadastro';


        $google =
            new GoogleAuthService();


        $url =
            $google->criarUrlAutorizacao(
                $state
            );


        header(
            'Location: '
            . $url
        );

        exit;
    }


    /*
    =================================
    INICIA LOGIN GOOGLE
    =================================
    */

    public function iniciarLoginGoogle(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        $state =
            bin2hex(
                random_bytes(32)
            );


        $_SESSION['google_oauth_state'] =
            $state;


        $_SESSION['google_oauth_action'] =
            'login';


        $google =
            new GoogleAuthService();


        $url =
            $google->criarUrlAutorizacao(
                $state
            );


        header(
            'Location: '
            . $url
        );

        exit;
    }


    /*
    =================================
    CALLBACK GOOGLE
    =================================
    */

    public function callbackCadastroGoogle(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        /*
        =================================
        VALIDA STATE
        =================================
        */

        $stateRecebido =
            isset($_GET['state'])
                ? (string)
                    $_GET['state']
                : '';


        $stateEsperado =
            isset(
                $_SESSION['google_oauth_state']
            )
                ? (string)
                    $_SESSION[
                        'google_oauth_state'
                    ]
                : '';


        $acao =
            isset(
                $_SESSION[
                    'google_oauth_action'
                ]
            )
                ? (string)
                    $_SESSION[
                        'google_oauth_action'
                    ]
                : '';


        unset(
            $_SESSION[
                'google_oauth_state'
            ],
            $_SESSION[
                'google_oauth_action'
            ]
        );


        if (
            $stateRecebido === ''
            ||
            $stateEsperado === ''
            ||
            !hash_equals(
                $stateEsperado,
                $stateRecebido
            )
        ) {

            $this->mostrarErroGoogle(
                'Não foi possível validar a autenticação com o Google.'
            );
        }


        /*
        =================================
        GOOGLE CANCELADO
        =================================
        */

        if (
            isset($_GET['error'])
        ) {

            $this->mostrarErroGoogle(
                'A autenticação com o Google foi cancelada ou não pôde ser concluída.'
            );
        }


        /*
        =================================
        VERIFICA AÇÃO
        =================================
        */

        if (
            $acao !== 'login'
            &&
            $acao !== 'cadastro'
        ) {

            $this->mostrarErroGoogle(
                'A ação de autenticação não pôde ser identificada.'
            );
        }


        /*
        =================================
        CODE
        =================================
        */

        $code =
            isset($_GET['code'])
                ? (string)
                    $_GET['code']
                : '';


        if (
            $code === ''
        ) {

            $this->mostrarErroGoogle(
                'O Google não retornou o código de autorização.'
            );
        }


        /*
        =================================
        TROCA CODE POR TOKEN
        =================================
        */

        try {

            $google =
                new GoogleAuthService();


            $token =
                $google->trocarCodigoPorToken(
                    $code
                );


            $dadosGoogle =
                $google->buscarUsuario(
                    (string)
                        $token['access_token']
                );

        } catch (
            \RuntimeException $erro
        ) {

            $this->mostrarErroGoogle(
                $erro->getMessage()
            );
        }


        /*
        =================================
        DADOS GOOGLE
        =================================
        */

        $googleSub =
            (string)
                $dadosGoogle['google_sub'];


        $email =
            (string)
                $dadosGoogle['email'];


        $nome =
            (string)
                $dadosGoogle['nome'];


        $fotoUrl =
            !empty(
                $dadosGoogle['foto_url']
            )
                ? (string)
                    $dadosGoogle['foto_url']
                : null;


        $emailVerificado =
            (bool)
                $dadosGoogle['email_verificado'];


        $repository =
            new ClienteRepository(
                $this->pdo
            );


        /*
        =================================
        PROCURA GOOGLE SUB
        =================================
        */

        $cliente =
            $repository->buscarPorGoogleSub(
                $googleSub
            );


        /*
        =================================
        LOGIN GOOGLE
        =================================
        */

        if (
            $acao === 'login'
        ) {

            if (
                $cliente === null
            ) {

                $clientePorEmail =
                    $repository->buscarPorEmail(
                        $email
                    );


                if (
                    $clientePorEmail !== null
                ) {

                    $this->mostrarErroGoogle(
                        'Esta conta já existe, mas ainda não está vinculada ao Google. Entre com sua senha ou utilize o cadastro com Google para fazer a vinculação.'
                    );
                }


                $this->mostrarErroGoogle(
                    'Não encontramos uma conta vinculada a este Google. Para criar uma conta, utilize "Cadastrar com Google".'
                );
            }


            /*
            ==============================
            LOGIN GOOGLE
            ==============================
            */

            $repository->atualizarUltimoAcesso(
                (int)
                    $cliente['id']
            );


            $this->iniciarSessaoCliente(
                (int)
                    $cliente['id'],

                (string)
                    $cliente['nome'],

                (string)
                    $cliente['email'],

                !empty(
                    $cliente['foto_url']
                )
                    ? (string)
                        $cliente['foto_url']
                    : null
            );


            Csrf::renovarCliente();


            /*
            =================================
            LOGIN GOOGLE → LOJA
            =================================
            */

            header(
                'Location: '
                . BASE_URL
                . '/'
            );

            exit;
        }


        /*
        =================================
        CADASTRO GOOGLE
        =================================
        */

        if (
            $cliente !== null
        ) {

            $repository->atualizarUltimoAcesso(
                (int)
                    $cliente['id']
            );


            $this->iniciarSessaoCliente(
                (int)
                    $cliente['id'],

                (string)
                    $cliente['nome'],

                (string)
                    $cliente['email'],

                !empty(
                    $cliente['foto_url']
                )
                    ? (string)
                        $cliente['foto_url']
                    : $fotoUrl
            );


            Csrf::renovarCliente();


            /*
            =================================
            GOOGLE JÁ CADASTRADO → LOJA
            =================================
            */

            header(
                'Location: '
                . BASE_URL
                . '/'
            );

            exit;
        }


        /*
        =================================
        PROCURA PELO E-MAIL
        =================================
        */

        $clientePorEmail =
            $repository->buscarPorEmail(
                $email
            );


        if (
            $clientePorEmail !== null
        ) {

            /*
            ==============================
            JÁ POSSUI GOOGLE DIFERENTE
            ==============================
            */

            if (
                !empty(
                    $clientePorEmail['google_sub']
                )
            ) {

                $this->mostrarErroGoogle(
                    'Este e-mail já está associado a outra conta Google.'
                );
            }


            /*
            ==============================
            VINCULA GOOGLE
            ==============================
            */

            $repository->vincularGoogle(
                (int)
                    $clientePorEmail['id'],

                $googleSub,

                $fotoUrl,

                $emailVerificado
            );


            $this->iniciarSessaoCliente(
                (int)
                    $clientePorEmail['id'],

                (string)
                    $clientePorEmail['nome'],

                (string)
                    $clientePorEmail['email'],

                $fotoUrl
            );


            Csrf::renovarCliente();


            /*
            =================================
            GOOGLE VINCULADO → LOJA
            =================================
            */

            header(
                'Location: '
                . BASE_URL
                . '/'
            );

            exit;
        }


        /*
        =================================
        CRIA NOVA CONTA GOOGLE
        =================================
        */

        $clienteId =
            $repository->criarComGoogle(
                $googleSub,
                $nome,
                $email,
                $fotoUrl,
                $emailVerificado
            );


        $this->iniciarSessaoCliente(
            $clienteId,
            $nome,
            $email,
            $fotoUrl
        );


        Csrf::renovarCliente();


        /*
        =================================
        NOVO CADASTRO GOOGLE → LOJA
        =================================
        */

        header(
            'Location: '
            . BASE_URL
            . '/'
        );

        exit;
    }


    /*
    =================================
    ERRO GOOGLE
    =================================
    */

    private function mostrarErroGoogle(
        string $mensagem
    ): never {

        $this->view(
            'site/cliente_login',
            [
                'tituloPagina' =>
                    'Entrar',

                'rotaAtual' =>
                    'login',

                'erro' =>
                    $mensagem,

                'email' =>
                    '',
            ]
        );

        exit;
    }


    /*
    =================================
    INICIA SESSÃO DO CLIENTE
    =================================
    */

    private function iniciarSessaoCliente(
        int $clienteId,
        string $nome,
        string $email,
        ?string $fotoUrl = null
    ): void {

        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        session_regenerate_id(
            true
        );


        $_SESSION['cliente_id'] =
            $clienteId;


        $_SESSION['cliente_nome'] =
            $nome;


        $_SESSION['cliente_email'] =
            $email;


        $_SESSION['cliente_foto_url'] =
            $fotoUrl;
    }


    /*
    =================================
    ERRO NO CADASTRO
    =================================
    */

    private function mostrarCadastroComErro(
        string $mensagem,
        string $nome,
        string $email
    ): never {

        $this->view(
            'site/cliente_cadastro',
            [
                'tituloPagina' =>
                    'Criar conta',

                'rotaAtual' =>
                    'cadastro',

                'erro' =>
                    $mensagem,

                'nome' =>
                    $nome,

                'email' =>
                    $email,
            ]
        );

        exit;
    }
}