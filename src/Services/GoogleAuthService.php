<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class GoogleAuthService
{
    private const AUTHORIZATION_ENDPOINT =
        'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_ENDPOINT =
        'https://oauth2.googleapis.com/token';

    private const USERINFO_ENDPOINT =
        'https://openidconnect.googleapis.com/v1/userinfo';


    private string $clientId;

    private string $clientSecret;

    private string $redirectUri;


    public function __construct()
    {
        $this->clientId =
            trim(
                (string) (
                    $_ENV['GOOGLE_CLIENT_ID']
                    ?? getenv('GOOGLE_CLIENT_ID')
                    ?? ''
                )
            );


        $this->clientSecret =
            trim(
                (string) (
                    $_ENV['GOOGLE_CLIENT_SECRET']
                    ?? getenv('GOOGLE_CLIENT_SECRET')
                    ?? ''
                )
            );


        $this->redirectUri =
            trim(
                (string) (
                    $_ENV['GOOGLE_REDIRECT_URI']
                    ?? getenv('GOOGLE_REDIRECT_URI')
                    ?? ''
                )
            );


        if (
            $this->clientId === ''
            ||
            $this->clientSecret === ''
            ||
            $this->redirectUri === ''
        ) {

            throw new RuntimeException(
                'As configurações do Google OAuth não foram encontradas.'
            );
        }
    }


    /*
    =================================
    URL DE AUTORIZAÇÃO
    =================================
    */

    public function criarUrlAutorizacao(
        string $state
    ): string {

        $parametros = [

            'client_id' =>
                $this->clientId,

            'redirect_uri' =>
                $this->redirectUri,

            'response_type' =>
                'code',

            'scope' =>
                'openid profile email',

            'state' =>
                $state,

            'access_type' =>
                'online',

            'include_granted_scopes' =>
                'true',

            'prompt' =>
                'select_account',
        ];


        return
            self::AUTHORIZATION_ENDPOINT
            . '?'
            . http_build_query(
                $parametros,
                '',
                '&',
                PHP_QUERY_RFC3986
            );
    }


    /*
    =================================
    TROCA CODE POR TOKEN
    =================================
    */

    public function trocarCodigoPorToken(
        string $code
    ): array {

        $dados = [

            'code' =>
                $code,

            'client_id' =>
                $this->clientId,

            'client_secret' =>
                $this->clientSecret,

            'redirect_uri' =>
                $this->redirectUri,

            'grant_type' =>
                'authorization_code',
        ];


        $resposta =
            $this->requisicaoPost(
                self::TOKEN_ENDPOINT,
                $dados
            );


        if (
            !isset(
                $resposta['access_token']
            )
        ) {

            throw new RuntimeException(
                'O Google não retornou um access token válido.'
            );
        }


        return $resposta;
    }


    /*
    =================================
    BUSCA DADOS DO USUÁRIO
    =================================
    */

    public function buscarUsuario(
        string $accessToken
    ): array {

        $resposta =
            $this->requisicaoGet(
                self::USERINFO_ENDPOINT,
                $accessToken
            );


        $sub =
            trim(
                (string) (
                    $resposta['sub']
                    ?? ''
                )
            );


        $email =
            trim(
                strtolower(
                    (string) (
                        $resposta['email']
                        ?? ''
                    )
                )
            );


        $nome =
            trim(
                (string) (
                    $resposta['name']
                    ?? ''
                )
            );


        $foto =
            trim(
                (string) (
                    $resposta['picture']
                    ?? ''
                )
            );


        $emailVerificado =
            !empty(
                $resposta['email_verified']
            );


        if (
            $sub === ''
        ) {

            throw new RuntimeException(
                'O Google não retornou o identificador do usuário.'
            );
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new RuntimeException(
                'O Google não retornou um e-mail válido.'
            );
        }


        if (
            $nome === ''
        ) {

            $nome =
                $email;
        }


        return [

            'google_sub' =>
                $sub,

            'nome' =>
                $nome,

            'email' =>
                $email,

            'foto_url' =>
                $foto !== ''
                    ? $foto
                    : null,

            'email_verificado' =>
                $emailVerificado,
        ];
    }


    /*
    =================================
    POST HTTP
    =================================
    */

    private function requisicaoPost(
        string $url,
        array $dados
    ): array {

        $ch =
            curl_init(
                $url
            );


        if (
            $ch === false
        ) {

            throw new RuntimeException(
                'Não foi possível iniciar a comunicação com o Google.'
            );
        }


        curl_setopt_array(
            $ch,
            [
                CURLOPT_POST =>
                    true,

                CURLOPT_POSTFIELDS =>
                    http_build_query(
                        $dados,
                        '',
                        '&',
                        PHP_QUERY_RFC3986
                    ),

                CURLOPT_RETURNTRANSFER =>
                    true,

                CURLOPT_TIMEOUT =>
                    15,

                CURLOPT_CONNECTTIMEOUT =>
                    10,

                CURLOPT_HTTPHEADER =>
                    [
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: application/json',
                    ],
            ]
        );


        $corpo =
            curl_exec(
                $ch
            );


        $erroCurl =
            curl_error(
                $ch
            );


        $statusHttp =
            (int) curl_getinfo(
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
                'Falha na comunicação com o Google: '
                . $erroCurl
            );
        }


        $dadosResposta =
            json_decode(
                $corpo,
                true
            );


        if (
            !is_array(
                $dadosResposta
            )
        ) {

            throw new RuntimeException(
                'O Google retornou uma resposta inválida.'
            );
        }


        if (
            $statusHttp < 200
            ||
            $statusHttp >= 300
        ) {

            $mensagem =
                (string) (
                    $dadosResposta['error_description']
                    ?? $dadosResposta['error']
                    ?? 'Erro desconhecido.'
                );


            throw new RuntimeException(
                'O Google recusou a autenticação: '
                . $mensagem
            );
        }


        return $dadosResposta;
    }


    /*
    =================================
    GET USERINFO
    =================================
    */

    private function requisicaoGet(
        string $url,
        string $accessToken
    ): array {

        $ch =
            curl_init(
                $url
            );


        if (
            $ch === false
        ) {

            throw new RuntimeException(
                'Não foi possível iniciar a comunicação com o Google.'
            );
        }


        curl_setopt_array(
            $ch,
            [
                CURLOPT_RETURNTRANSFER =>
                    true,

                CURLOPT_TIMEOUT =>
                    15,

                CURLOPT_CONNECTTIMEOUT =>
                    10,

                CURLOPT_HTTPHEADER =>
                    [
                        'Authorization: Bearer ' . $accessToken,
                        'Accept: application/json',
                    ],
            ]
        );


        $corpo =
            curl_exec(
                $ch
            );


        $erroCurl =
            curl_error(
                $ch
            );


        $statusHttp =
            (int) curl_getinfo(
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
                'Falha na comunicação com o Google: '
                . $erroCurl
            );
        }


        $dadosResposta =
            json_decode(
                $corpo,
                true
            );


        if (
            !is_array(
                $dadosResposta
            )
        ) {

            throw new RuntimeException(
                'O Google retornou dados inválidos do usuário.'
            );
        }


        if (
            $statusHttp < 200
            ||
            $statusHttp >= 300
        ) {

            throw new RuntimeException(
                'Não foi possível obter os dados da conta Google.'
            );
        }


        return $dadosResposta;
    }
}