<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\EnderecoRepository;

final class EnderecoController extends Controller
{
    /*
    =================================
    LISTA DE ENDEREÇOS
    =================================
    */

    public function index(): void
    {
        $clienteId =
            $this->clienteAutenticado();

        $repository =
            new EnderecoRepository(
                $this->pdo
            );

        $enderecos =
            $repository->buscarPorCliente(
                $clienteId
            );

        $this->view(
            'cliente/enderecos',
            [
                'tituloPagina' =>
                    'Meus endereços',

                'rotaAtual' =>
                    'enderecos',

                'enderecos' =>
                    $enderecos,

                'csrfToken' =>
                    Csrf::gerarCliente(),
            ]
        );
    }


    /*
    =================================
    NOVO ENDEREÇO
    =================================
    */

    public function novo(): void
    {
        $this->clienteAutenticado();


        /*
        =================================
        RETORNO
        =================================
        */

        $retorno =
            isset(
                $_GET['retorno']
            )
                ? trim(
                    (string)
                    $_GET['retorno']
                )
                : '';


        /*
        Apenas permitimos destinos
        internos conhecidos.
        */

        if (
            $retorno !== 'checkout'
        ) {
            $retorno = '';
        }


        $this->view(
            'cliente/endereco-form',
            [
                'tituloPagina' =>
                    'Novo endereço',

                'rotaAtual' =>
                    'enderecos',

                'endereco' =>
                    null,

                'csrfToken' =>
                    Csrf::gerarCliente(),

                'erro' =>
                    null,

                'retorno' =>
                    $retorno,
            ]
        );
    }


    /*
    =================================
    SALVAR ENDEREÇO
    =================================
    */

    public function salvar(): void
    {
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        /*
        =================================
        RETORNO
        =================================
        */

        $retorno =
            isset(
                $_POST['retorno']
            )
                ? trim(
                    (string)
                    $_POST['retorno']
                )
                : '';


        if (
            $retorno !== 'checkout'
        ) {
            $retorno = '';
        }


        /*
        =================================
        DADOS
        =================================
        */

        $dados =
            $this->obterDadosFormulario();


        $erro =
            $this->validarDados(
                $dados
            );


        if (
            $erro !== null
        ) {

            $rotaErro =
                '/cliente/enderecos/novo';


            if (
                $retorno === 'checkout'
            ) {
                $rotaErro .=
                    '?retorno=checkout&erro='
                    . rawurlencode(
                        $erro
                    );
            } else {
                $rotaErro .=
                    '?erro='
                    . rawurlencode(
                        $erro
                    );
            }


            $this->redirecionar(
                $rotaErro
            );

            return;
        }


        /*
        =================================
        REPOSITORY
        =================================
        */

        $repository =
            new EnderecoRepository(
                $this->pdo
            );


        $enderecos =
            $repository->buscarPorCliente(
                $clienteId
            );


        /*
        =================================
        PRIMEIRO ENDEREÇO
        =================================

        Se o cliente ainda não possui
        endereço, ele será principal
        automaticamente.
        */

        $principal =
            $dados['principal'];


        if (
            $enderecos === []
        ) {
            $principal = true;
        }


        /*
        =================================
        SALVA
        =================================
        */

        $novoEnderecoId =
            $repository->criar(
                $clienteId,
                $dados['identificacao'],
                $dados['destinatario'],
                $dados['cep'],
                $dados['logradouro'],
                $dados['numero'],
                $dados['complemento'],
                $dados['bairro'],
                $dados['cidade'],
                $dados['estado'],
                $principal
            );


        /*
        =================================
        GARANTE PRINCIPAL
        =================================
        */

        if (
            $principal
        ) {

            $repository->definirPrincipal(
                $novoEnderecoId,
                $clienteId
            );
        }


        /*
        =================================
        RETORNO PARA CHECKOUT
        =================================
        */

        if (
            $retorno === 'checkout'
        ) {

            $this->redirecionar(
                '/checkout?endereco='
                . $novoEnderecoId
            );

            return;
        }


        /*
        =================================
        VOLTA PARA ENDEREÇOS
        =================================
        */

        $this->redirecionar(
            '/cliente/enderecos?sucesso='
            . rawurlencode(
                'Endereço cadastrado com sucesso.'
            )
        );
    }


    /*
    =================================
    FORMULÁRIO DE EDIÇÃO
    =================================
    */

    public function editar(
        int $id
    ): void {
        $clienteId =
            $this->clienteAutenticado();


        $retorno =
            isset(
                $_GET['retorno']
            )
                ? trim(
                    (string)
                    $_GET['retorno']
                )
                : '';


        if (
            $retorno !== 'checkout'
        ) {
            $retorno = '';
        }


        $repository =
            new EnderecoRepository(
                $this->pdo
            );


        $endereco =
            $repository
            ->buscarPorIdDoCliente(
                $id,
                $clienteId
            );


        if (
            $endereco === null
        ) {
            http_response_code(404);

            require
                APP_ROOT
                . '/views/erros/404.php';

            return;
        }


        $this->view(
            'cliente/endereco-form',
            [
                'tituloPagina' =>
                    'Editar endereço',

                'rotaAtual' =>
                    'enderecos',

                'endereco' =>
                    $endereco,

                'csrfToken' =>
                    Csrf::gerarCliente(),

                'erro' =>
                    null,

                'retorno' =>
                    $retorno,
            ]
        );
    }


    /*
    =================================
    ATUALIZAR ENDEREÇO
    =================================
    */

    public function atualizar(
        int $id
    ): void {
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        /*
        =================================
        RETORNO
        =================================
        */

        $retorno =
            isset(
                $_POST['retorno']
            )
                ? trim(
                    (string)
                    $_POST['retorno']
                )
                : '';


        if (
            $retorno !== 'checkout'
        ) {
            $retorno = '';
        }


        /*
        =================================
        DADOS
        =================================
        */

        $dados =
            $this->obterDadosFormulario();


        $erro =
            $this->validarDados(
                $dados
            );


        if (
            $erro !== null
        ) {

            $rotaErro =
                '/cliente/enderecos/editar/'
                . $id;


            if (
                $retorno === 'checkout'
            ) {
                $rotaErro .=
                    '?retorno=checkout&erro='
                    . rawurlencode(
                        $erro
                    );
            } else {
                $rotaErro .=
                    '?erro='
                    . rawurlencode(
                        $erro
                    );
            }


            $this->redirecionar(
                $rotaErro
            );

            return;
        }


        /*
        =================================
        REPOSITORY
        =================================
        */

        $repository =
            new EnderecoRepository(
                $this->pdo
            );


        $endereco =
            $repository
            ->buscarPorIdDoCliente(
                $id,
                $clienteId
            );


        if (
            $endereco === null
        ) {
            http_response_code(404);

            require
                APP_ROOT
                . '/views/erros/404.php';

            return;
        }


        /*
        =================================
        ATUALIZA
        =================================
        */

        $repository->atualizar(
            $id,
            $clienteId,
            $dados['identificacao'],
            $dados['destinatario'],
            $dados['cep'],
            $dados['logradouro'],
            $dados['numero'],
            $dados['complemento'],
            $dados['bairro'],
            $dados['cidade'],
            $dados['estado']
        );


        /*
        =================================
        PRINCIPAL
        =================================
        */

        if (
            $dados['principal']
        ) {

            $repository->definirPrincipal(
                $id,
                $clienteId
            );
        }


        /*
        =================================
        RETORNO CHECKOUT
        =================================
        */

        if (
            $retorno === 'checkout'
        ) {

            $this->redirecionar(
                '/checkout?endereco='
                . $id
            );

            return;
        }


        /*
        =================================
        VOLTA PARA ENDEREÇOS
        =================================
        */

        $this->redirecionar(
            '/cliente/enderecos?sucesso='
            . rawurlencode(
                'Endereço atualizado com sucesso.'
            )
        );
    }


    /*
    =================================
    DEFINIR PRINCIPAL
    =================================
    */

    public function principal(
        int $id
    ): void {
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        $repository =
            new EnderecoRepository(
                $this->pdo
            );


        $endereco =
            $repository
            ->buscarPorIdDoCliente(
                $id,
                $clienteId
            );


        if (
            $endereco === null
        ) {
            http_response_code(404);

            require
                APP_ROOT
                . '/views/erros/404.php';

            return;
        }


        $repository->definirPrincipal(
            $id,
            $clienteId
        );


        $this->redirecionar(
            '/cliente/enderecos?sucesso='
            . rawurlencode(
                'Endereço principal atualizado.'
            )
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
        $clienteId =
            $this->clienteAutenticado();

        $this->validarCsrf();


        $repository =
            new EnderecoRepository(
                $this->pdo
            );


        $endereco =
            $repository
            ->buscarPorIdDoCliente(
                $id,
                $clienteId
            );


        if (
            $endereco === null
        ) {
            http_response_code(404);

            require
                APP_ROOT
                . '/views/erros/404.php';

            return;
        }


        $eraPrincipal =
            (int)
            $endereco['principal']
            === 1;


        $repository->excluir(
            $id,
            $clienteId
        );


        /*
        =================================
        SE EXCLUIU O PRINCIPAL
        =================================

        Escolhe automaticamente outro
        endereço como principal.
        */

        if (
            $eraPrincipal
        ) {

            $enderecos =
                $repository
                ->buscarPorCliente(
                    $clienteId
                );


            if (
                $enderecos !== []
            ) {

                $repository->definirPrincipal(
                    (int)
                    $enderecos[0]['id'],

                    $clienteId
                );
            }
        }


        $this->redirecionar(
            '/cliente/enderecos?sucesso='
            . rawurlencode(
                'Endereço excluído com sucesso.'
            )
        );
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
    DADOS DO FORMULÁRIO
    =================================
    */

    private function obterDadosFormulario(): array
    {
        return [
            'identificacao' =>
                trim(
                    (string)
                    (
                        $_POST[
                            'identificacao'
                        ]
                        ?? ''
                    )
                ),

            'destinatario' =>
                trim(
                    (string)
                    (
                        $_POST[
                            'destinatario'
                        ]
                        ?? ''
                    )
                ),

            'cep' =>
                trim(
                    (string)
                    (
                        $_POST['cep']
                        ?? ''
                    )
                ),

            'logradouro' =>
                trim(
                    (string)
                    (
                        $_POST[
                            'logradouro'
                        ]
                        ?? ''
                    )
                ),

            'numero' =>
                trim(
                    (string)
                    (
                        $_POST['numero']
                        ?? ''
                    )
                ),

            'complemento' =>
                trim(
                    (string)
                    (
                        $_POST[
                            'complemento'
                        ]
                        ?? ''
                    )
                ),

            'bairro' =>
                trim(
                    (string)
                    (
                        $_POST['bairro']
                        ?? ''
                    )
                ),

            'cidade' =>
                trim(
                    (string)
                    (
                        $_POST['cidade']
                        ?? ''
                    )
                ),

            'estado' =>
                strtoupper(
                    trim(
                        (string)
                        (
                            $_POST['estado']
                            ?? ''
                        )
                    )
                ),

            'principal' =>
                isset(
                    $_POST['principal']
                ),
        ];
    }


    /*
    =================================
    VALIDA DADOS
    =================================
    */

    private function validarDados(
        array $dados
    ): ?string {

        if (
            $dados['identificacao'] === ''
        ) {
            return
                'Informe uma identificação para o endereço.';
        }


        if (
            mb_strlen(
                $dados['identificacao'],
                'UTF-8'
            ) > 80
        ) {
            return
                'A identificação do endereço é muito longa.';
        }


        if (
            $dados['destinatario'] === ''
        ) {
            return
                'Informe o nome do destinatário.';
        }


        if (
            $dados['cep'] === ''
        ) {
            return
                'Informe o CEP.';
        }


        $cepNumeros =
            preg_replace(
                '/\D/',
                '',
                $dados['cep']
            );


        if (
            strlen(
                (string)
                $cepNumeros
            ) !== 8
        ) {
            return
                'Informe um CEP válido.';
        }


        if (
            $dados['logradouro'] === ''
        ) {
            return
                'Informe o logradouro.';
        }


        if (
            $dados['numero'] === ''
        ) {
            return
                'Informe o número.';
        }


        if (
            $dados['bairro'] === ''
        ) {
            return
                'Informe o bairro.';
        }


        if (
            $dados['cidade'] === ''
        ) {
            return
                'Informe a cidade.';
        }


        if (
            !preg_match(
                '/^[A-Z]{2}$/',
                $dados['estado']
            )
        ) {
            return
                'Informe o estado corretamente.';
        }


        return null;
    }


    /*
    =================================
    REDIRECIONA COM ERRO
    =================================
    */

    private function redirecionarComErro(
        string $rota,
        string $mensagem
    ): void {
        $this->redirecionar(
            $rota
            . '?erro='
            . rawurlencode(
                $mensagem
            )
        );
    }
}