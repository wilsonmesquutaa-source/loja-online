<?php

declare(strict_types=1);

use App\Helpers\View;

require APP_ROOT
    . '/views/layouts/site/header.php';

View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
        $rotaAtual,
    ]
);

$erro =
    $_GET['erro']
    ?? null;

$sucesso =
    $_GET['sucesso']
    ?? null;

?>

<main class="cliente-editar-page">

    <div class="cliente-editar-container">

        <header class="cliente-editar-header">

            <p class="cliente-editar-etiqueta">
                Minha conta
            </p>

            <h1>
                Editar perfil
            </h1>

            <p>
                Atualize seus dados pessoais e sua foto de perfil.
            </p>

        </header>

        <?php if ($erro !== null): ?>

            <div
                class="cliente-editar-alert cliente-editar-alert-danger"
                role="alert">
                <?= htmlspecialchars(
                    (string) $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>

        <?php if ($sucesso !== null): ?>

            <div
                class="cliente-editar-alert cliente-editar-alert-success"
                role="alert">
                <?= htmlspecialchars(
                    (string) $sucesso,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>

        <section class="cliente-editar-card">

            <div class="cliente-editar-card-body">

                <form
                    method="POST"
                    action="<?= BASE_URL ?>/cliente/perfil/atualizar"
                    enctype="multipart/form-data"
                    class="cliente-editar-form">

                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?= htmlspecialchars(
                                    $csrfToken,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                    <div class="cliente-editar-foto-area">

                        <div
                            class="cliente-editar-foto-editor"
                            id="fotoEditor">

                            <div
                                class="cliente-editar-foto-preview"
                                id="fotoPreview">

                                <?php if (
                                    !empty($cliente['foto_url'])
                                ): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                                    (string)
                                                    $cliente['foto_url'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        id="fotoImagem"
                                        alt="Foto de perfil">

                                <?php else: ?>

                                    <div
                                        class="cliente-editar-foto-placeholder"
                                        id="fotoPlaceholder">
                                        <span>👤</span>
                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="cliente-editar-foto-titulo">
                            Foto de perfil
                        </div>

                        <div class="cliente-editar-upload">

                            <input
                                type="file"
                                id="foto"
                                name="foto"
                                accept="image/jpeg,image/png,image/webp">

                            <small>
                                JPG, PNG ou WEBP. Máximo de 5 MB.
                            </small>

                        </div>

                        <div
                            class="cliente-editar-foto-controles"
                            id="fotoControles"
                            hidden>

                            <label for="fotoZoom">
                                Ajustar enquadramento
                            </label>

                            <input
                                type="range"
                                id="fotoZoom"
                                min="1"
                                max="3"
                                step="0.01"
                                value="1">

                            <button
                                type="button"
                                id="fotoCentralizar">
                                Centralizar foto
                            </button>

                        </div>

                    </div>

                    <div class="cliente-editar-form-group">

                        <label for="nome">
                            Nome completo
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            maxlength="150"
                            required
                            value="<?= htmlspecialchars(
                                        (string)
                                        (
                                            $cliente['nome']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                    </div>

                    <div class="cliente-editar-form-group">

                        <label for="email">
                            E-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="180"
                            required
                            value="<?= htmlspecialchars(
                                        (string)
                                        (
                                            $cliente['email']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                    </div>

                    <div class="cliente-editar-acoes">

                        <button
                            type="submit"
                            class="btn-marca">
                            Salvar alterações
                        </button>

                        <a
                            href="<?= BASE_URL ?>/"
                            class="btn-voltar-loja">
                            Voltar para a loja
                        </a>

                    </div>

                </form>

            </div>

        </section>

    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const inputFoto =
            document.getElementById('foto');

        const preview =
            document.getElementById('fotoPreview');

        const controles =
            document.getElementById('fotoControles');

        const zoom =
            document.getElementById('fotoZoom');

        const centralizar =
            document.getElementById('fotoCentralizar');

        const formulario =
            document.querySelector('.cliente-editar-form');


        if (
            !inputFoto ||
            !preview ||
            !controles ||
            !zoom ||
            !centralizar ||
            !formulario
        ) {
            return;
        }


        let imagem =
            document.getElementById('fotoImagem');

        let posX = 0;
        let posY = 0;

        /*
        Escala aplicada pelo usuário através
        do controle de zoom.
        */
        let escala = 1;

        let arrastando = false;

        let inicioX = 0;
        let inicioY = 0;


        /*
        =========================================================
        TAMANHO DO EDITOR
        =========================================================
        */

        function obterTamanhoEditor() {

            return preview.clientWidth;
        }


        /*
        =========================================================
        ESCALA INICIAL DA FOTO
        =========================================================
        */

        function obterEscalaInicial() {

            if (!imagem) {
                return 1;
            }

            const largura =
                imagem.naturalWidth;

            const altura =
                imagem.naturalHeight;

            const tamanho =
                obterTamanhoEditor();


            if (
                !largura ||
                !altura
            ) {
                return 1;
            }


            /*
            "Cover":

            A imagem precisa ser grande o bastante
            para preencher todo o círculo.
            */

            return Math.max(
                tamanho / largura,
                tamanho / altura
            );
        }


        /*
        =========================================================
        ATUALIZA POSIÇÃO DA IMAGEM
        =========================================================
        */

        function atualizarImagem() {

            if (!imagem) {
                return;
            }


            const escalaInicial =
                obterEscalaInicial();


            const escalaFinal =
                escalaInicial *
                escala;


            imagem.style.transform =
                `translate(-50%, -50%)
             translate(${posX}px, ${posY}px)
             scale(${escalaFinal})`;
        }


        /*
        =========================================================
        CENTRALIZA FOTO
        =========================================================
        */

        function centralizarFoto() {

            posX = 0;
            posY = 0;

            escala = 1;

            zoom.value =
                '1';

            atualizarImagem();
        }


        /*
        =========================================================
        PREPARA FOTO EXISTENTE
        =========================================================
        */

        function prepararFotoExistente() {

            if (!imagem) {
                return;
            }


            if (
                imagem.complete &&
                imagem.naturalWidth > 0
            ) {

                centralizarFoto();

                return;
            }


            imagem.addEventListener(
                'load',
                function() {

                    centralizarFoto();

                }, {
                    once: true
                }
            );
        }


        /*
        =========================================================
        CRIA PRÉ-VISUALIZAÇÃO
        =========================================================
        */

        function exibirEditor(src) {

            preview.innerHTML = '';


            imagem =
                document.createElement('img');


            imagem.id =
                'fotoImagem';


            imagem.alt =
                'Pré-visualização da foto de perfil';


            imagem.src =
                src;


            preview.appendChild(
                imagem
            );


            posX = 0;
            posY = 0;
            escala = 1;

            zoom.value =
                '1';


            controles.hidden =
                false;


            imagem.addEventListener(
                'load',
                function() {

                    atualizarImagem();

                }, {
                    once: true
                }
            );
        }


        /*
        =========================================================
        FOTO EXISTENTE
        =========================================================
        */

        prepararFotoExistente();


        /*
        =========================================================
        ESCOLHER NOVA FOTO
        =========================================================
        */

        inputFoto.addEventListener(
            'change',
            function() {

                const arquivo =
                    this.files[0];


                if (!arquivo) {
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

                    this.value = '';

                    alert(
                        'Use uma imagem JPG, PNG ou WEBP.'
                    );

                    return;
                }


                if (
                    arquivo.size >
                    5 * 1024 * 1024
                ) {

                    this.value = '';

                    alert(
                        'A foto deve ter no máximo 5 MB.'
                    );

                    return;
                }


                const leitor =
                    new FileReader();


                leitor.onload =
                    function(evento) {

                        exibirEditor(
                            evento.target.result
                        );

                    };


                leitor.readAsDataURL(
                    arquivo
                );
            }
        );


        /*
        =========================================================
        ZOOM
        =========================================================
        */

        zoom.addEventListener(
            'input',
            function() {

                escala =
                    parseFloat(
                        this.value
                    );

                atualizarImagem();
            }
        );


        /*
        =========================================================
        CENTRALIZAR
        =========================================================
        */

        centralizar.addEventListener(
            'click',
            function() {

                centralizarFoto();

            }
        );


        /*
        =========================================================
        ARRASTAR COM MOUSE
        =========================================================
        */

        preview.addEventListener(
            'mousedown',
            function(evento) {

                if (!imagem) {
                    return;
                }


                arrastando = true;


                inicioX =
                    evento.clientX -
                    posX;


                inicioY =
                    evento.clientY -
                    posY;


                preview.classList.add(
                    'arrastando'
                );
            }
        );


        document.addEventListener(
            'mousemove',
            function(evento) {

                if (!arrastando) {
                    return;
                }


                posX =
                    evento.clientX -
                    inicioX;


                posY =
                    evento.clientY -
                    inicioY;


                atualizarImagem();
            }
        );


        document.addEventListener(
            'mouseup',
            function() {

                arrastando = false;


                preview.classList.remove(
                    'arrastando'
                );
            }
        );


        /*
        =========================================================
        ARRASTAR NO CELULAR
        =========================================================
        */

        preview.addEventListener(
            'touchstart',
            function(evento) {

                if (!imagem) {
                    return;
                }


                const toque =
                    evento.touches[0];


                arrastando = true;


                inicioX =
                    toque.clientX -
                    posX;


                inicioY =
                    toque.clientY -
                    posY;
            }, {
                passive: true
            }
        );


        preview.addEventListener(
            'touchmove',
            function(evento) {

                if (!arrastando) {
                    return;
                }


                const toque =
                    evento.touches[0];


                posX =
                    toque.clientX -
                    inicioX;


                posY =
                    toque.clientY -
                    inicioY;


                atualizarImagem();
            }, {
                passive: true
            }
        );


        preview.addEventListener(
            'touchend',
            function() {

                arrastando = false;

            }
        );


        /*
        =========================================================
        SALVAMENTO
        =========================================================
        */

        formulario.addEventListener(
            'submit',
            function(evento) {

                /*
                Se nenhuma foto nova foi escolhida,
                envia o formulário normalmente.
                */

                if (
                    !imagem ||
                    !inputFoto.files.length
                ) {
                    return;
                }


                evento.preventDefault();


                const arquivo =
                    inputFoto.files[0];


                const imagemOriginal =
                    new Image();


                imagemOriginal.onload =
                    function() {

                        const tamanhoSaida =
                            500;


                        const canvas =
                            document.createElement(
                                'canvas'
                            );


                        canvas.width =
                            tamanhoSaida;


                        canvas.height =
                            tamanhoSaida;


                        const contexto =
                            canvas.getContext(
                                '2d'
                            );


                        if (!contexto) {

                            formulario.submit();

                            return;
                        }


                        /*
                        Escala base para preencher
                        o quadrado de 500x500.
                        */

                        const escalaBase =
                            Math.max(
                                tamanhoSaida /
                                imagemOriginal.naturalWidth,

                                tamanhoSaida /
                                imagemOriginal.naturalHeight
                            );


                        const escalaFinal =
                            escalaBase *
                            escala;


                        const largura =
                            imagemOriginal.naturalWidth *
                            escalaFinal;


                        const altura =
                            imagemOriginal.naturalHeight *
                            escalaFinal;


                        /*
                        O editor visual pode ter
                        280px ou 240px.

                        O deslocamento precisa ser
                        proporcional ao tamanho final.
                        */

                        const tamanhoEditor =
                            obterTamanhoEditor();


                        const fatorPosicao =
                            tamanhoSaida /
                            tamanhoEditor;


                        const x =
                            (
                                tamanhoSaida -
                                largura
                            ) / 2 +
                            (
                                posX *
                                fatorPosicao
                            );


                        const y =
                            (
                                tamanhoSaida -
                                altura
                            ) / 2 +
                            (
                                posY *
                                fatorPosicao
                            );


                        contexto.clearRect(
                            0,
                            0,
                            tamanhoSaida,
                            tamanhoSaida
                        );


                        contexto.drawImage(
                            imagemOriginal,
                            x,
                            y,
                            largura,
                            altura
                        );


                        canvas.toBlob(
                            function(blob) {

                                if (!blob) {

                                    formulario.submit();

                                    return;
                                }


                                const arquivoFinal =
                                    new File(
                                        [
                                            blob
                                        ],
                                        'foto-perfil.webp', {
                                            type: 'image/webp'
                                        }
                                    );


                                const transferencia =
                                    new DataTransfer();


                                transferencia.items.add(
                                    arquivoFinal
                                );


                                inputFoto.files =
                                    transferencia.files;


                                formulario.submit();

                            },
                            'image/webp',
                            0.88
                        );
                    };


                imagemOriginal.src =
                    URL.createObjectURL(
                        arquivo
                    );
            }
        );

    });
</script>

<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
