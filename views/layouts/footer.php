<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>


<script>
    function toggleMenu() {

        const sidebar =
            document.getElementById('sidebar');


        const content =
            document.getElementById('content');


        sidebar.classList.toggle('collapsed');

        content.classList.toggle('expanded');

    }
</script>
<script>
    document.querySelectorAll('.categoria-card').forEach(card => {

        card.addEventListener('click', () => {

            const id = card.dataset.id;
            const nome = card.dataset.nome;


            document.getElementById('tituloCategoria').innerText = nome;


            document.getElementById('conteudoCategoria').innerHTML = `
            <div class="text-center py-5">
                Carregando produtos...
            </div>
        `;


            const painel = new bootstrap.Offcanvas(
                document.getElementById('offcanvasCategoria')
            );

            painel.show();



            fetch(`/loja-online/public/produtos/categoria/${id}`)

                .then(response => {

                    if (!response.ok) {

                        throw new Error('Erro na requisição');

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



                    document.getElementById('conteudoCategoria').innerHTML = html;


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
</script>


</body>

</html>