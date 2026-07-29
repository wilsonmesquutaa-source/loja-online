<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;

final class ContatoController extends Controller
{
    public function index(): void
    {
        $this->view(
            'site/contato',
            [
                'tituloPagina' => 'Contato',
                'erros' => [],
                'sucesso' => null,
                'nome' => '',
                'email' => '',
                'mensagem' => '',
            ]
        );
    }

    public function enviar(): void
    {
        $nome = trim(
            (string) ($_POST['nome'] ?? '')
        );

        $email = trim(
            (string) ($_POST['email'] ?? '')
        );

        $mensagem = trim(
            (string) ($_POST['mensagem'] ?? '')
        );

        $erros = [];

        if ($nome === '') {
            $erros[] = 'Informe o nome.';
        }

        if (
            filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            $erros[] = 'Informe um e-mail válido.';
        }

        if (mb_strlen($mensagem) < 10) {
            $erros[] =
                'A mensagem deve possuir pelo menos 10 caracteres.';
        }

        if ($erros !== []) {
            http_response_code(422);

            $this->view(
                'site/contato',
                [
                    'tituloPagina' => 'Contato',
                    'erros' => $erros,
                    'sucesso' => null,
                    'nome' => $nome,
                    'email' => $email,
                    'mensagem' => $mensagem,
                ]
            );

            return;
        }

        $this->view(
            'site/contato',
            [
                'tituloPagina' => 'Contato',
                'erros' => [],
                'sucesso' =>
                    'Mensagem recebida com sucesso.',
                'nome' => '',
                'email' => '',
                'mensagem' => '',
            ]
        );
    }
}
