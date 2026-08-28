<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

?>

<main class="admin-container pt-5">


    <div class="mb-4">

        <a
            href="<?= BASE_URL ?>/admin/produtos/novo"
            class="
                btn
                btn-primary
            "
        >

            Adicionar produto

        </a>

    </div>


    <div
        class="
            table-responsive
        "
    >

        <table class="table">

            <thead>

                <tr>

                    <th>
                        Imagem
                    </th>

                    <th>
                        ID
                    </th>

                    <th>
                        Produto
                    </th>

                    <th>
                        Categoria
                    </th>

                    <th>
                        Estoque
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Ações
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php foreach (
                    $produtos
                    as $produto
                ): ?>

                    <tr>


                        <!-- IMAGEM -->

                        <td>

                            <?php if (
                                !empty(
                                    $produto[
                                        'imagem_url'
                                    ]
                                )
                            ): ?>

                                <img
                                    src="<?= BASE_URL
                                        . $produto[
                                            'imagem_url'
                                        ] ?>"
                                    alt="<?= htmlspecialchars(
                                        'Imagem de '
                                        . $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    style="
                                        width: 70px;
                                        height: 55px;
                                        object-fit: cover;
                                        border-radius: 10px;
                                        display: block;
                                    "
                                >

                            <?php else: ?>

                                <div
                                    style="
                                        width: 70px;
                                        height: 55px;
                                        border-radius: 10px;
                                        background: #f8fafc;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        color: #94a3b8;
                                    "
                                >

                                    <i
                                        class="
                                            bi
                                            bi-image
                                        "
                                    ></i>

                                </div>

                            <?php endif; ?>

                        </td>


                        <!-- ID -->

                        <td>

                            <?= (int)
                                $produto['id'] ?>

                        </td>


                        <!-- PRODUTO -->

                        <td>

                            <?= htmlspecialchars(
                                $produto['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- CATEGORIA -->

                        <td>

                            <?= htmlspecialchars(
                                $produto['categoria'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- ESTOQUE -->

                        <td>

                            <?= (int)
                                $produto['estoque'] ?>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?= htmlspecialchars(
                                $produto['status'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- AÇÕES -->

                        <td>

                            <a
                                href="<?= BASE_URL ?>/admin/produtos/editar/<?= (int) $produto['id'] ?>"
                                class="
                                    btn
                                    btn-warning
                                    btn-sm
                                "
                            >

                                Editar

                            </a>


                            <form
                                action="<?= BASE_URL ?>/admin/produtos/excluir/<?= (int) $produto['id'] ?>"
                                method="POST"
                                style="
                                    display: inline;
                                "
                            >

                                <button
                                    type="submit"
                                    class="
                                        btn
                                        btn-danger
                                        btn-sm
                                    "
                                    onclick="
                                        return confirm(
                                            'Deseja realmente excluir este produto? Esta ação não poderá ser recuperada.'
                                        );
                                    "
                                >

                                    Excluir

                                </button>

                            </form>

                        </td>


                    </tr>

                <?php endforeach; ?>


            </tbody>

        </table>

    </div>


</main>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';

