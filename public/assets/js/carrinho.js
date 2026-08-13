document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        =================================
        MODAL DE REMOÇÃO DO CARRINHO
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

    }
);