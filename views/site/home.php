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
| DESTAQUES
|--------------------------------------------------------------------------
*/

    View::componente(
        'site/sections/destaques',
        [
            'categoriasDestaques' =>
            $categoriasDestaques,
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