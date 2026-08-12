<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/site/header.php';

?>

<main>

    <div class="container py-5 text-center">

        <h1 class="display-4 fw-bold">
            404
        </h1>

        <h2 class="h4 mb-3">
            Página não encontrada
        </h2>

        <p class="text-secondary mb-4">
            A página que você tentou acessar
            não existe ou não está disponível.
        </p>

        <a
            href="<?= BASE_URL ?>/"
            class="btn btn-voltar-cardapio">

            <i class="bi bi-house me-1"></i>

            Voltar ao início

        </a>

    </div>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
