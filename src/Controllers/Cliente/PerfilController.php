<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Repositories\ClienteRepository;

final class PerfilController extends Controller
{
    public function index(): void
    {
        /*
        =================================
        VERIFICA SESSÃO DO CLIENTE
        =================================
        */

        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }


        $clienteId =
            isset($_SESSION['cliente_id'])
                ? (int) $_SESSION['cliente_id']
                : 0;


        if (
            $clienteId <= 0
        ) {
            $this->redirecionar(
                'login'
            );
        }


        /*
        =================================
        BUSCA CLIENTE
        =================================
        */

        $repository =
            new ClienteRepository(
                $this->pdo
            );


        $cliente =
            $repository->buscarPorId(
                $clienteId
            );


        /*
        =================================
        CLIENTE NÃO ENCONTRADO
        =================================
        */

        if (
            $cliente === null
        ) {

            unset(
                $_SESSION['cliente_id'],
                $_SESSION['cliente_nome'],
                $_SESSION['cliente_email']
            );


            $this->redirecionar(
                'login'
            );
        }


        /*
        =================================
        ATUALIZA ÚLTIMO ACESSO
        =================================
        */

        $repository->atualizarUltimoAcesso(
            $clienteId
        );


        /*
        =================================
        EXIBE PERFIL
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