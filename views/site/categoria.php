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


$editarIndice = filter_input(
    INPUT_GET,
    'editar',
    FILTER_VALIDATE_INT
);

$editarIndice =
    $editarIndice === false
    ? null
    : $editarIndice;


$quantidadesIniciais = [];


if (
    $editarIndice !== null
    &&
    isset(
        $_SESSION['carrinho'][$editarIndice]
    )
) {
    $item =
        $_SESSION['carrinho'][$editarIndice];

    foreach (
        $item['produtos'] ?? []
        as $produto
    ) {
        $quantidadesIniciais[(int) $produto['produto_id']] =
            (int) $produto['quantidade'];
    }
}

?>

<main>

    <div class="container py-5">

        <div class="mb-5">

            <a
                href="<?= BASE_URL ?>/carrinho"
                class="btn btn-voltar-cardapio">

                <i class="bi bi-arrow-left me-1"></i>

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

                'editarIndice' =>
                $editarIndice,
            ]
        );

        ?>

    </div>

</main>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
