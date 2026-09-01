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
            trim(
                (string) getenv(
                    'MERCADO_PAGO_ACCESS_TOKEN'
                )
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
        PAYLOAD
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


        if (
            $nomeTitular !== null
            &&
            trim(
                $nomeTitular
            ) !== ''
        ) {

            $payer[
                'first_name'
            ] =
                trim(
                    $nomeTitular
                );
        }


        /*
        =================================
        PAYLOAD
        =================================
        */

        $payload = [

            'transaction_amount' =>
                (float)
                $valorFormatado,

            'token' =>
                $tokenCartao,

            'description' =>
                'Pedido '
                . $referenciaExterna,

            'installments' =>
                $parcelas,

            'payment_method_id' =>
                $paymentMethodId,

            'external_reference' =>
                $referenciaExterna,

            'payer' =>
                $payer,

            'binary_mode' =>
                false,

            'three_d_secure_mode' =>
                'optional',
        ];


        if (
            $issuerId !== null
            &&
            trim(
                $issuerId
            ) !== ''
        ) {

            $payload[
                'issuer_id'
            ] =
                trim(
                    $issuerId
                );
        }


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
        ENVIA PARA API DE PAYMENTS
        =================================
        */

        $resposta =
            $this->requisicao(
                'POST',
                '/v1/payments',
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
                'O Mercado Pago não retornou o ID do pagamento com cartão.'
            );
        }


        return [

            'order_id' =>
                null,

            'payment_id' =>
                (string)
                $resposta['id'],

            'status' =>
                isset(
                    $resposta['status']
                )
                    ? (string)
                    $resposta['status']
                    : null,

            'status_detail' =>
                isset(
                    $resposta[
                        'status_detail'
                    ]
                )
                    ? (string)
                    $resposta[
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


        if (
            !is_array(
                $dados
            )
        ) {

            throw new RuntimeException(
                'O Mercado Pago retornou uma resposta inválida.'
            );
        }


        if (
            $codigoHttp < 200
            ||
            $codigoHttp >= 300
        ) {

            $mensagem =
                'Erro ao comunicar com o Mercado Pago.';


            if (
                isset(
                    $dados['message']
                )
                &&
                is_string(
                    $dados['message']
                )
            ) {

                $mensagem =
                    $dados['message'];
            }


            throw new RuntimeException(
                $mensagem
                . ' Código HTTP: '
                . $codigoHttp
            );
        }


        return
            $dados;
    }
}