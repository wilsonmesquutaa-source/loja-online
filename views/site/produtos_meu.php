<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/header.php';

?>

<main class="container py-5">

    <h1 class="mb-4">Produtos</h1>

    <div class="row g-4">

        <?php foreach ($categorias as $categoria): ?>

            <div class="col-md-6 col-lg-3">

                <div
                    class="card h-100 shadow-sm border-0 categoria-card"
                    data-id="<?= $categoria['id']; ?>"
                    data-nome="<?= htmlspecialchars($categoria['nome']); ?>"
                    style="cursor:pointer;">

                    <div class="card-body text-center">

                        <h5 class="card-title">
                            <?= htmlspecialchars($categoria['nome']); ?>
                        </h5>

                        <p>
                            <?= htmlspecialchars($categoria['descricao']); ?>
                        </p>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</main>


<div class="offcanvas offcanvas-top offcanvas-categoria"
    tabindex="-1"
    id="offcanvasCategoria">


    <div class="offcanvas-header">

        <h5 id="tituloCategoria">
            Produtos
        </h5>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas">
        </button>

    </div>

    <div class="offcanvas-body">

        <div id="conteudoCategoria">

            <div class="text-center py-5">
                Carregando...
            </div>

        </div>

    </div>

</div>





<?php

require APP_ROOT . '/views/layouts/footer.php';
