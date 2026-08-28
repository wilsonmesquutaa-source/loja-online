<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

$produto =
    $produto ?? null;

$imagemProduto =
    $imagemProduto ?? null;


$posicaoX =
    $imagemProduto !== null
    && isset(
        $imagemProduto['posicao_x']
    )
        ? (float)
            $imagemProduto['posicao_x']
        : 50.00;


$posicaoY =
    $imagemProduto !== null
    && isset(
        $imagemProduto['posicao_y']
    )
        ? (float)
            $imagemProduto['posicao_y']
        : 50.00;

?>

<main class="admin-container">

    <section class="card-admin p-4">


        <div class="mb-4">

            <h1 class="h3 mb-1">

                <?= $produto
                    ? 'Editar Produto'
                    : 'Novo Produto'
                ?>

            </h1>


            <p class="text-muted mb-0">

                <?= $produto
                    ? 'Atualize os dados do produto.'
                    : 'Cadastre um novo produto.'
                ?>

            </p>

        </div>


        <form
            method="POST"
            enctype="multipart/form-data"
            action="<?= BASE_URL ?><?= $produto
                ? '/admin/produtos/atualizar/'
                    . $produto['id']
                : '/admin/produtos/salvar'
            ?>"
        >


            <!-- CSRF -->

            <?php if (
                isset($csrfToken)
            ): ?>

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


            <!-- POSIÇÃO DA IMAGEM -->

            <input
                type="hidden"
                name="posicao_x"
                id="posicao_x"
                value="<?= htmlspecialchars(
                    (string)
                    $posicaoX,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <input
                type="hidden"
                name="posicao_y"
                id="posicao_y"
                value="<?= htmlspecialchars(
                    (string)
                    $posicaoY,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <!-- CATEGORIA -->

            <div class="mb-3">

                <label
                    class="form-label"
                    for="categoria_id"
                >
                    Categoria
                </label>


                <select
                    name="categoria_id"
                    id="categoria_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        Selecione uma categoria
                    </option>


                    <?php foreach (
                        $categorias
                        as $categoria
                    ): ?>

                        <option
                            value="<?= (int)
                                $categoria['id'] ?>"
                            <?= $produto
                                &&
                                (int)
                                    $produto[
                                        'categoria_id'
                                    ]
                                    ===
                                    (int)
                                    $categoria['id']
                                    ? 'selected'
                                    : ''
                            ?>
                        >

                            <?= htmlspecialchars(
                                $categoria['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- NOME -->

            <div class="mb-3">

                <label
                    class="form-label"
                    for="nome"
                >
                    Nome
                </label>


                <input
                    type="text"
                    name="nome"
                    id="nome"
                    class="form-control"
                    required
                    maxlength="150"
                    value="<?= htmlspecialchars(
                        $produto['nome'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>


            <!-- SLUG -->

            <div class="mb-3">

                <label
                    class="form-label"
                    for="slug"
                >
                    Slug
                </label>


                <input
                    type="text"
                    name="slug"
                    id="slug"
                    class="form-control"
                    required
                    maxlength="180"
                    value="<?= htmlspecialchars(
                        $produto['slug'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>


            <!-- DESCRIÇÃO -->

            <div class="mb-3">

                <label
                    class="form-label"
                    for="descricao"
                >
                    Descrição
                </label>


                <textarea
                    name="descricao"
                    id="descricao"
                    class="form-control"
                    rows="4"
                    maxlength="500"
                ><?= htmlspecialchars(
                    $produto['descricao'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

            </div>


            <!-- ESTOQUE -->

            <div class="mb-3">

                <label
                    class="form-label"
                    for="estoque"
                >
                    Estoque
                </label>


                <input
                    type="number"
                    name="estoque"
                    id="estoque"
                    class="form-control"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars(
                        (string)
                        (
                            $produto['estoque']
                            ?? 0
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>


            <!-- =================================
                 IMAGEM
            ================================== -->

            <div class="mb-4">

                <label
                    for="imagem"
                    class="form-label"
                >
                    Imagem do produto
                </label>


                <div
                    id="imagem-preview-container"
                    style="
                        position: relative;
                        width: 100%;
                        max-width: 420px;
                        height: 220px;
                        margin-bottom: 12px;
                        border-radius: 14px;
                        overflow: hidden;
                        background: #f8fafc;
                        border: 1px dashed #cbd5e1;
                        cursor: default;
                        user-select: none;
                        touch-action: none;
                    "
                >

                    <?php if (
                        $imagemProduto !== null
                        &&
                        !empty(
                            $imagemProduto[
                                'url_imagem'
                            ]
                        )
                    ): ?>

                        <img
                            id="imagem-preview"
                            src="<?= BASE_URL
                                . $imagemProduto[
                                    'url_imagem'
                                ] ?>"
                            alt="Pré-visualização do produto"
                            draggable="false"
                            style="
                                width: 100%;
                                height: 100%;
                                display: block;
                                object-fit: cover;
                                object-position:
                                    <?= (float)
                                        $posicaoX ?>%
                                    <?= (float)
                                        $posicaoY ?>%;
                                pointer-events: none;
                            "
                        >

                    <?php else: ?>

                        <div
                            id="imagem-preview-placeholder"
                            style="
                                width: 100%;
                                height: 100%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-direction: column;
                                gap: 8px;
                                color: #94a3b8;
                            "
                        >

                            <i
                                class="bi bi-image"
                                style="
                                    font-size: 3rem;
                                "
                            ></i>


                            <span>
                                Nenhuma imagem selecionada
                            </span>

                        </div>

                    <?php endif; ?>

                </div>


                <div
                    class="form-text mb-2"
                >

                    Escolha uma imagem e arraste dentro
                    da área acima para ajustar o enquadramento.

                </div>


                <button
                    type="button"
                    class="
                        btn
                        btn-outline-secondary
                        btn-sm
                        mb-3
                    "
                    id="imagem-centralizar"
                >

                    <i
                        class="
                            bi
                            bi-arrows-fullscreen
                            me-1
                        "
                    ></i>

                    Centralizar imagem

                </button>


                <input
                    type="file"
                    id="imagem"
                    name="imagem"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >


                <div class="form-text">

                    Formatos permitidos:
                    JPG, PNG e WEBP.
                    Tamanho máximo: 5 MB.

                    <?php if (
                        $imagemProduto !== null
                    ): ?>

                        Envie uma nova imagem para
                        substituir a atual.

                    <?php endif; ?>

                </div>


                <?php if (
                    $imagemProduto !== null
                ): ?>

                    <div
                        class="form-check mt-3"
                    >

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="excluir_imagem"
                            value="1"
                            id="excluir_imagem"
                        >


                        <label
                            class="form-check-label"
                            for="excluir_imagem"
                        >

                            Excluir imagem atual

                        </label>

                    </div>

                <?php endif; ?>

            </div>


            <!-- BOTÕES -->

            <div class="mt-4">

                <button
                    class="btn btn-success"
                    type="submit"
                >

                    <?= $produto
                        ? 'Atualizar'
                        : 'Salvar'
                    ?>

                </button>


                <a
                    href="<?= BASE_URL ?>/admin/produtos"
                    class="btn btn-secondary"
                >

                    Cancelar

                </a>

            </div>


        </form>

    </section>

</main>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const campoImagem =
            document.getElementById(
                'imagem'
            );


        const previewContainer =
            document.getElementById(
                'imagem-preview-container'
            );


        const centralizarBotao =
            document.getElementById(
                'imagem-centralizar'
            );


        const campoPosicaoX =
            document.getElementById(
                'posicao_x'
            );


        const campoPosicaoY =
            document.getElementById(
                'posicao_y'
            );


        if (
            !campoImagem ||
            !previewContainer ||
            !centralizarBotao ||
            !campoPosicaoX ||
            !campoPosicaoY
        ) {

            return;
        }


        let imagem =
            document.getElementById(
                'imagem-preview'
            );


        let arrastando =
            false;


        let inicioX =
            0;


        let inicioY =
            0;


        let posicaoXInicial =
            parseFloat(
                campoPosicaoX.value
            )
            || 50;


        let posicaoYInicial =
            parseFloat(
                campoPosicaoY.value
            )
            || 50;


        function limitar(
            valor,
            minimo,
            maximo
        ) {

            return Math.min(
                Math.max(
                    valor,
                    minimo
                ),
                maximo
            );
        }


        function atualizarPosicao(
            x,
            y
        ) {

            x =
                limitar(
                    x,
                    0,
                    100
                );


            y =
                limitar(
                    y,
                    0,
                    100
                );


            campoPosicaoX.value =
                x.toFixed(2);


            campoPosicaoY.value =
                y.toFixed(2);


            if (
                imagem
            ) {

                imagem.style.objectPosition =
                    x
                    + '% '
                    + y
                    + '%';
            }
        }


        function criarImagemPreview(
            src
        ) {

            previewContainer.innerHTML =
                '';


            imagem =
                document.createElement(
                    'img'
                );


            imagem.id =
                'imagem-preview';


            imagem.src =
                src;


            imagem.alt =
                'Pré-visualização do produto';


            imagem.draggable =
                false;


            imagem.style.width =
                '100%';


            imagem.style.height =
                '100%';


            imagem.style.display =
                'block';


            imagem.style.objectFit =
                'cover';


            imagem.style.objectPosition =
                '50% 50%';


            imagem.style.pointerEvents =
                'none';


            previewContainer.appendChild(
                imagem
            );


            campoPosicaoX.value =
                '50.00';


            campoPosicaoY.value =
                '50.00';


            previewContainer.style.cursor =
                'grab';
        }


        campoImagem.addEventListener(
            'change',
            function () {

                const arquivo =
                    this.files &&
                    this.files[0]
                        ? this.files[0]
                        : null;


                if (
                    !arquivo
                ) {

                    return;
                }


                const tiposPermitidos = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                if (
                    !tiposPermitidos.includes(
                        arquivo.type
                    )
                ) {

                    alert(
                        'Selecione uma imagem JPG, PNG ou WEBP.'
                    );


                    this.value =
                        '';


                    return;
                }


                const tamanhoMaximo =
                    5 * 1024 * 1024;


                if (
                    arquivo.size >
                    tamanhoMaximo
                ) {

                    alert(
                        'A imagem deve possuir no máximo 5 MB.'
                    );


                    this.value =
                        '';


                    return;
                }


                const leitor =
                    new FileReader();


                leitor.onload =
                    function (
                        evento
                    ) {

                        criarImagemPreview(
                            evento.target.result
                        );
                    };


                leitor.readAsDataURL(
                    arquivo
                );
            }
        );


        previewContainer.addEventListener(
            'pointerdown',
            function (
                evento
            ) {

                if (
                    !imagem
                ) {

                    return;
                }


                arrastando =
                    true;


                inicioX =
                    evento.clientX;


                inicioY =
                    evento.clientY;


                posicaoXInicial =
                    parseFloat(
                        campoPosicaoX.value
                    )
                    || 50;


                posicaoYInicial =
                    parseFloat(
                        campoPosicaoY.value
                    )
                    || 50;


                previewContainer.style.cursor =
                    'grabbing';


                previewContainer.setPointerCapture(
                    evento.pointerId
                );
            }
        );


        previewContainer.addEventListener(
            'pointermove',
            function (
                evento
            ) {

                if (
                    !arrastando
                    ||
                    !imagem
                ) {

                    return;
                }


                const largura =
                    previewContainer.clientWidth
                    || 1;


                const altura =
                    previewContainer.clientHeight
                    || 1;


                const deslocamentoX =
                    (
                        evento.clientX
                        -
                        inicioX
                    )
                    /
                    largura
                    *
                    100;


                const deslocamentoY =
                    (
                        evento.clientY
                        -
                        inicioY
                    )
                    /
                    altura
                    *
                    100;


                atualizarPosicao(
                    posicaoXInicial
                    +
                    deslocamentoX,

                    posicaoYInicial
                    +
                    deslocamentoY
                );
            }
        );


        function finalizarArraste() {

            if (
                !arrastando
            ) {

                return;
            }


            arrastando =
                false;


            previewContainer.style.cursor =
                imagem
                    ? 'grab'
                    : 'default';
        }


        previewContainer.addEventListener(
            'pointerup',
            finalizarArraste
        );


        previewContainer.addEventListener(
            'pointercancel',
            finalizarArraste
        );


        centralizarBotao.addEventListener(
            'click',
            function () {

                if (
                    !imagem
                ) {

                    return;
                }


                atualizarPosicao(
                    50,
                    50
                );
            }
        );


        if (
            imagem
        ) {

            previewContainer.style.cursor =
                'grab';
        }

    }
);

</script>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';

