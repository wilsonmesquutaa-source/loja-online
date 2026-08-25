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


$nome =
    $nome
    ?? '';


$email =
    $email
    ?? '';


$erroEnvio =
    $erroEnvio
    ?? null;

?>


<main class="cliente-verificacao-page">

    <div class="cliente-verificacao-card">


        <div class="cliente-verificacao-icone">

            <i class="bi bi-envelope-check"></i>

        </div>


        <div class="cliente-verificacao-cabecalho">

            <p class="cliente-verificacao-etiqueta">
                Cadastro
            </p>


            <h1>
                Confirme seu e-mail
            </h1>


            <p>

                Olá,
                <strong>
                    <?= htmlspecialchars(
                        $nome,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>!

            </p>


            <p>

                Enviamos um link de confirmação
                para:

            </p>


            <strong class="cliente-verificacao-email">

                <?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </strong>

        </div>


        <?php if (
            $erroEnvio !== null
        ): ?>

            <div
                class="cliente-verificacao-alerta"
            >

                <?= htmlspecialchars(
                    $erroEnvio,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <div class="cliente-verificacao-instrucoes">

            <p>

                Abra seu e-mail e clique no botão
                <strong>
                    "Confirmar meu e-mail"
                </strong>
                para ativar sua conta.

            </p>


            <p>

                Depois da confirmação,
                você será encaminhado
                automaticamente para continuar
                seu pedido.

            </p>


            <p class="cliente-verificacao-expiracao">

                <i class="bi bi-clock"></i>

                O link de confirmação é válido
                por 1 hora.

            </p>

        </div>


        <div class="cliente-verificacao-acoes">

            <a
                href="<?= BASE_URL ?>/cadastro"
                class="cliente-verificacao-btn-voltar"
            >

                <i class="bi bi-arrow-left"></i>

                Voltar ao cadastro

            </a>


            <a
                href="<?= BASE_URL ?>/login"
                class="cliente-verificacao-btn-login"
            >

                <i class="bi bi-box-arrow-in-right"></i>

                Já confirmei meu e-mail

            </a>

        </div>


    </div>

</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';

?>
