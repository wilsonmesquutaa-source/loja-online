<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Repositories\PedidoRepository;

final class PedidoController extends Controller
{
    /*
    =================================
    LISTAGEM
    =================================
    */

    public function index(): void
    {
        if (
            empty(
                $_SESSION['usuario_admin']['id']
            )
        ) {

            $this->redirecionar(
                '/login-admin'
            );

            return;
        }


        $pedidoRepository =
            new PedidoRepository(
                $this->pdo
            );


        /*
        =================================
        FILTRO POR STATUS
        =================================
        */

        $status =
            isset($_GET['status'])
                ? trim(
                    (string)
                    $_GET['status']
                )
                : null;


        $statusPermitidos = [
            'aguardando_pagamento',
            'pago',
            'em_separacao',
            'enviado',
            'entregue',
            'cancelado',
        ];


        if (
            $status === null
            ||
            !in_array(
                $status,
                $statusPermitidos,
                true
            )
        ) {

            $status = null;
        }


        /*
        =================================
        BUSCA OS PEDIDOS
        =================================
        */

        $pedidos =
            $pedidoRepository
            ->buscarTodosAdministrativo(
                $status
            );


        /*
        =================================
        CARREGA A VIEW
        =================================
        */

        $this->view(
            'admin/pedidos',
            [
                'tituloPagina' =>
                    'Pedidos',

                'pedidos' =>
                    $pedidos,

                'statusAtual' =>
                    $status,

                'csrfToken' =>
                    Csrf::gerar(),
            ]
        );
    }


    /*
    =================================
    DETALHES
    =================================
    */

    public function detalhes(
        int $id
    ): void {

        if (
            empty(
                $_SESSION['usuario_admin']['id']
            )
        ) {

            $this->redirecionar(
                '/login-admin'
            );

            return;
        }


        $pedidoRepository =
            new PedidoRepository(
                $this->pdo
            );


        /*
        =================================
        BUSCA PEDIDO
        =================================
        */

        $pedido =
            $pedidoRepository
            ->buscarPorIdAdministrativo(
                $id
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


        /*
        =================================
        ITENS
        =================================
        */

        $itens =
            $pedidoRepository
            ->buscarItens(
                $id
            );


        /*
        =================================
        ENDEREÇO
        =================================
        */

        $endereco =
            $pedidoRepository
            ->buscarEndereco(
                $id
            );


        /*
        =================================
        PAGAMENTO
        =================================
        */

        $pagamento =
            $pedidoRepository
            ->buscarPagamento(
                $id
            );


        /*
        =================================
        CARREGA A VIEW
        =================================
        */

        $this->view(
            'admin/pedido',
            [
                'tituloPagina' =>
                    'Pedido '
                    . $pedido['codigo'],

                'pedido' =>
                    $pedido,

                'itens' =>
                    $itens,

                'endereco' =>
                    $endereco,

                'pagamento' =>
                    $pagamento,

                'csrfToken' =>
                    Csrf::gerar(),
            ]
        );
    }


    /*
    =================================
    ATUALIZA STATUS
    =================================
    */

    public function atualizarStatus(
        int $id
    ): void {

        if (
            empty(
                $_SESSION['usuario_admin']['id']
            )
        ) {

            $this->redirecionar(
                '/login-admin'
            );

            return;
        }


        /*
        =================================
        CSRF
        =================================
        */

        $token =
            isset($_POST['_token'])
                ? (string)
                $_POST['_token']
                : null;


        if (
            !Csrf::validar(
                $token
            )
        ) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        /*
        =================================
        NOVO STATUS
        =================================
        */

        $novoStatus =
            trim(
                (string) (
                    $_POST['status']
                    ?? ''
                )
            );


        $statusPermitidos = [
            'aguardando_pagamento',
            'pago',
            'em_separacao',
            'enviado',
            'entregue',
            'cancelado',
        ];


        if (
            !in_array(
                $novoStatus,
                $statusPermitidos,
                true
            )
        ) {

            exit(
                'Status de pedido inválido.'
            );
        }


        /*
        =================================
        BUSCA PEDIDO ATUAL
        =================================
        */

        $pedidoRepository =
            new PedidoRepository(
                $this->pdo
            );


        $pedido =
            $pedidoRepository
            ->buscarPorIdAdministrativo(
                $id
            );


        if (
            $pedido === null
        ) {

            http_response_code(404);

            exit(
                'Pedido não encontrado.'
            );
        }


        /*
        =================================
        STATUS ATUAL
        =================================
        */

        $statusAtual =
            (string)
            $pedido['status'];


        /*
        =================================
        CANCELADO
        =================================
        */

        if (
            $novoStatus === 'cancelado'
        ) {

            /*
            -----------------------------
            NÃO PERMITE ALTERAR
            PEDIDO JÁ ENTREGUE
            -----------------------------
            */

            if (
                $statusAtual ===
                'entregue'
            ) {

                exit(
                    'Um pedido entregue não pode ser cancelado.'
                );
            }
        }


        /*
        =================================
        PEDIDO JÁ CANCELADO
        =================================
        */

        if (
            $statusAtual ===
            'cancelado'
            &&
            $novoStatus !==
            'cancelado'
        ) {

            exit(
                'Um pedido cancelado não pode voltar ao fluxo normal.'
            );
        }


        /*
        =================================
        FLUXO NORMAL
        =================================
        */

        $fluxo = [
            'aguardando_pagamento' =>
                0,

            'pago' =>
                1,

            'em_separacao' =>
                2,

            'enviado' =>
                3,

            'entregue' =>
                4,
        ];


        /*
        =================================
        NÃO APLICA REGRA AO CANCELAMENTO
        =================================
        */

        if (
            $novoStatus !== 'cancelado'
            &&
            $statusAtual !== 'cancelado'
        ) {

            $ordemAtual =
                $fluxo[
                    $statusAtual
                ]
                ?? null;


            $ordemNova =
                $fluxo[
                    $novoStatus
                ]
                ?? null;


            if (
                $ordemAtual === null
                ||
                $ordemNova === null
            ) {

                exit(
                    'Não foi possível validar a transição do pedido.'
                );
            }


            /*
            -----------------------------
            NÃO PERMITE VOLTAR
            -----------------------------
            */

            if (
                $ordemNova <
                $ordemAtual
            ) {

                exit(
                    'O pedido não pode voltar para uma etapa anterior.'
                );
            }
        }


        /*
        =================================
        ATUALIZA
        =================================
        */

        $pedidoRepository
            ->atualizarStatus(
                $id,
                $novoStatus
            );


        /*
        =================================
        REDIRECIONA
        =================================
        */

        $this->redirecionar(
            '/admin/pedidos/' . $id
        );
    }
}