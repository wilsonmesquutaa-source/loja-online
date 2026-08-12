document.addEventListener('DOMContentLoaded', function () {

    console.log('SITE.JS CARREGADO');


    /*
    =================================
    SELEÇÃO DE PRODUTOS
    =================================
    */

    const categoria =
        document.querySelector('.categoria-selecao');


    if (!categoria) {

        console.log(
            'Nenhuma seleção de categoria encontrada.'
        );

        return;
    }


    console.log(
        'Página de categoria encontrada.'
    );


    /*
    =================================
    TIPO DA CATEGORIA
    =================================
    */

    const tipoCategoria =
        categoria.dataset.tipoCategoria || 'unica';


    console.log(
        'Tipo da categoria:',
        tipoCategoria
    );


    /*
    =================================
    CATEGORIAS SEM LIMITE GERAL
    =================================

    Salgados grandes e empadão trabalham
    somente com a quantidade individual
    de cada produto.
    */

    const quantidadeLivre =
        tipoCategoria === 'salgados_grandes'
        ||
        tipoCategoria === 'empadao';


    if (quantidadeLivre) {

        console.log(
            'Categoria com quantidade livre.'
        );


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


                const inputQuantidade =
                    produto.querySelector(
                        '[data-input-quantidade]'
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


                /*
                =================================
                ESTADO INICIAL
                =================================
                */

                const valorInicial =
                    Number(
                        quantidade.textContent
                    ) || 0;


                botaoDiminuir.disabled =
                    valorInicial <= 0;


                if (inputQuantidade) {

                    inputQuantidade.value =
                        valorInicial;

                }


                /*
                =================================
                BOTÕES
                =================================
                */

                produto.addEventListener(
                    'click',
                    function (evento) {

                        /*
                        =============================
                        BOTÃO +
                        =============================
                        */

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


                            if (inputQuantidade) {

                                inputQuantidade.value =
                                    valor;

                            }


                            botaoDiminuir.disabled =
                                valor <= 0;


                            console.log(
                                'Produto adicionado:',
                                produto.dataset.produtoId,
                                'Quantidade:',
                                valor
                            );


                            return;

                        }


                        /*
                        =============================
                        BOTÃO -
                        =============================
                        */

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


                            if (inputQuantidade) {

                                inputQuantidade.value =
                                    valor;

                            }


                            botaoDiminuir.disabled =
                                valor <= 0;


                            console.log(
                                'Produto removido:',
                                produto.dataset.produtoId,
                                'Quantidade:',
                                valor
                            );


                            return;

                        }

                    }
                );

            }
        );


        /*
        =================================
        IMPORTANTE
        =================================

        Não continua para a lógica dos
        tradicionais e folhados.
        */

        return;

    }


    /*
    =================================
    DAQUI PARA BAIXO
    =================================

    LÓGICA ORIGINAL
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


    if (!contador) {

        console.error(
            'Contador de opções não encontrado.'
        );

        return;
    }


    if (!elementoLimite) {

        console.error(
            'Limite de opções não encontrado.'
        );

        return;
    }


    /*
    =================================
    LIMITE
    =================================
    */

    const limite =
        Number(
            elementoLimite.dataset.limiteOpcoes
        );


    console.log(
        'Limite da categoria:',
        limite
    );


    if (
        !Number.isInteger(limite)
        ||
        limite < 1
    ) {

        console.error(
            'Limite de opções inválido:',
            elementoLimite.dataset.limiteOpcoes
        );

        return;
    }


    /*
    =================================
    ESTADO
    =================================
    */

    let totalSelecionado = 0;


    /*
    =================================
    ATUALIZA INTERFACE
    =================================
    */

    function atualizarInterface() {

        contador.textContent =
            totalSelecionado;


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


                const valor =
                    Number(
                        quantidade.textContent
                    ) || 0;


                botaoAumentar.disabled =
                    totalSelecionado >= limite;


                botaoDiminuir.disabled =
                    valor <= 0;

            }
        );

    }


    /*
    =================================
    CLIQUE
    =================================
    */

    categoria.addEventListener(
        'click',
        function (evento) {

            /*
            =================================
            BOTÃO +
            =================================
            */

            const botaoAumentar =
                evento.target.closest(
                    '[data-aumentar]'
                );


            if (botaoAumentar) {

                evento.preventDefault();


                if (
                    totalSelecionado >= limite
                ) {

                    return;

                }


                const produto =
                    botaoAumentar.closest(
                        '[data-produto-wrapper]'
                    );


                if (!produto) {

                    console.error(
                        'Produto não encontrado.'
                    );

                    return;

                }


                const quantidade =
                    produto.querySelector(
                        '[data-quantidade]'
                    );


                if (!quantidade) {

                    console.error(
                        'Quantidade do produto não encontrada.'
                    );

                    return;

                }


                let valorAtual =
                    Number(
                        quantidade.textContent
                    ) || 0;


                valorAtual++;


                quantidade.textContent =
                    valorAtual;


                const inputQuantidade =
                    produto.querySelector(
                        '[data-input-quantidade]'
                    );


                if (inputQuantidade) {

                    inputQuantidade.value =
                        valorAtual;

                }


                totalSelecionado++;


                atualizarInterface();


                console.log(
                    'Produto adicionado:',
                    produto.dataset.produtoId,
                    'Quantidade:',
                    valorAtual,
                    'Total:',
                    totalSelecionado
                );


                return;

            }


            /*
            =================================
            BOTÃO -
            =================================
            */

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

                    console.error(
                        'Produto não encontrado.'
                    );

                    return;

                }


                const quantidade =
                    produto.querySelector(
                        '[data-quantidade]'
                    );


                if (!quantidade) {

                    console.error(
                        'Quantidade do produto não encontrada.'
                    );

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


                const inputQuantidade =
                    produto.querySelector(
                        '[data-input-quantidade]'
                    );


                if (inputQuantidade) {

                    inputQuantidade.value =
                        valorAtual;

                }


                totalSelecionado =
                    Math.max(
                        0,
                        totalSelecionado - 1
                    );


                atualizarInterface();


                console.log(
                    'Produto removido:',
                    produto.dataset.produtoId,
                    'Quantidade:',
                    valorAtual,
                    'Total:',
                    totalSelecionado
                );


                return;

            }

        }
    );


    /*
    =================================
    ESTADO INICIAL
    =================================
    */

    atualizarInterface();

});