<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class EntregaService
{
    /*
    =================================
    VALOR POR QUILÔMETRO
    =================================
    */

    private const VALOR_POR_KM = 2.00;


    /*
    =================================
    ENDEREÇO DE ORIGEM
    =================================
    */

    private const ENDERECO_ORIGEM =
        'Rua Dragão do Mar, 608, Praia de Iracema, Fortaleza - CE, Brasil';


    /*
    =================================
    CALCULA ENTREGA
    =================================
    */

    public function calcular(
        array $enderecoDestino
    ): array {

        $apiKey =
            (string)
            (
                $_ENV['GOOGLE_MAPS_API_KEY']
                ?? ''
            );


        if (
            $apiKey === ''
        ) {

            throw new RuntimeException(
                'A chave da API do Google Maps não foi configurada.'
            );
        }


        $enderecoDestinoTexto =
            $this->montarEndereco(
                $enderecoDestino
            );


        if (
            $enderecoDestinoTexto === ''
        ) {

            throw new RuntimeException(
                'O endereço de destino está incompleto.'
            );
        }


        /*
        =================================
        DADOS DA ROTA
        =================================
        */

        $dados =
            [
                'origin' => [
                    'address' =>
                        self::ENDERECO_ORIGEM,
                ],

                'destination' => [
                    'address' =>
                        $enderecoDestinoTexto,
                ],

                'travelMode' =>
                    'DRIVE',
            ];


        /*
        =================================
        REQUISIÇÃO
        =================================
        */

        $curl =
            curl_init(
                'https://routes.googleapis.com/directions/v2:computeRoutes'
            );


        if (
            $curl === false
        ) {

            throw new RuntimeException(
                'Não foi possível iniciar a consulta da rota.'
            );
        }


        $json =
            json_encode(
                $dados,
                JSON_UNESCAPED_UNICODE
                |
                JSON_UNESCAPED_SLASHES
            );


        if (
            $json === false
        ) {

            curl_close(
                $curl
            );

            throw new RuntimeException(
                'Não foi possível preparar os dados da rota.'
            );
        }


        curl_setopt_array(
            $curl,
            [
                CURLOPT_RETURNTRANSFER =>
                    true,

                CURLOPT_POST =>
                    true,

                CURLOPT_POSTFIELDS =>
                    $json,

                CURLOPT_HTTPHEADER => [

                    'Content-Type: application/json',

                    'X-Goog-Api-Key: '
                    . $apiKey,

                    'X-Goog-FieldMask: '
                    . 'routes.distanceMeters',
                ],

                CURLOPT_CONNECTTIMEOUT =>
                    10,

                CURLOPT_TIMEOUT =>
                    20,
            ]
        );


        $resposta =
            curl_exec(
                $curl
            );


        $codigoHttp =
            (int)
            curl_getinfo(
                $curl,
                CURLINFO_HTTP_CODE
            );


        $erroCurl =
            curl_error(
                $curl
            );


        curl_close(
            $curl
        );


        if (
            $resposta === false
        ) {

            throw new RuntimeException(
                'Não foi possível consultar a rota.'
                . (
                    $erroCurl !== ''
                    ? ' ' . $erroCurl
                    : ''
                )
            );
        }


        /*
        =================================
        DECODIFICA RESPOSTA
        =================================
        */

        $dadosResposta =
            json_decode(
                $resposta,
                true
            );


        if (
            !is_array(
                $dadosResposta
            )
        ) {

            throw new RuntimeException(
                'A resposta da API de rotas é inválida.'
            );
        }


        /*
        =================================
        ERRO DA API
        =================================
        */

        if (
            $codigoHttp < 200
            ||
            $codigoHttp >= 300
        ) {

            $mensagem =
                $dadosResposta[
                    'error'
                ][
                    'message'
                ]
                ??
                'Não foi possível calcular a distância da entrega.';


            throw new RuntimeException(
                $mensagem
            );
        }


        /*
        =================================
        DISTÂNCIA
        =================================
        */

        $distanciaMetros =
            $dadosResposta[
                'routes'
            ][0][
                'distanceMeters'
            ]
            ??
            null;


        if (
            !is_numeric(
                $distanciaMetros
            )
        ) {

            throw new RuntimeException(
                'A rota não retornou a distância da entrega.'
            );
        }


        $distanciaMetros =
            (float)
            $distanciaMetros;


        $distanciaKm =
            $distanciaMetros
            /
            1000;


        /*
        =================================
        FRETE
        =================================
        */

        $frete =
            $distanciaKm
            *
            self::VALOR_POR_KM;


        /*
        =================================
        ARREDONDAMENTO
        =================================
        */

        $frete =
            round(
                $frete,
                2
            );


        $distanciaKm =
            round(
                $distanciaKm,
                2
            );


        return [
            'distancia_metros' =>
                (int)
                round(
                    $distanciaMetros
                ),

            'distancia_km' =>
                $distanciaKm,

            'valor_por_km' =>
                self::VALOR_POR_KM,

            'frete' =>
                $frete,
        ];
    }


    /*
    =================================
    MONTA ENDEREÇO
    =================================
    */

    private function montarEndereco(
        array $endereco
    ): string {

        $partes = [];


        /*
        LOGRADOURO + NÚMERO
        */

        $logradouro =
            trim(
                (string)
                (
                    $endereco[
                        'logradouro'
                    ]
                    ?? ''
                )
            );


        $numero =
            trim(
                (string)
                (
                    $endereco[
                        'numero'
                    ]
                    ?? ''
                )
            );


        if (
            $logradouro !== ''
        ) {

            $partes[] =
                $logradouro
                .
                (
                    $numero !== ''
                    ? ', ' . $numero
                    : ''
                );
        }


        /*
        COMPLEMENTO
        */

        $complemento =
            trim(
                (string)
                (
                    $endereco[
                        'complemento'
                    ]
                    ?? ''
                )
            );


        if (
            $complemento !== ''
        ) {

            $partes[] =
                $complemento;
        }


        /*
        BAIRRO
        */

        $bairro =
            trim(
                (string)
                (
                    $endereco[
                        'bairro'
                    ]
                    ?? ''
                )
            );


        if (
            $bairro !== ''
        ) {

            $partes[] =
                $bairro;
        }


        /*
        CIDADE
        */

        $cidade =
            trim(
                (string)
                (
                    $endereco[
                        'cidade'
                    ]
                    ?? ''
                )
            );


        if (
            $cidade !== ''
        ) {

            $partes[] =
                $cidade;
        }


        /*
        ESTADO
        */

        $estado =
            strtoupper(
                trim(
                    (string)
                    (
                        $endereco[
                            'estado'
                        ]
                        ?? ''
                    )
                )
            );


        if (
            $estado !== ''
        ) {

            $partes[] =
                $estado;
        }


        /*
        PAÍS
        */

        $partes[] =
            'Brasil';


        return implode(
            ', ',
            array_filter(
                $partes,
                static function (
                    $parte
                ): bool {

                    return
                        trim(
                            (string)
                            $parte
                        )
                        !== '';
                }
            )
        );
    }
}