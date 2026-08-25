<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\CarrinhoRepository;
use App\Repositories\EnderecoRepository;
use App\Repositories\PedidoRepository;
use App\Services\EntregaService;
use App\Services\PedidoAgendaService;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

final class CheckoutController extends Controller
{
    /*
    =================================
    ENDEREÇO DE RETIRADA
    =================================
    */

    private const ENDERECO_RETIRADA =
        'Rua Dragão do Mar, 608, Praia de Iracema, Fortaleza - CE';


    /*
    =================================
    FUSO HORÁRIO
    =================================
    */

    private const TIMEZONE =
        'America/Fortaleza';


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
                '/cadastro?retorno=carrinho'
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
        MODALIDADE
        =================================
        */

        $modalidadeRecebimento =
            strtolower(
                trim(
                    (string)
                    (
                        $_GET['recebimento']
                        ?? 'entrega'
                    )
                )
            );


        if (
            !in_array(
                $modalidadeRecebimento,
                [
                    'entrega',
                    'retirada',
                ],
                true
            )
        ) {

            $modalidadeRecebimento =
                'entrega';
        }


        /*
        =================================
        REPOSITORIES / SERVICES
        =================================
        */

        $carrinhoRepository =
            new CarrinhoRepository(
                $this->pdo
            );


        $enderecoRepository =
            new EnderecoRepository(
                $this->pdo
            );


        $pedidoAgendaService =
            new PedidoAgendaService();


        /*
        =================================
        CARRINHO
        =================================
        */

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


        if (
            $modalidadeRecebimento ===
            'entrega'
        ) {

            /*
            ==============================
            ENDEREÇO INFORMADO NA URL
            ==============================
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
            ==============================
            ENDEREÇO PRINCIPAL
            ==============================
            */

            if (
                $enderecoSelecionado === null
                &&
                $enderecos !== []
            ) {

                foreach (
                    $enderecos as $endereco
                ) {

                    if (
                        (int)
                        $endereco['principal']
                        ===
                        1
                    ) {

                        $enderecoSelecionado =
                            $endereco;

                        break;
                    }
                }


                /*
                ==============================
                PRIMEIRO ENDEREÇO
                ==============================
                */

                if (
                    $enderecoSelecionado === null
                ) {

                    $enderecoSelecionado =
                        $enderecos[0];
                }
            }
        }


        /*
        =================================
        FRETE
        =================================
        */

        $frete =
            0.0;


        $distanciaKm =
            null;


        $freteDisponivel =
            true;


        if (
            $modalidadeRecebimento ===
            'entrega'
        ) {

            if (
                $enderecoSelecionado === null
            ) {

                $freteDisponivel =
                    false;

            } else {

                try {

                    $entregaService =
                        new EntregaService();


                    $entrega =
                        $entregaService
                        ->calcular(
                            $enderecoSelecionado
                        );


                    $frete =
                        (float)
                        $entrega['frete'];


                    $distanciaKm =
                        (float)
                        $entrega['distancia_km'];

                } catch (
                    RuntimeException $erroFrete
                ) {

                    $freteDisponivel =
                        false;


                    $frete =
                        0.0;


                    $distanciaKm =
                        null;
                }
            }
        }


        /*
        =================================
        RETIRADA
        =================================
        */

        if (
            $modalidadeRecebimento ===
            'retirada'
        ) {

            $frete =
                0.0;


            $freteDisponivel =
                true;


            $distanciaKm =
                null;
        }


        /*
        =================================
        HORÁRIOS DISPONÍVEIS
        =================================
        */

        $horariosDisponiveis =
            [];


        if (
            $modalidadeRecebimento ===
            'retirada'
        ) {

            $horariosDisponiveis =
                $pedidoAgendaService
                ->gerarHorariosRetirada(
                    $itens
                );

        } elseif (
            $modalidadeRecebimento ===
            'entrega'
            &&
            $enderecoSelecionado !== null
            &&
            $freteDisponivel
        ) {

            $horariosDisponiveis =
                $pedidoAgendaService
                ->gerarHorariosEntrega(
                    $itens
                );
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
            $frete
            -
            $desconto;


        /*
        =================================
        PODE FINALIZAR?
        =================================
        */

        $podeFinalizar =
            $horariosDisponiveis !== []
            &&
            (
                $modalidadeRecebimento ===
                'retirada'

                ||

                (
                    $modalidadeRecebimento ===
                    'entrega'

                    &&
                    $enderecoSelecionado !== null

                    &&
                    $freteDisponivel
                )
            );


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

                'modalidadeRecebimento' =>
                    $modalidadeRecebimento,

                'enderecoRetirada' =>
                    self::ENDERECO_RETIRADA,

                'subtotal' =>
                    $subtotal,

                'frete' =>
                    $frete,

                'distanciaKm' =>
                    $distanciaKm,

                'freteDisponivel' =>
                    $freteDisponivel,

                'horariosDisponiveis' =>
                    $horariosDisponiveis,

                'podeFinalizar' =>
                    $podeFinalizar,

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
        MODALIDADE
        =================================
        */

        $modalidadeRecebimento =
            strtolower(
                trim(
                    (string)
                    (
                        $_POST[
                            'modalidade_recebimento'
                        ]
                        ?? ''
                    )
                )
            );


        if (
            !in_array(
                $modalidadeRecebimento,
                [
                    'entrega',
                    'retirada',
                ],
                true
            )
        ) {

            $this->redirecionar(
                '/checkout?erro='
                . rawurlencode(
                    'Selecione a forma de recebimento.'
                )
            );

            return;
        }


        /*
        =================================
        HORÁRIO ESCOLHIDO
        =================================
        */

        $dataHoraAgendada =
            trim(
                (string)
                (
                    $_POST[
                        'data_hora_agendada'
                    ]
                    ?? ''
                )
            );


        if (
            $dataHoraAgendada === ''
        ) {

            $this->redirecionar(
                '/checkout?recebimento='
                . urlencode(
                    $modalidadeRecebimento
                )
                . '&erro='
                . rawurlencode(
                    'Selecione um horário para receber ou retirar o pedido.'
                )
            );

            return;
        }


        /*
        =================================
        ENDEREÇO ID
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
                '/checkout?recebimento='
                . urlencode(
                    $modalidadeRecebimento
                )
                . '&erro='
                . rawurlencode(
                    'Selecione uma forma de pagamento.'
                )
            );

            return;
        }


        /*
        =================================
        ENDEREÇO
        =================================
        */

        $endereco =
            null;


        if (
            $modalidadeRecebimento ===
            'entrega'
        ) {

            if (
                !$enderecoId
            ) {

                $this->redirecionar(
                    '/checkout?recebimento=entrega&erro='
                    . rawurlencode(
                        'Selecione um endereço de entrega.'
                    )
                );

                return;
            }


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
                    '/checkout?recebimento=entrega&erro='
                    . rawurlencode(
                        'O endereço selecionado não é válido.'
                    )
                );

                return;
            }
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
        VALIDA HORÁRIO
        =================================
        */

        $pedidoAgendaService =
            new PedidoAgendaService();


        try {

            $horarioAgendado =
                new DateTimeImmutable(
                    $dataHoraAgendada,
                    new DateTimeZone(
                        self::TIMEZONE
                    )
                );


            $agendaEscolhida =
                $pedidoAgendaService
                ->calcularAgendaEscolhida(
                    $itens,
                    $horarioAgendado
                );

        } catch (
            \Throwable $erroAgenda
        ) {

            $url =
                '/checkout?recebimento='
                . urlencode(
                    $modalidadeRecebimento
                );


            if (
                $modalidadeRecebimento ===
                'entrega'
                &&
                $enderecoId
            ) {

                $url .=
                    '&endereco='
                    . (int)
                    $enderecoId;
            }


            $url .=
                '&erro='
                . rawurlencode(
                    $erroAgenda->getMessage()
                );


            $this->redirecionar(
                $url
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
                    $item[
                        'preco_unitario'
                    ]
                )
                *
                (
                    (int)
                    $item[
                        'quantidade'
                    ]
                );
        }


        /*
        =================================
        FRETE
        =================================
        */

        $frete =
            0.0;


        $distanciaKm =
            null;


        if (
            $modalidadeRecebimento ===
            'entrega'
        ) {

            try {

                $entregaService =
                    new EntregaService();


                $entrega =
                    $entregaService
                    ->calcular(
                        $endereco
                    );


                $frete =
                    (float)
                    $entrega[
                        'frete'
                    ];


                $distanciaKm =
                    (float)
                    $entrega[
                        'distancia_km'
                    ];

            } catch (
                RuntimeException $erroFrete
            ) {

                $this->redirecionar(
                    '/checkout?recebimento=entrega&endereco='
                    . (int)
                    $enderecoId
                    . '&erro='
                    . rawurlencode(
                        'Não foi possível calcular o frete. Tente novamente.'
                    )
                );

                return;
            }
        }


        /*
        =================================
        RETIRADA
        =================================
        */

        if (
            $modalidadeRecebimento ===
            'retirada'
        ) {

            $frete =
                0.0;


            $distanciaKm =
                null;
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
        CONVERTE DATAS
        =================================
        */

        $dataHoraAgendadaBanco =
            $agendaEscolhida[
                'data_hora_agendada'
            ]->format(
                'Y-m-d H:i:s'
            );


        $inicioPreparoBanco =
            $agendaEscolhida[
                'inicio_preparo'
            ]->format(
                'Y-m-d H:i:s'
            );


        $fimPreparoBanco =
            $agendaEscolhida[
                'fim_preparo_previsto'
            ]->format(
                'Y-m-d H:i:s'
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
            ASSOCIA CARRINHO
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

                    $modalidadeRecebimento,

                    $dataHoraAgendadaBanco,

                    $inicioPreparoBanco,

                    $fimPreparoBanco,

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
                    $item[
                        'quantidade'
                    ];


                $precoUnitario =
                    (float)
                    $item[
                        'preco_unitario'
                    ];


                $subtotalItem =
                    $quantidade
                    *
                    $precoUnitario;


                $pedidoRepository
                    ->adicionarItem(
                        $pedidoId,

                        (int)
                        $item[
                            'produto_id'
                        ],

                        (string)
                        $item[
                            'nome'
                        ],

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

            if (
                $modalidadeRecebimento ===
                'entrega'
                &&
                $endereco !== null
            ) {

                $pedidoRepository
                    ->adicionarEndereco(
                        $pedidoId,

                        $endereco
                    );
            }


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