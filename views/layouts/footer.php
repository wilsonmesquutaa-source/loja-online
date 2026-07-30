<?php

declare(strict_types=1);

$baseUrl = defined('BASE_URL')
    ? BASE_URL
    : '';

?>

<script>

const BASE_URL = "<?= $baseUrl ?>";


</script>



<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>




<script>


const categorias =
document.querySelectorAll('.categoria-card');



if (categorias.length) {



    categorias.forEach(card => {



        card.addEventListener('click', () => {



            const id =
            card.dataset.id;


            const nome =
            card.dataset.nome;



            document.getElementById('tituloCategoria').innerText =
            nome;



            document.getElementById('conteudoCategoria').innerHTML = `

                <div class="text-center py-5">

                    Carregando produtos...

                </div>

            `;



            const painel =
            new bootstrap.Offcanvas(
                document.getElementById('offcanvasCategoria')
            );


            painel.show();




            fetch(`${BASE_URL}/produtos/categoria/${id}`)



            .then(response => {



                if (!response.ok) {


                    throw new Error(
                        'Erro na requisição'
                    );


                }



                return response.json();



            })



            .then(produtos => {



                let html = '';



                produtos.forEach(produto => {



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
                            class="btn btn-success">


                                Adicionar


                            </button>



                        </div>


                    </div>



                    `;



                });




                document.getElementById('conteudoCategoria').innerHTML =
                html;




            })



            .catch(error => {



                document.getElementById('conteudoCategoria').innerHTML = `


                    <div class="alert alert-danger">


                        Erro ao carregar produtos.


                    </div>


                `;



                console.error(error);



            });



        });



    });



}



</script>



</body>

</html>