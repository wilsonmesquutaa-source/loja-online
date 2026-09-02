<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class MercadoPagoService
{
    private const API_URL =
        'https://api.mercadopago.com';

    private string $accessToken;


    public function __construct()
    {
        $this->accessToken =
            $this->obterVariavelAmbiente(
                'MERCADO_PAGO_ACCESS_TOKEN'
            );


        if (
            $this->accessToken === ''
        ) {

            throw new RuntimeException(
                'MERCADO_PAGO_ACCESS_TOKEN não configurado.'
            );
        }
    }


    /*
    =================================
    OBTÉM VARIÁVEL DO AMBIENTE
    =================================
    */

    private function obterVariavelAmbiente(
        string $nome
    ): string {

        $valor =
            $_ENV[$nome]
            ?? null;


        if (
            !is_string($valor)
            ||
            trim($valor) === ''
        ) {

            $valor =
                $_SERVER[$nome]
                ?? null;
        }


        if (
            !is_string($valor)
            ||
            trim($valor) === ''
        ) {

            $valor =
                getenv($nome);
        }


        if (
            $valor === false
            ||
            $valor === null
        ) {

            return '';
        }


        return
            trim(
                (string)
                $valor
            );
    }


    /*
    =================================
    CRIA PAGAMENTO PIX
    =================================
    */

    public function criarPix(
        float $valor,
        string $referenciaExterna,
        string $emailCliente
    ): array {

        if (
            $valor <= 0
        ) {

            throw new RuntimeException(
                'O valor do pagamento deve ser maior que zero.'
            );
        }


        $emailCliente =
            trim(
                $emailCliente
            );


        if (
            $emailCliente === ''
            ||
            !filter_var(
                $emailCliente,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new RuntimeException(
                'E-mail do cliente inválido.'
            );
        }


        $valorFormatado =
            number_format(
                $valor,
                2,
                '.',
                ''
            );


        /*
        =================================
        IDEMPOTÊNCIA
        =================================
        */

        $idempotencyKey =
            bin2hex(
                random_bytes(16)
            );


        /*
        =================================
        PAYLOAD ORDERS API
        =================================
        */

        $payload = [

            'type' =>
                'online',

            'total_amount' =>
                $valorFormatado,

            'external_reference' =>
                $referenciaExterna,

            'processing_mode' =>
                'automatic',

            'transactions' => [

                'payments' => [

                    [

                        'amount' =>
                            $valorFormatado,

                        'payment_method' => [

                            'id' =>
                                'pix',

                            'type' =>
                                'bank_transfer',
                        ],

                        'expiration_time' =>
                            'P1D',
                    ],
                ],
            ],

            'payer' => [

                'email' =>
                    $emailCliente,
            ],
        ];


        /*
        =================================
        ENVIA PARA ORDERS API
        =================================
        */

        $resposta =
            $this->requisicao(
                'POST',
                '/v1/orders',
                $payload,
                [
                    'X-Idempotency-Key: '
                    . $idempotencyKey,
                ]
            );


        /*
        =================================
        VALIDA RESPOSTA
        =================================
        */

        if (
            empty(
                $resposta['id']
            )
        ) {

            throw new RuntimeException(
                'O Mercado Pago não retornou o ID da order.'
            );
        }


        if (
            empty(
                $resposta[
                    'transactions'
                ]['payments'][0]
            )
        ) {

            throw new RuntimeException(
                'O Mercado Pago não retornou os dados do Pix.'
            );
        }


        $pagamento =
            $resposta[
                'transactions'
            ]['payments'][0];


        $paymentMethod =
            $pagamento[
                'payment_method'
            ] ?? [];


        return [

            'order_id' =>
                (string)
                $resposta['id'],

            'payment_id' =>
                isset(
                    $pagamento['id']
                )
                    ? (string)
                    $pagamento['id']
                    : null,

            'status' =>
                isset(
                    $pagamento['status']
                )
                    ? (string)
                    $pagamento['status']
                    : null,

            'status_detail' =>
                isset(
                    $pagamento[
                        'status_detail'
                    ]
                )
                    ? (string)
                    $pagamento[
                        'status_detail'
                    ]
                    : null,

            'ticket_url' =>
                isset(
                    $paymentMethod[
                        'ticket_url'
                    ]
                )
                    ? (string)
                    $paymentMethod[
                        'ticket_url'
                    ]
                    : null,

            'qr_code' =>
                isset(
                    $paymentMethod[
                        'qr_code'
                    ]
                )
                    ? (string)
                    $paymentMethod[
                        'qr_code'
                    ]
                    : null,

            'qr_code_base64' =>
                isset(
                    $paymentMethod[
                        'qr_code_base64'
                    ]
                )
                    ? (string)
                    $paymentMethod[
                        'qr_code_base64'
                    ]
                    : null,

            'raw' =>
                $resposta,
        ];
    }


    /*
    =================================
    CRIA PAGAMENTO CARTÃO
    =================================

    Checkout Transparente
    Orders API
    */

    public function criarCartao(
        float $valor,
        string $referenciaExterna,
        string $emailCliente,
        string $tokenCartao,
        string $paymentMethodId,
        int $parcelas,
        ?string $issuerId = null,
        ?string $identificationType = null,
        ?string $identificationNumber = null,
        ?string $nomeTitular = null
    ): array {

        if (
            $valor <= 0
        ) {

            throw new RuntimeException(
                'O valor do pagamento deve ser maior que zero.'
            );
        }


        $emailCliente =
            trim(
                $emailCliente
            );


        if (
            $emailCliente === ''
            ||
            !filter_var(
                $emailCliente,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new RuntimeException(
                'E-mail do cliente inválido.'
            );
        }


        $tokenCartao =
            trim(
                $tokenCartao
            );


        if (
            $tokenCartao === ''
        ) {

            throw new RuntimeException(
                'Token do cartão não informado.'
            );
        }


        $paymentMethodId =
            trim(
                $paymentMethodId
            );


        if (
            $paymentMethodId === ''
        ) {

            throw new RuntimeException(
                'Meio de pagamento do cartão não informado.'
            );
        }


        if (
            $parcelas < 1
            ||
            $parcelas > 36
        ) {

            throw new RuntimeException(
                'Quantidade de parcelas inválida.'
            );
        }


        $valorFormatado =
            number_format(
                $valor,
                2,
                '.',
                ''
            );


        /*
        =================================
        PAGADOR
        =================================
        */

        $payer = [

            'email' =>
                $emailCliente,
        ];


        /*
        =================================
        IDENTIFICAÇÃO DO PAGADOR
        =================================
        */

        if (
            $identificationType !== null
            &&
            $identificationNumber !== null
            &&
            trim(
                $identificationType
            ) !== ''
            &&
            trim(
                $identificationNumber
            ) !== ''
        ) {

            $payer[
                'identification'
            ] = [

                'type' =>
                    trim(
                        $identificationType
                    ),

                'number' =>
                    trim(
                        $identificationNumber
                    ),
            ];
        }


        /*
        =================================
        PAYLOAD ORDERS API
        =================================

        O Card Payment Brick gera:

        - token
        - payment_method_id
        - installments
        - payer

        Esses dados são enviados ao backend
        para criação da Order.
        */

        $payload = [

            'type' =>
                'online',

            'processing_mode' =>
                'automatic',

            'total_amount' =>
                $valorFormatado,

            'external_reference' =>
                $referenciaExterna,

            'payer' =>
                $payer,

            'transactions' => [

                'payments' => [

                    [

                        'amount' =>
                            $valorFormatado,

                        'payment_method' => [

                            'id' =>
                                $paymentMethodId,

                            /*
                            =================================
                            TIPO DO CARTÃO
                            =================================
                            */

                            'type' =>
                                'credit_card',

                            'token' =>
                                $tokenCartao,

                            'installments' =>
                                $parcelas,
                        ],
                    ],
                ],
            ],
        ];


        /*
        =================================
        IDEMPOTÊNCIA
        =================================
        */

        $idempotencyKey =
            bin2hex(
                random_bytes(16)
            );


        /*
        =================================
        ENVIA PARA ORDERS API
        =================================
        */

        $resposta =
            $this->requisicao(
                'POST',
                '/v1/orders',
                $payload,
                [
                    'X-Idempotency-Key: '
                    . $idempotencyKey,
                ]
            );


        /*
        =================================
        VALIDA ORDER
        =================================
        */

        if (
            empty(
                $resposta['id']
            )
        ) {

            throw new RuntimeException(
                'O Mercado Pago não retornou o ID da order com cartão.'
            );
        }


        /*
        =================================
        VALIDA PAGAMENTO
        =================================
        */

        if (
            empty(
                $resposta[
                    'transactions'
                ]['payments'][0]
            )
        ) {

            throw new RuntimeException(
                'O Mercado Pago não retornou os dados do pagamento com cartão.'
            );
        }


        $pagamento =
            $resposta[
                'transactions'
            ]['payments'][0];


        /*
        =================================
        RETORNO PADRONIZADO
        =================================
        */

        return [

            'order_id' =>
                (string)
                $resposta['id'],

            'payment_id' =>
                isset(
                    $pagamento['id']
                )
                    ? (string)
                    $pagamento['id']
                    : null,

            'status' =>
                isset(
                    $pagamento['status']
                )
                    ? (string)
                    $pagamento['status']
                    : null,

            'status_detail' =>
                isset(
                    $pagamento[
                        'status_detail'
                    ]
                )
                    ? (string)
                    $pagamento[
                        'status_detail'
                    ]
                    : null,

            'ticket_url' =>
                null,

            'qr_code' =>
                null,

            'qr_code_base64' =>
                null,

            'raw' =>
                $resposta,
        ];
    }


    /*
    =================================
    CONSULTA ORDER
    =================================
    */

    public function buscarOrder(
        string $orderId
    ): array {

        $orderId =
            trim(
                $orderId
            );


        if (
            $orderId === ''
        ) {

            throw new RuntimeException(
                'ID da order inválido.'
            );
        }


        return
            $this->requisicao(
                'GET',
                '/v1/orders/'
                . rawurlencode(
                    $orderId
                )
            );
    }


    /*
    =================================
    CONSULTA PAGAMENTO
    =================================

    Mantido para compatibilidade
    com partes antigas do projeto.

    A integração principal via Orders
    deve utilizar buscarOrder().
    */

    public function buscarPagamento(
        string $paymentId
    ): array {

        $paymentId =
            trim(
                $paymentId
            );


        if (
            $paymentId === ''
        ) {

            throw new RuntimeException(
                'ID do pagamento inválido.'
            );
        }


        return
            $this->requisicao(
                'GET',
                '/v1/payments/'
                . rawurlencode(
                    $paymentId
                )
            );
    }


    /*
    =================================
    REQUISIÇÃO HTTP
    =================================
    */

    private function requisicao(
        string $metodo,
        string $endpoint,
        ?array $payload = null,
        array $headersExtras = []
    ): array {

        $url =
            self::API_URL
            . $endpoint;


        $headers = [

            'Accept: application/json',

            'Content-Type: application/json',

            'Authorization: Bearer '
            . $this->accessToken,
        ];


        foreach (
            $headersExtras
            as $header
        ) {

            $headers[] =
                $header;
        }


        $ch =
            curl_init(
                $url
            );


        if (
            $ch === false
        ) {

            throw new RuntimeException(
                'Não foi possível iniciar a conexão com o Mercado Pago.'
            );
        }


        $opcoes = [

            CURLOPT_RETURNTRANSFER =>
                true,

            CURLOPT_HTTPHEADER =>
                $headers,

            CURLOPT_TIMEOUT =>
                30,

            CURLOPT_CONNECTTIMEOUT =>
                10,

            CURLOPT_CUSTOMREQUEST =>
                $metodo,

            CURLOPT_SSL_VERIFYPEER =>
                true,

            CURLOPT_SSL_VERIFYHOST =>
                2,
        ];


        if (
            $payload !== null
        ) {

            $json =
                json_encode(
                    $payload,
                    JSON_UNESCAPED_UNICODE
                    |
                    JSON_UNESCAPED_SLASHES
                    |
                    JSON_THROW_ON_ERROR
                );


            $opcoes[
                CURLOPT_POSTFIELDS
            ] =
                $json;
        }


        curl_setopt_array(
            $ch,
            $opcoes
        );


        $corpo =
            curl_exec(
                $ch
            );


        $erroCurl =
            curl_error(
                $ch
            );


        $codigoHttp =
            (int)
            curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );


        curl_close(
            $ch
        );


        if (
            $corpo === false
        ) {

            throw new RuntimeException(
                'Erro de comunicação com o Mercado Pago: '
                . $erroCurl
            );
        }


        $dados =
            json_decode(
                $corpo,
                true
            );


        /*
        =================================
        RESPOSTA INVÁLIDA
        =================================
        */

        if (
            !is_array(
                $dados
            )
        ) {

            throw new RuntimeException(
                'O Mercado Pago retornou uma resposta inválida.'
                . ' HTTP: '
                . $codigoHttp
                . ' Resposta: '
                . $corpo
            );
        }


        /*
        =================================
        TRATAMENTO DE ERRO DA API
        =================================
        */

        if (
            $codigoHttp < 200
            ||
            $codigoHttp >= 300
        ) {

            $partesMensagem = [];


            /*
            =================================
            MESSAGE
            =================================
            */

            if (
                isset(
                    $dados['message']
                )
                &&
                is_string(
                    $dados['message']
                )
            ) {

                $partesMensagem[] =
                    'message: '
                    . $dados['message'];
            }


            /*
            =================================
            ERROR
            =================================
            */

            if (
                isset(
                    $dados['error']
                )
                &&
                is_string(
                    $dados['error']
                )
            ) {

                $partesMensagem[] =
                    'error: '
                    . $dados['error'];
            }


            /*
            =================================
            CAUSE
            =================================
            */

            if (
                isset(
                    $dados['cause']
                )
            ) {

                if (
                    is_string(
                        $dados['cause']
                    )
                ) {

                    $partesMensagem[] =
                        'cause: '
                        . $dados['cause'];

                } elseif (
                    is_array(
                        $dados['cause']
                    )
                ) {

                    $partesMensagem[] =
                        'cause: '
                        .
                        json_encode(
                            $dados['cause'],
                            JSON_UNESCAPED_UNICODE
                            |
                            JSON_UNESCAPED_SLASHES
                        );
                }
            }


            /*
            =================================
            DETAILS
            =================================
            */

            if (
                isset(
                    $dados['details']
                )
            ) {

                if (
                    is_string(
                        $dados['details']
                    )
                ) {

                    $partesMensagem[] =
                        'details: '
                        . $dados['details'];

                } elseif (
                    is_array(
                        $dados['details']
                    )
                ) {

                    $partesMensagem[] =
                        'details: '
                        .
                        json_encode(
                            $dados['details'],
                            JSON_UNESCAPED_UNICODE
                            |
                            JSON_UNESCAPED_SLASHES
                        );
                }
            }


            /*
            =================================
            RESPOSTA COMPLETA
            =================================

            Caso o Mercado Pago utilize
            outro campo para informar o erro,
            ainda teremos a resposta original.
            */

            $respostaCompleta =
                json_encode(
                    $dados,
                    JSON_UNESCAPED_UNICODE
                    |
                    JSON_UNESCAPED_SLASHES
                );


            $mensagem =
                'Erro ao comunicar com o Mercado Pago.';


            if (
                !empty(
                    $partesMensagem
                )
            ) {

                $mensagem .=
                    ' '
                    .
                    implode(
                        ' | ',
                        $partesMensagem
                    );
            }


            $mensagem .=
                ' Código HTTP: '
                . $codigoHttp;


            $mensagem .=
                ' Resposta: '
                . $respostaCompleta;


            throw new RuntimeException(
                $mensagem
            );
        }


        return
            $dados;
    }
}