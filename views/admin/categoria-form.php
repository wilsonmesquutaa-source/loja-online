<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

$categoria =
    $categoria ?? null;

$imagemCategoria =
    $imagemCategoria ?? null;


$posicaoX =
    $imagemCategoria !== null
    && isset(
        $imagemCategoria['posicao_x']
    )
        ? (float)
            $imagemCategoria['posicao_x']
        : 50.00;


$posicaoY =
    $imagemCategoria !== null
    && isset(
        $imagemCategoria['posicao_y']
    )
        ? (float)
            $imagemCategoria['posicao_y']
        : 50.00;

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
            enctype="multipart/form-data"
            action="<?= BASE_URL ?><?= $categoria
                ? '/admin/categorias/atualizar/'
                    . $categoria['id']
                : '/admin/categorias/salvar'
            ?>"
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


            <!-- POSIÇÃO -->

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


            <!-- =================================
                 IMAGEM
            ================================== -->

            <div class="mb-4">

                <label
                    for="imagem"
                    class="form-label"
                >
                    Imagem da categoria
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
                        cursor: grab;
                        user-select: none;
                        touch-action: none;
                    "
                >

                    <?php if (
                        $imagemCategoria !== null
                        &&
                        !empty(
                            $imagemCategoria[
                                'url_imagem'
                            ]
                        )
                    ): ?>

                        <img
                            id="imagem-preview"
                            src="<?= BASE_URL
                                . $imagemCategoria[
                                    'url_imagem'
                                ] ?>"
                            alt="Pré-visualização da imagem"
                            draggable="false"
                            style="
                                width: 100%;
                                height: 100%;
                                display: block;
                                object-fit: cover;
                                object-position:
                                    <?= htmlspecialchars(
                                        (string)
                                        $posicaoX,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>%
                                    <?= htmlspecialchars(
                                        (string)
                                        $posicaoY,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>%;
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
                                color: #94a3b8;
                                gap: 8px;
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
                    id="imagem-ajuda"
                >

                    Escolha uma imagem e arraste dentro
                    da área acima para ajustar o enquadramento.

                </div>


                <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm mb-3"
                    id="imagem-centralizar"
                >

                    <i
                        class="bi bi-arrows-fullscreen me-1"
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
                        $imagemCategoria !== null
                    ): ?>

                        Envie uma nova imagem para
                        substituir a atual.

                    <?php endif; ?>

                </div>


                <?php if (
                    $imagemCategoria !== null
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
                    x + '% ' + y + '%';
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
                'Pré-visualização da imagem';


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

            imagem.style.objectPosition =
                posicaoXInicial
                + '% '
                + posicaoYInicial
                + '%';

            previewContainer.style.cursor =
                'grab';
        }

    }
);

</script>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';