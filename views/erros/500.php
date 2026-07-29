<?php

declare(strict_types=1);

$tituloPagina = 'Erro interno';

require APP_ROOT . '/views/layouts/header.php';

?>

<main class="container py-5">

    <div class="text-center py-5">

        <p class="display-1 fw-bold text-danger mb-0">
            500
        </p>

        <h1 class="h2">
            Não foi possível concluir a solicitação
        </h1>

        <p class="text-secondary">
            Ocorreu um erro interno. Tente novamente mais tarde.
        </p>

        <a
            class="btn btn-primary"
            href="<?= BASE_URL ?>/"
        >
            Voltar ao início
        </a>

    </div>

</main>

<?php

require APP_ROOT . '/views/layouts/footer.php';
