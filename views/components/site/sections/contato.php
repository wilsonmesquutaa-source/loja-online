<?php

declare(strict_types=1);

$emailContato = $emailContato
    ?? 'contato@lojaonline.com';

$telefoneContato = $telefoneContato
    ?? '(85) 99999-9999';

?>

<section
    class="contato-section py-5"
    id="contato"
>

    <div class="container py-lg-4">

        <div class="row align-items-center g-4">

            <div class="col-lg-7">

                <p class="section-etiqueta text-white">
                    Atendimento
                </p>

                <h2 class="fw-bold">
                    Precisa de ajuda?
                </h2>

                <p class="lead mb-0">
                    Nossa equipe está disponível para responder
                    dúvidas sobre produtos, pedidos e pagamentos.
                </p>

            </div>

            <div class="col-lg-5">

                <div class="contato-card">

                    <p>
                        <i
                            class="bi bi-envelope me-2"
                            aria-hidden="true"
                        ></i>

                        <?=
                            htmlspecialchars(
                                $emailContato,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>
                    </p>

                    <p class="mb-0">
                        <i
                            class="bi bi-whatsapp me-2"
                            aria-hidden="true"
                        ></i>

                        <?=
                            htmlspecialchars(
                                $telefoneContato,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ?>
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>
