<?php

declare(strict_types=1);

$rotaAtual = $rotaAtual ?? '';

?>

<nav
    class="navbar navbar-expand-lg bg-white shadow-sm sticky-top"
    aria-label="Navegação principal"
>

    <div class="container">


        <!-- LOGO -->

        <a
            class="navbar-brand d-flex align-items-center gap-2"
            href="<?= BASE_URL ?>/"
        >

            <img
                src="<?= BASE_URL ?>/assets/images/logo.png"
                alt="Cantim do Lanche"
                height="55"
            >


           

        </a>



        <!-- BOTÃO MOBILE -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Abrir menu"
        >

            <span
                class="navbar-toggler-icon"
            ></span>

        </button>




        <div
            class="collapse navbar-collapse"
            id="menuPrincipal"
        >


            <!-- MENU CENTRAL -->

            <ul
                class="navbar-nav mx-auto align-items-lg-center"
            >


                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold
                        <?= $rotaAtual === 'home'
                            ? 'active'
                            : ''
                        ?>"
                        href="<?= BASE_URL ?>/"
                    >
                        Início
                    </a>

                </li>



                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold"
                        href="<?= BASE_URL ?>/produtos"
                    >
                        Cardápio
                    </a>

                </li>



                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold"
                        href="<?= BASE_URL ?>/quem-somos"
                    >
                        Sobre
                    </a>

                </li>



                <li class="nav-item">

                    <a
                        class="nav-link fw-semibold"
                        href="<?= BASE_URL ?>/contato"
                    >
                        Contato
                    </a>

                </li>


            </ul>






            <!-- ÁREA DIREITA -->

            <div
                class="
                d-flex
                flex-column
                flex-lg-row
                align-items-lg-center
                gap-2
                "
            >




                <!-- BUSCA -->

                <form
                    class="d-flex"
                    action="<?= BASE_URL ?>/produtos"
                    method="GET"
                >

                    <div
                        class="input-group"
                    >

                        <input
                            type="search"
                            name="busca"
                            class="form-control"
                            placeholder="Buscar salgados..."
                        >


                        <button
                            class="btn btn-warning"
                            type="submit"
                        >

                            <i
                                class="bi bi-search"
                            ></i>

                        </button>


                    </div>


                </form>





                <!-- CARRINHO -->

                <a
                    href="<?= BASE_URL ?>/carrinho"
                    class="btn btn-outline-warning position-relative"
                    title="Carrinho"
                >

                    <i
                        class="bi bi-cart3"
                    ></i>


                    <span
                        class="
                        position-absolute
                        top-0
                        start-100
                        translate-middle
                        badge
                        rounded-pill
                        bg-danger
                        "
                    >

                        0

                    </span>


                </a>






                <!-- LOGIN CLIENTE -->

                <a
                    href="<?= BASE_URL ?>/login"
                    class="btn btn-outline-secondary"
                >

                    <i
                        class="bi bi-person"
                    ></i>

                    Entrar

                </a>






                <!-- CADASTRO -->

                <a
                    href="<?= BASE_URL ?>/cadastro"
                    class="btn btn-warning text-white"
                >

                    Cadastrar

                </a>






                <!-- ADMIN -->

                <a
                    href="<?= BASE_URL ?>/login-admin"
                    class="btn btn-dark"
                >

                    <i
                        class="bi bi-shield-lock"
                    ></i>

                    Admin

                </a>




            </div>


        </div>


    </div>


</nav>