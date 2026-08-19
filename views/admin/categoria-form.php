<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

$categoria =
    $categoria ?? null;

?>

<main class="admin-container">

    <section class="card-admin p-4">

        <div class="mb-4">

            <h1 class="h3 mb-1">

                <?= $categoria
                    ? 'Editar Categoria'
                    : 'Nova Categoria'
                ?>

            </h1>

            <p class="text-muted mb-0">

                <?= $categoria
                    ? 'Atualize os dados da categoria.'
                    : 'Cadastre uma nova categoria.'
                ?>

            </p>

        </div>


        <form
            method="POST"
            action="<?= BASE_URL ?><?= $categoria
                ? '/admin/categorias/atualizar/'
                    . $categoria['id']
                : '/admin/categorias/salvar'
            ?>"
        >


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

                <label
                    for="nome"
                    class="form-label"
                >
                    Nome
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    class="form-control"
                    required
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $categoria['nome'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>


            <div class="mb-3">

                <label
                    for="slug"
                    class="form-label"
                >
                    Slug
                </label>

                <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="form-control"
                    required
                    maxlength="120"
                    value="<?= htmlspecialchars(
                        $categoria['slug'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>


            <div class="mb-3">

                <label
                    for="descricao"
                    class="form-label"
                >
                    Descrição
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    class="form-control"
                    rows="4"
                    maxlength="255"
                ><?= htmlspecialchars(
                    $categoria['descricao'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

            </div>


            <div class="row">


                <div class="col-md-4 mb-3">

                    <label
                        for="preco"
                        class="form-label"
                    >
                        Preço
                    </label>

                    <input
                        type="number"
                        id="preco"
                        name="preco"
                        class="form-control"
                        min="0"
                        step="0.01"
                        required
                        value="<?= htmlspecialchars(
                            (string) (
                                $categoria['preco']
                                ?? '0.00'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <div class="col-md-4 mb-3">

                    <label
                        for="preco_revenda"
                        class="form-label"
                    >
                        Preço de revenda
                    </label>

                    <input
                        type="number"
                        id="preco_revenda"
                        name="preco_revenda"
                        class="form-control"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            (string) (
                                $categoria[
                                    'preco_revenda'
                                ]
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <div class="col-md-4 mb-3">

                    <label
                        for="quantidade_minima_revenda"
                        class="form-label"
                    >
                        Mínimo para revenda
                    </label>

                    <input
                        type="number"
                        id="quantidade_minima_revenda"
                        name="quantidade_minima_revenda"
                        class="form-control"
                        min="1"
                        value="<?= htmlspecialchars(
                            (string) (
                                $categoria[
                                    'quantidade_minima_revenda'
                                ]
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


            </div>


            <div class="row">


                <div class="col-md-4 mb-3">

                    <label
                        for="ativo"
                        class="form-label"
                    >
                        Status
                    </label>

                    <select
                        id="ativo"
                        name="ativo"
                        class="form-control"
                    >

                        <option
                            value="1"
                            <?= (
                                !isset(
                                    $categoria['ativo']
                                )
                                ||
                                (int)
                                    $categoria['ativo']
                                    === 1
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Ativa
                        </option>

                        <option
                            value="0"
                            <?= (
                                isset(
                                    $categoria['ativo']
                                )
                                &&
                                (int)
                                    $categoria['ativo']
                                    === 0
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Inativa
                        </option>

                    </select>

                </div>


                <div class="col-md-4 mb-3">

                    <label
                        for="destaque"
                        class="form-label"
                    >
                        Destaque
                    </label>

                    <select
                        id="destaque"
                        name="destaque"
                        class="form-control"
                    >

                        <option
                            value="0"
                            <?= (
                                !isset(
                                    $categoria['destaque']
                                )
                                ||
                                (int)
                                    $categoria['destaque']
                                    === 0
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Normal
                        </option>

                        <option
                            value="1"
                            <?= (
                                isset(
                                    $categoria['destaque']
                                )
                                &&
                                (int)
                                    $categoria['destaque']
                                    === 1
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Destaque
                        </option>

                    </select>

                </div>


                <div class="col-md-4 mb-3">

                    <label
                        for="ordem_destaque"
                        class="form-label"
                    >
                        Ordem do destaque
                    </label>

                    <input
                        type="number"
                        id="ordem_destaque"
                        name="ordem_destaque"
                        class="form-control"
                        min="0"
                        value="<?= htmlspecialchars(
                            (string) (
                                $categoria[
                                    'ordem_destaque'
                                ]
                                ?? 0
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


            </div>


            <div class="mt-4">

                <button
                    class="btn btn-success"
                    type="submit"
                >

                    <?= $categoria
                        ? 'Atualizar'
                        : 'Salvar'
                    ?>

                </button>


                <a
                    href="<?= BASE_URL ?>/admin/categorias"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>


        </form>

    </section>

</main>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';