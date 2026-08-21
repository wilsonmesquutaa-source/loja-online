<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\CarrinhoRepository;
use App\Repositories\EnderecoRepository;
use App\Repositories\EntregaRepository;
use App\Repositories\PedidoRepository;

final class CheckoutController extends Controller
{
    /*
    =================================
    TOKEN DO CARRINHO
    =================================
    */

    private function obterTokenSessao(): string
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }


        if (
            empty(
                $_SESSION['carrinho_token']
            )
        ) {
            $_SESSION['carrinho_token'] =
                bin2hex(
                    random_bytes(32)
                );
        }


        return
            (string)
            $_SESSION['carrinho_token'];
    }


    /*
    =================================
    CLIENTE LOGADO
    =================================
    */

    private function obterClienteId(): int
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
    CHECKOUT
    =================================
    */

    public function index(): void
    {
        /*
        =================================
        CLIENTE
        =================================
        */

        $clienteId =
            $this->obterClienteId();


        /*
        =================================
        TOKEN
        =================================
        */

        $tokenSessao =
            $this->obterTokenSessao();


        /*
        =================================
        CARRINHO
        =================================
        */

        $carrinhoRepository =
            new CarrinhoRepository(
                $this->pdo
            );


        $carrinho =
            $carrinhoRepository
            ->buscarAbertoPorToken(
                $tokenSessao
            );


        if (
            $carrinho === null
        ) {
            $this->redirecionar(
                '/carrinho'
            );

            return;
        }


        /*
        =================================
        ITENS
        =================================
        */

        $itens =
            $carrinhoRepository
            ->buscarItens(
                (int)
                $carrinho['id']
            );


        if (
            $itens === []
        ) {
            $this->redirecionar(
                '/carrinho'
            );

            return;
        }


        /*
        =================================
        ASSOCIA CLIENTE AO CARRINHO
        =================================
        */

        $carrinhoRepository
            ->associarCliente(
                (int)
                $carrinho['id'],

                $clienteId
            );


        /*
        =================================
        SUBTOTAL
        =================================
        */

        $subtotal =
            0.0;


        foreach (
            $itens as $item
        ) {

            $subtotal +=
                (
                    (float)
                    $item['preco_unitario']
                )
                *
                (
                    (int)
                    $item['quantidade']
                );
        }


        /*
        =================================
        ENDEREÇOS
        =================================
        */

        $enderecoRepository =
            new EnderecoRepository(
                $this->pdo
            );


        $enderecos =
            $enderecoRepository
            ->buscarPorCliente(
                $clienteId
            );


        /*
        =================================
        ENDEREÇO SELECIONADO
        =================================
        */

        $enderecoSelecionado =
            null;


        /*
        =================================
        ENDEREÇO INFORMADO NA URL
        =================================
        */

        if (
            isset(
                $_GET['endereco']
            )
        ) {

            $enderecoId =
                filter_var(
                    $_GET['endereco'],
                    FILTER_VALIDATE_INT
                );


            if (
                $enderecoId
            ) {

                $enderecoSelecionado =
                    $enderecoRepository
                    ->buscarPorIdDoCliente(
                        (int)
                        $enderecoId,

                        $clienteId
                    );
            }
        }


        /*
        =================================
        ENDEREÇO PADRÃO
        =================================
        */

        if (
            $enderecoSelecionado === null
            &&
            $enderecos !== []
        ) {

            /*
            Primeiro tenta encontrar
            o endereço principal.
            */

            foreach (
                $enderecos as $endereco
            ) {

                if (
                    (int)
                    $endereco['principal']
                    === 1
                ) {

                    $enderecoSelecionado =
                        $endereco;

                    break;
                }
            }


            /*
            Se não houver principal,
            usa o primeiro.
            */

            if (
                $enderecoSelecionado === null
            ) {

                $enderecoSelecionado =
                    $enderecos[0];
            }
        }


        /*
        =================================
        FRETE
        =================================
        */

        $frete =
            null;


        $taxaEntrega =
            null;


        if (
            $enderecoSelecionado !== null
        ) {

            $entregaRepository =
                new EntregaRepository(
                    $this->pdo
                );


            $taxaEntrega =
                $entregaRepository
                ->buscarTaxa(
                    (string)
                    $enderecoSelecionado[
                        'bairro'
                    ],

                    (string)
                    $enderecoSelecionado[
                        'cidade'
                    ],

                    (string)
                    $enderecoSelecionado[
                        'estado'
                    ]
                );


            if (
                $taxaEntrega !== null
            ) {

                $frete =
                    (float)
                    $taxaEntrega['valor'];
            }
        }


        /*
        =================================
        DESCONTO
        =================================
        */

        $desconto =
            0.0;


        /*
        =================================
        TOTAL
        =================================
        */

        $total =
            $subtotal
            +
            (
                $frete ?? 0
            )
            -
            $desconto;


        /*
        =================================
        VIEW
        =================================
        */

        $this->view(
            'site/checkout',
            [
                'tituloPagina' =>
                    'Finalizar pedido',

                'rotaAtual' =>
                    'checkout',

                'itens' =>
                    $itens,

                'enderecos' =>
                    $enderecos,

                'enderecoSelecionado' =>
                    $enderecoSelecionado,

                'subtotal' =>
                    $subtotal,

                'frete' =>
                    $frete,

                'desconto' =>
                    $desconto,

                'total' =>
                    $total,

                'csrfToken' =>
                    Csrf::gerarCliente(),
            ]
        );
    }


    /*
    =================================
    FINALIZAR PEDIDO
    =================================
    */

    public function finalizar(): void
    {
        /*
        =================================
        CLIENTE
        =================================
        */

        $clienteId =
            $this->obterClienteId();


        /*
        =================================
        TOKEN
        =================================
        */

        $tokenSessao =
            $this->obterTokenSessao();


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

            http_response_code(403);

            exit(
                'Token CSRF inválido.'
            );
        }


        /*
        =================================
        ENDEREÇO
        =================================
        */

        $enderecoId =
            filter_input(
                INPUT_POST,
                'endereco_id',
                FILTER_VALIDATE_INT
            );


        /*
        =================================
        PAGAMENTO
        =================================
        */

        $metodoPagamento =
            strtolower(
                trim(
                    (string)
                    (
                        $_POST[
                            'metodo_pagamento'
                        ]
                        ?? ''
                    )
                )
            );


        /*
        =================================
        VALIDA ENDEREÇO
        =================================
        */

        if (
            !$enderecoId
        ) {

            $this->redirecionar(
                '/checkout?erro='
                . rawurlencode(
                    'Selecione um endereço de entrega.'
                )
            );

            return;
        }


        /*
        =================================
        VALIDA PAGAMENTO
        =================================
        */

        if (
            !in_array(
                $metodoPagamento,
                [
                    'pix',
                    'cartao',
                ],
                true
            )
        ) {

            $this->redirecionar(
                '/checkout?erro='
                . rawurlencode(
                    'Selecione uma forma de pagamento.'
                )
            );

            return;
        }


        /*
        =================================
        CARRINHO
        =================================
        */

        $carrinhoRepository =
            new CarrinhoRepository(
                $this->pdo
            );


        $carrinho =
            $carrinhoRepository
            ->buscarAbertoPorToken(
                $tokenSessao
            );


        if (
            $carrinho === null
        ) {

            $this->redirecionar(
                '/carrinho'
            );

            return;
        }


        /*
        =================================
        ITENS
        =================================
        */

        $itens =
            $carrinhoRepository
            ->buscarItens(
                (int)
                $carrinho['id']
            );


        if (
            $itens === []
        ) {

            $this->redirecionar(
                '/carrinho'
            );

            return;
        }


        /*
        =================================
        ASSOCIA CLIENTE
        =================================
        */

        $carrinhoRepository
            ->associarCliente(
                (int)
                $carrinho['id'],

                $clienteId
            );


        /*
        =================================
        ENDEREÇO
        =================================
        */

        $enderecoRepository =
            new EnderecoRepository(
                $this->pdo
            );


        $endereco =
            $enderecoRepository
            ->buscarPorIdDoCliente(
                (int)
                $enderecoId,

                $clienteId
            );


        if (
            $endereco === null
        ) {

            $this->redirecionar(
                '/checkout?erro='
                . rawurlencode(
                    'O endereço selecionado não é válido.'
                )
            );

            return;
        }


        /*
        =================================
        SUBTOTAL
        =================================
        */

        $subtotal =
            0.0;


        foreach (
            $itens as $item
        ) {

            $subtotal +=
                (
                    (float)
                    $item['preco_unitario']
                )
                *
                (
                    (int)
                    $item['quantidade']
                );
        }


        /*
        =================================
        FRETE
        =================================
        */

        $entregaRepository =
            new EntregaRepository(
                $this->pdo
            );


        $taxaEntrega =
            $entregaRepository
            ->buscarTaxa(
                (string)
                $endereco['bairro'],

                (string)
                $endereco['cidade'],

                (string)
                $endereco['estado']
            );


        /*
        =================================
        BAIRRO NÃO ATENDIDO
        =================================
        */

        if (
            $taxaEntrega === null
        ) {

            $this->redirecionar(
                '/checkout?endereco='
                . (int)
                $enderecoId
                . '&erro='
                . rawurlencode(
                    'Não realizamos entrega neste bairro no momento.'
                )
            );

            return;
        }


        $frete =
            (float)
            $taxaEntrega['valor'];


        /*
        =================================
        DESCONTO
        =================================
        */

        $desconto =
            0.0;


        /*
        =================================
        TOTAL
        =================================
        */

        $total =
            $subtotal
            +
            $frete
            -
            $desconto;


        /*
        =================================
        CÓDIGO
        =================================
        */

        $codigo =
            $this->gerarCodigoPedido();


        /*
        =================================
        REPOSITORY DO PEDIDO
        =================================
        */

        $pedidoRepository =
            new PedidoRepository(
                $this->pdo
            );


        /*
        =================================
        TRANSAÇÃO
        =================================
        */

        $this->pdo
            ->beginTransaction();


        try {

            /*
            ==============================
            CARRINHO
            ==============================
            */

            $carrinhoRepository
                ->associarCliente(
                    (int)
                    $carrinho['id'],

                    $clienteId
                );


            /*
            ==============================
            CRIA PEDIDO
            ==============================
            */

            $pedidoId =
                $pedidoRepository
                ->criarPedido(
                    $codigo,

                    $clienteId,

                    $subtotal,

                    $frete,

                    $desconto,

                    $total,

                    null
                );


            /*
            ==============================
            COPIA ITENS
            ==============================
            */

            foreach (
                $itens as $item
            ) {

                $quantidade =
                    (int)
                    $item['quantidade'];


                $precoUnitario =
                    (float)
                    $item['preco_unitario'];


                $subtotalItem =
                    $quantidade
                    *
                    $precoUnitario;


                $pedidoRepository
                    ->adicionarItem(
                        $pedidoId,

                        (int)
                        $item['produto_id'],

                        (string)
                        $item['nome'],

                        $quantidade,

                        $precoUnitario,

                        $subtotalItem
                    );
            }


            /*
            ==============================
            COPIA ENDEREÇO
            ==============================
            */

            $pedidoRepository
                ->adicionarEndereco(
                    $pedidoId,

                    $endereco
                );


            /*
            ==============================
            CRIA PAGAMENTO
            ==============================
            */

            $pedidoRepository
                ->criarPagamento(
                    $pedidoId,

                    $metodoPagamento,

                    $total
                );


            /*
            ==============================
            CONVERTE CARRINHO
            ==============================
            */

            $carrinhoRepository
                ->marcarConvertido(
                    (int)
                    $carrinho['id'],

                    $clienteId
                );


            /*
            ==============================
            COMMIT
            ==============================
            */

            $this->pdo
                ->commit();

        } catch (
            \Throwable $erro
        ) {

            if (
                $this->pdo
                ->inTransaction()
            ) {
                $this->pdo
                    ->rollBack();
            }


            throw $erro;
        }


        /*
        =================================
        SUCESSO
        =================================
        */

        $this->redirecionar(
            '/checkout/sucesso/'
            . $pedidoId
        );
    }


    /*
    =================================
    SUCESSO
    =================================
    */

    public function sucesso(
        int $pedidoId
    ): void {

        $clienteId =
            $this->obterClienteId();


        $repository =
            new PedidoRepository(
                $this->pdo
            );


        $pedido =
            $repository
            ->buscarPorIdDoCliente(
                $pedidoId,

                $clienteId
            );


        if (
            $pedido === null
        ) {

            http_response_code(404);

            require
                APP_ROOT
                . '/views/erros/404.php';

            return;
        }


        $this->view(
            'site/checkout-sucesso',
            [
                'tituloPagina' =>
                    'Pedido realizado',

                'rotaAtual' =>
                    'checkout',

                'pedido' =>
                    $pedido,
            ]
        );
    }


    /*
    =================================
    GERA CÓDIGO DO PEDIDO
    =================================
    */

    private function gerarCodigoPedido(): string
    {
        do {

            $codigo =
                'CL-'
                . date('Ymd')
                . '-'
                . strtoupper(
                    bin2hex(
                        random_bytes(3)
                    )
                );


            $stmt =
                $this->pdo
                ->prepare(
                    "
                        SELECT id
                        FROM pedidos
                        WHERE codigo = :codigo
                        LIMIT 1
                    "
                );


            $stmt->execute([
                ':codigo' =>
                    $codigo,
            ]);


            $existe =
                $stmt->fetch();

        } while (
            $existe !== false
        );


        return $codigo;
    }
}