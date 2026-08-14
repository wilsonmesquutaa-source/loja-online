<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\ClienteRepository;
use App\Services\GoogleAuthService;
use RuntimeException;

final class ClienteController extends Controller
{
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
            isset($_POST['_csrf'])
                ? (string) $_POST['_csrf']
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


        $this->iniciarSessaoCliente(
            $clienteId,
            $nome,
            $email
        );


        Csrf::renovarCliente();


        $this->redirecionar(
            'cliente/perfil'
        );
    }


    /*
    =================================
    INICIA GOOGLE OAUTH
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


        $google =
            new GoogleAuthService();


        $url =
            $google->criarUrlAutorizacao(
                $state
            );


        header(
            'Location: ' . $url
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


        $stateRecebido =
            isset($_GET['state'])
                ? (string) $_GET['state']
                : '';


        $stateEsperado =
            isset(
                $_SESSION['google_oauth_state']
            )
                ? (string)
                    $_SESSION['google_oauth_state']
                : '';


        unset(
            $_SESSION['google_oauth_state']
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

            $this->mostrarCadastroComErro(
                'Não foi possível validar a autenticação com o Google.',
                '',
                ''
            );
        }


        if (
            isset($_GET['error'])
        ) {

            $this->mostrarCadastroComErro(
                'O cadastro com Google foi cancelado ou não pôde ser concluído.',
                '',
                ''
            );
        }


        $code =
            isset($_GET['code'])
                ? (string) $_GET['code']
                : '';


        if (
            $code === ''
        ) {

            $this->mostrarCadastroComErro(
                'O Google não retornou o código de autorização.',
                '',
                ''
            );
        }


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
            RuntimeException $erro
        ) {

            $this->mostrarCadastroComErro(
                $erro->getMessage(),
                '',
                ''
            );
        }


        $repository =
            new ClienteRepository(
                $this->pdo
            );


        $googleSub =
            $dadosGoogle['google_sub'];


        $email =
            $dadosGoogle['email'];


        $nome =
            $dadosGoogle['nome'];


        $fotoUrl =
            $dadosGoogle['foto_url'];


        $emailVerificado =
            (bool)
                $dadosGoogle['email_verificado'];


        /*
        =================================
        JÁ EXISTE PELO GOOGLE SUB
        =================================
        */

        $cliente =
            $repository->buscarPorGoogleSub(
                $googleSub
            );


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

                $cliente['nome'],

                $cliente['email']
            );


            $this->redirecionar(
                'cliente/perfil'
            );
        }


        /*
        =================================
        JÁ EXISTE PELO E-MAIL
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
            Se a conta já possui outro
            google_sub, não fazemos uma
            associação silenciosa.
            */

            if (
                !empty(
                    $clientePorEmail['google_sub']
                )
            ) {

                $this->mostrarCadastroComErro(
                    'Este e-mail já está associado a uma conta diferente do Google.',
                    '',
                    $email
                );
            }


            /*
            Conta tradicional encontrada.
            Vincula o Google à conta existente.
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

                $clientePorEmail['nome'],

                $clientePorEmail['email']
            );


            $this->redirecionar(
                'cliente/perfil'
            );
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
            $email
        );


        $this->redirecionar(
            'cliente/perfil'
        );
    }


    /*
    =================================
    INICIA SESSÃO DO CLIENTE
    =================================
    */

    private function iniciarSessaoCliente(
        int $clienteId,
        string $nome,
        string $email
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