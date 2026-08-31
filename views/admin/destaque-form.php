<?php

declare(strict_types=1);

require APP_ROOT
    . '/views/layouts/admin-header.php';

$destaque =
    $destaque ?? null;

$categorias =
    $categorias ?? [];

$csrfToken =
    $csrfToken
    ?? \App\Helpers\Csrf::gerar();


$posicaoX =
    $destaque !== null
    && isset(
        $destaque['posicao_x']
    )
        ? (float)
            $destaque['posicao_x']
        : 50.00;


$posicaoY =
    $destaque !== null
    && isset(
        $destaque['posicao_y']
    )
        ? (float)
            $destaque['posicao_y']
        : 50.00;


$ativo =
    $destaque !== null
    && isset(
        $destaque['ativo']
    )
        ? (int)
            $destaque['ativo']
        : 1;

?>

<main class="admin-container">

    <section class="card-admin p-4">

        <!-- =================================
             CABEÇALHO
        ================================== -->

        <div class="mb-4">

            <h1 class="h3 mb-1">

                <?= $destaque
                    ? 'Editar Destaque'
                    : 'Novo Destaque'
                ?>

            </h1>


            <p class="text-muted mb-0">

                <?= $destaque
                    ? 'Atualize a imagem de destaque da categoria.'
                    : 'Cadastre uma imagem de destaque para uma categoria.'
                ?>

            </p>

        </div>


        <!-- =================================
             FORMULÁRIO
        ================================== -->

        <form
            method="POST"
            enctype="multipart/form-data"
            action="<?= BASE_URL ?><?= $destaque
                ? '/admin/destaques/atualizar/'
                    . (int) $destaque['id']
                : '/admin/destaques/salvar'
            ?>"
        >

            <!-- CSRF -->

            <input
                type="hidden"
                name="_token"
                value="<?= htmlspecialchars(
                    $csrfToken,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <!-- =================================
                 POSIÇÃO DA IMAGEM
            ================================== -->

            <input
                type="hidden"
                name="posicao_x"
                id="posicao_x"
                value="<?= htmlspecialchars(
                    (string) $posicaoX,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <input
                type="hidden"
                name="posicao_y"
                id="posicao_y"
                value="<?= htmlspecialchars(
                    (string) $posicaoY,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <!-- =================================
                 CATEGORIA
            ================================== -->

            <div class="mb-4">

                <label
                    for="categoria_id"
                    class="form-label"
                >

                    Categoria

                </label>


                <select
                    id="categoria_id"
                    name="categoria_id"
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
                            <?= (
                                $destaque
                                &&
                                (int)
                                    $destaque[
                                        'categoria_id'
                                    ]
                                    ===
                                    (int)
                                        $categoria['id']
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >

                            <?= htmlspecialchars(
                                (string)
                                    $categoria['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <div class="form-text">

                    Escolha a categoria que receberá
                    esta imagem de destaque.

                </div>

            </div>


            <!-- =================================
                 TEXTO ALTERNATIVO
            ================================== -->

            <div class="mb-4">

                <label
                    for="texto_alternativo"
                    class="form-label"
                >

                    Texto alternativo

                </label>


                <input
                    type="text"
                    id="texto_alternativo"
                    name="texto_alternativo"
                    class="form-control"
                    maxlength="255"
                    value="<?= htmlspecialchars(
                        (string) (
                            $destaque[
                                'texto_alternativo'
                            ]
                            ?? ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >


                <div class="form-text">

                    Descrição da imagem para acessibilidade.

                </div>

            </div>


            <!-- =================================
                 STATUS
            ================================== -->

            <div class="mb-4">

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
                        <?= $ativo === 1
                            ? 'selected'
                            : ''
                        ?>
                    >

                        Ativo

                    </option>


                    <option
                        value="0"
                        <?= $ativo === 0
                            ? 'selected'
                            : ''
                        ?>
                    >

                        Inativo

                    </option>

                </select>

            </div>


            <!-- =================================
                 IMAGEM
            ================================== -->

            <div class="mb-4">

                <label
                    for="imagem"
                    class="form-label"
                >

                    Imagem de destaque

                </label>


                <!-- =================================
                     PREVIEW
                ================================== -->

                <div
                    id="imagem-preview-container"
                    style="
                        position: relative;
                        width: 100%;
                        max-width: 720px;
                        height: 280px;
                        margin-bottom: 12px;
                        border-radius: 16px;
                        overflow: hidden;
                        background: #f8fafc;
                        border: 1px dashed #cbd5e1;
                        cursor: default;
                        user-select: none;
                        touch-action: none;
                    "
                >

                    <?php if (
                        $destaque !== null
                        &&
                        !empty(
                            $destaque[
                                'url_imagem'
                            ]
                        )
                    ): ?>

                        <img
                            id="imagem-preview"
                            src="<?= BASE_URL
                                . $destaque[
                                    'url_imagem'
                                ] ?>"
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
                            draggable="false"
                            style="
                                width: 100%;
                                height: 100%;
                                display: block;
                                object-fit: cover;
                                object-position:
                                    <?= $posicaoX ?>%
                                    <?= $posicaoY ?>%;
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
                                class="
                                    bi
                                    bi-image
                                "
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

                    Arraste a imagem dentro da área
                    para ajustar o enquadramento.

                    Você pode mover para cima,
                    para baixo, para a esquerda
                    e para a direita.

                </div>


                <!-- =================================
                     CENTRALIZAR
                ================================== -->

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


                <!-- =================================
                     UPLOAD
                ================================== -->

                <input
                    type="file"
                    id="imagem"
                    name="imagem"
                    class="form-control"
                    accept="
                        .jpg,
                        .jpeg,
                        .png,
                        .webp,
                        image/jpeg,
                        image/png,
                        image/webp
                    "
                    <?= $destaque
                        ? ''
                        : 'required'
                    ?>
                >


                <div class="form-text">

                    Formatos permitidos:
                    JPG, PNG e WEBP.

                    Tamanho máximo:
                    5 MB.

                    <?php if (
                        $destaque
                    ): ?>

                        Envie uma nova imagem
                        para substituir a atual.

                    <?php endif; ?>

                </div>


                <!-- =================================
                     EXCLUIR
                ================================== -->

                <?php if (
                    $destaque !== null
                    &&
                    !empty(
                        $destaque[
                            'url_imagem'
                        ]
                    )
                ): ?>

                    <div
                        class="
                            form-check
                            mt-3
                        "
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


            <!-- =================================
                 BOTÕES
            ================================== -->

            <div class="mt-4">

                <button
                    class="
                        btn
                        btn-success
                    "
                    type="submit"
                >

                    <?= $destaque
                        ? 'Atualizar'
                        : 'Salvar'
                    ?>

                </button>


                <a
                    href="<?= BASE_URL ?>/admin/destaques"
                    class="
                        btn
                        btn-secondary
                    "
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
                'Pré-visualização da imagem de destaque';


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


            posicaoXInicial =
                50;


            posicaoYInicial =
                50;


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


        previewContainer.addEventListener(
            'lostpointercapture',
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


                posicaoXInicial =
                    50;


                posicaoYInicial =
                    50;
            }
        );


        if (
            imagem
        ) {

            atualizarPosicao(
                posicaoXInicial,
                posicaoYInicial
            );


            previewContainer.style.cursor =
                'grab';
        }

    }
);

</script>


<?php

require APP_ROOT
    . '/views/layouts/admin-footer.php';