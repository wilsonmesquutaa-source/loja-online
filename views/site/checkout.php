<?php

declare(strict_types=1);

use App\Helpers\View;

require APP_ROOT
    . '/views/layouts/site/header.php';


View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
            $rotaAtual,
    ]
);


/*
=================================
DADOS
=================================
*/

$erro =
    $_GET['erro']
    ?? null;


$itens =
    $itens
    ?? [];


$enderecos =
    $enderecos
    ?? [];


$enderecoSelecionado =
    $enderecoSelecionado
    ?? null;


$modalidadeRecebimento =
    $modalidadeRecebimento
    ?? 'entrega';


$enderecoRetirada =
    $enderecoRetirada
    ?? 'Rua Dragão do Mar, 608, Praia de Iracema, Fortaleza - CE';


$horariosDisponiveis =
    $horariosDisponiveis
    ?? [];


$subtotal =
    (float)
    (
        $subtotal
        ?? 0
    );


$frete =
    (float)
    (
        $frete
        ?? 0
    );


$distanciaKm =
    $distanciaKm
    ?? null;


$freteDisponivel =
    isset(
        $freteDisponivel
    )
        ? (bool)
        $freteDisponivel
        : true;


$podeFinalizar =
    isset(
        $podeFinalizar
    )
        ? (bool)
        $podeFinalizar
        : false;


$desconto =
    (float)
    (
        $desconto
        ?? 0
    );


$total =
    (float)
    (
        $total
        ?? (
            $subtotal
            +
            $frete
            -
            $desconto
        )
    );


$csrfToken =
    $csrfToken
    ?? '';


$mercadoPagoPublicKey =
    trim(
        (string)
        (
            $mercadoPagoPublicKey
            ?? ''
        )
    );


/*
=================================
AGRUPA OS ITENS
=================================
*/

$gruposCheckout = [];


foreach (
    $itens as $item
) {

    $categoriaId =
        (int)
        $item[
            'categoria_id'
        ];


    $categoriaNome =
        (string)
        (
            $item[
                'categoria_nome'
            ]
            ?? ''
        );


    $nomeCategoria =
        mb_strtolower(
            trim(
                $categoriaNome
            ),
            'UTF-8'
        );


    $tipoCategoria =
        'unica';


    if (
        strpos(
            $nomeCategoria,
            'tradicionais'
        ) !== false
    ) {

        $tipoCategoria =
            'cento_tradicionais';

    } elseif (
        strpos(
            $nomeCategoria,
            'folhados'
        ) !== false
    ) {

        $tipoCategoria =
            'cento_folhados';

    } elseif (
        strpos(
            $nomeCategoria,
            'grandes'
        ) !== false
    ) {

        $tipoCategoria =
            'salgados_grandes';

    } elseif (
        strpos(
            $nomeCategoria,
            'empadão'
        ) !== false
        ||
        strpos(
            $nomeCategoria,
            'empadões'
        ) !== false
        ||
        strpos(
            $nomeCategoria,
            'empadao'
        ) !== false
        ||
        strpos(
            $nomeCategoria,
            'empadoes'
        ) !== false
    ) {

        $tipoCategoria =
            'empadao';
    }


    if (
        !isset(
            $gruposCheckout[
                $categoriaId
            ]
        )
    ) {

        $gruposCheckout[
            $categoriaId
        ] = [

            'tipo_categoria' =>
                $tipoCategoria,

            'categoria_nome' =>
                $categoriaNome,

            'produtos' =>
                [],

            'quantidade_total' =>
                0,

            'quantidade_centos' =>
                0,

            'preco_unitario' =>
                0.0,

            'subtotal' =>
                0.0,
        ];
    }


    $gruposCheckout[
        $categoriaId
    ]['produtos'][] = [

        'nome' =>
            (string)
            $item[
                'nome'
            ],

        'quantidade' =>
            (int)
            $item[
                'quantidade'
            ],

        'preco_unitario' =>
            (float)
            $item[
                'preco_unitario'
            ],
    ];


    $gruposCheckout[
        $categoriaId
    ]['quantidade_total'] +=
        (int)
        $item[
            'quantidade'
        ];
}


/*
=================================
CALCULA OS GRUPOS
=================================
*/

foreach (
    $gruposCheckout as
    &$grupo
) {

    $tipoCategoria =
        $grupo[
            'tipo_categoria'
        ];


    /*
    =================================
    CENTO TRADICIONAIS
    =================================
    */

    if (
        $tipoCategoria ===
        'cento_tradicionais'
    ) {

        $partesPorCento =
            4;


        $grupo[
            'quantidade_centos'
        ] =
            (int)
            ceil(
                $grupo[
                    'quantidade_total'
                ]
                /
                $partesPorCento
            );


        $precoPorParte =
            (float)
            (
                $grupo[
                    'produtos'
                ][0][
                    'preco_unitario'
                ]
                ?? 0
            );


        $grupo[
            'preco_unitario'
        ] =
            $precoPorParte
            *
            $partesPorCento;


        $grupo[
            'subtotal'
        ] =
            $grupo[
                'quantidade_centos'
            ]
            *
            $grupo[
                'preco_unitario'
            ];


    /*
    =================================
    CENTO FOLHADOS
    =================================
    */

    } elseif (
        $tipoCategoria ===
        'cento_folhados'
    ) {

        $partesPorCento =
            2;


        $grupo[
            'quantidade_centos'
        ] =
            (int)
            ceil(
                $grupo[
                    'quantidade_total'
                ]
                /
                $partesPorCento
            );


        $precoPorParte =
            (float)
            (
                $grupo[
                    'produtos'
                ][0][
                    'preco_unitario'
                ]
                ?? 0
            );


        $grupo[
            'preco_unitario'
        ] =
            $precoPorParte
            *
            $partesPorCento;


        $grupo[
            'subtotal'
        ] =
            $grupo[
                'quantidade_centos'
            ]
            *
            $grupo[
                'preco_unitario'
            ];


    /*
    =================================
    PRODUTOS UNITÁRIOS
    =================================
    */

    } else {

        $subtotalGrupo =
            0.0;


        foreach (
            $grupo[
                'produtos'
            ]
            as $produto
        ) {

            $subtotalGrupo +=
                (
                    (int)
                    $produto[
                        'quantidade'
                    ]
                )
                *
                (
                    (float)
                    $produto[
                        'preco_unitario'
                    ]
                );
        }


        $grupo[
            'subtotal'
        ] =
            $subtotalGrupo;


        $grupo[
            'preco_unitario'
        ] =
            (float)
            (
                $grupo[
                    'produtos'
                ][0][
                    'preco_unitario'
                ]
                ?? 0
            );
    }
}


unset($grupo);


/*
=================================
AGRUPA HORÁRIOS POR DATA
=================================
*/

$horariosPorData =
    [];


foreach (
    $horariosDisponiveis
    as $horario
) {

    $data =
        (string)
        (
            $horario[
                'data'
            ]
            ?? ''
        );


    if (
        $data === ''
    ) {

        continue;
    }


    if (
        !isset(
            $horariosPorData[
                $data
            ]
        )
    ) {

        $horariosPorData[
            $data
        ] = [];
    }


    $horariosPorData[
        $data
    ][] =
        $horario;
}


/*
=================================
TIPO DO HORÁRIO
=================================
*/

$textoTipoHorario =
    $modalidadeRecebimento ===
    'retirada'
        ? 'retirada'
        : 'entrega';

?>

<link
    rel="stylesheet"
    href="<?= htmlspecialchars(
        BASE_URL . '/assets/css/checkout.css',
        ENT_QUOTES,
        'UTF-8'
    ); ?>"
>


<main class="checkout-page">

    <div class="checkout-container">


        <!-- =================================
             CABEÇALHO
        ================================== -->

        <header class="checkout-header">

            <p class="checkout-etiqueta">
                Finalização
            </p>


            <h1>
                Finalizar pedido
            </h1>


            <p>
                Escolha como deseja receber seu pedido,
                selecione o horário e confira os valores
                antes de finalizar.
            </p>

        </header>


        <!-- =================================
             ERRO
        ================================== -->

        <?php if (
            $erro !== null
        ): ?>

            <div
                class="checkout-alert"
                role="alert"
            >

                <?= htmlspecialchars(
                    (string)
                    $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =================================
             FORMULÁRIO
        ================================== -->

        <form
            method="POST"
            action="<?= BASE_URL ?>/checkout/finalizar"
            class="checkout-form"
            id="checkout-form"
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?= htmlspecialchars(
                    $csrfToken,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <!-- =================================
                 MODALIDADE
            ================================== -->

            <section class="checkout-card mb-4">

                <div class="checkout-card-header">

                    <span>
                        1
                    </span>


                    <div>

                        <h2>
                            Forma de recebimento
                        </h2>


                        <p>
                            Escolha entre receber em seu endereço
                            ou retirar seu pedido.
                        </p>

                    </div>

                </div>


                <div class="checkout-pagamentos">

                    <label
                        class="checkout-pagamento-option"
                    >

                        <input
                            type="radio"
                            name="modalidade_recebimento"
                            value="entrega"
                            <?= $modalidadeRecebimento === 'entrega'
                                ? 'checked'
                                : ''
                            ?>
                        >


                        <div>

                            <strong>
                                Entrega
                            </strong>


                            <small>
                                Receba seu pedido
                                no endereço cadastrado.
                            </small>

                        </div>

                    </label>


                    <label
                        class="checkout-pagamento-option"
                    >

                        <input
                            type="radio"
                            name="modalidade_recebimento"
                            value="retirada"
                            <?= $modalidadeRecebimento === 'retirada'
                                ? 'checked'
                                : ''
                            ?>
                        >


                        <div>

                            <strong>
                                Retirada
                            </strong>


                            <small>
                                Retire seu pedido
                                no local de produção.
                            </small>

                        </div>

                    </label>

                </div>


                <?php if (
                    $modalidadeRecebimento ===
                    'retirada'
                ): ?>

                    <div
                        class="checkout-frete-alerta"
                        style="
                            border-color:
                                rgba(245, 124, 0, 0.18);

                            background:
                                #fffaf4;

                            color:
                                var(--marrom);
                        "
                    >

                        <strong>
                            Local de retirada
                        </strong>


                        <span>

                            <?= htmlspecialchars(
                                $enderecoRetirada,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>


                        <span>
                            Taxa de entrega:
                            R$ 0,00
                        </span>

                    </div>

                <?php endif; ?>

            </section>


            <div class="checkout-grid">


                <!-- =================================
                     ENDEREÇO / RETIRADA
                ================================== -->

                <?php if (
                    $modalidadeRecebimento ===
                    'entrega'
                ): ?>

                    <section class="checkout-card">

                        <div
                            class="checkout-card-header"
                        >

                            <span>
                                2
                            </span>


                            <div>

                                <h2>
                                    Endereço de entrega
                                </h2>


                                <p>
                                    Escolha onde deseja
                                    receber este pedido.
                                </p>

                            </div>

                        </div>


                        <?php if (
                            $enderecos === []
                        ): ?>

                            <div
                                class="checkout-endereco-vazio"
                                style="
                                    padding:
                                        30px 20px;

                                    box-shadow:
                                        none;
                                "
                            >

                                <div
                                    class="checkout-endereco-icone"
                                    style="
                                        font-size:
                                            2.5rem;
                                    "
                                >
                                    📍
                                </div>


                                <h2>
                                    Nenhum endereço cadastrado
                                </h2>


                                <p>
                                    Cadastre um endereço
                                    para receber seu pedido.
                                </p>


                                <a
                                    href="<?= BASE_URL ?>/cliente/enderecos/novo?retorno=checkout"
                                    class="checkout-btn-principal"
                                >
                                    Cadastrar endereço
                                </a>

                            </div>

                        <?php else: ?>

                            <div
                                class="checkout-enderecos"
                            >

                                <?php foreach (
                                    $enderecos
                                    as $endereco
                                ): ?>

                                    <label
                                        class="checkout-endereco-option"
                                    >

                                        <input
                                            type="radio"
                                            name="endereco_id"
                                            value="<?= (int) $endereco['id'] ?>"
                                            <?= (
                                                $enderecoSelecionado !== null
                                                &&
                                                (int)
                                                $enderecoSelecionado[
                                                    'id'
                                                ]
                                                ===
                                                (int)
                                                $endereco[
                                                    'id'
                                                ]
                                            )
                                                ? 'checked'
                                                : ''
                                            ?>
                                        >


                                        <div
                                            class="checkout-endereco-option-conteudo"
                                        >

                                            <div
                                                class="checkout-endereco-option-topo"
                                            >

                                                <strong>

                                                    <?= htmlspecialchars(
                                                        (string)
                                                        $endereco[
                                                            'identificacao'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </strong>


                                                <?php if (
                                                    (int)
                                                    $endereco[
                                                        'principal'
                                                    ]
                                                    === 1
                                                ): ?>

                                                    <span>
                                                        Principal
                                                    </span>

                                                <?php endif; ?>

                                            </div>


                                            <p>

                                                <?= htmlspecialchars(
                                                    (string)
                                                    $endereco[
                                                        'logradouro'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>,

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

                                                ·

                                                <?= htmlspecialchars(
                                                    (string)
                                                    $endereco[
                                                        'cidade'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                                -

                                                <?= htmlspecialchars(
                                                    (string)
                                                    $endereco[
                                                        'estado'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </p>


                                            <small>

                                                CEP:

                                                <?= htmlspecialchars(
                                                    (string)
                                                    $endereco[
                                                        'cep'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </small>

                                        </div>

                                    </label>

                                <?php endforeach; ?>

                            </div>


                            <?php if (
                                $enderecoSelecionado !== null
                            ): ?>

                                <?php if (
                                    $freteDisponivel
                                ): ?>

                                    <div
                                        class="checkout-frete-alerta"
                                        style="
                                            border-color:
                                                rgba(245, 124, 0, 0.18);

                                            background:
                                                #fffaf4;

                                            color:
                                                var(--marrom);
                                        "
                                    >

                                        <strong>
                                            Entrega calculada
                                        </strong>


                                        <?php if (
                                            $distanciaKm !== null
                                        ): ?>

                                            <span>

                                                Distância aproximada:

                                                <?= number_format(
                                                    (float)
                                                    $distanciaKm,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>

                                                km

                                            </span>

                                        <?php endif; ?>


                                        <span>

                                            Frete:

                                            R$

                                            <?= number_format(
                                                $frete,
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </span>

                                    </div>

                                <?php else: ?>

                                    <div
                                        class="checkout-frete-alerta"
                                        role="alert"
                                    >

                                        <strong>
                                            Frete não calculado
                                        </strong>


                                        <span>
                                            Não foi possível calcular
                                            a distância até o endereço
                                            selecionado.
                                        </span>

                                    </div>

                                <?php endif; ?>

                            <?php endif; ?>


                            <a
                                href="<?= BASE_URL ?>/cliente/enderecos/novo?retorno=checkout"
                                class="checkout-link-endereco"
                            >
                                + Adicionar outro endereço
                            </a>

                        <?php endif; ?>

                    </section>


                <?php else: ?>

                    <section class="checkout-card">

                        <div
                            class="checkout-card-header"
                        >

                            <span>
                                2
                            </span>


                            <div>

                                <h2>
                                    Retirada
                                </h2>


                                <p>
                                    Seu pedido ficará disponível
                                    para retirada no local.
                                </p>

                            </div>

                        </div>


                        <div
                            class="checkout-frete-alerta"
                            style="
                                border-color:
                                    rgba(245, 124, 0, 0.18);

                                background:
                                    #fffaf4;

                                color:
                                    var(--marrom);
                            "
                        >

                            <strong>
                                Endereço para retirada
                            </strong>


                            <span>

                                <?= htmlspecialchars(
                                    $enderecoRetirada,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>


                            <span>
                                Frete:
                                R$ 0,00
                            </span>

                        </div>

                    </section>

                <?php endif; ?>


                <!-- =================================
                     PAGAMENTO
                ================================== -->

                <section class="checkout-card">

                    <div
                        class="checkout-card-header"
                    >

                        <span>
                            3
                        </span>


                        <div>

                            <h2>
                                Forma de pagamento
                            </h2>


                            <p>
                                Escolha como deseja pagar.
                            </p>

                        </div>

                    </div>


                    <div
                        class="checkout-pagamentos"
                    >

                        <label
                            class="checkout-pagamento-option"
                        >

                            <input
                                type="radio"
                                name="metodo_pagamento"
                                value="pix"
                                checked
                            >


                            <div>

                                <strong>
                                    Pix
                                </strong>


                                <small>
                                    Pagamento instantâneo.
                                </small>

                            </div>

                        </label>


                        <label
                            class="checkout-pagamento-option"
                        >

                            <input
                                type="radio"
                                name="metodo_pagamento"
                                value="cartao"
                                <?= $mercadoPagoPublicKey === ''
                                    ? 'disabled'
                                    : ''
                                ?>
                            >


                            <div>

                                <strong>
                                    Cartão
                                </strong>


                                <small>

                                    <?php if (
                                        $mercadoPagoPublicKey !== ''
                                    ): ?>

                                        Crédito ou débito.

                                    <?php else: ?>

                                        Pagamento com cartão
                                        indisponível.

                                    <?php endif; ?>

                                </small>

                            </div>

                        </label>

                    </div>


                    <?php if (
                        $mercadoPagoPublicKey !== ''
                    ): ?>

                        <div
                            id="checkout-cartao-container"
                            style="
                                display: none;
                                margin-top: 24px;
                            "
                        >

                            <div
                                class="checkout-frete-alerta"
                                style="
                                    margin-bottom: 18px;
                                    border-color:
                                        rgba(245, 124, 0, 0.18);

                                    background:
                                        #fffaf4;

                                    color:
                                        var(--marrom);
                                "
                            >

                                <strong>
                                    Pagamento seguro
                                </strong>


                                <span>
                                    Os dados do cartão são processados
                                    diretamente pelo Mercado Pago.
                                </span>

                            </div>


                            <div
                                id="cardPaymentBrick_container"
                            ></div>

                        </div>

                    <?php endif; ?>

                </section>

            </div>


            <!-- =================================
                 HORÁRIO
            ================================== -->

            <section class="checkout-card mt-4">

                <div
                    class="checkout-card-header"
                >

                    <span>
                        4
                    </span>


                    <div>

                        <h2>
                            Escolha o horário
                        </h2>


                        <p>

                            Selecione quando deseja
                            <?= $textoTipoHorario ?>
                            seu pedido.

                        </p>

                    </div>

                </div>


                <?php if (
                    $horariosPorData === []
                ): ?>

                    <div
                        class="checkout-frete-alerta"
                        role="alert"
                    >

                        <strong>
                            Nenhum horário disponível
                        </strong>


                        <span>
                            Não encontramos horários disponíveis
                            para este pedido no momento.
                        </span>

                    </div>

                <?php else: ?>

                    <div
                        class="checkout-horarios"
                    >

                        <?php foreach (
                            $horariosPorData
                            as $data => $horarios
                        ): ?>

                            <?php

                            $primeiroHorario =
                                $horarios[0];

                            ?>


                            <div
                                class="checkout-horario-dia"
                            >

                                <h3>

                                    <?= htmlspecialchars(
                                        (string)
                                        $primeiroHorario[
                                            'data_formatada'
                                        ],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </h3>


                                <div
                                    class="checkout-horario-lista"
                                >

                                    <?php foreach (
                                        $horarios
                                        as $horario
                                    ): ?>

                                        <label
                                            class="checkout-horario-opcao"
                                        >

                                            <input
                                                type="radio"
                                                name="data_hora_agendada"
                                                value="<?= htmlspecialchars(
                                                    (string)
                                                    $horario[
                                                        'datetime'
                                                    ],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                required
                                            >


                                            <span>

                                                <?php if (
                                                    $modalidadeRecebimento ===
                                                    'entrega'
                                                ): ?>

                                                    <?= htmlspecialchars(
                                                        (string)
                                                        $horario[
                                                            'hora'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                    -

                                                    <?= htmlspecialchars(
                                                        (string)
                                                        $horario[
                                                            'hora_fim'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                <?php else: ?>

                                                    <?= htmlspecialchars(
                                                        (string)
                                                        $horario[
                                                            'hora'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                <?php endif; ?>

                                            </span>

                                        </label>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <div
                        class="checkout-frete-alerta"
                        style="
                            border-color:
                                rgba(245, 124, 0, 0.18);

                            background:
                                #fffaf4;

                            color:
                                var(--marrom);
                        "
                    >

                        <strong>
                            Horário de funcionamento
                        </strong>


                        <span>
                            Atendimento e produção:
                            todos os dias, das 08:00 às 17:00.
                        </span>


                        <span>
                            Os horários exibidos já consideram
                            o prazo mínimo necessário para preparar
                            seu pedido.
                        </span>

                    </div>

                <?php endif; ?>

            </section>


            <!-- =================================
                 RESUMO
            ================================== -->

            <section
                class="checkout-card checkout-resumo mt-4"
            >

                <div
                    class="checkout-card-header"
                >

                    <span>
                        5
                    </span>


                    <div>

                        <h2>
                            Resumo do pedido
                        </h2>


                        <p>
                            Confira os valores antes
                            de finalizar.
                        </p>

                    </div>

                </div>


                <div
                    class="checkout-produtos"
                >

                    <?php foreach (
                        $gruposCheckout
                        as $grupo
                    ): ?>

                        <div
                            class="checkout-produto-grupo"
                        >

                            <div
                                class="checkout-produto-lista"
                            >

                                <?php foreach (
                                    $grupo[
                                        'produtos'
                                    ]
                                    as $produto
                                ): ?>

                                    <div
                                        class="checkout-produto-linha"
                                    >

                                        <span>

                                            <?= htmlspecialchars(
                                                (string)
                                                $produto[
                                                    'nome'
                                                ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            ×

                                            <?= (int)
                                            $produto[
                                                'quantidade'
                                            ] ?>

                                        </span>

                                    </div>

                                <?php endforeach; ?>

                            </div>


                            <?php if (
                                $grupo[
                                    'tipo_categoria'
                                ]
                                ===
                                'cento_tradicionais'

                                ||

                                $grupo[
                                    'tipo_categoria'
                                ]
                                ===
                                'cento_folhados'
                            ): ?>

                                <div
                                    class="checkout-produto-detalhes"
                                >

                                    <div>

                                        <span>
                                            Quantidade
                                        </span>


                                        <strong>

                                            <?= (int)
                                            $grupo[
                                                'quantidade_centos'
                                            ] ?>

                                            <?= (
                                                (int)
                                                $grupo[
                                                    'quantidade_centos'
                                                ]
                                                ===
                                                1
                                            )
                                                ? 'cento'
                                                : 'centos'
                                            ?>

                                        </strong>

                                    </div>


                                    <div>

                                        <span>
                                            Valor
                                        </span>


                                        <strong>

                                            R$

                                            <?= number_format(
                                                (float)
                                                $grupo[
                                                    'preco_unitario'
                                                ],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                            / cento

                                        </strong>

                                    </div>

                                </div>


                            <?php elseif (
                                $grupo[
                                    'tipo_categoria'
                                ]
                                ===
                                'salgados_grandes'
                            ): ?>

                                <div
                                    class="checkout-produto-detalhes"
                                >

                                    <div>

                                        <span>
                                            Quantidade total
                                        </span>


                                        <strong>

                                            <?= (int)
                                            $grupo[
                                                'quantidade_total'
                                            ] ?>

                                            unidades

                                        </strong>

                                    </div>


                                    <div>

                                        <span>
                                            Preço por unidade
                                        </span>


                                        <strong>

                                            R$

                                            <?= number_format(
                                                (float)
                                                $grupo[
                                                    'preco_unitario'
                                                ],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                    </div>

                                </div>


                            <?php elseif (
                                $grupo[
                                    'tipo_categoria'
                                ]
                                ===
                                'empadao'
                            ): ?>

                                <div
                                    class="checkout-produto-detalhes"
                                >

                                    <div>

                                        <span>
                                            Quantidade
                                        </span>


                                        <strong>

                                            <?= (int)
                                            $grupo[
                                                'quantidade_total'
                                            ] ?>

                                            unidade(s)

                                        </strong>

                                    </div>


                                    <div>

                                        <span>
                                            Preço por unidade
                                        </span>


                                        <strong>

                                            R$

                                            <?= number_format(
                                                (float)
                                                $grupo[
                                                    'preco_unitario'
                                                ],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                    </div>

                                </div>

                            <?php endif; ?>


                            <div
                                class="checkout-produto-subtotal"
                            >

                                <span>
                                    Subtotal
                                </span>


                                <strong>

                                    R$

                                    <?= number_format(
                                        (float)
                                        $grupo[
                                            'subtotal'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </strong>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


                <div
                    class="checkout-valores"
                >

                    <div>

                        <span>
                            Subtotal
                        </span>


                        <strong>

                            R$

                            <?= number_format(
                                $subtotal,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>


                    <div>

                        <span>
                            Entrega
                        </span>


                        <strong>

                            <?= $modalidadeRecebimento === 'retirada'

                                ? 'R$ 0,00'

                                : (

                                    $freteDisponivel

                                    ? 'R$ '
                                        . number_format(
                                            $frete,
                                            2,
                                            ',',
                                            '.'
                                        )

                                    : 'A calcular'
                                )
                            ?>

                        </strong>

                    </div>


                    <?php if (
                        $distanciaKm !== null
                        &&
                        $modalidadeRecebimento ===
                        'entrega'
                    ): ?>

                        <div>

                            <span>
                                Distância
                            </span>


                            <strong>

                                <?= number_format(
                                    (float)
                                    $distanciaKm,
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                                km

                            </strong>

                        </div>

                    <?php endif; ?>


                    <?php if (
                        $desconto > 0
                    ): ?>

                        <div>

                            <span>
                                Desconto
                            </span>


                            <strong>

                                R$

                                <?= number_format(
                                    $desconto,
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </div>

                    <?php endif; ?>


                    <div
                        class="checkout-total"
                    >

                        <span>
                            Total
                        </span>


                        <strong>

                            R$

                            <?= number_format(
                                $total,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>

                </div>


                <div
                    class="checkout-acoes"
                >

                    <a
                        href="<?= BASE_URL ?>/carrinho"
                        class="checkout-btn-voltar"
                    >
                        Voltar ao carrinho
                    </a>


                    <button
                        type="submit"
                        class="checkout-btn-finalizar"
                        id="checkout-btn-finalizar"
                        <?= !$podeFinalizar
                            ? 'disabled'
                            : ''
                        ?>
                    >

                        Continuar para pagamento

                    </button>

                </div>

            </section>

        </form>

    </div>

</main>


<?php if (
    $mercadoPagoPublicKey !== ''
): ?>

    <script
        src="https://sdk.mercadopago.com/js/v2"
    ></script>

<?php endif; ?>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        =================================
        ELEMENTOS
        =================================
        */

        const checkoutForm =
            document.getElementById(
                'checkout-form'
            );


        const botaoFinalizar =
            document.getElementById(
                'checkout-btn-finalizar'
            );


        const radiosPagamento =
            document.querySelectorAll(
                'input[name="metodo_pagamento"]'
            );


        const radioPix =
            document.querySelector(
                'input[name="metodo_pagamento"][value="pix"]'
            );


        const radioCartao =
            document.querySelector(
                'input[name="metodo_pagamento"][value="cartao"]'
            );


        const containerCartao =
            document.getElementById(
                'checkout-cartao-container'
            );


        const containerBrick =
            document.getElementById(
                'cardPaymentBrick_container'
            );


        const podeUsarCartao =
            <?= $mercadoPagoPublicKey !== ''
                ? 'true'
                : 'false'
            ?>;


        const publicKey =
            <?= json_encode(
                $mercadoPagoPublicKey,
                JSON_UNESCAPED_SLASHES
                |
                JSON_UNESCAPED_UNICODE
            ) ?>;


        const valorTotal =
            <?= number_format(
                $total,
                2,
                '.',
                ''
            ) ?>;


        let cardPaymentBrickController =
            null;


        let carregandoCartao =
            false;


        /*
        =================================
        MODALIDADE
        =================================
        */

        const modalidades =
            document.querySelectorAll(
                'input[name="modalidade_recebimento"]'
            );


        modalidades.forEach(
            function (radio) {

                radio.addEventListener(
                    'change',
                    function () {

                        const url =
                            new URL(
                                window.location.href
                            );


                        url.searchParams.set(
                            'recebimento',
                            this.value
                        );


                        url.searchParams.delete(
                            'endereco'
                        );


                        window.location.href =
                            url.toString();
                    }
                );
            }
        );


        /*
        =================================
        ENDEREÇO
        =================================
        */

        const enderecos =
            document.querySelectorAll(
                'input[name="endereco_id"]'
            );


        enderecos.forEach(
            function (radio) {

                radio.addEventListener(
                    'change',
                    function () {

                        const url =
                            new URL(
                                window.location.href
                            );


                        url.searchParams.set(
                            'recebimento',
                            'entrega'
                        );


                        url.searchParams.set(
                            'endereco',
                            this.value
                        );


                        window.location.href =
                            url.toString();
                    }
                );
            }
        );


        /*
        =================================
        HORÁRIO
        =================================
        */

        const horarios =
            document.querySelectorAll(
                'input[name="data_hora_agendada"]'
            );


        horarios.forEach(
            function (radio) {

                radio.addEventListener(
                    'change',
                    function () {

                        horarios.forEach(
                            function (outroHorario) {

                                const opcao =
                                    outroHorario.closest(
                                        '.checkout-horario-opcao'
                                    );


                                if (
                                    opcao
                                ) {

                                    opcao.classList.remove(
                                        'selecionado'
                                    );
                                }
                            }
                        );


                        const opcaoAtual =
                            this.closest(
                                '.checkout-horario-opcao'
                            );


                        if (
                            opcaoAtual
                        ) {

                            opcaoAtual.classList.add(
                                'selecionado'
                            );
                        }
                    }
                );

            }
        );


        /*
        =================================
        PAGAMENTO
        =================================
        */

        radiosPagamento.forEach(
            function (radio) {

                radio.addEventListener(
                    'change',
                    function () {

                        if (
                            this.value ===
                            'cartao'
                        ) {

                            ativarCartao();

                        } else {

                            ativarPix();
                        }
                    }
                );
            }
        );


        /*
        =================================
        ATIVA PIX
        =================================
        */

        function ativarPix() {

            if (
                containerCartao
            ) {

                containerCartao.style.display =
                    'none';
            }


            if (
                botaoFinalizar
            ) {

                botaoFinalizar.style.display =
                    '';
            }


            destruirBrick();
        }


        /*
        =================================
        ATIVA CARTÃO
        =================================
        */

        async function ativarCartao() {

            if (
                !podeUsarCartao
                ||
                !radioCartao
                ||
                radioCartao.disabled
            ) {
                return;
            }


            if (
                !containerCartao
                ||
                !containerBrick
            ) {
                return;
            }


            containerCartao.style.display =
                'block';


            if (
                botaoFinalizar
            ) {

                botaoFinalizar.style.display =
                    'none';
            }


            if (
                cardPaymentBrickController
            ) {

                return;
            }


            if (
                carregandoCartao
            ) {

                return;
            }


            carregandoCartao =
                true;


            try {

                if (
                    typeof MercadoPago ===
                    'undefined'
                ) {

                    throw new Error(
                        'O SDK do Mercado Pago não foi carregado.'
                    );
                }


                if (
                    !publicKey
                ) {

                    throw new Error(
                        'A Public Key do Mercado Pago não foi configurada.'
                    );
                }


                const mercadoPago =
                    new MercadoPago(
                        publicKey
                    );


                const bricksBuilder =
                    mercadoPago.bricks();


                const settings = {

                    initialization: {

                        amount:
                            Number(
                                valorTotal
                            ),
                    },


                    callbacks: {

                        onReady:
                            function () {

                                carregandoCartao =
                                    false;
                            },


                        onSubmit:
                            function (
                                formData
                            ) {

                                return new Promise(
                                    function (
                                        resolve,
                                        reject
                                    ) {

                                        try {

                                            if (
                                                !formData
                                                ||
                                                !formData.token
                                            ) {

                                                throw new Error(
                                                    'O Mercado Pago não gerou o token do cartão.'
                                                );
                                            }


                                            /*
                                            ==========================
                                            TOKEN
                                            ==========================
                                            */

                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'card_token',
                                                formData.token
                                            );


                                            /*
                                            ==========================
                                            PAYMENT METHOD
                                            ==========================
                                            */

                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'card_payment_method_id',
                                                formData.payment_method_id
                                                    || ''
                                            );


                                            /*
                                            ==========================
                                            ISSUER
                                            ==========================
                                            */

                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'card_issuer_id',
                                                formData.issuer_id
                                                    || ''
                                            );


                                            /*
                                            ==========================
                                            PARCELAS
                                            ==========================
                                            */

                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'card_installments',
                                                formData.installments
                                                    || '1'
                                            );


                                            /*
                                            ==========================
                                            NOME DO TITULAR
                                            ==========================
                                            */

                                            const nomeTitular =
                                                formData.cardholderName
                                                ||
                                                (
                                                    formData.payer
                                                    &&
                                                    formData.payer.first_name
                                                )
                                                ||
                                                '';


                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'cardholder_name',
                                                nomeTitular
                                            );


                                            /*
                                            ==========================
                                            DOCUMENTO
                                            ==========================
                                            */

                                            let tipoDocumento =
                                                '';


                                            let numeroDocumento =
                                                '';


                                            if (
                                                formData.cardholderIdentificationType
                                            ) {

                                                tipoDocumento =
                                                    formData.cardholderIdentificationType;
                                            }


                                            if (
                                                formData.cardholderIdentificationNumber
                                            ) {

                                                numeroDocumento =
                                                    formData.cardholderIdentificationNumber;
                                            }


                                            if (
                                                formData.payer
                                                &&
                                                formData.payer.identification
                                            ) {

                                                if (
                                                    formData.payer.identification.type
                                                ) {

                                                    tipoDocumento =
                                                        formData
                                                            .payer
                                                            .identification
                                                            .type;
                                                }


                                                if (
                                                    formData.payer.identification.number
                                                ) {

                                                    numeroDocumento =
                                                        formData
                                                            .payer
                                                            .identification
                                                            .number;
                                                }
                                            }


                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'cardholder_identification_type',
                                                tipoDocumento
                                            );


                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'cardholder_identification_number',
                                                numeroDocumento
                                            );


                                            /*
                                            ==========================
                                            MÉTODO
                                            ==========================
                                            */

                                            adicionarCampoOculto(
                                                checkoutForm,
                                                'metodo_pagamento',
                                                'cartao'
                                            );


                                            /*
                                            ==========================
                                            ENVIA FORMULÁRIO
                                            ==========================
                                            */

                                            HTMLFormElement
                                                .prototype
                                                .submit
                                                .call(
                                                    checkoutForm
                                                );


                                            resolve();

                                        } catch (
                                            erro
                                        ) {

                                            console.error(
                                                'Erro ao preparar pagamento com cartão:',
                                                erro
                                            );


                                            reject(
                                                erro
                                            );
                                        }
                                    }
                                );
                            },


                        onError:
                            function (
                                erro
                            ) {

                                console.error(
                                    'Erro no Card Payment Brick:',
                                    erro
                                );


                                carregandoCartao =
                                    false;


                                alert(
                                    'Não foi possível carregar ou processar o pagamento com cartão. Tente novamente.'
                                );
                            },
                    },
                };


                cardPaymentBrickController =
                    await bricksBuilder.create(
                        'cardPayment',
                        'cardPaymentBrick_container',
                        settings
                    );


            } catch (
                erro
            ) {

                console.error(
                    'Erro ao criar Card Payment Brick:',
                    erro
                );


                carregandoCartao =
                    false;


                containerCartao.style.display =
                    'none';


                if (
                    botaoFinalizar
                ) {

                    botaoFinalizar.style.display =
                        '';
                }


                alert(
                    'Não foi possível carregar o pagamento com cartão. Verifique a configuração da Public Key do Mercado Pago.'
                );
            }
        }


        /*
        =================================
        DESTRÓI BRICK
        =================================
        */

        function destruirBrick() {

            if (
                cardPaymentBrickController
                &&
                typeof
                cardPaymentBrickController.unmount ===
                'function'
            ) {

                try {

                    const resultado =
                        cardPaymentBrickController
                        .unmount();


                    if (
                        resultado
                        &&
                        typeof resultado.catch ===
                        'function'
                    ) {

                        resultado.catch(
                            function () {}
                        );
                    }

                } catch (
                    erro
                ) {

                    console.error(
                        'Erro ao desmontar Card Payment Brick:',
                        erro
                    );
                }
            }


            cardPaymentBrickController =
                null;


            carregandoCartao =
                false;
        }


        /*
        =================================
        CAMPO OCULTO
        =================================
        */

        function adicionarCampoOculto(
            formulario,
            nome,
            valor
        ) {

            let campo =
                formulario.querySelector(
                    'input[name="' +
                    nome +
                    '"]'
                );


            if (
                !campo
            ) {

                campo =
                    document.createElement(
                        'input'
                    );


                campo.type =
                    'hidden';


                campo.name =
                    nome;


                formulario.appendChild(
                    campo
                );
            }


            campo.value =
                valor === null
                    ? ''
                    : String(
                        valor
                    );
        }


        /*
        =================================
        SUBMIT NORMAL
        =================================
        */

        if (
            checkoutForm
        ) {

            checkoutForm.addEventListener(
                'submit',
                function (
                    evento
                ) {

                    if (
                        radioCartao
                        &&
                        radioCartao.checked
                    ) {

                        /*
                        O pagamento com cartão
                        deve ser submetido pelo
                        botão interno do Brick.
                        */

                        if (
                            !cardPaymentBrickController
                        ) {

                            evento.preventDefault();

                            ativarCartao();

                            return;
                        }


                        /*
                        Se já existe Brick, não
                        usamos o botão externo.
                        */

                        evento.preventDefault();

                    }

                }
            );
        }


        /*
        =================================
        ESTADO INICIAL
        =================================
        */

        if (
            radioPix
            &&
            radioPix.checked
        ) {

            ativarPix();
        }

    }
);
</script>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';

?>