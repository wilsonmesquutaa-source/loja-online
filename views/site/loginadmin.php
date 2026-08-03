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
        Login administrativo — Loja Online
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

    <main
        class="container min-vh-100
               d-flex align-items-center
               justify-content-center py-5"
    >

        <div
            class="col-12 col-sm-10
                   col-md-7 col-lg-5"
        >

            <div class="card border-0 shadow">

                <div class="card-body p-4 p-lg-5">

                    <div class="text-center mb-4">

                        <i
                            class="bi bi-shield-lock
                                   display-4 text-primary"
                            aria-hidden="true"
                        ></i>

                        <h1 class="h3 mt-3">
                            Área administrativa
                        </h1>

                        <p class="text-secondary">
                            Informe seu e-mail e sua senha.
                        </p>

                    </div>

                    <?php if ($erro !== null): ?>

                        <div
                            class="alert alert-danger"
                            role="alert"
                        >
                            <?=
                                htmlspecialchars(
                                    (string) $erro,
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ?>
                        </div>

                    <?php endif; ?>

                    <form
                        action="<?=
                            BASE_URL
                        ?>/login-admin"
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

                        <div class="mb-3">

                            <label
                                class="form-label"
                                for="email"
                            >
                                E-mail
                            </label>

                            <input
                                class="form-control"
                                type="email"
                                id="email"
                                name="email"
                                value="<?=
                                    htmlspecialchars(
                                        (string) $email,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>"
                                autocomplete="username"
                                required
                                autofocus
                            >

                        </div>

                        <div class="mb-4">

                            <label
                                class="form-label"
                                for="senha"
                            >
                                Senha
                            </label>

                            <input
                                class="form-control"
                                type="password"
                                id="senha"
                                name="senha"
                                autocomplete="current-password"
                                required
                            >

                        </div>

                        <div class="d-grid">

                            <button
                                class="btn btn-primary btn-lg"
                                type="submit"
                            >
                                <i
                                    class="bi bi-box-arrow-in-right me-1"
                                    aria-hidden="true"
                                ></i>

                                Entrar
                            </button>

                        </div>

                    </form>

                    <div class="text-center mt-4">

                        <a
                            class="text-decoration-none"
                            href="<?= BASE_URL ?>/"
                        >
                            Voltar para a loja
                        </a>

                    </div>

                </div>
            </div>

        </div>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
