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


        /*
        =================================
        ESTADO DO CENTO
        =================================
        */

        const tipoCategoria =
            categoria.dataset.tipoCategoria
            || 'unica';


        const ehCento =
            tipoCategoria ===
            'cento_tradicionais'
            ||
            tipoCategoria ===
            'cento_folhados';


        const quantidadeLivre =
            tipoCategoria ===
            'salgados_grandes'
            ||
            tipoCategoria ===
            'empadao';


        /*
        Cada posição representa um setor:

        0 = superior esquerdo
        1 = superior direito
        2 = inferior direito
        3 = inferior esquerdo
        */

        const setoresCento =
            Array.from(
                categoria.querySelectorAll(
                    '[data-cento-setor]'
                )
            );


        const quantidadeSetores =
            setoresCento.length;


        const setoresOcupados =
            Array(
                quantidadeSetores
            ).fill(null);


        /*
        =================================
        FUNÇÕES AUXILIARES
        =================================
        */

        function atualizarInputQuantidade(
            produto,
            valor
        ) {

            const input =
                produto.querySelector(
                    '[data-input-quantidade]'
                );


            if (input) {

                input.value =
                    valor;

            }

        }


        function obterIdProduto(
            produto
        ) {

            const elemento =
                produto.querySelector(
                    '[data-produto-id]'
                );


            if (!elemento) {
                return '';
            }


            return elemento.dataset.produtoId
                || '';

        }


        function obterNomeProduto(
            produto
        ) {

            const nome =
                produto.querySelector(
                    '.produto-selecao-card h4'
                );


            if (!nome) {
                return '';
            }


            return nome.textContent.trim();

        }


        function encontrarPrimeiroSetorLivre() {

            return setoresOcupados.findIndex(
                function (id) {

                    return id === null;

                }
            );

        }


        /*
        =================================
        MODAL CENTO INCOMPLETO
        =================================
        */

        function abrirModalCentoIncompleto() {

            if (
                !modalElement
                ||
                typeof bootstrap ===
                    'undefined'
            ) {
                return;
            }


            if (mensagemModal) {

                if (
                    tipoCategoria ===
                    'cento_tradicionais'
                ) {

                    mensagemModal.innerHTML =
                        '<strong>Alerta! Falta completar o cento!</strong>' +
                        '<br><br>' +
                        'O cento tradicional deve ter ' +
                        '<strong>4 partes de 25 salgados</strong>, ' +
                        'totalizando 100 unidades.';

                } else if (
                    tipoCategoria ===
                    'cento_folhados'
                ) {

                    mensagemModal.innerHTML =
                        '<strong>Alerta! Falta completar o cento!</strong>' +
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
        ATUALIZA CÍRCULO DO CENTO
        =================================
        */

        function atualizarPreviewCento() {

            if (!ehCento) {
                return;
            }


            setoresCento.forEach(
                function (
                    setor,
                    indice
                ) {

                    const texto =
                        setor.querySelector(
                            '[data-cento-texto]'
                        );


                    const produtoId =
                        setoresOcupados[
                            indice
                        ];


                    const ocupado =
                        produtoId !== null;


                    setor.classList.toggle(
                        'ativo',
                        ocupado
                    );


                    if (!texto) {
                        return;
                    }


                    if (!ocupado) {

                        texto.textContent =
                            '';

                        return;

                    }


                    const produto =
                        categoria.querySelector(
                            `[data-produto-id="${produtoId}"]`
                        );


                    if (!produto) {

                        texto.textContent =
                            '';

                        return;

                    }


                    texto.textContent =
                        obterNomeProduto(
                            produto
                        );

                }
            );


            const status =
                categoria.querySelector(
                    '[data-cento-status]'
                );


            if (status) {

                const totalSelecionado =
                    setoresOcupados.filter(
                        function (id) {

                            return id !== null;

                        }
                    ).length;


                const limite =
                    quantidadeSetores;


                status.innerHTML =
                    totalSelecionado ===
                        limite

                        ? '<small>Cento completo.</small>'

                        : '<small>Monte seu cento.</small>';

            }

        }


        /*
        =================================
        CONTADOR GRANDES / EMPADÃO
        =================================
        */

        function atualizarContadorQuantidade() {

            const contador =
                categoria.querySelector(
                    '[data-contador-quantidade]'
                );


            if (!contador) {
                return;
            }


            let total =
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


            /*
            =================================
            LIMPAR GRANDES / EMPADÃO
            =================================
            */

            const botaoLimpar =
                categoria.querySelector(
                    '[data-limpar-cento]'
                );


            if (botaoLimpar) {

                botaoLimpar.addEventListener(
                    'click',
                    function (evento) {

                        evento.preventDefault();


                        produtos.forEach(
                            function (produto) {

                                const quantidade =
                                    produto.querySelector(
                                        '[data-quantidade]'
                                    );


                                const input =
                                    produto.querySelector(
                                        '[data-input-quantidade]'
                                    );


                                const menos =
                                    produto.querySelector(
                                        '[data-diminuir]'
                                    );


                                if (quantidade) {

                                    quantidade.textContent =
                                        '0';

                                }


                                if (input) {

                                    input.value =
                                        '0';

                                }


                                if (menos) {

                                    menos.disabled =
                                        true;

                                }

                            }
                        );


                        atualizarContadorQuantidade();

                    }
                );

            }


            atualizarContadorQuantidade();

        }


        /*
        =================================
        TRADICIONAIS / FOLHADOS
        =================================
        */

        if (ehCento) {

            const contador =
                categoria.querySelector(
                    '[data-contador-opcoes]'
                );


            const elementoLimite =
                categoria.querySelector(
                    '[data-limite-opcoes]'
                );


            const limite =
                elementoLimite
                    ? Number(
                        elementoLimite
                            .dataset
                            .limiteOpcoes
                    )
                    : quantidadeSetores;


            const produtos =
                categoria.querySelectorAll(
                    '[data-produto-wrapper]'
                );


            /*
            =================================
            RESTAURA SELEÇÕES INICIAIS
            MODO EDIÇÃO
            =================================
            */

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


                    atualizarInputQuantidade(
                        produto,
                        valor
                    );


                    if (
                        valor <= 0
                    ) {
                        return;
                    }


                    const produtoId =
                        obterIdProduto(
                            produto
                        );


                    if (!produtoId) {
                        return;
                    }


                    for (
                        let i = 0;
                        i < valor;
                        i++
                    ) {

                        const setorLivre =
                            encontrarPrimeiroSetorLivre();


                        if (
                            setorLivre ===
                            -1
                        ) {
                            break;
                        }


                        setoresOcupados[
                            setorLivre
                        ] =
                            produtoId;

                    }

                }
            );


            /*
            =================================
            INTERFACE
            =================================
            */

            function atualizarInterface() {

                const totalSelecionado =
                    setoresOcupados.filter(
                        function (id) {

                            return id !== null;

                        }
                    ).length;


                if (contador) {

                    contador.textContent =
                        totalSelecionado;

                }


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


                        /*
                        =================================
                        CARD SELECIONADO
                        =================================

                        Mantém o contorno laranja
                        enquanto o produto tiver
                        quantidade maior que zero.
                        */

                        const card =
                            produto.querySelector(
                                '.produto-selecao-card'
                            );


                        if (card) {

                            card.classList.toggle(
                                'selecionado',
                                valor > 0
                            );

                        }


                        /*
                        O + trava somente quando
                        o cento estiver completo.
                        */

                        mais.disabled =
                            totalSelecionado >=
                            limite;


                        /*
                        O - trava somente quando
                        o sabor estiver zerado.
                        */

                        menos.disabled =
                            valor <= 0;

                    }
                );


                atualizarPreviewCento();

            }


            /*
            =================================
            AUMENTAR / DIMINUIR
            =================================
            */

            categoria.addEventListener(
                'click',
                function (evento) {

                    const mais =
                        evento.target.closest(
                            '[data-aumentar]'
                        );


                    if (mais) {

                        evento.preventDefault();


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


                        const produtoId =
                            obterIdProduto(
                                produto
                            );


                        if (!produtoId) {
                            return;
                        }


                        const totalSelecionado =
                            setoresOcupados.filter(
                                function (id) {

                                    return id !== null;

                                }
                            ).length;


                        if (
                            totalSelecionado >=
                            limite
                        ) {
                            return;
                        }


                        const setorLivre =
                            encontrarPrimeiroSetorLivre();


                        if (
                            setorLivre ===
                            -1
                        ) {
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


                        setoresOcupados[
                            setorLivre
                        ] =
                            produtoId;


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


                        const produtoId =
                            obterIdProduto(
                                produto
                            );


                        if (!produtoId) {
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


                        /*
                        Procura a última posição
                        ocupada por este sabor.
                        */

                        let setorRemover =
                            -1;


                        for (
                            let i =
                                setoresOcupados.length - 1;
                            i >= 0;
                            i--
                        ) {

                            if (
                                setoresOcupados[i] ===
                                produtoId
                            ) {

                                setorRemover =
                                    i;

                                break;

                            }

                        }


                        if (
                            setorRemover ===
                            -1
                        ) {
                            return;
                        }


                        /*
                        Libera somente a parte
                        correspondente.
                        */

                        setoresOcupados[
                            setorRemover
                        ] =
                            null;


                        valor--;


                        quantidade.textContent =
                            valor;


                        atualizarInputQuantidade(
                            produto,
                            valor
                        );


                        atualizarInterface();

                    }

                }
            );


            /*
            =================================
            LIMPAR CENTO
            =================================
            */

            const botaoLimpar =
                categoria.querySelector(
                    '[data-limpar-cento]'
                );


            if (botaoLimpar) {

                botaoLimpar.addEventListener(
                    'click',
                    function (evento) {

                        evento.preventDefault();


                        setoresOcupados.fill(
                            null
                        );


                        produtos.forEach(
                            function (produto) {

                                const quantidade =
                                    produto.querySelector(
                                        '[data-quantidade]'
                                    );


                                const input =
                                    produto.querySelector(
                                        '[data-input-quantidade]'
                                    );


                                const menos =
                                    produto.querySelector(
                                        '[data-diminuir]'
                                    );


                                if (quantidade) {

                                    quantidade.textContent =
                                        '0';

                                }


                                if (input) {

                                    input.value =
                                        '0';

                                }


                                if (menos) {

                                    menos.disabled =
                                        true;

                                }

                            }
                        );


                        atualizarInterface();

                    }
                );

            }


            atualizarInterface();

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

                    if (!ehCento) {
                        return;
                    }


                    const totalSelecionado =
                        setoresOcupados.filter(
                            function (id) {

                                return id !== null;

                            }
                        ).length;


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