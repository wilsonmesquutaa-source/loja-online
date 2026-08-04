<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/admin-header.php';

?>

<main class="admin-container pt-5">
    <a
        href="<?= BASE_URL ?>/admin/produtos/novo"
        class="btn btn-primary mb-4 ms-3">
        Adicionar produto
    </a>



    <table class="table">


        <thead>

            <tr>

                <th>ID</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Estoque</th>
                <th>Status</th>
                <th>Ações</th>

            </tr>

        </thead>



        <tbody>


            <?php foreach ($produtos as $produto): ?>


                <tr>


                    <td>
                        <?= (int) $produto['id'] ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $produto['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $produto['categoria'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>


                    <td>
                        <?= (int) $produto['estoque'] ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $produto['status'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>


                    <td>


                        <a
                            href="<?= BASE_URL ?>/admin/produtos/editar/<?= (int) $produto['id'] ?>"
                            class="btn btn-warning btn-sm">
                            Editar
                        </a>


                        <form
                            action="<?= BASE_URL ?>/admin/produtos/excluir/<?= (int) $produto['id'] ?>"
                            method="POST"
                            style="display:inline;">

                            <button
                                type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Deseja realmente excluir este produto? Esta ação não poderá ser recuperada');">
                                Excluir
                            </button>

                        </form>


                    </td>


                </tr>


            <?php endforeach; ?>


        </tbody>


    </table>


</main>



<?php

require APP_ROOT . '/views/layouts/admin-footer.php';