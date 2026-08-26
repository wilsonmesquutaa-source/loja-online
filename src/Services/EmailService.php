<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

final class EmailService
{
    private PHPMailer $mailer;


    public function __construct()
    {
        $this->mailer =
            new PHPMailer(true);


        try {

            $host =
                (string)
                (
                    $_ENV['MAIL_HOST']
                    ?? getenv('MAIL_HOST')
                    ?? ''
                );


            $port =
                (int)
                (
                    $_ENV['MAIL_PORT']
                    ?? getenv('MAIL_PORT')
                    ?? 587
                );


            $username =
                (string)
                (
                    $_ENV['MAIL_USERNAME']
                    ?? getenv('MAIL_USERNAME')
                    ?? ''
                );


            $password =
                (string)
                (
                    $_ENV['MAIL_PASSWORD']
                    ?? getenv('MAIL_PASSWORD')
                    ?? ''
                );


            $encryption =
                strtolower(
                    (string)
                    (
                        $_ENV['MAIL_ENCRYPTION']
                        ?? getenv('MAIL_ENCRYPTION')
                        ?? 'tls'
                    )
                );


            $fromAddress =
                (string)
                (
                    $_ENV['MAIL_FROM_ADDRESS']
                    ?? getenv('MAIL_FROM_ADDRESS')
                    ?? ''
                );


            $fromName =
                (string)
                (
                    $_ENV['MAIL_FROM_NAME']
                    ?? getenv('MAIL_FROM_NAME')
                    ?? 'Cantim do Lanche'
                );


            if (
                $host === ''
                ||
                $username === ''
                ||
                $password === ''
                ||
                $fromAddress === ''
            ) {

                throw new RuntimeException(
                    'As configurações de e-mail não foram carregadas corretamente pelo .env.'
                );
            }


            $this->mailer->isSMTP();


            $this->mailer->Host =
                $host;


            $this->mailer->SMTPAuth =
                true;


            $this->mailer->Username =
                $username;


            $this->mailer->Password =
                $password;


            if (
                $encryption === 'ssl'
            ) {

                $this->mailer->SMTPSecure =
                    PHPMailer::ENCRYPTION_SMTPS;

            } else {

                $this->mailer->SMTPSecure =
                    PHPMailer::ENCRYPTION_STARTTLS;
            }


            $this->mailer->Port =
                $port;


            $this->mailer->CharSet =
                'UTF-8';


            $this->mailer->setFrom(
                $fromAddress,
                $fromName
            );


            $this->mailer->addReplyTo(
                $fromAddress,
                $fromName
            );

        } catch (
            Exception $erro
        ) {

            throw new RuntimeException(
                'Erro ao configurar e-mail: '
                . $erro->getMessage(),
                0,
                $erro
            );
        }
    }


    /*
    =================================
    ENVIA TESTE
    =================================
    */

    public function enviarTeste(
        string $destinatario
    ): void {

        try {

            $this->mailer
                ->clearAddresses();


            $this->mailer
                ->addAddress(
                    $destinatario
                );


            $this->mailer
                ->isHTML(true);


            $this->mailer->Subject =
                'Teste de e-mail - Cantim do Lanche';


            $this->mailer->Body = '
                <div style="
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 30px;
                ">

                    <h1>
                        Cantim do Lanche
                    </h1>

                    <p>
                        Este é um teste do sistema
                        de envio de e-mails.
                    </p>

                    <p>
                        Se você recebeu esta mensagem,
                        o SMTP está funcionando corretamente.
                    </p>

                </div>
            ';


            $this->mailer->AltBody =
                'Teste de e-mail do Cantim do Lanche. '
                . 'Se você recebeu esta mensagem, '
                . 'o SMTP está funcionando corretamente.';


            $this->mailer
                ->send();

        } catch (
            Exception $erro
        ) {

            throw new RuntimeException(
                'Não foi possível enviar o e-mail: '
                . $erro->getMessage(),
                0,
                $erro
            );
        }
    }


    /*
    =================================
    ENVIA VERIFICAÇÃO
    =================================
    */

    public function enviarVerificacao(
        string $destinatario,
        string $nome,
        string $urlVerificacao
    ): void {

        try {

            $this->mailer
                ->clearAddresses();


            $this->mailer
                ->addAddress(
                    $destinatario,
                    $nome
                );


            $this->mailer
                ->isHTML(true);


            $this->mailer->Subject =
                'Confirme seu e-mail - Cantim do Lanche';


            $nomeSeguro =
                htmlspecialchars(
                    $nome,
                    ENT_QUOTES |
                    ENT_SUBSTITUTE |
                    ENT_HTML5,
                    'UTF-8'
                );


            $urlSegura =
                htmlspecialchars(
                    $urlVerificacao,
                    ENT_QUOTES |
                    ENT_SUBSTITUTE |
                    ENT_HTML5,
                    'UTF-8'
                );


            $this->mailer->Body = '

                <!DOCTYPE html>

                <html lang="pt-BR">

                <head>

                    <meta charset="UTF-8">

                    <meta
                        name="viewport"
                        content="width=device-width, initial-scale=1.0"
                    >

                    <title>
                        Confirme seu e-mail
                    </title>

                </head>


                <body style="
                    margin: 0;
                    padding: 0;
                    background: #f8f5f0;
                    font-family: Arial, Helvetica, sans-serif;
                ">

                    <div style="
                        padding: 40px 20px;
                    ">

                        <div style="
                            width: 100%;
                            max-width: 600px;
                            margin: 0 auto;
                            background: #ffffff;
                            border-radius: 18px;
                            padding: 35px;
                            box-sizing: border-box;
                            border: 1px solid #eee5dc;
                        ">

                            <h1 style="
                                margin: 0 0 25px;
                                color: #f57c00;
                                font-size: 28px;
                            ">

                                Cantim do Lanche

                            </h1>


                            <p style="
                                color: #3e2723;
                                font-size: 16px;
                                line-height: 1.6;
                            ">

                                Olá,
                                ' . $nomeSeguro . '!

                            </p>


                            <p style="
                                color: #4e342e;
                                font-size: 15px;
                                line-height: 1.6;
                            ">

                                Sua conta foi criada
                                com sucesso no
                                Cantim do Lanche.

                            </p>


                            <p style="
                                color: #4e342e;
                                font-size: 15px;
                                line-height: 1.6;
                            ">

                                Para confirmar seu
                                endereço de e-mail,
                                clique no botão abaixo:

                            </p>


                            <div style="
                                text-align: center;
                                margin: 30px 0;
                            ">

                                <a
                                    href="' . $urlSegura . '"
                                    target="_blank"
                                    style="
                                        display: inline-block;
                                        padding: 14px 28px;
                                        background: #f57c00;
                                        color: #ffffff;
                                        text-decoration: none;
                                        border-radius: 30px;
                                        font-size: 16px;
                                        font-weight: bold;
                                    "
                                >

                                    Confirmar meu e-mail

                                </a>

                            </div>


                            <p style="
                                color: #4e342e;
                                font-size: 14px;
                                line-height: 1.6;
                            ">

                                Este link é válido por
                                <strong>1 hora</strong>.

                            </p>


                            <p style="
                                color: #777777;
                                font-size: 13px;
                                line-height: 1.6;
                            ">

                                Se o botão não funcionar,
                                copie e cole o endereço
                                abaixo no navegador:

                            </p>


                            <p style="
                                padding: 12px;
                                background: #f8f5f0;
                                border-radius: 8px;
                                word-break: break-all;
                                color: #555555;
                                font-size: 12px;
                            ">

                                ' . $urlSegura . '

                            </p>


                            <p style="
                                color: #888888;
                                font-size: 12px;
                                line-height: 1.6;
                            ">

                                Se você não criou uma conta
                                no Cantim do Lanche,
                                ignore esta mensagem.

                            </p>

                        </div>

                    </div>

                </body>

                </html>

            ';


            $this->mailer->AltBody =
                'Olá, '
                . $nome
                . '!'
                . PHP_EOL
                . PHP_EOL
                . 'Sua conta foi criada com sucesso '
                . 'no Cantim do Lanche.'
                . PHP_EOL
                . PHP_EOL
                . 'Para confirmar seu endereço de e-mail, '
                . 'acesse o link abaixo:'
                . PHP_EOL
                . PHP_EOL
                . $urlVerificacao
                . PHP_EOL
                . PHP_EOL
                . 'Este link é válido por 1 hora.'
                . PHP_EOL
                . PHP_EOL
                . 'Se você não criou uma conta '
                . 'no Cantim do Lanche, '
                . 'ignore esta mensagem.';


            $this->mailer
                ->send();

        } catch (
            Exception $erro
        ) {

            throw new RuntimeException(
                'Não foi possível enviar o e-mail de verificação: '
                . $erro->getMessage(),
                0,
                $erro
            );
        }
    }
}