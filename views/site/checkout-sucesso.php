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

<main class="checkout-page">

    <div class="checkout-sucesso">

        <div class="checkout-sucesso-icone">
            ✓
        </div>

        <p class="checkout-etiqueta">
            Pedido recebido
        </p>

        <h1>
            Pedido realizado com sucesso!
        </h1>

        <p>
            Seu pedido foi registrado e está aguardando o pagamento.
        </p>


        <div class="checkout-sucesso-codigo">

            <span>
                Número do pedido
            </span>

            <strong>
                <?= htmlspecialchars(
                    (string)
                    $pedido['codigo'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>

        </div>


        <div class="checkout-sucesso-total">

            <span>
                Total
            </span>

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

        </div>


        <div class="checkout-sucesso-acoes">

            <a
                href="<?= BASE_URL ?>/cliente/pedidos/<?= (int) $pedido['id'] ?>"
                class="checkout-btn-principal"
            >
                Ver pedido
            </a>

            <a
                href="<?= BASE_URL ?>/cardapio"
                class="checkout-btn-voltar"
            >
                Voltar ao cardápio
            </a>

        </div>

    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';