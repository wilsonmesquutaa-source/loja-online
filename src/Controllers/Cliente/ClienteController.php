<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\ClienteRepository;
use App\Repositories\EnderecoRepository;
use App\Services\GoogleAuthService;
use App\Services\EmailService;

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
            !empty($_SESSION['cliente_id'])
        ) {

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


        if (
            $cliente === null
            ||
            empty($cliente['senha_hash'])
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


        if (
            (int)
            (
                $cliente['email_verificado']
                ?? 0
            )
            !== 1
        ) {

            $this->mostrarLoginComErro(
                'Seu e-mail ainda não foi confirmado. Verifique sua caixa de entrada e clique no link de confirmação.',
                $email
            );
        }


        $this->iniciarSessaoCliente(
            (int)
            $cliente['id'],

            (string)
            $cliente['nome'],

            (string)
            $cliente['email'],

            !empty($cliente['foto_url'])
                ? (string)
                $cliente['foto_url']
                : null
        );


        $repository->atualizarUltimoAcesso(
            (int)
            $cliente['id']
        );


        Csrf::renovarCliente();


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

            exit('Token CSRF inválido.');
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
        $retorno =
            trim(
                (string)
                (
                    $_GET['retorno']
                    ?? ''
                )
            );


        if (
            $retorno !==
            'carrinho'
        ) {

            $retorno =
                '';
        }


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

                'retorno' =>
                $retorno,

                'identificacao' =>
                'Minha casa',

                'destinatario' =>
                '',

                'cep' =>
                '',

                'logradouro' =>
                '',

                'numero' =>
                '',

                'complemento' =>
                '',

                'bairro' =>
                '',

                'cidade' =>
                'Fortaleza',

                'estado' =>
                'CE',
            ]
        );
    }


    /*
    =================================
    VERIFICA E-MAIL
    =================================
    */

    public function verificarEmail(): void
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        $token =
            trim(
                (string)
                (
                    $_GET['token']
                    ?? ''
                )
            );


        /*
        =================================
        VALIDA TOKEN
        =================================
        */

        if (
            $token === ''
            ||
            !preg_match(
                '/^[a-f0-9]{64}$/i',
                $token
            )
        ) {

            $this->mostrarVerificacaoComErro(
                'O link de confirmação é inválido.'
            );
        }


        /*
        =================================
        GERA HASH
        =================================
        */

        $tokenHash =
            hash(
                'sha256',
                $token
            );


        /*
        =================================
        REPOSITORY
        =================================
        */

        $repository =
            new ClienteRepository(
                $this->pdo
            );


        /*
        =================================
        BUSCA PELO HASH
        =================================
        */

        $cliente =
            $repository
            ->buscarPorTokenVerificacao(
                $tokenHash
            );


        /*
        =================================
        TOKEN NÃO ENCONTRADO
        =================================
        */

        if (
            $cliente === null
        ) {

            $this->mostrarVerificacaoComErro(
                'O link de confirmação não é válido. Solicite um novo e-mail de confirmação.'
            );
        }


        /*
        =================================
        VERIFICA EXPIRAÇÃO
        =================================
        */

        $expiraEm =
            trim(
                (string)
                (
                    $cliente['token_verificacao_expira_em']
                    ?? ''
                )
            );


        if (
            $expiraEm === ''
        ) {

            $this->mostrarVerificacaoComErro(
                'O link de confirmação expirou. Solicite um novo e-mail de confirmação.'
            );
        }


        /*
        =================================
        INTERPRETA DATA COMO UTC
        =================================
        */

        $dataExpiracao =
            \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $expiraEm,
                new \DateTimeZone('UTC')
            );


        if (
            $dataExpiracao === false
        ) {

            $this->mostrarVerificacaoComErro(
                'Não foi possível validar a validade do link de confirmação.'
            );
        }


        /*
        =================================
        COMPARA COM UTC ATUAL
        =================================
        */

        $agoraUtc =
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('UTC')
            );


        if (
            $dataExpiracao <= $agoraUtc
        ) {

            $this->mostrarVerificacaoComErro(
                'O link de confirmação expirou. Solicite um novo e-mail de confirmação.'
            );
        }


        /*
        =================================
        JÁ CONFIRMADO
        =================================
        */

        if (
            (int)
            (
                $cliente['email_verificado']
                ?? 0
            )
            === 1
        ) {

            $this->finalizarLoginAposVerificacao(
                $cliente
            );

            return;
        }


        /*
        =================================
        CONFIRMA E-MAIL
        =================================
        */

        $repository
            ->verificarEmail(
                (int)
                $cliente['id']
            );


        /*
        =================================
        ATUALIZA ÚLTIMO ACESSO
        =================================
        */

        $repository
            ->atualizarUltimoAcesso(
                (int)
                $cliente['id']
            );


        /*
        =================================
        LOGIN
        =================================
        */

        $this->finalizarLoginAposVerificacao(
            $cliente
        );
    }


    /*
    =================================
    LOGIN APÓS VERIFICAÇÃO
    =================================
    */

    private function finalizarLoginAposVerificacao(
        array $cliente
    ): void {

        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        /*
        =================================
        INICIA SESSÃO
        =================================
        */

        $this->iniciarSessaoCliente(
            (int)
            $cliente['id'],

            (string)
            $cliente['nome'],

            (string)
            $cliente['email'],

            !empty($cliente['foto_url'])
                ? (string)
                $cliente['foto_url']
                : null
        );


        Csrf::renovarCliente();


        /*
        =================================
        DESTINO APÓS CONFIRMAÇÃO
        =================================
        */

        $retorno =
            isset(
                $_SESSION['cadastro_retorno']
            )
            ? (string)
            $_SESSION['cadastro_retorno']
            : '';


        unset(
            $_SESSION['cadastro_retorno']
        );


        /*
        =================================
        DEFINE DESTINO
        =================================
        */

        $destino =
            $retorno === 'carrinho'
                ? BASE_URL . '/carrinho'
                : BASE_URL . '/';


        /*
        =================================
        PROTEGE DADOS PARA HTML
        =================================
        */

        $nomeSeguro =
            htmlspecialchars(
                (string)
                $cliente['nome'],
                ENT_QUOTES |
                ENT_SUBSTITUTE |
                ENT_HTML5,
                'UTF-8'
            );


        $destinoSeguro =
            htmlspecialchars(
                $destino,
                ENT_QUOTES |
                ENT_SUBSTITUTE |
                ENT_HTML5,
                'UTF-8'
            );


        /*
        =================================
        MENSAGEM DE CONFIRMAÇÃO
        =================================
        */

        echo '
            <!DOCTYPE html>

            <html lang="pt-BR">

            <head>

                <meta charset="UTF-8">

                <meta
                    name="viewport"
                    content="width=device-width, initial-scale=1.0"
                >

                <title>
                    E-mail confirmado - Cantim do Lanche
                </title>

                <style>

                    * {
                        box-sizing:
                            border-box;
                    }

                    body {
                        margin:
                            0;

                        min-height:
                            100vh;

                        display:
                            flex;

                        align-items:
                            center;

                        justify-content:
                            center;

                        padding:
                            20px;

                        background:
                            #f8f5f0;

                        font-family:
                            Arial,
                            Helvetica,
                            sans-serif;
                    }

                    .confirmacao {
                        width:
                            100%;

                        max-width:
                            500px;

                        padding:
                            40px 30px;

                        background:
                            #ffffff;

                        border-radius:
                            20px;

                        text-align:
                            center;

                        box-shadow:
                            0 10px 30px
                            rgba(
                                0,
                                0,
                                0,
                                0.08
                            );
                    }

                    .icone {
                        width:
                            70px;

                        height:
                            70px;

                        margin:
                            0 auto 20px;

                        display:
                            flex;

                        align-items:
                            center;

                        justify-content:
                            center;

                        border-radius:
                            50%;

                        background:
                            #f57c00;

                        color:
                            #ffffff;

                        font-size:
                            34px;

                        font-weight:
                            bold;
                    }

                    h1 {
                        margin:
                            0 0 15px;

                        color:
                            #3e2723;

                        font-size:
                            26px;
                    }

                    p {
                        margin:
                            0 0 25px;

                        color:
                            #666666;

                        font-size:
                            16px;

                        line-height:
                            1.6;
                    }

                    .confirmacao strong {
                        color:
                            #3e2723;
                    }

                    button {
                        border:
                            0;

                        padding:
                            13px 35px;

                        background:
                            #f57c00;

                        color:
                            #ffffff;

                        border-radius:
                            30px;

                        font-size:
                            16px;

                        font-weight:
                            bold;

                        cursor:
                            pointer;
                    }

                    button:hover {
                        opacity:
                            0.9;
                    }

                </style>

            </head>


            <body>

                <div class="confirmacao">

                    <div class="icone">
                        ✓
                    </div>


                    <h1>
                        E-mail confirmado!
                    </h1>


                    <p>

                        Olá,
                        <strong>
                            ' . $nomeSeguro . '
                        </strong>!

                        <br><br>

                        Seu e-mail foi verificado
                        com sucesso e sua conta
                        já está ativa.

                    </p>


                    <button
                        type="button"
                        onclick="window.location.href=\'' .
                        $destinoSeguro .
                        '\'"
                    >
                        OK
                    </button>

                </div>

            </body>

            </html>
        ';


        exit;
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
                '',
                [],
                ''
            );
        }


        $retorno =
            trim(
                (string)
                (
                    $_POST['retorno']
                    ?? ''
                )
            );


        if (
            $retorno !==
            'carrinho'
        ) {

            $retorno =
                '';
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


        $identificacao =
            trim(
                (string) (
                    $_POST['identificacao']
                    ?? 'Minha casa'
                )
            );


        $destinatario =
            trim(
                (string) (
                    $_POST['destinatario']
                    ?? ''
                )
            );


        $cep =
            preg_replace(
                '/\D+/',
                '',
                (string) (
                    $_POST['cep']
                    ?? ''
                )
            );


        $logradouro =
            trim(
                (string) (
                    $_POST['logradouro']
                    ?? ''
                )
            );


        $numero =
            trim(
                (string) (
                    $_POST['numero']
                    ?? ''
                )
            );


        $complemento =
            trim(
                (string) (
                    $_POST['complemento']
                    ?? ''
                )
            );


        $bairro =
            trim(
                (string) (
                    $_POST['bairro']
                    ?? ''
                )
            );


        $cidade =
            trim(
                (string) (
                    $_POST['cidade']
                    ?? 'Fortaleza'
                )
            );


        $estado =
            strtoupper(
                trim(
                    (string) (
                        $_POST['estado']
                        ?? 'CE'
                    )
                )
            );


        if (
            $nome === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe seu nome completo.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
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
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
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
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            strlen($email) > 180
        ) {

            $this->mostrarCadastroComErro(
                'O e-mail informado é muito longo.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            strlen($senha) < 8
        ) {

            $this->mostrarCadastroComErro(
                'A senha deve ter pelo menos 8 caracteres.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $senha !==
            $senhaConfirmacao
        ) {

            $this->mostrarCadastroComErro(
                'As senhas não coincidem.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $identificacao === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe uma identificação para o endereço.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $destinatario === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe o nome do destinatário.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            strlen($cep) !== 8
        ) {

            $this->mostrarCadastroComErro(
                'Informe um CEP válido.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $logradouro === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe o logradouro.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $numero === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe o número do endereço.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $bairro === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe o bairro.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            $cidade === ''
        ) {

            $this->mostrarCadastroComErro(
                'Informe a cidade.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        if (
            !preg_match(
                '/^[A-Z]{2}$/',
                $estado
            )
        ) {

            $this->mostrarCadastroComErro(
                'Informe um estado válido.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        $clienteRepository =
            new ClienteRepository(
                $this->pdo
            );


        $enderecoRepository =
            new EnderecoRepository(
                $this->pdo
            );


        if (
            $clienteRepository->emailExiste(
                $email
            )
        ) {

            $this->mostrarCadastroComErro(
                'Já existe uma conta cadastrada com este e-mail. Entre com sua conta existente.',
                $nome,
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
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
                $email,
                $this->dadosEnderecoFormulario(
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento,
                    $bairro,
                    $cidade,
                    $estado
                ),
                $retorno
            );
        }


        $tokenVerificacao =
            bin2hex(
                random_bytes(32)
            );


        $tokenVerificacaoHash =
            hash(
                'sha256',
                $tokenVerificacao
            );


        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {

            session_start();
        }


        $_SESSION['cadastro_retorno'] =
            $retorno;


        $this->pdo
            ->beginTransaction();


        try {

            $clienteId =
                $clienteRepository
                ->criar(
                    $nome,
                    $email,
                    $senhaHash
                );


            $clienteRepository
                ->criarTokenVerificacao(
                    $clienteId,
                    $tokenVerificacaoHash
                );


            $enderecoRepository
                ->criar(
                    $clienteId,
                    $identificacao,
                    $destinatario,
                    $cep,
                    $logradouro,
                    $numero,
                    $complemento !== ''
                        ? $complemento
                        : null,
                    $bairro,
                    $cidade,
                    $estado,
                    true
                );


            $this->pdo
                ->commit();

        } catch (
            \Throwable $erro
        ) {

            if (
                $this->pdo->inTransaction()
            ) {

                $this->pdo
                    ->rollBack();
            }


            throw $erro;
        }


        /*
        =================================
        URL DE VERIFICAÇÃO
        =================================
        */

        $appUrl =
            trim(
                (string)
                (
                    $_ENV['APP_URL']
                    ?? ''
                )
            );


        if (
            $appUrl === ''
        ) {

            $appUrl =
                'http://localhost/loja-online';
        }


        $urlVerificacao =
            rtrim(
                $appUrl,
                '/'
            )
            . '/cadastro/verificar-email?token='
            . rawurlencode(
                $tokenVerificacao
            );


        /*
        =================================
        ENVIA E-MAIL
        =================================
        */

        try {

            $emailService =
                new EmailService();


            $emailService
                ->enviarVerificacao(
                    $email,
                    $nome,
                    $urlVerificacao
                );

        } catch (
            \Throwable $erro
        ) {

            $this->view(
                'site/cliente_verificacao_pendente',
                [
                    'tituloPagina' =>
                    'Confirme seu e-mail',

                    'rotaAtual' =>
                    'cadastro',

                    'nome' =>
                    $nome,

                    'email' =>
                    $email,

                    'erroEnvio' =>
                    'Sua conta foi criada, mas não conseguimos enviar o e-mail de confirmação.',
                ]
            );

            exit;
        }


        /*
        =================================
        AGUARDA CONFIRMAÇÃO
        =================================
        */

        $this->view(
            'site/cliente_verificacao_pendente',
            [
                'tituloPagina' =>
                'Confirme seu e-mail',

                'rotaAtual' =>
                'cadastro',

                'nome' =>
                $nome,

                'email' =>
                $email,

                'erroEnvio' =>
                null,
            ]
        );

        exit;
    }


    /*
    =================================
    DADOS DE ENDEREÇO PARA ERRO
    =================================
    */

    private function dadosEnderecoFormulario(
        string $identificacao,
        string $destinatario,
        string $cep,
        string $logradouro,
        string $numero,
        string $complemento,
        string $bairro,
        string $cidade,
        string $estado
    ): array {

        return [
            'identificacao' =>
            $identificacao,

            'destinatario' =>
            $destinatario,

            'cep' =>
            $cep,

            'logradouro' =>
            $logradouro,

            'numero' =>
            $numero,

            'complemento' =>
            $complemento,

            'bairro' =>
            $bairro,

            'cidade' =>
            $cidade,

            'estado' =>
            $estado,
        ];
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
            $google
            ->criarUrlAutorizacao(
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
            $google
            ->criarUrlAutorizacao(
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
            $_SESSION['google_oauth_state']
            : '';


        $acao =
            isset(
                $_SESSION['google_oauth_action']
            )
            ? (string)
            $_SESSION['google_oauth_action']
            : '';


        unset(
            $_SESSION['google_oauth_state'],
            $_SESSION['google_oauth_action']
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


        if (
            isset($_GET['error'])
        ) {

            $this->mostrarErroGoogle(
                'A autenticação com o Google foi cancelada ou não pôde ser concluída.'
            );
        }


        if (
            $acao !== 'login'
            &&
            $acao !== 'cadastro'
        ) {

            $this->mostrarErroGoogle(
                'A ação de autenticação não pôde ser identificada.'
            );
        }


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


        try {

            $google =
                new GoogleAuthService();


            $token =
                $google
                ->trocarCodigoPorToken(
                    $code
                );


            $dadosGoogle =
                $google
                ->buscarUsuario(
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
            !empty($dadosGoogle['foto_url'])
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


        $cliente =
            $repository
            ->buscarPorGoogleSub(
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
                    $repository
                    ->buscarPorEmail(
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


            $repository
                ->atualizarUltimoAcesso(
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

                !empty($cliente['foto_url'])
                    ? (string)
                    $cliente['foto_url']
                    : null
            );


            Csrf::renovarCliente();


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

            $repository
                ->atualizarUltimoAcesso(
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

                !empty($cliente['foto_url'])
                    ? (string)
                    $cliente['foto_url']
                    : $fotoUrl
            );


            Csrf::renovarCliente();


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
            $repository
            ->buscarPorEmail(
                $email
            );


        if (
            $clientePorEmail !== null
        ) {

            if (
                !empty($clientePorEmail['google_sub'])
            ) {

                $this->mostrarErroGoogle(
                    'Este e-mail já está associado a outra conta Google.'
                );
            }


            $repository
                ->vincularGoogle(
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


            header(
                'Location: '
                    . BASE_URL
                    . '/'
            );

            exit;
        }


        /*
        =================================
        NOVA CONTA GOOGLE
        =================================
        */

        $clienteId =
            $repository
            ->criarComGoogle(
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
        string $email,
        array $endereco = [],
        string $retorno = ''
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

                'retorno' =>
                $retorno,

                'identificacao' =>
                $endereco['identificacao']
                    ?? 'Minha casa',

                'destinatario' =>
                $endereco['destinatario']
                    ?? '',

                'cep' =>
                $endereco['cep']
                    ?? '',

                'logradouro' =>
                $endereco['logradouro']
                    ?? '',

                'numero' =>
                $endereco['numero']
                    ?? '',

                'complemento' =>
                $endereco['complemento']
                    ?? '',

                'bairro' =>
                $endereco['bairro']
                    ?? '',

                'cidade' =>
                $endereco['cidade']
                    ?? 'Fortaleza',

                'estado' =>
                $endereco['estado']
                    ?? 'CE',
            ]
        );

        exit;
    }


    /*
    =================================
    ERRO DE VERIFICAÇÃO
    =================================
    */

    private function mostrarVerificacaoComErro(
        string $mensagem
    ): never {

        $this->view(
            'site/cliente_verificacao_pendente',
            [
                'tituloPagina' =>
                'Confirme seu e-mail',

                'rotaAtual' =>
                'cadastro',

                'nome' =>
                '',

                'email' =>
                '',

                'erroEnvio' =>
                $mensagem,
            ]
        );

        exit;
    }
}