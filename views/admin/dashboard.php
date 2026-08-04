<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/admin-header.php';

?>


<main class="admin-container">

    <section class="cards ms-3">


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

                    <?= (int) $valor ?>

                </strong>



            </article>


        <?php endforeach; ?>


    </section>



</main>



<?php

require APP_ROOT . '/views/layouts/admin-footer.php';