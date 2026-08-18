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


    /*
    |--------------------------------------------------------------------------
    | HERO
    |--------------------------------------------------------------------------
    */

    View::componente(
        'site/sections/hero',
        [
            'tituloHero' =>
                $tituloHero,

            'textoHero' =>
                $textoHero,
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | CARDÁPIO
    |--------------------------------------------------------------------------
    */

    View::componente(
        'site/sections/cardapio',
        [
            'categorias' =>
                $categorias,

            'etiquetaProdutos' =>
                'Cardápio',
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | BENEFÍCIOS
    |--------------------------------------------------------------------------
    */

    View::componente(
        'site/sections/beneficios',
        [
            'beneficios' =>
                $beneficios,
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | CONTATO
    |--------------------------------------------------------------------------
    */

    View::componente(
        'site/sections/contato',
        [
            'emailContato' =>
                $emailContato,

            'telefoneContato' =>
                $telefoneContato,

            'whatsappContato' =>
                $whatsappContato,

            'instagramContato' =>
                $instagramContato,

            'facebookContato' =>
                $facebookContato,
        ]
    );

    ?>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
?>