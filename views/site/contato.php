<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/header.php';

?>

<main class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <h1>Contato</h1>

            <?php if ($sucesso !== null): ?>
                <div class="alert alert-success">
                    <?=
                        htmlspecialchars(
                            $sucesso,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($erros !== []): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li>
                                <?=
                                    htmlspecialchars(
                                        $erro,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form
                action="<?= BASE_URL ?>/contato"
                method="post"
                class="mt-4"
            >

                <div class="mb-3">
                    <label class="form-label" for="nome">
                        Nome
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?=
                            htmlspecialchars(
                                $nome,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">
                        E-mail
                    </label>

                    <input
                        class="form-control"
                        type="email"
                        id="email"
                        name="email"
                        value="<?=
                            htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="mensagem">
                        Mensagem
                    </label>

                    <textarea
                        class="form-control"
                        id="mensagem"
                        name="mensagem"
                        rows="5"
                        required
                    ><?=
                        htmlspecialchars(
                            $mensagem,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?></textarea>
                </div>

                <button class="btn btn-primary" type="submit">
                    Enviar
                </button>

            </form>

        </div>
    </div>

</main>

<?php

require APP_ROOT . '/views/layouts/footer.php';
