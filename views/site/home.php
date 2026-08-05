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
        'site/sections/hero',
        [
            'tituloHero' =>
                $tituloHero,

            'textoHero' =>
                $textoHero,
        ]
    );

    View::componente(
        'site/sections/beneficios',
        [
            'beneficios' =>
                $beneficios,
        ]
    );

    View::componente(
        'site/sections/produtos',
        [
            'produtos' =>
                $produtos,
        ]
    );

    View::componente(
        'site/sections/contato',
        [
            'emailContato' =>
                $emailContato,

            'telefoneContato' =>
                $telefoneContato,
        ]
    );

    ?>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
