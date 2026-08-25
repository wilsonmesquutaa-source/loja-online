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
                getenv('MAIL_HOST')
                ?: ($_ENV['MAIL_HOST'] ?? '');


            $port =
                getenv('MAIL_PORT')
                ?: ($_ENV['MAIL_PORT'] ?? '587');


            $username =
                getenv('MAIL_USERNAME')
                ?: ($_ENV['MAIL_USERNAME'] ?? '');


            $password =
                getenv('MAIL_PASSWORD')
                ?: ($_ENV['MAIL_PASSWORD'] ?? '');


            $encryption =
                getenv('MAIL_ENCRYPTION')
                ?: ($_ENV['MAIL_ENCRYPTION'] ?? 'tls');


            $fromAddress =
                getenv('MAIL_FROM_ADDRESS')
                ?: ($_ENV['MAIL_FROM_ADDRESS'] ?? '');


            $fromName =
                getenv('MAIL_FROM_NAME')
                ?: ($_ENV['MAIL_FROM_NAME'] ?? 'Cantim do Lanche');


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
                strtolower(
                    $encryption
                ) === 'ssl'
            ) {

                $this->mailer->SMTPSecure =
                    PHPMailer::ENCRYPTION_SMTPS;
            } else {

                $this->mailer->SMTPSecure =
                    PHPMailer::ENCRYPTION_STARTTLS;
            }


            $this->mailer->Port =
                (int)
                $port;


            $this->mailer->CharSet =
                'UTF-8';


            $this->mailer->setFrom(
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
                        Este é um teste do sistema de envio
                        de e-mails.
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


            $this->mailer->send();
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
ENVIA E-MAIL DE VERIFICAÇÃO
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
                    ENT_QUOTES,
                    'UTF-8'
                );


            $urlSegura =
                htmlspecialchars(
                    $urlVerificacao,
                    ENT_QUOTES,
                    'UTF-8'
                );


            $this->mailer->Body = '

            <div style="
                font-family:
                    Arial,
                    Helvetica,
                    sans-serif;

                background:
                    #f8f5f0;

                padding:
                    40px 20px;
            ">

                <div style="
                    max-width:
                        600px;

                    margin:
                        0 auto;

                    background:
                        #ffffff;

                    border-radius:
                        18px;

                    padding:
                        35px;
                ">

                    <h1 style="
                        color:
                            #f57c00;

                        margin-top:
                            0;
                    ">

                        Cantim do Lanche

                    </h1>


                    <p>

                        Olá,
                        ' . $nomeSeguro . '!

                    </p>


                    <p>

                        Sua conta foi criada
                        com sucesso.

                    </p>


                    <p>

                        Para confirmar
                        seu endereço de e-mail,
                        clique no botão abaixo:

                    </p>


                    <p style="
                        text-align:
                            center;

                        margin:
                            30px 0;
                    ">

                        <a
                            href="' . $urlSegura . '"
                            style="
                                display:
                                    inline-block;

                                padding:
                                    14px 28px;

                                background:
                                    #f57c00;

                                color:
                                    #ffffff;

                                text-decoration:
                                    none;

                                border-radius:
                                    30px;

                                font-weight:
                                    bold;
                            "
                        >

                            Confirmar meu e-mail

                        </a>

                    </p>


                    <p>

                        Este link é válido por
                        <strong>
                            1 hora
                        </strong>.

                    </p>


                    <p style="
                        color:
                            #666666;

                        font-size:
                            13px;
                    ">

                        Se você não criou uma
                        conta no Cantim do Lanche,
                        ignore esta mensagem.

                    </p>

                </div>

            </div>
        ';


            $this->mailer->AltBody =
                'Olá, '
                . $nome
                . '!'
                . PHP_EOL
                . PHP_EOL
                . 'Confirme seu e-mail no Cantim do Lanche: '
                . $urlVerificacao
                . PHP_EOL
                . PHP_EOL
                . 'Este link é válido por 1 hora.';


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
