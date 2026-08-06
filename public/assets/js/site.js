document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        =================================
        VOLTAR AO TOPO
        =================================
        */


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




        /*
        =================================
        SCROLL SUAVE LINKS INTERNOS
        =================================
        */


        const linksInternos = document.querySelectorAll(
            'a[href^="#"]'
        );


        linksInternos.forEach(
            function (link) {


                link.addEventListener(
                    'click',
                    function (evento) {


                        const destino =
                            document.querySelector(
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







        /*
        =================================
        CATEGORIAS - CARREGAMENTO AJAX
        =================================
        */


        const categorias =
            document.querySelectorAll(
                '.categoria-card'
            );



        if (categorias.length) {



            categorias.forEach(
                function (card) {



                    card.addEventListener(
                        'click',
                        function () {



                            const id =
                                card.dataset.id;



                            const nome =
                                card.dataset.nome;



                            const titulo =
                                document.getElementById(
                                    'tituloCategoria'
                                );



                            const conteudo =
                                document.getElementById(
                                    'conteudoCategoria'
                                );



                            if (titulo) {

                                titulo.innerText = nome;

                            }



                            if (conteudo) {


                                conteudo.innerHTML = `

                                    <div class="text-center py-5">

                                        Carregando produtos...

                                    </div>

                                `;


                            }




                            const painel =
                                new bootstrap.Offcanvas(
                                    document.getElementById(
                                        'offcanvasCategoria'
                                    )
                                );



                            painel.show();






                            fetch(
                                `${BASE_URL}/produtos/categoria/${id}`
                            )

                            .then(
                                response => {


                                    if (!response.ok) {


                                        throw new Error(
                                            'Erro na requisição'
                                        );


                                    }


                                    return response.json();


                                }
                            )



                            .then(
                                produtos => {


                                    let html = '';



                                    produtos.forEach(
                                        produto => {



                                            html += `


                                            <div class="card mb-3 shadow-sm">

                                                <div class="card-body">


                                                    <h5>
                                                        ${produto.nome}
                                                    </h5>


                                                    <p>
                                                        ${produto.descricao ?? ''}
                                                    </p>


                                                    <button 
                                                    class="btn btn-warning">

                                                        Adicionar

                                                    </button>


                                                </div>

                                            </div>


                                            `;


                                        }
                                    );



                                    conteudo.innerHTML = html;



                                }
                            )



                            .catch(
                                erro => {



                                    conteudo.innerHTML = `

                                    <div class="alert alert-danger">

                                        Erro ao carregar produtos.

                                    </div>

                                    `;



                                    console.error(erro);



                                }
                            );



                        }
                    );



                }
            );



        }



    }
);