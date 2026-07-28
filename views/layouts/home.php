<?php

declare(strict_types=1);

$tituloPagina = 'Página inicial — Loja Online';

require $raizProjeto . '/views/layouts/header.php';

?>

<main class="container py-5">

    <section
        class="bg-primary text-white rounded-4 p-5 shadow"
    >
        <h1>Bem-vindo à Loja Online</h1>

        <p class="lead">
            Projeto desenvolvido durante a UC12.
        </p>

        <a
            class="btn btn-light"
            href="index.php?pagina=produtos"
        >
            Ver produtos
        </a>
    </section>

</main>

<?php

require $raizProjeto . '/views/layouts/footer.php';
