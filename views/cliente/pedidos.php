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

?>

<main class="cliente-pedidos-page">

    <div class="cliente-pedidos-container">

        <header class="cliente-pedidos-header">

            <p class="cliente-pedidos-etiqueta">
                Minha conta
            </p>

            <h1>
                Meus pedidos
            </h1>

            <p>
                Consulte seu histórico de pedidos e acompanhe cada compra.
            </p>

        </header>


        <?php if ($pedidos === []): ?>

            <section class="cliente-pedidos-vazio">

                <div class="cliente-pedidos-vazio-icone">
                    🛍️
                </div>

                <h2>
                    Você ainda não fez nenhum pedido.
                </h2>

                <p>
                    Quando você fizer uma compra, seus pedidos aparecerão aqui.
                </p>

                <a
                    href="<?= BASE_URL ?>/cardapio"
                    class="cliente-pedidos-btn-principal"
                >
                    Ver cardápio
                </a>

            </section>

        <?php else: ?>

            <section class="cliente-pedidos-lista">

                <?php foreach (
                    $pedidos
                    as $pedido
                ): ?>

                    <?php

                    $statusLabels = [
                        'aguardando_pagamento' =>
                            'Aguardando pagamento',

                        'pago' =>
                            'Pago',

                        'em_separacao' =>
                            'Em separação',

                        'enviado' =>
                            'Enviado',

                        'entregue' =>
                            'Entregue',

                        'cancelado' =>
                            'Cancelado',
                    ];

                    $statusClasses = [
                        'aguardando_pagamento' =>
                            'aguardando',

                        'pago' =>
                            'pago',

                        'em_separacao' =>
                            'separacao',

                        'enviado' =>
                            'enviado',

                        'entregue' =>
                            'entregue',

                        'cancelado' =>
                            'cancelado',
                    ];

                    $status =
                        (string)
                        $pedido['status'];

                    ?>

                    <article class="cliente-pedido-card">

                        <div class="cliente-pedido-card-topo">

                            <div>

                                <p class="cliente-pedido-codigo">
                                    Pedido
                                    <strong>
                                        <?= htmlspecialchars(
                                            (string)
                                            $pedido['codigo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </p>

                                <p class="cliente-pedido-data">

                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            (string)
                                            $pedido['criado_em']
                                        )
                                    ) ?>

                                </p>

                            </div>


                            <span
                                class="
                                    cliente-pedido-status
                                    cliente-pedido-status-<?= htmlspecialchars(
                                        $statusClasses[$status]
                                        ?? 'aguardando',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>"
                            >

                                <?= htmlspecialchars(
                                    $statusLabels[$status]
                                    ?? $status,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        </div>


                        <div class="cliente-pedido-resumo">

                            <div>

                                <span>
                                    Subtotal
                                </span>

                                <strong>
                                    R$
                                    <?= number_format(
                                        (float)
                                        $pedido['subtotal'],
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
                                    R$
                                    <?= number_format(
                                        (float)
                                        $pedido['frete'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                            </div>


                            <?php if (
                                (float)
                                $pedido['desconto']
                                > 0
                            ): ?>

                                <div>

                                    <span>
                                        Desconto
                                    </span>

                                    <strong>
                                        R$
                                        <?= number_format(
                                            (float)
                                            $pedido['desconto'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </strong>

                                </div>

                            <?php endif; ?>


                            <div>

                                <span>
                                    Total
                                </span>

                                <strong
                                    class="cliente-pedido-total"
                                >
                                    R$
                                    <?= number_format(
                                        (float)
                                        $pedido['total'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="cliente-pedido-card-acoes">

                            <a
                                href="<?= BASE_URL ?>/cliente/pedidos/<?= (int) $pedido['id'] ?>"
                                class="cliente-pedido-btn"
                            >
                                Ver detalhes
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';