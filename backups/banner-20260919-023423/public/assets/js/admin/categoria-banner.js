'use strict';

/* ==================================
   EDITOR DO BANNER DA CATEGORIA
================================== */

(function () {

    function iniciarEditorBannerCategoria() {

        const campoImagem =
            document.getElementById('banner_imagem');

        const previewContainer =
            document.getElementById('banner-preview-container');

        const campoPosicaoX =
            document.getElementById('banner_posicao_x');

        const campoPosicaoY =
            document.getElementById('banner_posicao_y');

        if (
            !campoImagem
            || !previewContainer
            || !campoPosicaoX
            || !campoPosicaoY
        ) {
            return;
        }

        let imagem =
            document.getElementById('banner-preview');

        let arrastando = false;
        let inicioX = 0;
        let inicioY = 0;

        let posicaoXInicial =
            Number(campoPosicaoX.value)
            || 50;

        let posicaoYInicial =
            Number(campoPosicaoY.value)
            || 50;


        function limitar(valor) {

            return Math.min(
                Math.max(valor, 0),
                100
            );
        }


        function atualizarPosicao(
            x,
            y
        ) {

            const xLimitado =
                limitar(x);

            const yLimitado =
                limitar(y);

            campoPosicaoX.value =
                xLimitado.toFixed(2);

            campoPosicaoY.value =
                yLimitado.toFixed(2);

            if (imagem) {

                imagem.style.objectPosition =
                    xLimitado
                    + '% '
                    + yLimitado
                    + '%';
            }
        }


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
                'banner-preview';

            imagem.src =
                origem;

            imagem.alt =
                'Pré-visualização da imagem de fundo';

            imagem.draggable =
                false;

            previewContainer.appendChild(
                imagem
            );

            atualizarPosicao(
                50,
                50
            );
        }


        campoImagem.addEventListener(
            'change',
            function () {

                const arquivo =
                    campoImagem.files
                    && campoImagem.files[0]
                        ? campoImagem.files[0]
                        : null;

                if (!arquivo) {
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
                    > 8 * 1024 * 1024
                ) {

                    alert(
                        'A imagem de fundo deve possuir no máximo 8 MB.'
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


        previewContainer.addEventListener(
            'pointerdown',
            function (evento) {

                if (!imagem) {
                    return;
                }

                arrastando =
                    true;

                inicioX =
                    evento.clientX;

                inicioY =
                    evento.clientY;

                posicaoXInicial =
                    Number(
                        campoPosicaoX.value
                    )
                    || 50;

                posicaoYInicial =
                    Number(
                        campoPosicaoY.value
                    )
                    || 50;

                if (
                    typeof previewContainer.setPointerCapture
                    === 'function'
                ) {

                    previewContainer.setPointerCapture(
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
            function (evento) {

                if (
                    !arrastando
                    || !imagem
                ) {
                    return;
                }

                const largura =
                    Math.max(
                        previewContainer.clientWidth,
                        1
                    );

                const altura =
                    Math.max(
                        previewContainer.clientHeight,
                        1
                    );

                const deslocamentoX =
                    (
                        evento.clientX
                        - inicioX
                    )
                    / largura
                    * 100;

                const deslocamentoY =
                    (
                        evento.clientY
                        - inicioY
                    )
                    / altura
                    * 100;

                atualizarPosicao(
                    posicaoXInicial
                    - deslocamentoX,
                    posicaoYInicial
                    - deslocamentoY
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


        document
            .querySelectorAll(
                '[data-mover-banner]'
            )
            .forEach(
                function (botao) {

                    botao.addEventListener(
                        'click',
                        function () {

                            const passo =
                                6;

                            const direcao =
                                botao.getAttribute(
                                    'data-mover-banner'
                                );

                            const x =
                                Number(
                                    campoPosicaoX.value
                                )
                                || 50;

                            const y =
                                Number(
                                    campoPosicaoY.value
                                )
                                || 50;

                            let novoX =
                                x;

                            let novoY =
                                y;

                            if (
                                direcao === 'direita'
                            ) {
                                novoX +=
                                    passo;
                            }

                            if (
                                direcao === 'esquerda'
                            ) {
                                novoX -=
                                    passo;
                            }

                            if (
                                direcao === 'baixo'
                            ) {
                                novoY +=
                                    passo;
                            }

                            if (
                                direcao === 'cima'
                            ) {
                                novoY -=
                                    passo;
                            }

                            atualizarPosicao(
                                novoX,
                                novoY
                            );
                        }
                    );
                }
            );


        const centralizar =
            document.getElementById(
                'banner-centralizar'
            );

        if (centralizar) {

            centralizar.addEventListener(
                'click',
                function () {

                    atualizarPosicao(
                        50,
                        50
                    );
                }
            );
        }


        if (imagem) {

            atualizarPosicao(
                Number(
                    campoPosicaoX.value
                )
                || 50,

                Number(
                    campoPosicaoY.value
                )
                || 50
            );
        }
    }


    if (
        document.readyState
        === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            iniciarEditorBannerCategoria
        );

    } else {

        iniciarEditorBannerCategoria();
    }

})();