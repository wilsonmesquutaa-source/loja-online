document.addEventListener('DOMContentLoaded', function () {

    console.log('SITE.JS CARREGADO');


    const categoria =
        document.querySelector(
            '.categoria-selecao'
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


    /*
    =================================
    CONTADOR DE QUANTIDADE
    GRANDES / EMPADÃO
    =================================
    */

    function atualizarContadorQuantidade() {

        if (!categoria) {
            return;
        }


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

        if (!categoria) {
            return;
        }


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

            status.innerHTML =
                totalSelecionado >=
                    setores.length
                    ? '<small>Cento completo.</small>'
                    : '<small>Monte seu cento.</small>';
        }
    }


    /*
    =================================
    SEM CATEGORIA
    =================================
    */

    if (!categoria) {

        console.log(
            'Nenhuma seleção de categoria encontrada.'
        );

    } else {

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
        GRANDES / EMPADÃO
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
    }


    /*
    =================================
    MODAL DE REMOÇÃO
    =================================
    */

    const botoesRemover =
        document.querySelectorAll(
            '[data-remover-carrinho]'
        );


    const inputCategoria =
        document.getElementById(
            'categoriaRemoverCarrinho'
        );


    const nomeItem =
        document.querySelector(
            '[data-nome-item-remover]'
        );


    botoesRemover.forEach(
        function (botao) {

            botao.addEventListener(
                'click',
                function () {

                    if (
                        inputCategoria
                    ) {

                        inputCategoria.value =
                            botao.dataset.categoriaId
                            || '';
                    }


                    if (
                        nomeItem
                    ) {

                        nomeItem.textContent =
                            botao.dataset.nome
                            || 'este item';
                    }
                }
            );
        }
    );

});