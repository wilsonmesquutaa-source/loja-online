<?php

declare(strict_types=1);

$emailContato = $emailContato
    ?? 'contato@cantimdolanche.com';

$telefoneContato = $telefoneContato
    ?? '(85) 99236-7866';

$whatsappContato = $whatsappContato
    ?? '5585992367866';

$instagramContato = $instagramContato
    ?? '@cantimdolanche';

$facebookContato = $facebookContato
    ?? 'Cantim do Lanche';

?>

<section
    class="contato-section py-5"
    id="contato"
>

    <div class="container py-lg-5">

        <div class="row align-items-center g-5">


            <div class="col-lg-7">

                <p class="section-etiqueta text-white">
                    Atendimento
                </p>


                <h2 class="display-6 fw-bold text-white">
                    Faça seu pedido
                </h2>


                <p class="lead text-white">

                    Tem dúvidas sobre sabores,
                    quantidades ou entrega?

                    Nossa equipe está pronta para
                    ajudar você a escolher os melhores
                    salgados para seu momento especial.

                </p>


                <a
                    href="https://wa.me/<?= $whatsappContato ?>"
                    target="_blank"
                    class="btn btn-warning btn-lg text-white"
                >

                    <i class="bi bi-whatsapp me-2"></i>

                    Pedir pelo WhatsApp

                </a>


            </div>



            <div class="col-lg-5">


                <div class="contato-card shadow-lg">


                    <h3 class="h5 fw-bold mb-4">
                        Entre em contato
                    </h3>



                    <p>

                        <i class="bi bi-envelope-fill me-2"></i>

                        <?= htmlspecialchars(
                            $emailContato,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>




                    <p>

                        <i class="bi bi-telephone-fill me-2"></i>

                        <?= htmlspecialchars(
                            $telefoneContato,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>




                    <p>

                        <i class="bi bi-instagram me-2"></i>

                        <?= htmlspecialchars(
                            $instagramContato,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>




                    <p class="mb-0">

                        <i class="bi bi-facebook me-2"></i>

                        <?= htmlspecialchars(
                            $facebookContato,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>



                </div>


            </div>


        </div>


    </div>


</section>