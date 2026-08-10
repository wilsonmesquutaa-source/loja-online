document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        =================================
        VOLTAR AO TOPO
        =================================
        */

        const botaoTopo = document.querySelector(
            '[data-voltar-topo]'
        );

        if (botaoTopo) {

            botaoTopo.addEventListener(
                'click',
                function () {

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                }
            );

        }


        /*
        =================================
        SCROLL SUAVE LINKS INTERNOS
        =================================
        */

        const linksInternos =
            document.querySelectorAll(
                'a[href^="#"]'
            );

        linksInternos.forEach(
            function (link) {

                link.addEventListener(
                    'click',
                    function (evento) {

                        const href =
                            link.getAttribute('href');

                        if (
                            !href ||
                            href === '#'
                        ) {
                            return;
                        }

                        const destino =
                            document.querySelector(href);

                        if (!destino) {
                            return;
                        }

                        evento.preventDefault();

                        destino.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                    }
                );

            }
        );


        /*
        =================================
        CATEGORIAS
        =================================
        */

        const categorias =
            document.querySelectorAll(
                '.categoria-card'
            );


        categorias.forEach(
            function (card) {

                card.addEventListener(
                    'click',
                    function () {

                        const id =
                            card.dataset.id;

                        const nome =
                            card.dataset.nome;

                        const titulo =
                            document.getElementById(
                                'tituloCategoria'
                            );

                        const conteudo =
                            document.getElementById(
                                'conteudoCategoria'
                            );

                        const elementoOffcanvas =
                            document.getElementById(
                                'offcanvasCategoria'
                            );


                        if (!elementoOffcanvas) {

                            console.error(
                                'Offcanvas #offcanvasCategoria não encontrado.'
                            );

                            return;

                        }


                        if (titulo) {

                            titulo.innerText = nome;

                        }


                        if (conteudo) {

                            conteudo.innerHTML = `
                                <div class="text-center py-5">
                                    Carregando produtos...
                                </div>
                            `;

                        }


                        const painel =
                            bootstrap.Offcanvas.getOrCreateInstance(
                                elementoOffcanvas
                            );


                        painel.show();


                        fetch(
                            `${BASE_URL}/produtos/categoria/${id}?_=${Date.now()}`,
                            {
                                cache: 'no-store'
                            }
                        )

                            .then(
                                function (response) {

                                    if (!response.ok) {

                                        throw new Error(
                                            'Erro na requisição'
                                        );

                                    }

                                    return response.text();

                                }
                            )

                            .then(
                                function (html) {

                                    if (conteudo) {

                                        conteudo.innerHTML =
                                            html;

                                        inicializarSelecaoProdutos(
                                            conteudo
                                        );

                                    }

                                }
                            )

                            .catch(
                                function (erro) {

                                    if (conteudo) {

                                        conteudo.innerHTML = `
                                        <div class="alert alert-danger">
                                            Erro ao carregar produtos.
                                        </div>
                                    `;

                                    }

                                    console.error(erro);

                                }
                            );

                    }
                );

            }
        );


        /*
        =================================
        SELEÇÃO DE PRODUTOS
        =================================
        */

        function inicializarSelecaoProdutos(
            container
        ) {

            const contador =
                container.querySelector(
                    '[data-contador-opcoes]'
                );


            const primeiroBotao =
                container.querySelector(
                    '[data-aumentar]'
                );


            if (!contador || !primeiroBotao) {
                return;
            }


            const elementoLimite =
                container.querySelector(
                    '[data-limite-opcoes]'
                );

            if (!elementoLimite) {
                return;
            }

            const limite =
                parseInt(
                    elementoLimite.dataset.limiteOpcoes,
                    10
                );


            const wrappers =
                container.querySelectorAll(
                    '[data-produto-wrapper]'
                );


            wrappers.forEach(
                function (wrapper) {

                    const botaoAumentar =
                        wrapper.querySelector(
                            '[data-aumentar]'
                        );

                    const botaoDiminuir =
                        wrapper.querySelector(
                            '[data-diminuir]'
                        );

                    const quantidade =
                        wrapper.querySelector(
                            '[data-quantidade]'
                        );


                    let valor = 0;


                    botaoAumentar.addEventListener(
                        'click',
                        function () {

                            if (
                                totalSelecionado >=
                                limite
                            ) {
                                return;
                            }


                            valor++;

                            totalSelecionado++;


                            quantidade.innerText =
                                valor;


                            botaoDiminuir.disabled =
                                false;


                            if (
                                totalSelecionado >=
                                limite
                            ) {

                                container
                                    .querySelectorAll(
                                        '[data-aumentar]'
                                    )
                                    .forEach(
                                        function (botao) {

                                            botao.disabled =
                                                true;

                                        }
                                    );

                            }

                            contador.innerText =
                                totalSelecionado;

                        }
                    );


                    botaoDiminuir.addEventListener(
                        'click',
                        function () {

                            if (valor <= 0) {
                                return;
                            }


                            valor--;

                            totalSelecionado--;


                            quantidade.innerText =
                                valor;


                            if (valor === 0) {

                                botaoDiminuir.disabled =
                                    true;

                            }


                            container
                                .querySelectorAll(
                                    '[data-aumentar]'
                                )
                                .forEach(
                                    function (botao) {

                                        botao.disabled =
                                            false;

                                    }
                                );


                            contador.innerText =
                                totalSelecionado;

                        }
                    );

                }
            );

        }

    }
);