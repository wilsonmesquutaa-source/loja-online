<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Repositories\PedidoRepository;

final class PedidoController extends Controller
{
    /*
    =================================
    MEUS PEDIDOS
    =================================
    */

    public function index(): void
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
                '/login'
            );

            return;
        }

        $clienteId =
            (int)
            $_SESSION['cliente_id'];

        $repository =
            new PedidoRepository(
                $this->pdo
            );

        $pedidos =
            $repository->buscarPorCliente(
                $clienteId
            );

        $this->view(
            'cliente/pedidos',
            [
                'tituloPagina' =>
                    'Meus pedidos',

                'rotaAtual' =>
                    'pedidos',

                'pedidos' =>
                    $pedidos,
            ]
        );
    }


    /*
    =================================
    DETALHES DO PEDIDO
    =================================
    */

    public function visualizar(
        int $id
    ): void {
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
                '/login'
            );

            return;
        }

        $clienteId =
            (int)
            $_SESSION['cliente_id'];

        $repository =
            new PedidoRepository(
                $this->pdo
            );

        $pedido =
            $repository
            ->buscarPorIdDoCliente(
                $id,
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

        $itens =
            $repository->buscarItens(
                $id
            );

        $endereco =
            $repository->buscarEndereco(
                $id
            );

        $this->view(
            'cliente/pedido-detalhes',
            [
                'tituloPagina' =>
                    'Pedido '
                    . $pedido['codigo'],

                'rotaAtual' =>
                    'pedidos',

                'pedido' =>
                    $pedido,

                'itens' =>
                    $itens,

                'endereco' =>
                    $endereco,
            ]
        );
    }
}