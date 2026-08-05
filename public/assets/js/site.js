document.addEventListener(
    'DOMContentLoaded',
    function () {
        const botaoTopo = document.querySelector(
            '[data-voltar-topo]'
        );

        if (botaoTopo) {
            botaoTopo.addEventListener(
                'click',
                function () {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            );
        }

        const linksInternos = document.querySelectorAll(
            'a[href^="#"]'
        );

        linksInternos.forEach(
            function (link) {
                link.addEventListener(
                    'click',
                    function (evento) {
                        const destino = document.querySelector(
                            link.getAttribute('href')
                        );

                        if (!destino) {
                            return;
                        }

                        evento.preventDefault();

                        destino.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                );
            }
        );
    }
);
