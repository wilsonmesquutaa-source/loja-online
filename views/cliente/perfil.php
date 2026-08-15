<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/site/header.php';

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
    <form
    method="POST"
    action="<?= BASE_URL ?>/logout"
    class="mt-4"
>
    <input
        type="hidden"
        name="_csrf"
        value="<?= htmlspecialchars(
            \App\Helpers\Csrf::gerarCliente(),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <button
        type="submit"
        class="btn btn-danger"
    >
        <i class="bi bi-box-arrow-right"></i>
        Sair da conta
    </button>
</form>

</main>

<?php

require APP_ROOT . '/views/layouts/site/footer.php';
