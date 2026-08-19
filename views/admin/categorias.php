<?php

declare(strict_types=1);

use App\Helpers\View;

require APP_ROOT
    . '/views/layouts/admin-header.php';?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Categorias
            </h1>

            <p class="text-muted mb-0">
                Gerencie as categorias do cardápio.
            </p>
        </div>

    </div>


    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>
                                Ordem
                            </th>

                            <th>
                                Categoria
                            </th>

                            <th>
                                Preço
                            </th>

                            <th>
                                Revenda
                            </th>

                            <th>
                                Destaque
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach (
                            $categorias
                            as $categoria
                        ): ?>

                            <tr>

                                <td>
                                    <?= (int)
                                        $categoria[
                                            'ordem_destaque'
                                        ] ?>
                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            (string)
                                                $categoria[
                                                    'nome'
                                                ]
                                        ) ?>
                                    </strong>

                                    <?php if (
                                        !empty(
                                            $categoria[
                                                'descricao'
                                            ]
                                        )
                                    ): ?>

                                        <br>

                                        <small
                                            class="text-muted"
                                        >
                                            <?= htmlspecialchars(
                                                (string)
                                                    $categoria[
                                                        'descricao'
                                                    ]
                                            ) ?>
                                        </small>

                                    <?php endif; ?>

                                </td>


                                <td>
                                    R$
                                    <?= number_format(
                                        (float)
                                            $categoria[
                                                'preco'
                                            ],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>


                                <td>

                                    <?php if (
                                        $categoria[
                                            'preco_revenda'
                                        ] !== null
                                    ): ?>

                                        R$
                                        <?= number_format(
                                            (float)
                                                $categoria[
                                                    'preco_revenda'
                                                ],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="text-muted"
                                        >
                                            Não definido
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if (
                                        (int)
                                            $categoria[
                                                'destaque'
                                            ] === 1
                                    ): ?>

                                        <span
                                            class="badge bg-warning text-dark"
                                        >
                                            Destaque
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-secondary"
                                        >
                                            Normal
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if (
                                        (int)
                                            $categoria[
                                                'ativo'
                                            ] === 1
                                    ): ?>

                                        <span
                                            class="badge bg-success"
                                        >
                                            Ativa
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-danger"
                                        >
                                            Inativa
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';
?>