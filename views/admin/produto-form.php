<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/admin-header.php';

$produto = $produto ?? null;

?>

<main class="admin-container">


    <div class="admin-top">

        <h1 class="dashboard-title">

            <?= $produto
                ? 'Editar Produto'
                : 'Novo Produto'
            ?>

        </h1>

    </div>



    <section class="card-admin p-4">


        <form method="POST"

            action="<?= $baseUrl ?><?= $produto
                                        ? '/admin/produtos/atualizar/' . $produto['id']
                                        : '/admin/produtos/salvar'
                                    ?>">



            <div class="mb-3">

                <label class="form-label">
                    Categoria
                </label>


                <select
                    name="categoria_id"
                    class="form-control"
                    required>


                    <?php foreach ($categorias as $categoria): ?>


                        <option
                            value="<?= $categoria['id'] ?>"

                            <?= $produto
                                && $produto['categoria_id'] == $categoria['id']
                                ? 'selected'
                                : ''
                            ?>>

                            <?= htmlspecialchars(
                                $categoria['nome']
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>

            </div>





            <div class="mb-3">

                <label class="form-label">
                    Nome
                </label>


                <input
                    type="text"
                    name="nome"
                    class="form-control"
                    required

                    value="<?= htmlspecialchars(
                                $produto['nome'] ?? ''
                            ) ?>">

            </div>





            <div class="mb-3">

                <label class="form-label">
                    Slug
                </label>


                <input
                    type="text"
                    name="slug"
                    class="form-control"

                    value="<?= htmlspecialchars(
                                $produto['slug'] ?? ''
                            ) ?>">

            </div>





            <div class="mb-3">

                <label class="form-label">
                    Descrição
                </label>


                <textarea
                    name="descricao"
                    class="form-control"
                    rows="4">

                        <?= htmlspecialchars(
                            $produto['descricao'] ?? ''
                        ) ?>

                </textarea>


            </div>





            <div class="mb-3">

                <label class="form-label">
                    Estoque
                </label>


                <input
                    type="number"
                    name="estoque"
                    class="form-control"

                    value="<?= htmlspecialchars(
                                (string)($produto['estoque'] ?? 0)
                            ) ?>">

            </div>





            <button
                class="btn btn-success">

                Atualizar

            </button>



            <a
                href="<?= $baseUrl ?>/admin/produtos"
                class="btn btn-secondary">

                Cancelar

            </a>



        </form>


    </section>


</main>



<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
