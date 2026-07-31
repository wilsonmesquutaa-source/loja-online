<?php

declare(strict_types=1);

require APP_ROOT . '/views/layouts/admin-header.php';

?>


<main class="admin-container">


    <h1>
        Produtos
    </h1>


    <a
        href="<?= $baseUrl ?>/admin/produtos/novo"
        class="btn btn-primary mb-4">
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
                        <?= $produto['id'] ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($produto['nome']) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($produto['categoria']) ?>
                    </td>


                    <td>
                        <?= $produto['estoque'] ?>
                    </td>


                    <td>
                        <?= $produto['status'] ?>
                    </td>


                    <td>


                        <a
                            href="<?= $baseUrl ?>/admin/produtos/editar/<?= $produto['id'] ?>"
                            class="btn btn-warning btn-sm">
                            Editar
                        </a>


                        <a
                            href="<?= BASE_URL ?>/admin/produtos/excluir/<?= $produto['id'] ?>"
                            class="btn btn-danger"
                            onclick="return confirm('Deseja realmente excluir este produto?');">
                            Excluir
                        </a>

                    </td>


                </tr>


            <?php endforeach; ?>


        </tbody>


    </table>


</main>



<?php

require APP_ROOT . '/views/layouts/admin-footer.php';
