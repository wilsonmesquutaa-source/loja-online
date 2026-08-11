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
    ELEMENTOS
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


                /*
                =================================
                BOTÃO +
                =================================

                O botão só fica bloqueado quando
                o limite geral foi atingido.

                Portanto:

                Coxinha 1 → pode +
                Coxinha 2 → pode +
                Coxinha 3 → pode +
                Coxinha 4 → pode +

                desde que o total geral ainda
                esteja abaixo do limite.
                */

                botaoAumentar.disabled =
                    totalSelecionado >= limite;


                /*
                =================================
                BOTÃO -
                =================================
                */

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


                /*
                Não permite ultrapassar
                o limite da categoria.
                */

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


                /*
                =================================
                AUMENTA A QUANTIDADE
                =================================
                */

                let valorAtual =
                    Number(
                        quantidade.textContent
                    ) || 0;


                valorAtual++;


                quantidade.textContent =
                    valorAtual;


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


                /*
                Não permite quantidade negativa.
                */

                if (valorAtual <= 0) {

                    return;
                }


                valorAtual--;


                quantidade.textContent =
                    valorAtual;


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