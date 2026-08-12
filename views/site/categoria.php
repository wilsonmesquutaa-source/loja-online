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

        <div class="mb-5">

            <a
                href="<?= BASE_URL ?>/produtos"
                class="btn btn-voltar-cardapio"

                <i class="bi bi-arrow-left me-1"></i>

                Voltar ao cardápio

            </a>

        </div>
        <?php

        View::componente(
            'site/sections/categoria',
            [
                'categoria' => $categoria,
                'produtos' => $produtos,
                'limiteOpcoes' => $limiteOpcoes,
                'tipoCategoria' => $tipoCategoria,
            ]
        );

        ?>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
