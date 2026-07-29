<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/header.php';

?>

<main class="container py-5">

    <h1>Meu perfil</h1>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">

            <p>
                <strong>Nome:</strong>
                <?=
                    htmlspecialchars(
                        $cliente['nome'],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ?>
            </p>

            <p class="mb-0">
                <strong>E-mail:</strong>
                <?=
                    htmlspecialchars(
                        $cliente['email'],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ?>
            </p>

        </div>
    </div>

</main>

<?php

require APP_ROOT . '/views/layouts/footer.php';
