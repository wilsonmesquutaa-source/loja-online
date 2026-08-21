<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\ClienteRepository;

final class SegurancaController extends Controller
{
    /*
    =================================
    PÁGINA
    =================================
    */

    public function index(): void
    {
        $clienteId =
            $this->clienteAutenticado();

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
            $this->redirecionar(
                '/login'
            );

            return;
        }

        $this->view(
            'cliente/seguranca',
            [
                'tituloPagina' =>
                    'Segurança',

                'rotaAtual' =>
                    'seguranca',

                'cliente' =>
                    $cliente,

                'csrfToken' =>
                    Csrf::gerarCliente(),

                'erro' =>
                    $_GET['erro']
                    ?? null,

                'sucesso' =>
                    $_GET['sucesso']
                    ?? null,
            ]
        );
    }


    /*
    =================================
    ALTERAR SENHA
    =================================
    */

    public function alterarSenha(): void
    {
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        $senhaAtual =
            (string) (
                $_POST['senha_atual']
                ?? ''
            );

        $novaSenha =
            (string) (
                $_POST['nova_senha']
                ?? ''
            );

        $confirmacao =
            (string) (
                $_POST['confirmacao_senha']
                ?? ''
            );


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
            $this->redirecionar(
                '/login'
            );

            return;
        }


        /*
        Conta Google sem senha local.
        */

        if (
            empty(
                $cliente['senha_hash']
            )
        ) {
            $this->redirecionarComErro(
                'Sua conta não possui uma senha local. Ela foi criada ou vinculada ao Google.'
            );

            return;
        }


        if (
            $senhaAtual === ''
        ) {
            $this->redirecionarComErro(
                'Informe sua senha atual.'
            );

            return;
        }


        if (
            !password_verify(
                $senhaAtual,
                (string)
                $cliente['senha_hash']
            )
        ) {
            $this->redirecionarComErro(
                'A senha atual está incorreta.'
            );

            return;
        }


        if (
            strlen($novaSenha) < 8
        ) {
            $this->redirecionarComErro(
                'A nova senha deve ter pelo menos 8 caracteres.'
            );

            return;
        }


        if (
            $novaSenha !==
            $confirmacao
        ) {
            $this->redirecionarComErro(
                'As senhas não coincidem.'
            );

            return;
        }


        $senhaHash =
            password_hash(
                $novaSenha,
                PASSWORD_DEFAULT
            );


        if (
            $senhaHash === false
        ) {
            $this->redirecionarComErro(
                'Não foi possível proteger a nova senha.'
            );

            return;
        }


        $repository->atualizarSenha(
            $clienteId,
            $senhaHash
        );


        Csrf::renovarCliente();


        $this->redirecionar(
            '/cliente/seguranca?sucesso='
            . rawurlencode(
                'Senha alterada com sucesso.'
            )
        );
    }


    /*
    =================================
    ALTERAR E-MAIL
    =================================
    */

    public function alterarEmail(): void
    {
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        $novoEmail =
            trim(
                strtolower(
                    (string) (
                        $_POST['novo_email']
                        ?? ''
                    )
                )
            );

        $senha =
            (string) (
                $_POST['senha_email']
                ?? ''
            );


        if (
            !filter_var(
                $novoEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->redirecionarComErro(
                'Informe um e-mail válido.'
            );

            return;
        }


        if (
            strlen($novoEmail) > 180
        ) {
            $this->redirecionarComErro(
                'O e-mail informado é muito longo.'
            );

            return;
        }


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
            $this->redirecionar(
                '/login'
            );

            return;
        }


        /*
        Conta tradicional:
        exige senha atual.
        */

        if (
            !empty(
                $cliente['senha_hash']
            )
        ) {

            if (
                $senha === ''
            ) {
                $this->redirecionarComErro(
                    'Informe sua senha atual para alterar o e-mail.'
                );

                return;
            }


            if (
                !password_verify(
                    $senha,
                    (string)
                    $cliente['senha_hash']
                )
            ) {
                $this->redirecionarComErro(
                    'A senha informada está incorreta.'
                );

                return;
            }
        }


        /*
        Não permite trocar para o
        mesmo e-mail.
        */

        if (
            strtolower(
                (string)
                $cliente['email']
            )
            ===
            $novoEmail
        ) {
            $this->redirecionarComErro(
                'O novo e-mail é igual ao e-mail atual.'
            );

            return;
        }


        if (
            $repository
            ->emailExisteParaOutroCliente(
                $novoEmail,
                $clienteId
            )
        ) {
            $this->redirecionarComErro(
                'Este e-mail já está sendo usado por outra conta.'
            );

            return;
        }


        /*
        Contas Google não perdem o vínculo
        com o Google. O google_sub continua
        preservado.
        */

        $repository->atualizarEmail(
            $clienteId,
            $novoEmail
        );


        $_SESSION['cliente_email'] =
            $novoEmail;


        Csrf::renovarCliente();


        $this->redirecionar(
            '/cliente/seguranca?sucesso='
            . rawurlencode(
                'E-mail alterado com sucesso.'
            )
        );
    }


    /*
    =================================
    EXCLUIR / ENCERRAR CONTA
    =================================
    */

    public function excluirConta(): void
    {
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        $confirmacao =
            trim(
                (string) (
                    $_POST['confirmacao_exclusao']
                    ?? ''
                )
            );


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
            $this->redirecionar(
                '/login'
            );

            return;
        }


        /*
        Exige confirmação digitando
        o e-mail atual.
        */

        if (
            strtolower(
                $confirmacao
            )
            !==
            strtolower(
                (string)
                $cliente['email']
            )
        ) {
            $this->redirecionarComErro(
                'Para confirmar a exclusão, digite exatamente seu e-mail atual.'
            );

            return;
        }


        /*
        Conta com pedidos:
        preserva histórico e remove
        os dados pessoais.
        */

        if (
            $repository->possuiPedidos(
                $clienteId
            )
        ) {

            $repository->anonimizarConta(
                $clienteId
            );

        } else {

            $repository->excluirConta(
                $clienteId
            );
        }


        /*
        Encerra sessão.
        */

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
        Volta para a loja.
        */

        header(
            'Location: '
            . BASE_URL
            . '/?conta_excluida=1'
        );

        exit;
    }


    /*
    =================================
    CLIENTE AUTENTICADO
    =================================
    */

    private function clienteAutenticado(): int
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

            exit;
        }


        return
            (int)
            $_SESSION['cliente_id'];
    }


    /*
    =================================
    CSRF
    =================================
    */

    private function validarCsrf(): void
    {
        $token =
            isset(
                $_POST['_csrf']
            )
                ? (string)
                    $_POST['_csrf']
                : null;


        if (
            !Csrf::validarCliente(
                $token
            )
        ) {
            http_response_code(403);

            exit(
                'Token CSRF inválido.'
            );
        }
    }


    /*
    =================================
    ERRO
    =================================
    */

    private function redirecionarComErro(
        string $mensagem
    ): void {
        $this->redirecionar(
            '/cliente/seguranca?erro='
            . rawurlencode(
                $mensagem
            )
        );
    }
}