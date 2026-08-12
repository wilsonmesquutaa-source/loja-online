<?php

declare(strict_types=1);

use App\Helpers\View;

require APP_ROOT
    . '/views/layouts/site/header.php';

View::componente(
    'site/navbar',
    [
        'rotaAtual' => $rotaAtual,
    ]
);

?>

<main>

    <div class="container py-5">

        <h1 class="fw-bold mb-4">
            Seu carrinho
        </h1>

        <?php if ($carrinho === []): ?>

            <div class="alert alert-info">
                Seu carrinho está vazio.
            </div>

            <a
                href="<?= BASE_URL ?>/produtos"
                class="btn btn-voltar-cardapio">

                <i class="bi bi-arrow-left me-1"></i>

                Voltar ao cardápio

            </a>

        <?php else: ?>

            <?php foreach ($carrinho as $indice => $item): ?>

                <div class="card shadow-sm border-0 mb-3">

                    <div class="card-body">

                        <h2 class="h5 fw-bold mb-3">
                            <?= htmlspecialchars(
                                $item['categoria_nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p class="small text-secondary mb-3">
                            <?= htmlspecialchars(
                                $item['tipo_categoria'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <?php foreach (
                            $item['produtos']
                            as $produto
                        ): ?>

                            <div
                                class="d-flex justify-content-between
                                       align-items-center mb-2">

                                <span>

                                    <?= htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                    ×

                                    <?= (int) $produto['quantidade'] ?>

                                </span>

                            </div>

                        <?php endforeach; ?>

                        <hr>

                        <div
                            class="d-flex justify-content-between">

                            <span>
                                Quantidade total
                            </span>

                            <strong>
                                <?= (int) $item['quantidade_total'] ?>
                            </strong>

                        </div>

                        <div
                            class="d-flex justify-content-between
                                   mt-2">

                            <span>
                                Preço
                            </span>

                            <strong>

                                R$

                                <?= number_format(
                                    (float) $item['preco_unitario'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </div>

                        <div
                            class="d-flex justify-content-between
                                   mt-2">

                            <span>
                                Subtotal
                            </span>

                            <strong>

                                R$

                                <?= number_format(
                                    (float) $item['subtotal'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
