<?php

declare(strict_types=1);

$tituloPagina = 'Página não encontrada';

require $raizProjeto . '/views/layouts/header.php';

?>

<main class="container py-5">

    <div class="text-center py-5">

        <p class="display-1 fw-bold text-primary mb-0">
            404
        </p>

        <h1 class="h2">
            Página não encontrada
        </h1>

        <p class="text-secondary">
            O endereço informado não corresponde a uma página cadastrada.
        </p>

        <a
            class="btn btn-primary"
            href="index.php?pagina=home"
        >
            Voltar para o início
        </a>

    </div>

</main>

<?php

require $raizProjeto . '/views/layouts/footer.php';
