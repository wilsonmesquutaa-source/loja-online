<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\CarrinhoRepository;
use App\Repositories\EnderecoRepository;
use App\Repositories\PedidoRepository;
use App\Services\EntregaService;
use App\Services\MercadoPagoService;
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
    E-MAIL DO CLIENTE
    =================================
    */

    private function obterEmailCliente(): string
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }


        $email =
            trim(
                (string)
                (
                    $_SESSION['cliente_email']
                    ?? ''
                )
            );


        if (
            $email === ''
            ||
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new RuntimeException(
                'Não foi possível identificar o e-mail do cliente.'
            );
        }


        return $email;
    }


    /*
    =================================
    MAPA STATUS MERCADO PAGO
    =================================
    */

    private function mapearStatusPagamento(
        ?string $status
    ): string {

        $status =
            strtolower(
                trim(
                    (string)
                    $status
                )
            );


        switch (
            $status
        ) {

            case 'approved':

                return 'aprovado';


            case 'rejected':

                return 'recusado';


            case 'cancelled':
            case 'canceled':

                return 'cancelado';


            case 'refunded':

                return 'reembolsado';


            case 'pending':
            case 'in_process':
            case 'in_process_payment':

                return 'pendente';


            default:

                return 'pendente';
        }
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
        HORÁRIOS
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
        PODE FINALIZAR
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
        CHAVE PÚBLICA MERCADO PAGO
        =================================
        */

        $mercadoPagoPublicKey =
            trim(
                (string) getenv(
                    'MERCADO_PAGO_PUBLIC_KEY'
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

                'mercadoPagoPublicKey' =>
                    $mercadoPagoPublicKey,

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
        E-MAIL
        =================================
        */

        $emailCliente =
            $this->obterEmailCliente();


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
        HORÁRIO
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
        DADOS DO CARTÃO
        =================================
        */

        $cardToken =
            trim(
                (string)
                (
                    $_POST[
                        'card_token'
                    ]
                    ?? ''
                )
            );


        $cardPaymentMethodId =
            trim(
                (string)
                (
                    $_POST[
                        'card_payment_method_id'
                    ]
                    ?? ''
                )
            );


        $cardIssuerId =
            trim(
                (string)
                (
                    $_POST[
                        'card_issuer_id'
                    ]
                    ?? ''
                )
            );


        $cardInstallments =
            filter_var(
                $_POST[
                    'card_installments'
                ]
                ?? null,
                FILTER_VALIDATE_INT
            );


        $cardholderName =
            trim(
                (string)
                (
                    $_POST[
                        'cardholder_name'
                    ]
                    ?? ''
                )
            );


        $cardholderIdentificationType =
            trim(
                (string)
                (
                    $_POST[
                        'cardholder_identification_type'
                    ]
                    ?? ''
                )
            );


        $cardholderIdentificationNumber =
            trim(
                (string)
                (
                    $_POST[
                        'cardholder_identification_number'
                    ]
                    ?? ''
                )
            );


        if (
            $metodoPagamento ===
            'cartao'
        ) {

            if (
                $cardToken === ''
            ) {

                $this->redirecionar(
                    '/checkout?recebimento='
                    . urlencode(
                        $modalidadeRecebimento
                    )
                    . '&erro='
                    . rawurlencode(
                        'Não foi possível gerar o token do cartão. Tente novamente.'
                    )
                );

                return;
            }


            if (
                $cardPaymentMethodId === ''
            ) {

                $this->redirecionar(
                    '/checkout?recebimento='
                    . urlencode(
                        $modalidadeRecebimento
                    )
                    . '&erro='
                    . rawurlencode(
                        'Não foi possível identificar a bandeira do cartão.'
                    )
                );

                return;
            }


            if (
                $cardInstallments === false
                ||
                $cardInstallments < 1
            ) {

                $cardInstallments =
                    1;
            }


            if (
                $cardInstallments > 36
            ) {

                $cardInstallments =
                    36;
            }
        }


        /*
        =================================
        ENDEREÇO DE ENTREGA
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

            } catch (
                RuntimeException $erroFrete
            ) {

                $this->redirecionar(
                    '/checkout?recebimento=entrega&endereco='
                    . (int) $enderecoId
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


        if (
            $total <= 0
        ) {

            throw new RuntimeException(
                'O valor total do pedido deve ser maior que zero.'
            );
        }


        /*
        =================================
        CÓDIGO
        =================================
        */

        $codigo =
            $this->gerarCodigoPedido();


        /*
        =================================
        REPOSITORY
        =================================
        */

        $pedidoRepository =
            new PedidoRepository(
                $this->pdo
            );


        /*
        =================================
        DATAS
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
            CRIA PAGAMENTO LOCAL
            ==============================
            */

            $pagamentoId =
                $pedidoRepository
                ->criarPagamento(
                    $pedidoId,

                    $metodoPagamento,

                    $total
                );


            /*
            ==============================
            MERCADO PAGO
            ==============================
            */

            $mercadoPago =
                new MercadoPagoService();


            /*
            ==============================
            PIX
            ==============================
            */

            if (
                $metodoPagamento ===
                'pix'
            ) {

                $pagamentoMercadoPago =
                    $mercadoPago
                    ->criarPix(
                        $total,

                        'pedido_' . $pedidoId,

                        $emailCliente
                    );

            /*
            ==============================
            CARTÃO
            ==============================
            */

            } else {

                $pagamentoMercadoPago =
                    $mercadoPago
                    ->criarCartao(
                        $total,

                        'pedido_' . $pedidoId,

                        $emailCliente,

                        $cardToken,

                        $cardPaymentMethodId,

                        (int)
                        $cardInstallments,

                        $cardIssuerId !== ''
                            ? $cardIssuerId
                            : null,

                        $cardholderIdentificationType !== ''
                            ? $cardholderIdentificationType
                            : null,

                        $cardholderIdentificationNumber !== ''
                            ? $cardholderIdentificationNumber
                            : null,

                        $cardholderName !== ''
                            ? $cardholderName
                            : null
                    );
            }


            /*
            ==============================
            STATUS MERCADO PAGO
            ==============================
            */

            $statusMercadoPago =
                isset(
                    $pagamentoMercadoPago[
                        'status'
                    ]
                )
                    ? (string)
                    $pagamentoMercadoPago[
                        'status'
                    ]
                    : null;


            /*
            ==============================
            STATUS LOCAL
            ==============================
            */

            $statusPagamento =
                $this->mapearStatusPagamento(
                    $statusMercadoPago
                );


            /*
            ==============================
            EXPIRAÇÃO PIX
            ==============================
            */

            $expiraEm =
                null;


            if (
                $metodoPagamento ===
                'pix'
            ) {

                $expiraEm =
                    (new DateTimeImmutable(
                        'now',
                        new DateTimeZone(
                            self::TIMEZONE
                        )
                    ))
                    ->modify(
                        '+24 hours'
                    )
                    ->format(
                        'Y-m-d H:i:s'
                    );
            }


            /*
            ==============================
            ID EXTERNO
            ==============================
            */

            $pagamentoExternoId =
                $pagamentoMercadoPago[
                    'payment_id'
                ]
                ??
                $pagamentoMercadoPago[
                    'order_id'
                ]
                ??
                null;


            /*
            ==============================
            ATUALIZA PAGAMENTO
            ==============================
            */

            $pedidoRepository
                ->atualizarDadosPagamento(
                    $pagamentoId,

                    $pagamentoExternoId,

                    $pagamentoMercadoPago[
                        'qr_code'
                    ]
                        ??
                        null,

                    $expiraEm,

                    $statusPagamento
                );


            /*
            ==============================
            APROVADO
            ==============================
            */

            if (
                $statusPagamento ===
                'aprovado'
            ) {

                $agora =
                    (new DateTimeImmutable(
                        'now',
                        new DateTimeZone(
                            self::TIMEZONE
                        )
                    ))
                    ->format(
                        'Y-m-d H:i:s'
                    );


                $pedidoRepository
                    ->atualizarStatusPagamento(
                        $pagamentoId,

                        'aprovado',

                        $agora
                    );


                $pedidoRepository
                    ->atualizarStatus(
                        $pedidoId,

                        'pago'
                    );
            }


            /*
            ==============================
            RECUSADO
            ==============================
            */

            elseif (
                $statusPagamento ===
                'recusado'
            ) {

                $pedidoRepository
                    ->atualizarStatusPagamento(
                        $pagamentoId,

                        'recusado'
                    );


                $pedidoRepository
                    ->atualizarStatus(
                        $pedidoId,

                        'cancelado'
                    );
            }


            /*
            ==============================
            CANCELADO
            ==============================
            */

            elseif (
                $statusPagamento ===
                'cancelado'
            ) {

                $pedidoRepository
                    ->atualizarStatusPagamento(
                        $pagamentoId,

                        'cancelado'
                    );


                $pedidoRepository
                    ->atualizarStatus(
                        $pedidoId,

                        'cancelado'
                    );
            }


            /*
            ==============================
            GUARDA ORDER NA SESSÃO
            ==============================
            */

            if (
                session_status() !==
                PHP_SESSION_ACTIVE
            ) {

                session_start();
            }


            if (
                isset(
                    $pagamentoMercadoPago[
                        'order_id'
                    ]
                )
                &&
                $pagamentoMercadoPago[
                    'order_id'
                ] !== null
            ) {

                $_SESSION[
                    'mercado_pago_order_' . $pedidoId
                ] =
                    $pagamentoMercadoPago[
                        'order_id'
                    ];
            }


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


        /*
        =================================
        PEDIDO
        =================================
        */

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


        /*
        =================================
        PAGAMENTO
        =================================
        */

        $pagamento =
            $repository
            ->buscarPagamento(
                $pedidoId
            );


        /*
        =================================
        VIEW
        =================================
        */

        $this->view(
            'site/checkout-sucesso',
            [
                'tituloPagina' =>
                    'Pagamento do pedido',

                'rotaAtual' =>
                    'checkout',

                'pedido' =>
                    $pedido,

                'pagamento' =>
                    $pagamento,
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
                        SELECT
                            id
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