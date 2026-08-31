<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

$destaques =
    $destaques ?? [];

$csrfToken =
    $csrfToken
    ?? \App\Helpers\Csrf::gerar();

?>

<main class="admin-container">

    <section class="card-admin p-4">

        <!-- =================================
             CABEÇALHO
        ================================== -->

        <div
            class="
                d-flex
                justify-content-between
                align-items-center
                mb-4
            "
        >

            <div>

                <h1 class="h3 mb-1">

                    Destaques

                </h1>


                <p class="text-muted mb-0">

                    Gerencie as imagens de destaque
                    das páginas das categorias.

                </p>

            </div>


            <div>

                <a
                    href="<?= BASE_URL ?>/admin/destaques/novo"
                    class="btn btn-success"
                >

                    <i
                        class="
                            bi
                            bi-plus-lg
                            me-1
                        "
                    ></i>

                    Novo destaque

                </a>

            </div>

        </div>


        <!-- =================================
             LISTAGEM
        ================================== -->

        <?php if (
            $destaques === []
        ): ?>

            <div
                class="
                    alert
                    alert-info
                "
            >

                <i
                    class="
                        bi
                        bi-info-circle
                        me-2
                    "
                ></i>

                Nenhuma imagem de destaque
                foi cadastrada ainda.

            </div>

        <?php else: ?>


            <div class="table-responsive">

                <table
                    class="
                        table
                        table-hover
                        align-middle
                    "
                >

                    <thead>

                        <tr>

                            <th>
                                Imagem
                            </th>


                            <th>
                                Categoria
                            </th>


                            <th>
                                Posição
                            </th>


                            <th>
                                Status
                            </th>


                            <th class="text-end">
                                Ações
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $destaques
                            as $destaque
                        ): ?>


                            <?php

                            $urlImagem =
                                (string) (
                                    $destaque[
                                        'url_imagem'
                                    ]
                                    ?? ''
                                );


                            $posicaoX =
                                isset(
                                    $destaque[
                                        'posicao_x'
                                    ]
                                )
                                    ? (float)
                                        $destaque[
                                            'posicao_x'
                                        ]
                                    : 50.00;


                            $posicaoY =
                                isset(
                                    $destaque[
                                        'posicao_y'
                                    ]
                                )
                                    ? (float)
                                        $destaque[
                                            'posicao_y'
                                        ]
                                    : 50.00;


                            $ativo =
                                (
                                    (int)
                                    (
                                        $destaque[
                                            'ativo'
                                        ]
                                        ?? 0
                                    )
                                ) === 1;

                            ?>


                            <tr>


                                <!-- =================================
                                     IMAGEM
                                ================================== -->

                                <td>

                                    <?php if (
                                        $urlImagem !== ''
                                    ): ?>

                                        <div
                                            style="
                                                width: 180px;
                                                height: 90px;
                                                overflow: hidden;
                                                border-radius: 12px;
                                                background: #f8fafc;
                                                border: 1px solid #e5e7eb;
                                            "
                                        >

                                            <img
                                                src="<?= BASE_URL
                                                    . $urlImagem ?>"
                                                alt="<?= htmlspecialchars(
                                                    (string) (
                                                        $destaque[
                                                            'texto_alternativo'
                                                        ]
                                                        ?? 'Imagem de destaque'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                style="
                                                    width: 100%;
                                                    height: 100%;
                                                    display: block;
                                                    object-fit: cover;
                                                    object-position:
                                                        <?= $posicaoX ?>%
                                                        <?= $posicaoY ?>%;
                                                "
                                            >

                                        </div>

                                    <?php else: ?>

                                        <div
                                            style="
                                                width: 180px;
                                                height: 90px;
                                                border-radius: 12px;
                                                background: #f8fafc;
                                                border: 1px dashed #cbd5e1;
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
                                                    fs-3
                                                "
                                            ></i>

                                        </div>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     CATEGORIA
                                ================================== -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            (string) (
                                                $destaque[
                                                    'categoria'
                                                ]
                                                ?? 'Categoria'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </strong>


                                    <?php if (
                                        !empty(
                                            $destaque[
                                                'categoria_slug'
                                            ]
                                        )
                                    ): ?>

                                        <br>

                                        <small
                                            class="text-muted"
                                        >

                                            /

                                            <?= htmlspecialchars(
                                                (string)
                                                    $destaque[
                                                        'categoria_slug'
                                                    ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </small>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     POSIÇÃO
                                ================================== -->

                                <td>

                                    <small
                                        class="text-muted"
                                    >

                                        X:

                                        <?= number_format(
                                            $posicaoX,
                                            2,
                                            ',',
                                            '.'
                                        ) ?>%

                                        <br>

                                        Y:

                                        <?= number_format(
                                            $posicaoY,
                                            2,
                                            ',',
                                            '.'
                                        ) ?>%

                                    </small>

                                </td>


                                <!-- =================================
                                     STATUS
                                ================================== -->

                                <td>

                                    <?php if (
                                        $ativo
                                    ): ?>

                                        <span
                                            class="
                                                badge
                                                bg-success
                                            "
                                        >

                                            Ativo

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="
                                                badge
                                                bg-secondary
                                            "
                                        >

                                            Inativo

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     AÇÕES
                                ================================== -->

                                <td>

                                    <div
                                        class="
                                            d-flex
                                            justify-content-end
                                            gap-2
                                        "
                                    >


                                        <!-- EDITAR -->

                                        <a
                                            href="<?= BASE_URL
                                                ?>/admin/destaques/editar/<?= (int) $destaque['id'] ?>"
                                            class="
                                                btn
                                                btn-primary
                                                btn-sm
                                            "
                                        >

                                            <i
                                                class="
                                                    bi
                                                    bi-pencil
                                                "
                                            ></i>

                                            Editar

                                        </a>


                                        <!-- ATIVAR / DESATIVAR -->

                                        <form
                                            method="POST"
                                            action="<?= BASE_URL
                                                ?>/admin/destaques/alternar/<?= (int) $destaque['id'] ?>"
                                            class="m-0"
                                        >

                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="<?= htmlspecialchars(
                                                    $csrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="
                                                    btn
                                                    btn-outline-secondary
                                                    btn-sm
                                                "
                                                title="<?= $ativo
                                                    ? 'Desativar'
                                                    : 'Ativar'
                                                ?>"
                                            >

                                                <i
                                                    class="
                                                        bi
                                                        <?= $ativo
                                                            ? 'bi-eye-slash'
                                                            : 'bi-eye'
                                                        ?>"
                                                ></i>

                                                <?= $ativo
                                                    ? 'Desativar'
                                                    : 'Ativar'
                                                ?>

                                            </button>

                                        </form>


                                        <!-- EXCLUIR -->

                                        <form
                                            method="POST"
                                            action="<?= BASE_URL
                                                ?>/admin/destaques/excluir/<?= (int) $destaque['id'] ?>"
                                            class="m-0"
                                            onsubmit="
                                                return confirm(
                                                    'Tem certeza que deseja excluir esta imagem de destaque?'
                                                );
                                            "
                                        >

                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="<?= htmlspecialchars(
                                                    $csrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="
                                                    btn
                                                    btn-outline-danger
                                                    btn-sm
                                                "
                                            >

                                                <i
                                                    class="
                                                        bi
                                                        bi-trash
                                                    "
                                                ></i>

                                                Excluir

                                            </button>

                                        </form>


                                    </div>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        <?php endif; ?>


    </section>

</main>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';