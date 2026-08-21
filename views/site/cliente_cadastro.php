<?php

declare(strict_types=1);

use App\Helpers\View;
use App\Helpers\Csrf;

require APP_ROOT
    . '/views/layouts/site/header.php';

View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
            $rotaAtual,
    ]
);

$erro =
    $erro
    ?? null;

$nome =
    $nome
    ?? '';

$email =
    $email
    ?? '';

?>




<main class="cliente-cadastro">

    <div class="cliente-cadastro-card">


        <div class="cliente-cadastro-cabecalho">

            <div class="cliente-cadastro-icone">

                <i class="bi bi-person-plus"></i>

            </div>


            <h1>
                Crie sua conta
            </h1>


            <p>
                Cadastre-se para fazer seus pedidos
                com mais facilidade.
            </p>

        </div>


        <?php if ($erro): ?>

            <div class="cliente-cadastro-erro">

                <?= htmlspecialchars(
                    $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="<?= BASE_URL ?>/cadastro"
            class="cliente-cadastro-formulario"
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


            <div class="cliente-cadastro-campo">

                <label for="nome">
                    Nome completo
                </label>


                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars(
                        $nome,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Digite seu nome"
                    maxlength="150"
                    autocomplete="name"
                    required
                >

            </div>


            <div class="cliente-cadastro-campo">

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


            <div class="cliente-cadastro-campo">

                <label for="senha">
                    Senha
                </label>


                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Crie uma senha"
                    autocomplete="new-password"
                    required
                >


                <span class="cliente-cadastro-senha-info">

                    Use uma senha segura para proteger sua conta.

                </span>

            </div>


            <div class="cliente-cadastro-campo">

                <label for="senha_confirmacao">
                    Confirmar senha
                </label>


                <input
                    type="password"
                    id="senha_confirmacao"
                    name="senha_confirmacao"
                    placeholder="Digite a senha novamente"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="cliente-cadastro-botao"
            >

                <i class="bi bi-person-plus"></i>

                Criar minha conta

            </button>


            <div class="cliente-cadastro-divisor">

                <span>
                    ou
                </span>

            </div>


            <a
                href="<?= BASE_URL ?>/cadastro/google"
                class="cliente-cadastro-google"
            >

                <i class="bi bi-google"></i>

                Cadastrar com Google

            </a>


        </form>


        <p class="cliente-cadastro-login">

            Já possui uma conta?

            <a
                href="<?= BASE_URL ?>/login"
            >
                Entrar
            </a>

        </p>


    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';

?>