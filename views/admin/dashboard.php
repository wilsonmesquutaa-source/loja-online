<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/header.php';
require APP_ROOT . '/views/layouts/admin-header.php';
?>

<main class="admin-container">

    <div class="admin-top">

        <h1 class="dashboard-title">
            Dashboard administrativo
        </h1>

        <span class="admin-user">
            Administrador
        </span>

    </div>


    <section class="cards">

        <?php foreach ($indicadores as $nome => $valor): ?>

            <article class="card-admin">

                <h2>
                    <?= htmlspecialchars(
                        ucfirst($nome),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>


                <strong>
                    <?= (int)$valor ?>
                </strong>


            </article>

        <?php endforeach; ?>


    </section>


</main>


<?php

require APP_ROOT . '/views/layouts/footer.php';