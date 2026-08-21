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

?>

<main class="cliente-seguranca-page">

    <div class="cliente-seguranca-container">

        <header class="cliente-seguranca-header">

            <p class="cliente-seguranca-etiqueta">
                Minha conta
            </p>

            <h1>
                Segurança
            </h1>

            <p>
                Gerencie sua senha, seu e-mail e o acesso à sua conta.
            </p>

        </header>


        <?php if ($erro !== null): ?>

            <div
                class="
                    cliente-seguranca-alert
                    cliente-seguranca-alert-danger
                "
            >
                <?= htmlspecialchars(
                    (string) $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <?php if ($sucesso !== null): ?>

            <div
                class="
                    cliente-seguranca-alert
                    cliente-seguranca-alert-success
                "
            >
                <?= htmlspecialchars(
                    (string) $sucesso,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <!-- =================================================
             SENHA
        ================================================== -->

        <section class="cliente-seguranca-card">

            <div class="cliente-seguranca-card-header">

                <div class="cliente-seguranca-icone">
                    🔐
                </div>

                <div>

                    <h2>
                        Alterar senha
                    </h2>

                    <p>
                        Atualize sua senha de acesso à conta.
                    </p>

                </div>

            </div>


            <?php if (
                !empty(
                    $cliente['senha_hash']
                )
            ): ?>

                <form
                    method="POST"
                    action="<?= BASE_URL ?>/cliente/seguranca/senha"
                    class="cliente-seguranca-form"
                >

                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?= htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >


                    <div class="cliente-seguranca-form-group">

                        <label for="senha_atual">
                            Senha atual
                        </label>

                        <input
                            type="password"
                            id="senha_atual"
                            name="senha_atual"
                            autocomplete="current-password"
                            required
                        >

                    </div>


                    <div class="cliente-seguranca-form-group">

                        <label for="nova_senha">
                            Nova senha
                        </label>

                        <input
                            type="password"
                            id="nova_senha"
                            name="nova_senha"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >

                        <small>
                            A senha deve ter pelo menos 8 caracteres.
                        </small>

                    </div>


                    <div class="cliente-seguranca-form-group">

                        <label for="confirmacao_senha">
                            Confirmar nova senha
                        </label>

                        <input
                            type="password"
                            id="confirmacao_senha"
                            name="confirmacao_senha"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="cliente-seguranca-btn-principal"
                    >
                        Alterar senha
                    </button>

                </form>

            <?php else: ?>

                <div class="cliente-seguranca-google-info">

                    <strong>
                        Conta vinculada ao Google
                    </strong>

                    <p>
                        Esta conta não possui uma senha local. O acesso é realizado pelo Google.
                    </p>

                </div>

            <?php endif; ?>

        </section>


        <!-- =================================================
             E-MAIL
        ================================================== -->

        <section class="cliente-seguranca-card">

            <div class="cliente-seguranca-card-header">

                <div class="cliente-seguranca-icone">
                    ✉️
                </div>

                <div>

                    <h2>
                        Alterar e-mail
                    </h2>

                    <p>
                        Seu e-mail atual é
                        <strong>
                            <?= htmlspecialchars(
                                (string)
                                $cliente['email'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="<?= BASE_URL ?>/cliente/seguranca/email"
                class="cliente-seguranca-form"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= htmlspecialchars(
                        $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >


                <div class="cliente-seguranca-form-group">

                    <label for="novo_email">
                        Novo e-mail
                    </label>

                    <input
                        type="email"
                        id="novo_email"
                        name="novo_email"
                        maxlength="180"
                        autocomplete="email"
                        required
                    >

                </div>


                <?php if (
                    !empty(
                        $cliente['senha_hash']
                    )
                ): ?>

                    <div class="cliente-seguranca-form-group">

                        <label for="senha_email">
                            Senha atual
                        </label>

                        <input
                            type="password"
                            id="senha_email"
                            name="senha_email"
                            autocomplete="current-password"
                            required
                        >

                    </div>

                <?php endif; ?>


                <button
                    type="submit"
                    class="cliente-seguranca-btn-principal"
                >
                    Alterar e-mail
                </button>

            </form>

        </section>


        <!-- =================================================
             EXCLUSÃO
        ================================================== -->

        <section
            class="
                cliente-seguranca-card
                cliente-seguranca-card-perigo
            "
        >

            <div class="cliente-seguranca-card-header">

                <div class="cliente-seguranca-icone">
                    ⚠️
                </div>

                <div>

                    <h2>
                        Excluir conta
                    </h2>

                    <p>
                        Essa ação encerra o acesso à sua conta.
                    </p>

                </div>

            </div>


            <div class="cliente-seguranca-perigo-texto">

                <p>
                    Antes de excluir sua conta, certifique-se de que deseja realmente continuar.
                </p>

                <p>
                    Se você possui pedidos, o histórico será preservado de forma anonimizada para manter os registros da loja.
                </p>

            </div>


            <form
                method="POST"
                action="<?= BASE_URL ?>/cliente/seguranca/excluir"
                class="cliente-seguranca-form"
                onsubmit="
                    return confirm(
                        'Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.'
                    );
                "
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= htmlspecialchars(
                        $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >


                <div class="cliente-seguranca-form-group">

                    <label for="confirmacao_exclusao">
                        Digite seu e-mail para confirmar
                    </label>

                    <input
                        type="email"
                        id="confirmacao_exclusao"
                        name="confirmacao_exclusao"
                        autocomplete="off"
                        required
                    >

                    <small>
                        <?= htmlspecialchars(
                            (string)
                            $cliente['email'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>

                </div>


                <button
                    type="submit"
                    class="cliente-seguranca-btn-excluir"
                >
                    Excluir minha conta
                </button>

            </form>

        </section>

    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';