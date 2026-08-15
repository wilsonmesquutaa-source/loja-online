<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;

final class PerfilController extends Controller
{
    public function index(): void
    {
        /*
        =================================
        VERIFICA LOGIN
        =================================
        */

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
                'login'
            );
        }


        /*
        =================================
        DADOS DO CLIENTE
        =================================
        */

        $cliente = [
            'id' =>
                (int)
                $_SESSION['cliente_id'],

            'nome' =>
                (string)
                (
                    $_SESSION['cliente_nome']
                    ?? ''
                ),

            'email' =>
                (string)
                (
                    $_SESSION['cliente_email']
                    ?? ''
                ),
        ];


        /*
        =================================
        VIEW
        =================================
        */

        $this->view(
            'cliente/perfil',
            [
                'tituloPagina' =>
                    'Meu perfil',

                'rotaAtual' =>
                    'perfil',

                'cliente' =>
                    $cliente,
            ]
        );
    }
}