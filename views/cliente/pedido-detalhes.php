<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/site/header.php';


$pedido =
    $pedido ?? null;

$itens =
    $itens ?? [];

$endereco =
    $endereco ?? null;


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
STATUS
=================================
*/

$nomesStatus = [

    'aguardando_pagamento' =>
        'Aguardando pagamento',

    'pago' =>
        'Pagamento confirmado',

    'em_separacao' =>
        'Em preparação',

    'enviado' =>
        'Saiu para entrega',

    'entregue' =>
        'Pedido entregue',

    'cancelado' =>
        'Pedido cancelado',
];


$status =
    (string)
    $pedido['status'];


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
                $pedido['criado_em']
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


/*
=================================
CLASSE DO STATUS
=================================
*/

$classeStatus =
    'pedido-status-neutro';


switch (
    $status
) {

    case 'aguardando_pagamento':

        $classeStatus =
            'pedido-status-pagamento';

        break;


    case 'pago':

        $classeStatus =
            'pedido-status-pago';

        break;


    case 'em_separacao':

        $classeStatus =
            'pedido-status-preparacao';

        break;


    case 'enviado':

        $classeStatus =
            'pedido-status-enviado';

        break;


    case 'entregue':

        $classeStatus =
            'pedido-status-entregue';

        break;


    case 'cancelado':

        $classeStatus =
            'pedido-status-cancelado';

        break;
}

?>


<link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/cliente_pedidos.css"
>


<main class="cliente-pedidos-page">


    <div class="container py-5">


        <!-- =================================
             CABEÇALHO
        ================================== -->

        <div class="cliente-pedido-topo">


            <a
                href="<?= BASE_URL ?>/cliente/pedidos"
                class="cliente-pedido-voltar"
            >

                <i class="bi bi-arrow-left"></i>

                Voltar para meus pedidos

            </a>


            <div class="cliente-pedido-titulo">

                <span>

                    Pedido

                </span>


                <h1>

                    #<?= htmlspecialchars(
                        (string)
                        $pedido['codigo'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </h1>


                <p>

                    Realizado em
                    <?= htmlspecialchars(
                        $dataPedido,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </p>

            </div>

        </div>


        <!-- =================================
             STATUS
        ================================== -->

        <section class="cliente-pedido-status-card">


            <div>

                <span class="cliente-pedido-status-label">

                    Status do pedido

                </span>


                <h2>

                    <?= htmlspecialchars(
                        $nomeStatus,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </h2>

            </div>


            <span
                class="
                    cliente-pedido-status-badge
                    <?= $classeStatus ?>
                "
            >

                <?= htmlspecialchars(
                    $nomeStatus,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>


        </section>


        <div class="row g-4 mt-1">


            <!-- =================================
                 CONTEÚDO PRINCIPAL
            ================================== -->

            <div class="col-lg-8">


                <!-- =================================
                     ITENS
                ================================== -->

                <section class="cliente-pedido-card">


                    <div class="cliente-pedido-card-header">

                        <h2>

                            Itens do pedido

                        </h2>


                        <span>

                            <?= count($itens) ?>

                            <?= count($itens) === 1
                                ? 'item'
                                : 'itens'
                            ?>

                        </span>

                    </div>


                    <?php if (
                        $itens === []
                    ): ?>

                        <div
                            class="
                                cliente-pedido-vazio
                            "
                        >

                            Nenhum item encontrado.

                        </div>

                    <?php else: ?>


                        <div
                            class="
                                cliente-pedido-itens
                            "
                        >


                            <?php foreach (
                                $itens
                                as $item
                            ): ?>


                                <div
                                    class="
                                        cliente-pedido-item
                                    "
                                >


                                    <div
                                        class="
                                            cliente-pedido-item-info
                                        "
                                    >

                                        <h3>

                                            <?= htmlspecialchars(
                                                (string)
                                                $item[
                                                    'nome_produto'
                                                ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </h3>


                                        <span>

                                            <?= (int)
                                            $item[
                                                'quantidade'
                                            ] ?>

                                            x

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

                                        </span>

                                    </div>


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


                                </div>


                            <?php endforeach; ?>


                        </div>


                    <?php endif; ?>


                </section>


                <!-- =================================
                     OBSERVAÇÃO
                ================================== -->

                <?php if (
                    !empty(
                        $pedido[
                            'observacao'
                        ]
                    )
                ): ?>


                    <section
                        class="
                            cliente-pedido-card
                            mt-4
                        "
                    >


                        <div
                            class="
                                cliente-pedido-card-header
                            "
                        >

                            <h2>

                                Observação

                            </h2>

                        </div>


                        <div
                            class="
                                cliente-pedido-observacao
                            "
                        >

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

                        </div>


                    </section>


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


                    <section
                        class="
                            cliente-pedido-card
                            mt-4
                        "
                    >


                        <div
                            class="
                                cliente-pedido-card-header
                            "
                        >

                            <h2>

                                Endereço de entrega

                            </h2>

                        </div>


                        <div
                            class="
                                cliente-pedido-endereco
                            "
                        >


                            <strong>

                                <?= htmlspecialchars(
                                    (string)
                                    $endereco[
                                        'destinatario'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>


                            <p>

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

                                <p>

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


                            <p>

                                <?= htmlspecialchars(
                                    (string)
                                    $endereco[
                                        'bairro'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>


                                <br>


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

                                CEP:

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


                    </section>


                <?php endif; ?>


            </div>


            <!-- =================================
                 LATERAL
            ================================== -->

            <div class="col-lg-4">


                <!-- =================================
                     RECEBIMENTO
                ================================== -->

                <section
                    class="
                        cliente-pedido-card
                    "
                >


                    <div
                        class="
                            cliente-pedido-card-header
                        "
                    >

                        <h2>

                            Recebimento

                        </h2>

                    </div>


                    <div
                        class="
                            cliente-pedido-informacoes
                        "
                    >


                        <div>

                            <span>

                                Modalidade

                            </span>


                            <strong>

                                <?= $modalidade ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Data agendada

                            </span>


                            <strong>

                                <?= htmlspecialchars(
                                    $dataAgendada,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>

                        </div>


                    </div>


                </section>


                <!-- =================================
                     RESUMO
                ================================== -->

                <section
                    class="
                        cliente-pedido-card
                        mt-4
                    "
                >


                    <div
                        class="
                            cliente-pedido-card-header
                        "
                    >

                        <h2>

                            Resumo do pedido

                        </h2>

                    </div>


                    <div
                        class="
                            cliente-pedido-resumo
                        "
                    >


                        <div>

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


                        <div>

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


                        <div>

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
                                cliente-pedido-total
                            "
                        >

                            <span>

                                Total

                            </span>


                            <strong>

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


                </section>


            </div>


        </div>


    </div>


</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';