<?php

declare(strict_types=1);

use App\Helpers\View;

$tituloPagina = 'Novo destaque da Home';

require APP_ROOT . '/views/layouts/admin-header.php';

$categorias = $categorias ?? [];
$produtos = $produtos ?? [];
$destaqueHome = $destaqueHome ?? null;
$tipoAtual = (string) ($destaqueHome['tipo'] ?? '');
$itemAtual = (int) ($destaqueHome['item_id'] ?? 0);

?>

<main class="admin-container destaque-home-editor">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                <?= $destaqueHome !== null ? 'Editar destaque da Home' : 'Novo destaque da Home' ?>
            </h1>

            <p class="text-muted mb-0">
                <?= $destaqueHome !== null ? 'Atualize o item e a posição desta vitrine.' : 'Escolha uma categoria ou produto para exibir na página inicial.' ?>
            </p>

        </div>

        <a
            href="<?= htmlspecialchars(
                BASE_URL . '/admin/destaques-home',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Voltar
        </a>

    </div>


    <div class="row justify-content-center">

        <div class="col-12 col-xl-8">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            BASE_URL . ($destaqueHome !== null ? '/admin/destaques-home/atualizar/' . (int) $destaqueHome['id'] : '/admin/destaques-home/salvar'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                        <!-- CSRF -->

                        <input
                            type="hidden"
                            name="_token"
                            value="<?= htmlspecialchars(
                                \App\Helpers\Csrf::gerar(),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >


                        <!-- =================================
                             TIPO DE DESTAQUE
                        ================================== -->

                        <div class="mb-4">

                            <label
                                for="tipo"
                                class="form-label fw-semibold"
                            >
                                Tipo de destaque
                            </label>

                            <select
                                name="tipo"
                                id="tipo"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Selecione...
                                </option>

                                <option value="categoria"<?= $tipoAtual === 'categoria' ? ' selected' : '' ?>>
                                    Categoria
                                </option>

                                <option value="produto"<?= $tipoAtual === 'produto' ? ' selected' : '' ?>>
                                    Produto
                                </option>

                            </select>

                            <div class="form-text">
                                Escolha se o destaque será uma categoria ou um produto.
                            </div>

                        </div>


                        <!-- =================================
                             CATEGORIA
                        ================================== -->

                        <div
                            class="mb-4"
                            id="campo-categoria"
                            style="display: none;"
                        >

                            <label
                                for="categoria_id"
                                class="form-label fw-semibold"
                            >
                                Categoria
                            </label>

                            <select
                                name="categoria_id"
                                id="categoria_id"
                                class="form-select"
                            >

                                <option value="">
                                    Selecione uma categoria...
                                </option>

                                <?php foreach ($categorias as $categoria): ?>

                                    <?php

                                    $categoriaId =
                                        (int) (
                                            $categoria['id']
                                            ?? 0
                                        );

                                    $categoriaNome =
                                        (string) (
                                            $categoria['nome']
                                            ?? ''
                                        );

                                    ?>

                                    <option
                                        value="<?= $categoriaId ?>"<?= $tipoAtual === 'categoria' && $itemAtual === $categoriaId ? ' selected' : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $categoriaNome,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <div class="form-text">
                                Somente categorias ativas podem ser adicionadas.
                            </div>

                        </div>


                        <!-- =================================
                             PRODUTO
                        ================================== -->

                        <div
                            class="mb-4"
                            id="campo-produto"
                            style="display: none;"
                        >

                            <label
                                for="produto_id"
                                class="form-label fw-semibold"
                            >
                                Produto
                            </label>

                            <select
                                name="produto_id"
                                id="produto_id"
                                class="form-select"
                            >

                                <option value="">
                                    Selecione um produto...
                                </option>

                                <?php foreach ($produtos as $produto): ?>

                                    <?php

                                    $produtoId =
                                        (int) (
                                            $produto['id']
                                            ?? 0
                                        );

                                    $produtoNome =
                                        (string) (
                                            $produto['nome']
                                            ?? ''
                                        );

                                    $categoriaNomeProduto =
                                        (string) (
                                            $produto['categoria_nome']
                                            ?? ''
                                        );

                                    ?>

                                    <option
                                        value="<?= $produtoId ?>"<?= $tipoAtual === 'produto' && $itemAtual === $produtoId ? ' selected' : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $produtoNome,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        <?php if (
                                            $categoriaNomeProduto !== ''
                                        ): ?>

                                            <?= ' | ' ?>

                                            <?= htmlspecialchars(
                                                $categoriaNomeProduto,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        <?php endif; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <div class="form-text">
                                Somente produtos ativos vinculados a categorias ativas podem ser adicionados.
                            </div>

                        </div>


                        <!-- =================================
                             ORDEM
                        ================================== -->

                        <div class="mb-4">

                            <label
                                for="ordem"
                                class="form-label fw-semibold"
                            >
                                Ordem
                            </label>

                            <input
                                type="number"
                                name="ordem"
                                id="ordem"
                                class="form-control"
                                value="<?= (int) ($destaqueHome['ordem'] ?? 0) ?>"
                                min="0"
                            >

                            <div class="form-text">
                                Quanto menor o número, mais cedo o item aparecerá nos destaques.
                                Deixe 0 para usar a ordenação automática.
                            </div>

                        </div>


                        <!-- =================================
                             BOTÕES
                        ================================== -->

                        <div class="d-flex flex-wrap gap-2 pt-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-check-lg me-1"></i>

                                <?= $destaqueHome !== null ? 'Salvar alterações' : 'Adicionar destaque' ?>

                            </button>


                            <a
                                href="<?= htmlspecialchars(
                                    BASE_URL . '/admin/destaques-home',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                class="btn btn-outline-secondary"
                            >

                                Cancelar

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</main>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const tipo =
            document.getElementById(
                'tipo'
            );


        const campoCategoria =
            document.getElementById(
                'campo-categoria'
            );


        const campoProduto =
            document.getElementById(
                'campo-produto'
            );


        const categoria =
            document.getElementById(
                'categoria_id'
            );


        const produto =
            document.getElementById(
                'produto_id'
            );


        function atualizarCampos() {

            const valor =
                tipo.value;


            campoCategoria.style.display =
                'none';


            campoProduto.style.display =
                'none';


            categoria.removeAttribute(
                'required'
            );


            produto.removeAttribute(
                'required'
            );


            if (
                valor === 'categoria'
            ) {

                campoCategoria.style.display =
                    'block';


                categoria.setAttribute(
                    'required',
                    'required'
                );

            }


            if (
                valor === 'produto'
            ) {

                campoProduto.style.display =
                    'block';


                produto.setAttribute(
                    'required',
                    'required'
                );

            }

        }


        tipo.addEventListener(
            'change',
            atualizarCampos
        );


        atualizarCampos();

    }
);

</script>

<?php require APP_ROOT . '/views/layouts/admin-footer.php'; ?>
