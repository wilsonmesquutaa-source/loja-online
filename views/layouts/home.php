<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/header.php';

?>

<main class="container py-5">

    <section class="bg-primary text-white rounded-4 p-5">

        <h1>
            <?=
                htmlspecialchars(
                    $mensagem,
                    ENT_QUOTES,
                    'UTF-8'
                )
            ?>
        </h1>

        <p class="lead">
            Projeto estruturado com rotas, Controllers e views.
        </p>

        <a
            class="btn btn-light"
            href="<?= BASE_URL ?>/produtos"
        >
            Ver produtos
        </a>

    </section>

</main>

<?php

require APP_ROOT . '/views/layouts/footer.php';
