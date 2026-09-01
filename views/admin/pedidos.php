<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

$pedidos =
    $pedidos ?? [];

$statusAtual =
    $statusAtual ?? null;

$csrfToken =
    $csrfToken
    ?? \App\Helpers\Csrf::gerar();


/*
=================================
TRADUÇÃO DOS STATUS
=================================
*/

$nomesStatus = [

    'aguardando_pagamento' =>
        'Aguardando pagamento',

    'pago' =>
        'Pago',

    'em_separacao' =>
        'Em preparação',

    'enviado' =>
        'Saiu para entrega',

    'entregue' =>
        'Entregue',

    'cancelado' =>
        'Cancelado',
];


?>


<div class="container-fluid">


    <!-- =================================
         CABEÇALHO
    ================================== -->

    <div
        class="
            d-flex
            justify-content-between
            align-items-center
            mb-4
        ">

        <div>

            <h1 class="h3 mb-1">

                Pedidos

            </h1>


            <p class="text-muted mb-0">

                Gerencie os pedidos realizados
                pelos clientes.

            </p>

        </div>

    </div>


    <!-- =================================
         FILTROS
    ================================== -->

    <div class="card mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="<?= BASE_URL ?>/admin/pedidos"
                class="
                    row
                    g-3
                    align-items-end
                ">

                <div
                    class="
                        col-md-5
                        col-lg-4
                    ">

                    <label
                        for="status"
                        class="form-label">

                        Filtrar por status

                    </label>


                    <select
                        id="status"
                        name="status"
                        class="form-control">

                        <option
                            value=""
                            <?= $statusAtual === null
                                ? 'selected'
                                : ''
                            ?>>

                            Todos os pedidos

                        </option>


                        <?php foreach (
                            $nomesStatus
                            as $valor => $nome
                        ): ?>

                            <option
                                value="<?= htmlspecialchars(
                                    $valor,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= $statusAtual === $valor
                                    ? 'selected'
                                    : ''
                                ?>>

                                <?= htmlspecialchars(
                                    $nome,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div
                    class="
                        col-md-auto
                    ">

                    <button
                        type="submit"
                        class="
                            btn
                            btn-primary
                        ">

                        <i
                            class="
                                bi
                                bi-funnel
                                me-1
                            "></i>

                        Filtrar

                    </button>

                </div>


                <?php if (
                    $statusAtual !== null
                ): ?>

                    <div
                        class="
                            col-md-auto
                        ">

                        <a
                            href="<?= BASE_URL ?>/admin/pedidos"
                            class="
                                btn
                                btn-outline-secondary
                            ">

                            Limpar filtro

                        </a>

                    </div>

                <?php endif; ?>

            </form>

        </div>

    </div>


    <!-- =================================
         LISTAGEM
    ================================== -->

    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table
                    class="
                        table
                        table-hover
                        align-middle
                    ">

                    <thead>

                        <tr>

                            <th>
                                Pedido
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Recebimento
                            </th>

                            <th>
                                Agendamento
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Ações
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if (
                            $pedidos === []
                        ): ?>


                            <tr>

                                <td
                                    colspan="7"
                                    class="
                                        text-center
                                        py-5
                                    ">

                                    <div
                                        class="
                                            text-muted
                                        ">

                                        <i
                                            class="
                                                bi
                                                bi-inbox
                                            "
                                            style="
                                                font-size: 2.5rem;
                                            "></i>


                                        <div class="mt-2">

                                            Nenhum pedido encontrado.

                                        </div>

                                    </div>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach (
                                $pedidos
                                as $pedido
                            ): ?>


                                <?php

                                $status =
                                    (string)
                                    $pedido['status'];


                                $nomeStatus =
                                    $nomesStatus[$status]
                                    ?? $status;


                                /*
                                -----------------------------
                                COR DO STATUS
                                -----------------------------
                                */

                                $classeStatus =
                                    'bg-secondary';


                                switch (
                                    $status
                                ) {

                                    case 'aguardando_pagamento':

                                        $classeStatus =
                                            'bg-warning text-dark';

                                        break;


                                    case 'pago':

                                        $classeStatus =
                                            'bg-primary';

                                        break;


                                    case 'em_separacao':

                                        $classeStatus =
                                            'bg-info text-dark';

                                        break;


                                    case 'enviado':

                                        $classeStatus =
                                            'bg-secondary';

                                        break;


                                    case 'entregue':

                                        $classeStatus =
                                            'bg-success';

                                        break;


                                    case 'cancelado':

                                        $classeStatus =
                                            'bg-danger';

                                        break;
                                }


                                /*
                                -----------------------------
                                DATA
                                -----------------------------
                                */

                                $dataPedido =
                                    !empty(
                                        $pedido['criado_em']
                                    )
                                    ? date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $pedido['criado_em']
                                        )
                                    )
                                    : '-';


                                /*
                                -----------------------------
                                AGENDAMENTO
                                -----------------------------
                                */

                                $agendamento =
                                    !empty(
                                        $pedido['data_hora_agendada']
                                    )
                                    ? date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $pedido[
                                                'data_hora_agendada'
                                            ]
                                        )
                                    )
                                    : '-';


                                /*
                                -----------------------------
                                MODALIDADE
                                -----------------------------
                                */

                                $modalidade =
                                    $pedido[
                                        'modalidade_recebimento'
                                    ]
                                    === 'entrega'
                                    ? 'Entrega'
                                    : 'Retirada';


                                ?>


                                <tr>


                                    <!-- PEDIDO -->

                                    <td>

                                        <strong>

                                            #<?= htmlspecialchars(
                                                (string)
                                                $pedido['codigo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </strong>


                                        <br>


                                        <small
                                            class="
                                                text-muted
                                            ">

                                            <?= htmlspecialchars(
                                                $dataPedido,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </small>

                                    </td>


                                    <!-- CLIENTE -->

                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                (string)
                                                $pedido[
                                                    'nome_cliente'
                                                ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </strong>


                                        <br>


                                        <small
                                            class="
                                                text-muted
                                            ">

                                            <?= htmlspecialchars(
                                                (string)
                                                $pedido[
                                                    'email_cliente'
                                                ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </small>

                                    </td>


                                    <!-- RECEBIMENTO -->

                                    <td>

                                        <?= $modalidade ?>

                                    </td>


                                    <!-- AGENDAMENTO -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $agendamento,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- TOTAL -->

                                    <td>

                                        <strong>

                                            R$

                                            <?= number_format(
                                                (float)
                                                $pedido['total'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <span
                                            class="
                                                badge
                                                <?= $classeStatus ?>
                                            ">

                                            <?= htmlspecialchars(
                                                $nomeStatus,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- AÇÕES -->

                                    <td>

                                        <a
                                            href="<?= BASE_URL ?>/admin/pedidos/<?= (int) $pedido['id'] ?>"
                                            class="
                                                btn
                                                btn-primary
                                                btn-sm
                                            ">

                                            <i
                                                class="
                                                    bi
                                                    bi-eye
                                                    me-1
                                                "></i>

                                            Ver pedido

                                        </a>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>