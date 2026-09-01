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

$pagamento =
    $pagamento ?? null;

?>

<main class="checkout-page">

    <div class="checkout-sucesso">

        <div class="checkout-sucesso-icone">
            ✓
        </div>

        <p class="checkout-etiqueta">
            Pedido recebido
        </p>

        <h1>
            Pedido realizado com sucesso!
        </h1>

        <p>
            Seu pedido foi registrado e está aguardando o pagamento.
        </p>


        <div class="checkout-sucesso-codigo">

            <span>
                Número do pedido
            </span>

            <strong>
                <?= htmlspecialchars(
                    (string)
                    $pedido['codigo'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>

        </div>


        <div class="checkout-sucesso-total">

            <span>
                Total
            </span>

            <strong>
                R$
                <?= number_format(
                    (float)
                    $pedido['total'],
                    2,
                    ',',
                    '.'
                ) ?>
            </strong>

        </div>


        <?php if (
            $pagamento !== null
            &&
            ($pagamento['metodo'] ?? '') === 'pix'
            &&
            !empty(
                $pagamento['pix_copia_cola']
            )
        ): ?>

            <div
                class="checkout-pix"
                style="
                    margin-top: 30px;
                    padding: 24px;
                    border-radius: 16px;
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    text-align: left;
                "
            >

                <div
                    style="
                        text-align: center;
                        margin-bottom: 20px;
                    "
                >

                    <h2
                        style="
                            margin-bottom: 8px;
                        "
                    >
                        Pagamento via Pix
                    </h2>

                    <p
                        style="
                            margin: 0;
                            color: #64748b;
                        "
                    >
                        Copie o código abaixo e pague pelo aplicativo do seu banco.
                    </p>

                </div>


                <label
                    for="pix-copia-cola"
                    style="
                        display: block;
                        margin-bottom: 8px;
                        font-weight: 600;
                    "
                >
                    Pix copia e cola
                </label>


                <textarea
                    id="pix-copia-cola"
                    readonly
                    rows="5"
                    style="
                        width: 100%;
                        resize: none;
                        padding: 12px;
                        border: 1px solid #cbd5e1;
                        border-radius: 10px;
                        background: #ffffff;
                        font-size: 13px;
                        line-height: 1.5;
                    "
                ><?= htmlspecialchars(
                    (string)
                    $pagamento['pix_copia_cola'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>


                <button
                    type="button"
                    id="btn-copiar-pix"
                    class="checkout-btn-principal"
                    style="
                        width: 100%;
                        margin-top: 12px;
                        border: none;
                        cursor: pointer;
                    "
                >
                    Copiar código Pix
                </button>


                <p
                    id="pix-copiado"
                    style="
                        display: none;
                        margin: 12px 0 0;
                        text-align: center;
                        color: #16a34a;
                        font-weight: 600;
                    "
                >
                    Código Pix copiado!
                </p>


                <?php if (
                    !empty(
                        $pagamento['expira_em']
                    )
                ): ?>

                    <p
                        style="
                            margin: 16px 0 0;
                            text-align: center;
                            color: #64748b;
                            font-size: 14px;
                        "
                    >
                        Este pagamento expira em
                        <?= date(
                            'd/m/Y H:i',
                            strtotime(
                                (string)
                                $pagamento['expira_em']
                            )
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <div class="checkout-sucesso-acoes">

            <a
                href="<?= BASE_URL ?>/cliente/pedidos/<?= (int) $pedido['id'] ?>"
                class="checkout-btn-principal"
            >
                Ver pedido
            </a>

            <a
                href="<?= BASE_URL ?>/cardapio"
                class="checkout-btn-voltar"
            >
                Voltar ao cardápio
            </a>

        </div>

    </div>

</main>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const botaoCopiar =
            document.getElementById(
                'btn-copiar-pix'
            );

        const campoPix =
            document.getElementById(
                'pix-copia-cola'
            );

        const mensagemCopiado =
            document.getElementById(
                'pix-copiado'
            );


        if (
            !botaoCopiar ||
            !campoPix
        ) {
            return;
        }


        botaoCopiar.addEventListener(
            'click',
            function () {

                const codigo =
                    campoPix.value.trim();


                if (!codigo) {
                    return;
                }


                if (
                    navigator.clipboard
                    &&
                    window.isSecureContext
                ) {

                    navigator.clipboard
                        .writeText(codigo)
                        .then(
                            function () {

                                mostrarConfirmacao();

                            }
                        )
                        .catch(
                            function () {

                                copiarAlternativo();

                            }
                        );

                    return;
                }


                copiarAlternativo();
            }
        );


        function copiarAlternativo() {

            campoPix.focus();

            campoPix.select();

            campoPix.setSelectionRange(
                0,
                campoPix.value.length
            );


            try {

                const sucesso =
                    document.execCommand(
                        'copy'
                    );


                if (sucesso) {
                    mostrarConfirmacao();
                }

            } catch (erro) {

                console.error(
                    'Não foi possível copiar o Pix.',
                    erro
                );

            }

        }


        function mostrarConfirmacao() {

            mensagemCopiado.style.display =
                'block';


            botaoCopiar.textContent =
                'Código copiado';


            setTimeout(
                function () {

                    mensagemCopiado.style.display =
                        'none';

                    botaoCopiar.textContent =
                        'Copiar código Pix';

                },
                2500
            );

        }

    }
);

</script>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';