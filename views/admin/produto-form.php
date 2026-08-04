<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/admin-header.php';

$produto = $produto ?? null;

?>

<main class="admin-container">

    <section class="card-admin p-4">


        <form method="POST"

            action="<?= BASE_URL ?><?= $produto
                ? '/admin/produtos/atualizar/' . $produto['id']
                : '/admin/produtos/salvar'
            ?>">



            <?php if (isset($csrfToken)): ?>

                <input
                    type="hidden"
                    name="_token"
                    value="<?= htmlspecialchars(
                        $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            <?php endif; ?>



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
                                $categoria['nome'],
                                ENT_QUOTES,
                                'UTF-8'
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
                        $produto['nome'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
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
                        $produto['slug'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

            </div>





            <div class="mb-3">

                <label class="form-label">
                    Descrição
                </label>


                <textarea
                    name="descricao"
                    class="form-control"
                    rows="4"><?= htmlspecialchars(
                        $produto['descricao'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>


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
                        (string)($produto['estoque'] ?? 0),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

            </div>





            <button
                class="btn btn-success"
                type="submit">

                <?= $produto
                    ? 'Atualizar'
                    : 'Salvar'
                ?>

            </button>



            <a
                href="<?= BASE_URL ?>/admin/produtos"
                class="btn btn-secondary">

                Cancelar

            </a>



        </form>


    </section>


</main>



<?php

require APP_ROOT . '/views/layouts/admin-footer.php';