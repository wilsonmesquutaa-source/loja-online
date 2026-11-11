'use strict';


/*
=====================================================
EDITOR DE IMAGEM DA CATEGORIA

Arquivo isolado do admin.js.

Ele não controla:
- sidebar;
- menu administrativo;
- produtos;
- usuário;
- outros editores.

=====================================================
*/

(function () {


    function iniciarEditorImagemCategoria() {


        const campoImagem =
            document.getElementById(
                'categoria-imagem-arquivo'
            );


        const palco =
            document.getElementById(
                'categoria-imagem-palco'
            );


        const previewContainer =
            document.getElementById(
                'categoria-imagem-preview-container'
            );


        const campoPosicaoX =
            document.getElementById(
                'categoria-posicao-x'
            );


        const campoPosicaoY =
            document.getElementById(
                'categoria-posicao-y'
            );


        const campoEscala =
            document.getElementById(
                'categoria-escala'
            );


        const controleZoom =
            document.getElementById(
                'categoria-imagem-zoom'
            );


        const valorZoom =
            document.getElementById(
                'categoria-imagem-zoom-valor'
            );


        const botaoCentralizar =
            document.getElementById(
                'categoria-imagem-centralizar'
            );


        if (
            !campoImagem
            ||
            !palco
            ||
            !previewContainer
            ||
            !campoPosicaoX
            ||
            !campoPosicaoY
            ||
            !campoEscala
        ) {

            return;

        }


        let imagem =
            document.getElementById(
                'categoria-imagem-preview'
            );


        let guia =
            document.getElementById(
                'categoria-imagem-guia'
            );


        let arrastando =
            false;


        let inicioX =
            0;


        let inicioY =
            0;


        let posicaoXInicial =
            50;


        let posicaoYInicial =
            50;


        /*
        =====================================================
        UTILITÁRIOS
        =====================================================
        */

        function limitar(
            valor,
            minimo,
            maximo
        ) {

            return Math.min(
                Math.max(
                    valor,
                    minimo
                ),
                maximo
            );

        }


        function numero(
            valor,
            padrao
        ) {

            const convertido =
                Number(
                    valor
                );


            return Number.isFinite(
                convertido
            )
                ? convertido
                : padrao;

        }


        function escalaAtual() {

            return limitar(
                numero(
                    campoEscala.value,
                    1
                ),
                0.10,
                2.00
            );

        }


        /*
        =====================================================
        APLICA O MESMO MODELO USADO NA LOJA
        =====================================================

        O site utiliza:

        object-fit: contain;
        object-position;
        scale;
        transform-origin;

        Portanto o editor utiliza exatamente a mesma lógica.

        =====================================================
        */

        function aplicarNaImagem(
            elemento,
            x,
            y,
            escala
        ) {

            if (
                !elemento
            ) {

                return;

            }


            elemento.style.objectFit =
                'contain';


            elemento.style.objectPosition =
                x
                + '% '
                + y
                + '%';


            elemento.style.transformOrigin =
                x
                + '% '
                + y
                + '%';


            elemento.style.scale =
                String(
                    escala
                );

        }


        /*
        =====================================================
        ATUALIZA ENQUADRAMENTO
        =====================================================
        */

        function atualizarEnquadramento(
            x,
            y,
            escalaInformada
        ) {


            const xLimitado =
                limitar(
                    numero(
                        x,
                        50
                    ),
                    0,
                    100
                );


            const yLimitado =
                limitar(
                    numero(
                        y,
                        50
                    ),
                    0,
                    100
                );


            const escala =
                limitar(
                    numero(
                        escalaInformada,
                        escalaAtual()
                    ),
                    0.10,
                    2.00
                );


            campoPosicaoX.value =
                xLimitado.toFixed(
                    2
                );


            campoPosicaoY.value =
                yLimitado.toFixed(
                    2
                );


            campoEscala.value =
                escala.toFixed(
                    2
                );


            if (
                controleZoom
            ) {

                controleZoom.value =
                    String(
                        Math.round(
                            escala * 100
                        )
                    );

            }


            if (
                valorZoom
            ) {

                valorZoom.textContent =
                    Math.round(
                        escala * 100
                    )
                    + '%';

            }


            aplicarNaImagem(
                imagem,
                xLimitado,
                yLimitado,
                escala
            );


            /*
            A guia é somente uma referência visual.

            A moldura laranja é a representação exata
            do card da loja.
            */

            if (
                guia
            ) {

                guia.style.objectPosition =
                    xLimitado
                    + '% '
                    + yLimitado
                    + '%';

            }

        }


        /*
        =====================================================
        CRIA GUIA
        =====================================================
        */

        function criarGuia(
            origem
        ) {

            if (
                !guia
            ) {

                guia =
                    document.createElement(
                        'img'
                    );


                guia.id =
                    'categoria-imagem-guia';


                guia.className =
                    'categoria-imagem-guia';


                guia.alt =
                    '';


                guia.setAttribute(
                    'aria-hidden',
                    'true'
                );


                palco.insertBefore(
                    guia,
                    previewContainer
                );

            }


            guia.src =
                origem;

        }


        /*
        =====================================================
        CRIA PREVIEW DE NOVO ARQUIVO
        =====================================================
        */

        function criarPreview(
            origem
        ) {


            previewContainer.innerHTML =
                '';


            imagem =
                document.createElement(
                    'img'
                );


            imagem.id =
                'categoria-imagem-preview';


            imagem.src =
                origem;


            imagem.alt =
                'Pré-visualização da imagem da categoria';


            imagem.draggable =
                false;


            previewContainer.appendChild(
                imagem
            );


            criarGuia(
                origem
            );


            /*
            Nova imagem começa:
            - centralizada;
            - 100%;
            */

            atualizarEnquadramento(
                50,
                50,
                1
            );

        }


        /*
        =====================================================
        SELEÇÃO DO ARQUIVO
        =====================================================
        */

        campoImagem.addEventListener(
            'change',
            function () {


                const arquivo =
                    campoImagem.files
                    &&
                    campoImagem.files[0]
                        ? campoImagem.files[0]
                        : null;


                if (
                    !arquivo
                ) {

                    return;

                }


                const tiposPermitidos = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                if (
                    !tiposPermitidos.includes(
                        arquivo.type
                    )
                ) {

                    alert(
                        'Selecione uma imagem JPG, PNG ou WEBP.'
                    );


                    campoImagem.value =
                        '';


                    return;

                }


                if (
                    arquivo.size
                    >
                    5 * 1024 * 1024
                ) {

                    alert(
                        'A imagem deve possuir no máximo 5 MB.'
                    );


                    campoImagem.value =
                        '';


                    return;

                }


                const leitor =
                    new FileReader();


                leitor.addEventListener(
                    'load',
                    function () {

                        criarPreview(
                            String(
                                leitor.result
                            )
                        );

                    }
                );


                leitor.readAsDataURL(
                    arquivo
                );

            }
        );


        /*
        =====================================================
        ARRASTAR
        =====================================================
        */

        previewContainer.addEventListener(
            'pointerdown',
            function (
                evento
            ) {


                if (
                    !imagem
                ) {

                    return;

                }


                arrastando =
                    true;


                inicioX =
                    evento.clientX;


                inicioY =
                    evento.clientY;


                posicaoXInicial =
                    numero(
                        campoPosicaoX.value,
                        50
                    );


                posicaoYInicial =
                    numero(
                        campoPosicaoY.value,
                        50
                    );


                if (
                    typeof previewContainer
                        .setPointerCapture
                    ===
                    'function'
                ) {

                    previewContainer
                        .setPointerCapture(
                            evento.pointerId
                        );

                }


                previewContainer
                    .classList
                    .add(
                        'arrastando'
                    );

            }
        );


        previewContainer.addEventListener(
            'pointermove',
            function (
                evento
            ) {


                if (
                    !arrastando
                    ||
                    !imagem
                ) {

                    return;

                }


                const deslocamentoX =
                    (
                        evento.clientX
                        -
                        inicioX
                    )
                    /
                    Math.max(
                        previewContainer.clientWidth,
                        1
                    )
                    *
                    100;


                const deslocamentoY =
                    (
                        evento.clientY
                        -
                        inicioY
                    )
                    /
                    Math.max(
                        previewContainer.clientHeight,
                        1
                    )
                    *
                    100;


                atualizarEnquadramento(
                    posicaoXInicial
                    +
                    deslocamentoX,

                    posicaoYInicial
                    +
                    deslocamentoY,

                    escalaAtual()
                );

            }
        );


        function finalizarArraste() {

            arrastando =
                false;


            previewContainer
                .classList
                .remove(
                    'arrastando'
                );

        }


        previewContainer.addEventListener(
            'pointerup',
            finalizarArraste
        );


        previewContainer.addEventListener(
            'pointercancel',
            finalizarArraste
        );


        /*
        =====================================================
        SETAS
        =====================================================
        */

        document
            .querySelectorAll(
                '[data-mover-categoria-imagem]'
            )
            .forEach(
                function (
                    botao
                ) {


                    botao.addEventListener(
                        'click',
                        function () {


                            const passo =
                                5;


                            const direcao =
                                botao.dataset
                                    .moverCategoriaImagem;


                            const x =
                                numero(
                                    campoPosicaoX.value,
                                    50
                                );


                            const y =
                                numero(
                                    campoPosicaoY.value,
                                    50
                                );


                            atualizarEnquadramento(

                                x
                                +
                                (
                                    direcao ===
                                    'direita'
                                        ? passo
                                        : direcao ===
                                            'esquerda'
                                            ? -passo
                                            : 0
                                ),

                                y
                                +
                                (
                                    direcao ===
                                    'baixo'
                                        ? passo
                                        : direcao ===
                                            'cima'
                                            ? -passo
                                            : 0
                                ),

                                escalaAtual()

                            );

                        }
                    );

                }
            );


        /*
        =====================================================
        CENTRALIZAR
        =====================================================
        */

        if (
            botaoCentralizar
        ) {

            botaoCentralizar.addEventListener(
                'click',
                function () {


                    atualizarEnquadramento(
                        50,
                        50,
                        escalaAtual()
                    );

                }
            );

        }


        /*
        =====================================================
        ZOOM
        =====================================================
        */

        if (
            controleZoom
        ) {

            controleZoom.addEventListener(
                'input',
                function () {


                    atualizarEnquadramento(

                        numero(
                            campoPosicaoX.value,
                            50
                        ),

                        numero(
                            campoPosicaoY.value,
                            50
                        ),

                        numero(
                            controleZoom.value,
                            100
                        )
                        /
                        100

                    );

                }
            );

        }


        /*
        =====================================================
        ESTADO INICIAL
        =====================================================
        */

        if (
            imagem
        ) {

            atualizarEnquadramento(

                numero(
                    campoPosicaoX.value,
                    50
                ),

                numero(
                    campoPosicaoY.value,
                    50
                ),

                escalaAtual()

            );

        }

    }


    /*
    =====================================================
    INICIALIZAÇÃO
    =====================================================
    */

    if (
        document.readyState
        ===
        'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            iniciarEditorImagemCategoria
        );

    } else {

        iniciarEditorImagemCategoria();

    }


})();