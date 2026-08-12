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

$editar =
    filter_input(
        INPUT_GET,
        'editar',
        FILTER_VALIDATE_INT
    );

$editarCategoriaId =
    $editar !== false
    ? (int) $categoria['id']
    : null;

?>

<main>

    <div class="container py-5">

        <div class="mb-5">

            <a
                href="<?= BASE_URL ?>/carrinho"
                class="btn btn-voltar-cardapio">

                <i
                    class="bi bi-arrow-left me-1">
                </i>

                Voltar ao carrinho

            </a>

        </div>


        <?php

        View::componente(
            'site/sections/categoria',
            [
                'categoria' =>
                    $categoria,

                'produtos' =>
                    $produtos,

                'limiteOpcoes' =>
                    $limiteOpcoes,

                'tipoCategoria' =>
                    $tipoCategoria,

                'quantidadesIniciais' =>
                    $quantidadesIniciais,

                'editarCategoriaId' =>
                    $editarCategoriaId,
            ]
        );

        ?>

    </div>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';