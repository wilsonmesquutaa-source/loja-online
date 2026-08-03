<?php

declare(strict_types=1);

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Dashboard administrativo
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >
</head>

<body class="bg-body-tertiary">

    <nav class="navbar bg-dark navbar-dark">
        <div class="container">

            <a
                class="navbar-brand"
                href="<?= BASE_URL ?>/admin"
            >
                Painel administrativo
            </a>

            <form
                action="<?=
                    BASE_URL
                ?>/logout-admin"
                method="post"
            >

                <input
                    type="hidden"
                    name="_token"
                    value="<?=
                        htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>"
                >

                <button
                    class="btn btn-outline-light"
                    type="submit"
                >
                    <i
                        class="bi bi-box-arrow-right me-1"
                        aria-hidden="true"
                    ></i>

                    Sair
                </button>

            </form>

        </div>
    </nav>

    <header class="bg-primary text-white py-5">
        <div class="container">

            <p class="mb-1">
                Administração
            </p>

            <h1 class="display-6 fw-bold">
                Dashboard
            </h1>

            <p class="lead mb-0">
                Bem-vindo,

                <strong>
                    <?=
                        htmlspecialchars(
                            (string)
                                $usuarioAdmin[
                                    'nome'
                                ],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>
                </strong>.
            </p>

        </div>
    </header>

    <main class="container py-5">

        <div class="row g-4">

            <?php
                foreach (
                    $indicadores
                    as $nome => $valor
                ):
            ?>

                <div class="col-sm-6 col-lg-3">

                    <article
                        class="card border-0
                               shadow-sm h-100"
                    >

                        <div class="card-body">

                            <h2
                                class="h6 text-uppercase
                                       text-secondary"
                            >
                                <?=
                                    htmlspecialchars(
                                        ucfirst($nome),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>
                            </h2>

                            <p
                                class="display-5
                                       fw-semibold mb-0"
                            >
                                <?= (int) $valor ?>
                            </p>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

        <section
            class="card border-0
                   shadow-sm mt-5"
        >

            <div class="card-body">

                <h2 class="h5">
                    Usuário conectado
                </h2>

                <p class="mb-1">
                    <strong>Nome:</strong>

                    <?=
                        htmlspecialchars(
                            (string)
                                $usuarioAdmin[
                                    'nome'
                                ],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>
                </p>

                <p class="mb-0">
                    <strong>E-mail:</strong>

                    <?=
                        htmlspecialchars(
                            (string)
                                $usuarioAdmin[
                                    'email'
                                ],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>
                </p>

            </div>

        </section>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
