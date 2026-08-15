<?php

declare(strict_types=1);

use App\Helpers\Csrf;

$erro =
    $erro
    ?? null;

$email =
    $email
    ?? '';

?>

<?php

require APP_ROOT
    . '/views/layouts/site/header.php';

?>

<link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/cliente_login.css">


<main class="cliente-login">

    <div class="cliente-login-card">


        <div class="cliente-login-cabecalho">

            <div class="cliente-login-icone">

                <i class="bi bi-person"></i>

            </div>


            <h1>
                Entrar
            </h1>


            <p>
                Acesse sua conta para acompanhar
                seus pedidos.
            </p>

        </div>


        <?php if ($erro): ?>

            <div class="cliente-login-erro">

                <?= htmlspecialchars(
                    $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="<?= BASE_URL ?>/login"
            class="cliente-login-formulario"
        >


            <input
                type="hidden"
                name="_csrf"
                value="<?= htmlspecialchars(
                    Csrf::gerarCliente(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <div class="cliente-login-campo">

                <label for="email">

                    E-mail

                </label>


                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Digite seu e-mail"
                    maxlength="180"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="cliente-login-campo">

                <label for="senha">

                    Senha

                </label>


                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    autocomplete="current-password"
                    required
                >

            </div>


            <div class="cliente-login-opcoes">

                <a
                    href="#"
                    class="cliente-login-esqueci-senha"
                >

                    Esqueci minha senha

                </a>

            </div>


            <button
                type="submit"
                class="cliente-login-botao"
            >

                <i class="bi bi-box-arrow-in-right"></i>

                Entrar

            </button>


            <div class="cliente-login-divisor">

                <span>
                    ou
                </span>

            </div>


            <a href="<?= BASE_URL ?>/login/google"
                class="cliente-login-google">

                <i class="bi bi-google"></i>

                Entrar com Google

            </a>


        </form>


        <p class="cliente-login-cadastro">

            Ainda não possui uma conta?

            <a
                href="<?= BASE_URL ?>/cadastro"
            >

                Criar conta

            </a>

        </p>


    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';

?>