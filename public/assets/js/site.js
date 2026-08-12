document.addEventListener('DOMContentLoaded', function () {

    console.log('SITE.JS CARREGADO');


    /*
    =================================
    SELEÇÃO DE CATEGORIA
    =================================
    */

    const categoria =
        document.querySelector(
            '.categoria-selecao'
        );


    if (categoria) {

        const tipoCategoria =
            categoria.dataset.tipoCategoria
            || 'unica';


        function atualizarInputQuantidade(
            produto,
            valor
        ) {

            const inputQuantidade =
                produto.querySelector(
                    '[data-input-quantidade]'
                );

            if (inputQuantidade) {
                inputQuantidade.value = valor;
            }
        }


        /*
        =================================
        PREVIEW DO CENTO
        =================================
        */

        function atualizarPreviewCento() {

            if (
                tipoCategoria !== 'cento_tradicionais'
                &&
                tipoCategoria !== 'cento_folhados'
            ) {
                return;
            }

            const setores =
                categoria.querySelectorAll(
                    '[data-cento-setor]'
                );

            const status =
                categoria.querySelector(
                    '[data-cento-status]'
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

                    if (
                        indice + 1 <=
                        totalSelecionado
                    ) {
                        setor.classList.add(
                            'ativo'
                        );
                    } else {
                        setor.classList.remove(
                            'ativo'
                        );
                    }
                }
            );

            if (status) {

                const partes =
                    setores.length;

                status.innerHTML =
                    totalSelecionado >= partes
                        ? '<small>Cento completo.</small>'
                        : '<small>Monte seu cento.</small>';
            }
        }


        /*
        =================================
        GRANDES / EMPADÃO
        =================================
        */

        const quantidadeLivre =
            tipoCategoria === 'salgados_grandes'
            ||
            tipoCategoria === 'empadao';


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

                    const botaoAumentar =
                        produto.querySelector(
                            '[data-aumentar]'
                        );

                    const botaoDiminuir =
                        produto.querySelector(
                            '[data-diminuir]'
                        );

                    if (
                        !quantidade
                        ||
                        !botaoAumentar
                        ||
                        !botaoDiminuir
                    ) {
                        return;
                    }

                    const valorInicial =
                        Number(
                            quantidade.textContent
                        ) || 0;

                    botaoDiminuir.disabled =
                        valorInicial <= 0;

                    atualizarInputQuantidade(
                        produto,
                        valorInicial
                    );

                    produto.addEventListener(
                        'click',
                        function (evento) {

                            const botaoMais =
                                evento.target.closest(
                                    '[data-aumentar]'
                                );

                            if (botaoMais) {

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

                                botaoDiminuir.disabled =
                                    valor <= 0;

                                return;
                            }

                            const botaoMenos =
                                evento.target.closest(
                                    '[data-diminuir]'
                                );

                            if (botaoMenos) {

                                evento.preventDefault();

                                let valor =
                                    Number(
                                        quantidade.textContent
                                    ) || 0;

                                if (valor <= 0) {
                                    return;
                                }

                                valor--;

                                quantidade.textContent =
                                    valor;

                                atualizarInputQuantidade(
                                    produto,
                                    valor
                                );

                                botaoDiminuir.disabled =
                                    valor <= 0;
                            }
                        }
                    );
                }
            );

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
                        elementoLimite.dataset.limiteOpcoes
                    );

                if (
                    Number.isInteger(limite)
                    &&
                    limite >= 1
                ) {

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

                        const itens =
                            categoria.querySelectorAll(
                                '[data-produto-wrapper]'
                            );

                        itens.forEach(
                            function (produto) {

                                const quantidade =
                                    produto.querySelector(
                                        '[data-quantidade]'
                                    );

                                const botaoAumentar =
                                    produto.querySelector(
                                        '[data-aumentar]'
                                    );

                                const botaoDiminuir =
                                    produto.querySelector(
                                        '[data-diminuir]'
                                    );

                                if (
                                    !quantidade
                                    ||
                                    !botaoAumentar
                                    ||
                                    !botaoDiminuir
                                ) {
                                    return;
                                }

                                const valor =
                                    Number(
                                        quantidade.textContent
                                    ) || 0;

                                botaoAumentar.disabled =
                                    totalSelecionado >=
                                    limite;

                                botaoDiminuir.disabled =
                                    valor <= 0;
                            }
                        );

                        atualizarPreviewCento();
                    }


                    categoria.addEventListener(
                        'click',
                        function (evento) {

                            const botaoAumentar =
                                evento.target.closest(
                                    '[data-aumentar]'
                                );

                            if (botaoAumentar) {

                                evento.preventDefault();

                                if (
                                    totalSelecionado >=
                                    limite
                                ) {
                                    return;
                                }

                                const produto =
                                    botaoAumentar.closest(
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

                                let valorAtual =
                                    Number(
                                        quantidade.textContent
                                    ) || 0;

                                valorAtual++;

                                quantidade.textContent =
                                    valorAtual;

                                atualizarInputQuantidade(
                                    produto,
                                    valorAtual
                                );

                                totalSelecionado++;

                                atualizarInterface();

                                return;
                            }


                            const botaoDiminuir =
                                evento.target.closest(
                                    '[data-diminuir]'
                                );

                            if (botaoDiminuir) {

                                evento.preventDefault();

                                const produto =
                                    botaoDiminuir.closest(
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

                                let valorAtual =
                                    Number(
                                        quantidade.textContent
                                    ) || 0;

                                if (valorAtual <= 0) {
                                    return;
                                }

                                valorAtual--;

                                quantidade.textContent =
                                    valorAtual;

                                atualizarInputQuantidade(
                                    produto,
                                    valorAtual
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

    const indiceRemover =
        document.getElementById(
            'indiceRemoverCarrinho'
        );

    const nomeItemRemover =
        document.querySelector(
            '[data-nome-item-remover]'
        );


    if (
        indiceRemover
        &&
        botoesRemover.length
    ) {

        botoesRemover.forEach(
            function (botao) {

                botao.addEventListener(
                    'click',
                    function () {

                        indiceRemover.value =
                            botao.dataset.indice
                            || '';

                        if (nomeItemRemover) {

                            nomeItemRemover.textContent =
                                botao.dataset.nome
                                || 'este item';
                        }

                    }
                );
            }
        );
    }

});