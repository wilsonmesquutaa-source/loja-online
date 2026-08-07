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

<main>

    <?php


    View::componente(
        'site/sections/produtos',
        [
            'categorias' =>
            $categorias,
        ]
    );


    ?>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
