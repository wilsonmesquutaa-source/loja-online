<?php

declare(strict_types=1);

$tituloHero = $tituloHero
    ?? 'Salgados artesanais feitos com muito amor';

$textoHero = $textoHero
    ?? 'Salgados fresquinhos para festas, eventos ou aquele lanche especial.';

?>

<section class="hero-loja py-5">

    <div class="container py-lg-5">

        <div class="row align-items-center g-5">


            <!-- TEXTO PRINCIPAL -->

            <div class="col-lg-7">


                <p class="hero-etiqueta mb-3">

                    Cantim do Lanche

                </p>




                <h1 class="display-4 fw-bold">


                    <?= 
                        htmlspecialchars(
                            $tituloHero,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>


                </h1>




                <p class="lead mt-3 mb-4">


                    <?= 
                        htmlspecialchars(
                            $textoHero,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>


                </p>





                <div class="d-flex flex-wrap gap-3">


                    <a
                        class="btn btn-warning btn-lg text-white"
                        href="<?= BASE_URL ?>/cardapio"
                    >

                        <i class="bi bi-basket me-2"></i>

                        Ver cardápio

                    </a>





                    <a
                        class="btn btn-outline-dark btn-lg"
                        href="#contato"
                    >

                        <i class="bi bi-whatsapp me-2"></i>

                        Fazer pedido

                    </a>


                </div>


            </div>







            <!-- CARD LATERAL -->

            <div class="col-lg-5">


                <div class="hero-card shadow-lg">



                    <i
                        class="bi bi-basket-fill"
                        aria-hidden="true"
                    ></i>





                    <h2 class="h4 mt-3">

                        Salgados feitos à mão

                    </h2>





                    <p class="mb-0">

                        Coxinhas, risoles, folhados e empadão
                        de frango preparados com carinho,
                        ingredientes selecionados e aquele
                        sabor artesanal para festas, eventos
                        ou aquele lanche especial.

                    </p>



                </div>


            </div>



        </div>


    </div>


</section>