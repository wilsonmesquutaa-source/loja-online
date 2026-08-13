document.addEventListener(
    'DOMContentLoaded',
    function () {

        const categoria =
            document.querySelector(
                '.categoria-selecao'
            );


        if (!categoria) {
            return;
        }


        const formulario =
            categoria.closest(
                '[data-form-carrinho]'
            );


        const modalElement =
            document.getElementById(
                'modalCentoIncompleto'
            );


        const mensagemModal =
            document.querySelector(
                '[data-mensagem-cento-incompleto]'
            );


        function atualizarInputQuantidade(
            produto,
            valor
        ) {
            const input =
                produto.querySelector(
                    '[data-input-quantidade]'
                );

            if (input) {
                input.value = valor;
            }
        }


        function abrirModalCentoIncompleto() {

            if (
                !modalElement
                ||
                typeof bootstrap ===
                    'undefined'
            ) {
                return;
            }


            const tipoCategoria =
                categoria.dataset.tipoCategoria
                || 'unica';


            if (mensagemModal) {

                if (
                    tipoCategoria ===
                    'cento_tradicionais'
                ) {

                    mensagemModal.innerHTML =
                        'O Cantim do Lanche não trabalha ' +
                        'com a venda de partes de cento.' +
                        '<br><br>' +
                        'O cento tradicional deve ter ' +
                        '<strong>4 partes de 25 salgados</strong>, ' +
                        'totalizando 100 unidades.';

                } else if (
                    tipoCategoria ===
                    'cento_folhados'
                ) {

                    mensagemModal.innerHTML =
                        'O Cantim do Lanche não trabalha ' +
                        'com a venda de partes de cento.' +
                        '<br><br>' +
                        'O cento folhado deve ter ' +
                        '<strong>2 partes de 50 salgados</strong>, ' +
                        'totalizando 100 unidades.';
                }
            }


            const modal =
                bootstrap.Modal
                    .getOrCreateInstance(
                        modalElement
                    );

            modal.show();
        }


        /*
        =================================
        CONTADOR GRANDES / EMPADÃO
        =================================
        */

        function atualizarContadorQuantidade() {

            const tipoCategoria =
                categoria.dataset.tipoCategoria
                || 'unica';


            if (
                tipoCategoria !==
                    'salgados_grandes'
                &&
                tipoCategoria !==
                    'empadao'
            ) {
                return;
            }


            const contador =
                categoria.querySelector(
                    '[data-contador-quantidade]'
                );


            if (!contador) {
                return;
            }


            let total = 0;


            const produtos =
                categoria.querySelectorAll(
                    '[data-produto-wrapper]'
                );


            produtos.forEach(
                function (produto) {

                    const quantidade =
                        produto.querySelector(
                            '[data-quantidade]'
                        );


                    if (!quantidade) {
                        return;
                    }


                    total +=
                        Number(
                            quantidade.textContent
                        ) || 0;
                }
            );


            contador.textContent =
                total;


            const unidade =
                contador.nextElementSibling;


            if (unidade) {

                unidade.textContent =
                    total === 1
                        ? 'unidade'
                        : 'unidades';
            }
        }


        /*
        =================================
        PREVIEW DO CENTO
        =================================
        */

        function atualizarPreviewCento() {

            const tipo =
                categoria.dataset.tipoCategoria
                || 'unica';


            if (
                tipo !==
                    'cento_tradicionais'
                &&
                tipo !==
                    'cento_folhados'
            ) {
                return;
            }


            const setores =
                categoria.querySelectorAll(
                    '[data-cento-setor]'
                );


            if (!setores.length) {
                return;
            }


            let totalSelecionado = 0;


            const produtos =
                categoria.querySelectorAll(
                    '[data-produto-wrapper]'
                );


            produtos.forEach(
                function (produto) {

                    const quantidade =
                        produto.querySelector(
                            '[data-quantidade]'
                        );


                    if (!quantidade) {
                        return;
                    }


                    totalSelecionado +=
                        Number(
                            quantidade.textContent
                        ) || 0;
                }
            );


            setores.forEach(
                function (setor, indice) {

                    setor.classList.toggle(
                        'ativo',
                        indice + 1 <=
                            totalSelecionado
                    );
                }
            );


            const status =
                categoria.querySelector(
                    '[data-cento-status]'
                );


            if (status) {

                const completo =
                    tipo ===
                    'cento_tradicionais'
                        ? totalSelecionado === 4
                        : totalSelecionado === 2;


                status.innerHTML =
                    completo
                        ? '<small>Cento completo.</small>'
                        : '<small>Monte seu cento.</small>';
            }
        }


        /*
        =================================
        TIPO DA CATEGORIA
        =================================
        */

        const tipoCategoria =
            categoria.dataset.tipoCategoria
            || 'unica';


        const quantidadeLivre =
            tipoCategoria ===
                'salgados_grandes'
            ||
            tipoCategoria ===
                'empadao';


        /*
        =================================
        SALGADOS GRANDES / EMPADÃO
        =================================
        */

        if (quantidadeLivre) {

            const produtos =
                categoria.querySelectorAll(
                    '[data-produto-wrapper]'
                );


            produtos.forEach(
                function (produto) {

                    const quantidade =
                        produto.querySelector(
                            '[data-quantidade]'
                        );

                    const mais =
                        produto.querySelector(
                            '[data-aumentar]'
                        );

                    const menos =
                        produto.querySelector(
                            '[data-diminuir]'
                        );


                    if (
                        !quantidade
                        ||
                        !mais
                        ||
                        !menos
                    ) {
                        return;
                    }


                    const valorInicial =
                        Number(
                            quantidade.textContent
                        ) || 0;


                    menos.disabled =
                        valorInicial <= 0;


                    atualizarInputQuantidade(
                        produto,
                        valorInicial
                    );


                    mais.addEventListener(
                        'click',
                        function (evento) {

                            evento.preventDefault();


                            let valor =
                                Number(
                                    quantidade.textContent
                                ) || 0;


                            valor++;


                            quantidade.textContent =
                                valor;


                            atualizarInputQuantidade(
                                produto,
                                valor
                            );


                            menos.disabled =
                                valor <= 0;


                            atualizarContadorQuantidade();
                        }
                    );


                    menos.addEventListener(
                        'click',
                        function (evento) {

                            evento.preventDefault();


                            let valor =
                                Number(
                                    quantidade.textContent
                                ) || 0;


                            if (
                                valor <= 0
                            ) {
                                return;
                            }


                            valor--;


                            quantidade.textContent =
                                valor;


                            atualizarInputQuantidade(
                                produto,
                                valor
                            );


                            menos.disabled =
                                valor <= 0;


                            atualizarContadorQuantidade();
                        }
                    );
                }
            );


            atualizarContadorQuantidade();


        } else {

            /*
            =================================
            TRADICIONAIS / FOLHADOS
            =================================
            */

            const contador =
                categoria.querySelector(
                    '[data-contador-opcoes]'
                );


            const elementoLimite =
                categoria.querySelector(
                    '[data-limite-opcoes]'
                );


            if (
                contador
                &&
                elementoLimite
            ) {

                const limite =
                    Number(
                        elementoLimite
                            .dataset
                            .limiteOpcoes
                    );


                let totalSelecionado =
                    0;


                const produtos =
                    categoria.querySelectorAll(
                        '[data-produto-wrapper]'
                    );


                produtos.forEach(
                    function (produto) {

                        const quantidade =
                            produto.querySelector(
                                '[data-quantidade]'
                            );


                        if (!quantidade) {
                            return;
                        }


                        const valor =
                            Number(
                                quantidade.textContent
                            ) || 0;


                        totalSelecionado +=
                            valor;


                        atualizarInputQuantidade(
                            produto,
                            valor
                        );
                    }
                );


                function atualizarInterface() {

                    contador.textContent =
                        totalSelecionado;


                    produtos.forEach(
                        function (produto) {

                            const quantidade =
                                produto.querySelector(
                                    '[data-quantidade]'
                                );

                            const mais =
                                produto.querySelector(
                                    '[data-aumentar]'
                                );

                            const menos =
                                produto.querySelector(
                                    '[data-diminuir]'
                                );


                            if (
                                !quantidade
                                ||
                                !mais
                                ||
                                !menos
                            ) {
                                return;
                            }


                            const valor =
                                Number(
                                    quantidade.textContent
                                ) || 0;


                            mais.disabled =
                                totalSelecionado >=
                                limite;


                            menos.disabled =
                                valor <= 0;
                        }
                    );


                    atualizarPreviewCento();
                }


                categoria.addEventListener(
                    'click',
                    function (evento) {

                        const mais =
                            evento.target.closest(
                                '[data-aumentar]'
                            );


                        if (mais) {

                            evento.preventDefault();


                            if (
                                totalSelecionado >=
                                limite
                            ) {
                                return;
                            }


                            const produto =
                                mais.closest(
                                    '[data-produto-wrapper]'
                                );


                            if (!produto) {
                                return;
                            }


                            const quantidade =
                                produto.querySelector(
                                    '[data-quantidade]'
                                );


                            if (!quantidade) {
                                return;
                            }


                            let valor =
                                Number(
                                    quantidade.textContent
                                ) || 0;


                            valor++;


                            quantidade.textContent =
                                valor;


                            atualizarInputQuantidade(
                                produto,
                                valor
                            );


                            totalSelecionado++;


                            atualizarInterface();


                            return;
                        }


                        const menos =
                            evento.target.closest(
                                '[data-diminuir]'
                            );


                        if (menos) {

                            evento.preventDefault();


                            const produto =
                                menos.closest(
                                    '[data-produto-wrapper]'
                                );


                            if (!produto) {
                                return;
                            }


                            const quantidade =
                                produto.querySelector(
                                    '[data-quantidade]'
                                );


                            if (!quantidade) {
                                return;
                            }


                            let valor =
                                Number(
                                    quantidade.textContent
                                ) || 0;


                            if (
                                valor <= 0
                            ) {
                                return;
                            }


                            valor--;


                            quantidade.textContent =
                                valor;


                            atualizarInputQuantidade(
                                produto,
                                valor
                            );


                            totalSelecionado =
                                Math.max(
                                    0,
                                    totalSelecionado - 1
                                );


                            atualizarInterface();
                        }
                    }
                );


                atualizarInterface();
            }
        }


        /*
        =================================
        VALIDA FORMULÁRIO
        =================================
        */

        if (formulario) {

            formulario.addEventListener(
                'submit',
                function (evento) {

                    if (
                        tipoCategoria !==
                            'cento_tradicionais'
                        &&
                        tipoCategoria !==
                            'cento_folhados'
                    ) {
                        return;
                    }


                    let totalSelecionado = 0;


                    const produtos =
                        categoria.querySelectorAll(
                            '[data-produto-wrapper]'
                        );


                    produtos.forEach(
                        function (produto) {

                            const quantidade =
                                produto.querySelector(
                                    '[data-quantidade]'
                                );


                            if (!quantidade) {
                                return;
                            }


                            totalSelecionado +=
                                Number(
                                    quantidade.textContent
                                ) || 0;
                        }
                    );


                    const quantidadeObrigatoria =
                        tipoCategoria ===
                            'cento_tradicionais'
                            ? 4
                            : 2;


                    if (
                        totalSelecionado !==
                        quantidadeObrigatoria
                    ) {

                        evento.preventDefault();

                        abrirModalCentoIncompleto();
                    }
                }
            );
        }

    }
);