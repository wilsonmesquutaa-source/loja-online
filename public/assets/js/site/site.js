document.addEventListener(
    'DOMContentLoaded',
    function () {

        console.log(
            'SITE.JS CARREGADO'
        );


        const galerias =
            document.querySelectorAll(
                '[data-hero-galeria]'
            );


        galerias.forEach(
            function (
                galeria
            ) {

                inicializarGaleriaHero(
                    galeria
                );

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| GALERIA DA HOME
|--------------------------------------------------------------------------
*/

function inicializarGaleriaHero(
    galeria
) {

    const slides =
        Array.from(
            galeria.querySelectorAll(
                '[data-hero-slide]'
            )
        );


    const indicadores =
        galeria.querySelector(
            '[data-hero-indicadores]'
        );


    /*
    |--------------------------------------------------------------------------
    | VERIFICA QUANTIDADE DE SLIDES
    |--------------------------------------------------------------------------
    */

    if (
        slides.length <= 1
    ) {

        return;

    }


    let indiceAtual =
        0;


    let intervalo =
        null;


    /*
    |--------------------------------------------------------------------------
    | CRIAR INDICADORES
    |--------------------------------------------------------------------------
    */

    const botoesIndicadores =
        [];


    if (
        indicadores
    ) {

        indicadores.innerHTML =
            '';


        slides.forEach(
            function (
                _slide,
                indice
            ) {

                const indicador =
                    document.createElement(
                        'button'
                    );


                indicador.type =
                    'button';


                indicador.className =
                    'hero-banner-indicador';


                indicador.setAttribute(
                    'aria-label',
                    'Mostrar banner '
                    +
                    (
                        indice + 1
                    )
                );


                if (
                    indice === 0
                ) {

                    indicador.classList.add(
                        'ativo'
                    );

                }


                indicador.addEventListener(
                    'click',
                    function () {

                        mostrarSlide(
                            indice
                        );


                        reiniciarIntervalo();

                    }
                );


                indicadores.appendChild(
                    indicador
                );


                botoesIndicadores.push(
                    indicador
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR SLIDE
    |--------------------------------------------------------------------------
    */

    function mostrarSlide(
        novoIndice
    ) {

        indiceAtual =
            (
                novoIndice
                +
                slides.length
            )
            %
            slides.length;


        slides.forEach(
            function (
                slide,
                indice
            ) {

                const ativo =
                    indice === indiceAtual;


                slide.classList.toggle(
                    'ativo',
                    ativo
                );


                slide.setAttribute(
                    'aria-hidden',
                    ativo
                        ? 'false'
                        : 'true'
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ATUALIZAR INDICADORES
        |--------------------------------------------------------------------------
        */

        botoesIndicadores.forEach(
            function (
                botao,
                indice
            ) {

                botao.classList.toggle(
                    'ativo',
                    indice === indiceAtual
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INICIAR INTERVALO
    |--------------------------------------------------------------------------
    */

    function iniciarIntervalo() {

        if (
            intervalo !== null
        ) {

            return;

        }


        intervalo =
            window.setInterval(
                function () {

                    mostrarSlide(
                        indiceAtual + 1
                    );

                },
                4500
            );

    }


    /*
    |--------------------------------------------------------------------------
    | PARAR INTERVALO
    |--------------------------------------------------------------------------
    */

    function pararIntervalo() {

        if (
            intervalo === null
        ) {

            return;

        }


        window.clearInterval(
            intervalo
        );


        intervalo =
            null;

    }


    /*
    |--------------------------------------------------------------------------
    | REINICIAR INTERVALO
    |--------------------------------------------------------------------------
    */

    function reiniciarIntervalo() {

        pararIntervalo();


        iniciarIntervalo();

    }


    /*
    |--------------------------------------------------------------------------
    | INICIAR GALERIA
    |--------------------------------------------------------------------------
    */

    mostrarSlide(
        0
    );


    iniciarIntervalo();

}