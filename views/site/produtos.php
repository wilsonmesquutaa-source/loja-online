<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/header.php';

?>

<main class="container py-5">

    <h1 class="mb-4">Produtos</h1>

    <div class="row g-4">

        <?php foreach ($produtos as $produto): ?>

            <div class="col-md-6 col-lg-4">
                <article class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">

                        <h2 class="h5">
                            <?=
                                htmlspecialchars(
                                    $produto['nome'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ?>
                        </h2>

                        <p class="text-primary fs-4 fw-bold mt-auto">
                            R$
                            <?=
                                number_format(
                                    (float) $produto['preco'],
                                    2,
                                    ',',
                                    '.'
                                )
                            ?>
                        </p>

                        <a class="btn btn-primary" href="#">
                            Ver produto
                        </a>

                    </div>
                </article>
            </div>

        <?php endforeach; ?>

    </div>

</main>

<?php

require APP_ROOT . '/views/layouts/footer.php';
