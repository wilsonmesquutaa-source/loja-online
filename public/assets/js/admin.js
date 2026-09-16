document.addEventListener(
    'DOMContentLoaded',
    function () {

        const sidebar =
            document.getElementById(
                'sidebar'
            );


        const menuAbrir =
            document.getElementById(
                'menuAbrir'
            );


        const menuFechar =
            document.getElementById(
                'menuFechar'
            );


        if (
            !sidebar
        ) {

            return;

        }


        /*
        ==================================
        ABRIR MENU
        ==================================
        */

        if (
            menuAbrir
        ) {

            menuAbrir.addEventListener(
                'click',
                function () {

                    sidebar.classList.add(
                        'aberto'
                    );

                    menuAbrir.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                }
            );

        }


        /*
        ==================================
        FECHAR MENU
        ==================================
        */

        if (
            menuFechar
        ) {

            menuFechar.addEventListener(
                'click',
                function () {

                    sidebar.classList.remove(
                        'aberto'
                    );

                    if (menuAbrir) {

                        menuAbrir.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                }
            );

        }


        document.addEventListener(
            'keydown',
            function (evento) {

                if (
                    evento.key === 'Escape'
                    && sidebar.classList.contains('aberto')
                ) {

                    sidebar.classList.remove('aberto');

                    if (menuAbrir) {

                        menuAbrir.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                }

            }
        );

    }
);


/* ==================================
   LISTAGEM DE PRODUTOS
================================== */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const busca = document.getElementById('buscaProdutos');
        const lista = document.getElementById('listaProdutos');
        const vazio = document.getElementById('produtosVazio');
        const filtros = document.querySelectorAll('.produto-filtro');
        const modal = document.getElementById('modalExcluirProduto');
        const nomeModal = document.getElementById('modalExcluirNome');
        const confirmar = document.getElementById('confirmarExcluirProduto');

        if (!lista) {
            return;
        }

        let filtroAtual = 'todos';
        let formularioPendente = null;

        const aplicarFiltros = function () {
            const termo = (busca ? busca.value : '').trim().toLocaleLowerCase('pt-BR');
            const linhas = Array.from(lista.querySelectorAll('tr'));
            let visiveis = 0;

            linhas.forEach(function (linha) {
                const correspondeBusca = linha.dataset.produto.includes(termo);
                const estoque = Number(linha.dataset.estoque || 0);
                const status = linha.dataset.status || '';
                const correspondeFiltro =
                    filtroAtual === 'todos'
                    || status === filtroAtual
                    || (filtroAtual === 'baixo' && estoque <= 5);

                const mostrar = correspondeBusca && correspondeFiltro;

                linha.classList.toggle('produto-oculto', !mostrar);

                if (mostrar) {
                    visiveis += 1;
                }
            });

            if (vazio) {
                vazio.hidden = visiveis !== 0;
            }
        };

        if (busca) {
            busca.addEventListener('input', aplicarFiltros);
        }

        filtros.forEach(function (botao) {
            botao.addEventListener('click', function () {
                filtroAtual = botao.dataset.filtro || 'todos';

                filtros.forEach(function (item) {
                    item.classList.toggle('ativo', item === botao);
                });

                aplicarFiltros();
            });
        });

        const fecharModal = function () {
            if (!modal) {
                return;
            }

            modal.classList.remove('aberto');
            modal.setAttribute('aria-hidden', 'true');
            formularioPendente = null;
        };

        document.querySelectorAll('.produto-excluir-form').forEach(function (formulario) {
            formulario.addEventListener('submit', function (evento) {
                evento.preventDefault();

                if (!modal || !nomeModal) {
                    return;
                }

                formularioPendente = formulario;
                nomeModal.textContent = formulario.dataset.produtoNome || 'este produto';
                modal.classList.add('aberto');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        if (confirmar) {
            confirmar.addEventListener('click', function () {
                if (formularioPendente) {
                    formularioPendente.submit();
                }
            });
        }

        document.querySelectorAll('[data-fechar-modal]').forEach(function (botao) {
            botao.addEventListener('click', fecharModal);
        });

        if (modal) {
            modal.addEventListener('click', function (evento) {
                if (evento.target === modal) {
                    fecharModal();
                }
            });
        }

        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') {
                fecharModal();
            }
        });

    }
);


/* ==================================
   MENSAGEM DE SUCESSO
================================== */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const mensagem = document.querySelector('[data-admin-sucesso]');

        if (!mensagem) {
            return;
        }

        const fechar = function () {
            mensagem.classList.add('admin-sucesso--saindo');
            window.setTimeout(function () {
                mensagem.remove();
            }, 250);
        };

        const botaoFechar = mensagem.querySelector('[data-fechar-sucesso]');

        if (botaoFechar) {
            botaoFechar.addEventListener('click', fechar);
        }

        window.setTimeout(fechar, 5000);
    }
);


/* ==================================
   EDITOR DE IMAGEM DA CATEGORIA
================================== */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const campoImagem = document.getElementById('imagem');
        const palco = document.getElementById('imagem-palco');
        const previewContainer = document.getElementById('imagem-preview-container');
        const campoPosicaoX = document.getElementById('posicao_x');
        const campoPosicaoY = document.getElementById('posicao_y');
        const campoEscala = document.getElementById('escala');
        const controleZoom = document.getElementById('imagem-zoom');
        const valorZoom = document.getElementById('imagem-zoom-valor');

        if (!campoImagem || !palco || !previewContainer || !campoPosicaoX || !campoPosicaoY || !campoEscala) {
            return;
        }

        let imagem = document.getElementById('imagem-preview');
        let guia = document.getElementById('imagem-guia');
        let arrastando = false;
        let inicioX = 0;
        let inicioY = 0;
        let posicaoXInicial = Number(campoPosicaoX.value) || 50;
        let posicaoYInicial = Number(campoPosicaoY.value) || 50;

        const limitar = function (valor) {
            return Math.min(Math.max(valor, 0), 100);
        };

        const atualizarEnquadramento = function (x, y) {
            const xLimitado = limitar(x);
            const yLimitado = limitar(y);
            const escala = Math.min(Math.max(Number(campoEscala.value) || 1.2, 1.05), 2);
            const excedente = (escala - 1) * 100;

            campoPosicaoX.value = xLimitado.toFixed(2);
            campoPosicaoY.value = yLimitado.toFixed(2);
            campoEscala.value = escala.toFixed(2);

            if (controleZoom) {
                controleZoom.value = String(Math.round(escala * 100));
            }

            if (valorZoom) {
                valorZoom.textContent = Math.round(escala * 100) + '%';
            }

            if (imagem) {
                imagem.style.objectPosition = xLimitado + '% ' + yLimitado + '%';
                imagem.style.width = (escala * 100) + '%';
                imagem.style.height = (escala * 100) + '%';
                imagem.style.left = (-(xLimitado / 100) * excedente) + '%';
                imagem.style.top = (-(yLimitado / 100) * excedente) + '%';
            }

            if (guia) {
                const largura = previewContainer.clientWidth * escala;
                const altura = previewContainer.clientHeight * escala;
                const inicioEsquerda = (palco.clientWidth - previewContainer.clientWidth) / 2;
                const inicioTopo = (palco.clientHeight - previewContainer.clientHeight) / 2;

                guia.style.width = largura + 'px';
                guia.style.height = altura + 'px';
                guia.style.left = (inicioEsquerda - (xLimitado / 100) * (largura - previewContainer.clientWidth)) + 'px';
                guia.style.top = (inicioTopo - (yLimitado / 100) * (altura - previewContainer.clientHeight)) + 'px';
            }
        };

        const atualizarPosicao = atualizarEnquadramento;

        const criarGuia = function (origem) {
            if (!guia) {
                guia = document.createElement('img');
                guia.id = 'imagem-guia';
                guia.className = 'categoria-imagem-guia';
                guia.alt = '';
                guia.setAttribute('aria-hidden', 'true');
                palco.insertBefore(guia, previewContainer);
            }

            guia.src = origem;
        };

        const criarPreview = function (origem) {
            previewContainer.innerHTML = '';
            imagem = document.createElement('img');
            imagem.id = 'imagem-preview';
            imagem.src = origem;
            imagem.alt = 'Pré-visualização da imagem';
            imagem.draggable = false;
            imagem.style.left = '-10%';
            imagem.style.top = '-10%';
            previewContainer.appendChild(imagem);
            criarGuia(origem);
            atualizarEnquadramento(50, 50);
        };

        campoImagem.addEventListener('change', function () {
            const arquivo = campoImagem.files && campoImagem.files[0]
                ? campoImagem.files[0]
                : null;

            if (!arquivo) {
                return;
            }

            if (!['image/jpeg', 'image/png', 'image/webp'].includes(arquivo.type)) {
                alert('Selecione uma imagem JPG, PNG ou WEBP.');
                campoImagem.value = '';
                return;
            }

            if (arquivo.size > 5 * 1024 * 1024) {
                alert('A imagem deve possuir no máximo 5 MB.');
                campoImagem.value = '';
                return;
            }

            const leitor = new FileReader();
            leitor.addEventListener('load', function () {
                criarPreview(String(leitor.result));
            });
            leitor.readAsDataURL(arquivo);
        });

        previewContainer.addEventListener('pointerdown', function (evento) {
            if (!imagem) {
                return;
            }

            arrastando = true;
            inicioX = evento.clientX;
            inicioY = evento.clientY;
            posicaoXInicial = Number(campoPosicaoX.value) || 50;
            posicaoYInicial = Number(campoPosicaoY.value) || 50;
            previewContainer.setPointerCapture(evento.pointerId);
            previewContainer.classList.add('arrastando');
        });

        previewContainer.addEventListener('pointermove', function (evento) {
            if (!arrastando || !imagem) {
                return;
            }

            const deslocamentoX = (evento.clientX - inicioX) / Math.max(previewContainer.clientWidth, 1) * 100;
            const deslocamentoY = (evento.clientY - inicioY) / Math.max(previewContainer.clientHeight, 1) * 100;
            atualizarPosicao(posicaoXInicial + deslocamentoX, posicaoYInicial + deslocamentoY);
        });

        const finalizarArraste = function () {
            arrastando = false;
            previewContainer.classList.remove('arrastando');
        };

        previewContainer.addEventListener('pointerup', finalizarArraste);
        previewContainer.addEventListener('pointercancel', finalizarArraste);

        document.querySelectorAll('[data-mover-imagem]').forEach(function (botao) {
            botao.addEventListener('click', function () {
                const passo = 8;
                const direcao = botao.dataset.moverImagem;
                const x = Number(campoPosicaoX.value) || 50;
                const y = Number(campoPosicaoY.value) || 50;

                atualizarPosicao(
                    x + (direcao === 'direita' ? passo : direcao === 'esquerda' ? -passo : 0),
                    y + (direcao === 'baixo' ? passo : direcao === 'cima' ? -passo : 0)
                );
            });
        });

        const centralizar = document.getElementById('imagem-centralizar');

        if (centralizar) {
            centralizar.addEventListener('click', function () {
                atualizarPosicao(50, 50);
            });
        }

        if (controleZoom) {
            controleZoom.addEventListener('input', function () {
                campoEscala.value = (Number(controleZoom.value) / 100).toFixed(2);
                atualizarEnquadramento(
                    Number(campoPosicaoX.value) || 50,
                    Number(campoPosicaoY.value) || 50
                );
            });
        }

        if (imagem) {
            atualizarEnquadramento(
                Number(campoPosicaoX.value) || 50,
                Number(campoPosicaoY.value) || 50
            );
        }

    }
);


/* ==================================
   CARDÁPIO
================================== */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const buscaCategorias =
            document.getElementById('buscaCategorias');

        const listaCategorias =
            document.getElementById('listaCategorias');

        const vazioCategorias =
            document.getElementById('categoriasVazio');

        if (!buscaCategorias || !listaCategorias) {
            return;
        }

        buscaCategorias.addEventListener(
            'input',
            function () {

                const termo = buscaCategorias.value
                    .trim()
                    .toLocaleLowerCase('pt-BR');

                let visiveis = 0;

                listaCategorias.querySelectorAll('.categoria-card')
                    .forEach(function (card) {

                        const mostrar = card.dataset.categoria
                            .includes(termo);

                        card.hidden = !mostrar;

                        if (mostrar) {
                            visiveis += 1;
                        }

                    });

                if (vazioCategorias) {
                    vazioCategorias.hidden = visiveis !== 0;
                }

            }
        );

    }
);
