<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';


$pedido =
    $pedido ?? null;

$itens =
    $itens ?? [];

$endereco =
    $endereco ?? null;

$pagamento =
    $pagamento ?? null;

$csrfToken =
    $csrfToken
    ?? \App\Helpers\Csrf::gerar();


/*
=================================
VERIFICA PEDIDO
=================================
*/

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
STATUS
=================================
*/

$status =
    (string)
    $pedido['status'];


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


$nomeStatus =
    $nomesStatus[
        $status
    ]
    ?? $status;


/*
=================================
MODALIDADE
=================================
*/

$modalidade =
    $pedido[
        'modalidade_recebimento'
    ] === 'entrega'
        ? 'Entrega'
        : 'Retirada';


/*
=================================
DATAS
=================================
*/

$dataPedido =
    !empty(
        $pedido['criado_em']
    )
        ? date(
            'd/m/Y H:i',
            strtotime(
                $pedido[
                    'criado_em'
                ]
            )
        )
        : '-';


$dataAgendada =
    !empty(
        $pedido[
            'data_hora_agendada'
        ]
    )
        ? date(
            'd/m/Y H:i',
            strtotime(
                $pedido[
                    'data_hora_agendada'
                ]
            )
        )
        : 'Não informado';


$inicioPreparo =
    !empty(
        $pedido[
            'inicio_preparo'
        ]
    )
        ? date(
            'd/m/Y H:i',
            strtotime(
                $pedido[
                    'inicio_preparo'
                ]
            )
        )
        : 'Não iniciado';


$fimPreparo =
    !empty(
        $pedido[
            'fim_preparo_previsto'
        ]
    )
        ? date(
            'd/m/Y H:i',
            strtotime(
                $pedido[
                    'fim_preparo_previsto'
                ]
            )
        )
        : 'Não informado';


/*
=================================
PAGAMENTO
=================================
*/

$nomesPagamento = [

    'pix' =>
        'Pix',

    'cartao' =>
        'Cartão',
];


$nomesStatusPagamento = [

    'pendente' =>
        'Pendente',

    'aprovado' =>
        'Aprovado',

    'recusado' =>
        'Recusado',

    'cancelado' =>
        'Cancelado',

    'reembolsado' =>
        'Reembolsado',
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
        "
    >

        <div>

            <h1 class="h3 mb-1">

                Pedido #

                <?= htmlspecialchars(
                    (string)
                    $pedido['codigo'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </h1>


            <p class="text-muted mb-0">

                Realizado em
                <?= htmlspecialchars(
                    $dataPedido,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </p>

        </div>


        <div>

            <a
                href="<?= BASE_URL ?>/admin/pedidos"
                class="btn btn-secondary"
            >

                <i
                    class="
                        bi
                        bi-arrow-left
                        me-1
                    "
                ></i>

                Voltar

            </a>

        </div>

    </div>


    <div class="row g-4">


        <!-- =================================
             COLUNA PRINCIPAL
        ================================== -->

        <div class="col-lg-8">


            <!-- =================================
                 STATUS
            ================================== -->

            <div class="card mb-4">

                <div class="card-body">

                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                            flex-wrap
                            gap-3
                        "
                    >

                        <div>

                            <h2
                                class="
                                    h5
                                    mb-1
                                "
                            >

                                Status do pedido

                            </h2>


                            <p
                                class="
                                    text-muted
                                    mb-0
                                "
                            >

                                Situação atual do pedido.

                            </p>

                        </div>


                        <span
                            class="
                                badge
                                fs-6
                                <?= $classeStatus ?>
                            "
                        >

                            <?= htmlspecialchars(
                                $nomeStatus,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================
                 ITENS
            ================================== -->

            <div class="card mb-4">

                <div class="card-header">

                    <h2 class="h5 mb-0">

                        Itens do pedido

                    </h2>

                </div>


                <div class="card-body p-0">

                    <?php if (
                        $itens === []
                    ): ?>

                        <div
                            class="
                                text-center
                                text-muted
                                py-4
                            "
                        >

                            Nenhum item encontrado.

                        </div>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table
                                class="
                                    table
                                    table-hover
                                    align-middle
                                    mb-0
                                "
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Produto
                                        </th>

                                        <th
                                            class="
                                                text-center
                                            "
                                        >
                                            Quantidade
                                        </th>

                                        <th
                                            class="
                                                text-end
                                            "
                                        >
                                            Unitário
                                        </th>

                                        <th
                                            class="
                                                text-end
                                            "
                                        >
                                            Subtotal
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php foreach (
                                        $itens
                                        as $item
                                    ): ?>

                                        <tr>

                                            <td>

                                                <strong>

                                                    <?= htmlspecialchars(
                                                        (string)
                                                        $item[
                                                            'nome_produto'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </strong>

                                            </td>


                                            <td
                                                class="
                                                    text-center
                                                "
                                            >

                                                <?= (int)
                                                $item[
                                                    'quantidade'
                                                ] ?>

                                            </td>


                                            <td
                                                class="
                                                    text-end
                                                "
                                            >

                                                R$

                                                <?= number_format(
                                                    (float)
                                                    $item[
                                                        'preco_unitario'
                                                    ],
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </td>


                                            <td
                                                class="
                                                    text-end
                                                "
                                            >

                                                <strong>

                                                    R$

                                                    <?= number_format(
                                                        (float)
                                                        $item[
                                                            'subtotal'
                                                        ],
                                                        2,
                                                        ',',
                                                        '.'
                                                    ) ?>

                                                </strong>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- =================================
                 OBSERVAÇÃO
            ================================== -->

            <?php if (
                !empty(
                    $pedido['observacao']
                )
            ): ?>

                <div class="card mb-4">

                    <div class="card-header">

                        <h2 class="h5 mb-0">

                            Observação

                        </h2>

                    </div>


                    <div class="card-body">

                        <p class="mb-0">

                            <?= nl2br(
                                htmlspecialchars(
                                    (string)
                                    $pedido[
                                        'observacao'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>

                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================
                 ENDEREÇO
            ================================== -->

            <?php if (
                $modalidade ===
                'Entrega'
                &&
                $endereco !== null
            ): ?>

                <div class="card mb-4">

                    <div class="card-header">

                        <h2 class="h5 mb-0">

                            Endereço de entrega

                        </h2>

                    </div>


                    <div class="card-body">

                        <p class="mb-1">

                            <strong>

                                Destinatário:

                            </strong>


                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'destinatario'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>


                        <p class="mb-1">

                            <strong>

                                Endereço:

                            </strong>


                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'logradouro'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>


                           ,
                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'numero'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>


                        <?php if (
                            !empty(
                                $endereco[
                                    'complemento'
                                ]
                            )
                        ): ?>

                            <p class="mb-1">

                                <strong>

                                    Complemento:

                                </strong>


                                <?= htmlspecialchars(
                                    (string)
                                    $endereco[
                                        'complemento'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </p>

                        <?php endif; ?>


                        <p class="mb-1">

                            <strong>

                                Bairro:

                            </strong>


                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'bairro'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>


                        <p class="mb-1">

                            <strong>

                                Cidade:

                            </strong>


                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'cidade'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>


                            /

                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'estado'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>


                        <p class="mb-0">

                            <strong>

                                CEP:

                            </strong>


                            <?= htmlspecialchars(
                                (string)
                                $endereco[
                                    'cep'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>

                    </div>

                </div>

            <?php endif; ?>


        </div>


        <!-- =================================
             COLUNA LATERAL
        ================================== -->

        <div class="col-lg-4">


            <!-- =================================
                 ALTERAR STATUS
            ================================== -->

            <div class="card mb-4">

                <div class="card-header">

                    <h2 class="h5 mb-0">

                        Atualizar pedido

                    </h2>

                </div>


                <div class="card-body">

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/admin/pedidos/status/<?= (int) $pedido['id'] ?>"
                    >

                        <input
                            type="hidden"
                            name="_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >


                        <div class="mb-3">

                            <label
                                for="status"
                                class="form-label"
                            >

                                Novo status

                            </label>


                            <select
                                id="status"
                                name="status"
                                class="form-control"
                                required
                            >

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
                                        <?= $status === $valor
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= htmlspecialchars(
                                            $nome,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-primary
                                w-100
                            "
                        >

                            <i
                                class="
                                    bi
                                    bi-arrow-repeat
                                    me-1
                                "
                            ></i>

                            Atualizar status

                        </button>

                    </form>

                </div>

            </div>


            <!-- =================================
                 CLIENTE
            ================================== -->

            <div class="card mb-4">

                <div class="card-header">

                    <h2 class="h5 mb-0">

                        Cliente

                    </h2>

                </div>


                <div class="card-body">

                    <p class="mb-1">

                        <strong>

                            Nome:

                        </strong>


                        <?= htmlspecialchars(
                            (string)
                            $pedido[
                                'nome_cliente'
                            ],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>


                    <p class="mb-0">

                        <strong>

                            E-mail:

                        </strong>


                        <?= htmlspecialchars(
                            (string)
                            $pedido[
                                'email_cliente'
                            ],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>

                </div>

            </div>


            <!-- =================================
                 RECEBIMENTO
            ================================== -->

            <div class="card mb-4">

                <div class="card-header">

                    <h2 class="h5 mb-0">

                        Recebimento

                    </h2>

                </div>


                <div class="card-body">

                    <p class="mb-2">

                        <strong>

                            Modalidade:

                        </strong>


                        <?= $modalidade ?>

                    </p>


                    <p class="mb-2">

                        <strong>

                            Agendado:

                        </strong>


                        <?= htmlspecialchars(
                            $dataAgendada,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>


                    <p class="mb-2">

                        <strong>

                            Início do preparo:

                        </strong>


                        <?= htmlspecialchars(
                            $inicioPreparo,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>


                    <p class="mb-0">

                        <strong>

                            Previsão:

                        </strong>


                        <?= htmlspecialchars(
                            $fimPreparo,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>

                </div>

            </div>


            <!-- =================================
                 PAGAMENTO
            ================================== -->

            <div class="card mb-4">

                <div class="card-header">

                    <h2 class="h5 mb-0">

                        Pagamento

                    </h2>

                </div>


                <div class="card-body">

                    <?php if (
                        $pagamento === null
                    ): ?>

                        <p class="text-muted mb-0">

                            Nenhum pagamento
                            registrado.

                        </p>

                    <?php else: ?>

                        <p class="mb-2">

                            <strong>

                                Método:

                            </strong>


                            <?= htmlspecialchars(
                                $nomesPagamento[
                                    $pagamento[
                                        'metodo'
                                    ]
                                ]
                                ?? $pagamento[
                                    'metodo'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>


                        <p class="mb-2">

                            <strong>

                                Status:

                            </strong>


                            <?= htmlspecialchars(
                                $nomesStatusPagamento[
                                    $pagamento[
                                        'status'
                                    ]
                                ]
                                ?? $pagamento[
                                    'status'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </p>


                        <p class="mb-0">

                            <strong>

                                Valor:

                            </strong>


                            R$

                            <?= number_format(
                                (float)
                                $pagamento[
                                    'valor'
                                ],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </p>

                    <?php endif; ?>

                </div>

            </div>


            <!-- =================================
                 RESUMO FINANCEIRO
            ================================== -->

            <div class="card">

                <div class="card-header">

                    <h2 class="h5 mb-0">

                        Resumo

                    </h2>

                </div>


                <div class="card-body">


                    <div
                        class="
                            d-flex
                            justify-content-between
                            mb-2
                        "
                    >

                        <span>

                            Subtotal

                        </span>


                        <strong>

                            R$

                            <?= number_format(
                                (float)
                                $pedido[
                                    'subtotal'
                                ],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            mb-2
                        "
                    >

                        <span>

                            Frete

                        </span>


                        <strong>

                            R$

                            <?= number_format(
                                (float)
                                $pedido[
                                    'frete'
                                ],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            mb-2
                        "
                    >

                        <span>

                            Desconto

                        </span>


                        <strong>

                            R$

                            <?= number_format(
                                (float)
                                $pedido[
                                    'desconto'
                                ],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>


                    <hr>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <span>

                            <strong>

                                Total

                            </strong>

                        </span>


                        <strong
                            class="
                                fs-5
                            "
                        >

                            R$

                            <?= number_format(
                                (float)
                                $pedido[
                                    'total'
                                ],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>

                </div>

            </div>


        </div>

    </div>

</div>