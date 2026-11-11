'use strict';

(() => {
    function iniciar() {
        const porId = id => document.getElementById(id);
        const palco = porId('banner-palco');
        const preview = porId('banner-preview-container');
        const upload = porId('banner_imagem');
        const x = porId('banner_posicao_x');
        const y = porId('banner_posicao_y');
        const escala = porId('banner_escala');
        if (!palco || !preview || !upload || !x || !y || !escala) return;
        const superficie = preview.querySelector('.categoria-banner-superficie');
        const zoom = porId('banner-zoom');
        const largura = porId('banner-largura');
        const rolagem = porId('banner-rolagem');
        let imagem = porId('banner-preview');
        let proporcao = 1;
        let arraste = null;
        let versaoUpload = 0;
        const numero = (valor, padrao) => Number.isFinite(Number(valor)) && valor !== '' ? Number(valor) : padrao;
        const limitar = (valor, min, max) => Math.min(max, Math.max(min, valor));

        function atualizar() {
            x.value = limitar(numero(x.value, 50), 0, 100).toFixed(2);
            y.value = limitar(numero(y.value, 50), 0, 100).toFixed(2);
            escala.value = limitar(numero(escala.value, 1), 1, 2).toFixed(2);
            zoom.value = String(Math.round(Number(escala.value) * 100));
            porId('banner-zoom-valor').textContent = zoom.value + '%';
            if (imagem) {
                imagem.style.setProperty('--banner-x', x.value + '%');
                imagem.style.setProperty('--banner-y', y.value + '%');
                imagem.style.setProperty('--banner-escala', escala.value);
            }
        }

        function dimensionar() {
            const pixels = limitar(numero(largura.value, 1440), 280, 3840);
            preview.style.width = pixels + 'px';
            proporcao = Math.min(1, palco.clientWidth / pixels);
            preview.style.transform = 'scale(' + proporcao + ')';
            palco.style.height = (superficie.offsetHeight * proporcao) + 'px';
            window.CategoriaBanner.atualizar(preview, numero(rolagem.value, 0));
        }

        largura.addEventListener('input', dimensionar);
        rolagem.addEventListener('input', dimensionar);
        new ResizeObserver(dimensionar).observe(palco);
        zoom.addEventListener('input', () => { escala.value = Number(zoom.value) / 100; atualizar(); });
        porId('banner-centralizar').addEventListener('click', () => { x.value = y.value = '50'; atualizar(); });

        // Com cover + escala, a posição é (tamanho da área - tamanho renderizado) * percentual.
        function espacos() {
            const w = superficie.clientWidth;
            const h = superficie.clientHeight;
            const ajuste = Math.max(w / imagem.naturalWidth, h / imagem.naturalHeight) * Number(escala.value);
            return [w - imagem.naturalWidth * ajuste, h - imagem.naturalHeight * ajuste];
        }
        function mover(dx, dy, inicialX = Number(x.value), inicialY = Number(y.value)) {
            if (!imagem || !imagem.naturalWidth) return;
            const [espacoX, espacoY] = espacos();
            x.value = Math.abs(espacoX) < 0.01 ? inicialX : inicialX + dx / espacoX * 100;
            y.value = Math.abs(espacoY) < 0.01 ? inicialY : inicialY + dy / espacoY * 100;
            atualizar();
        }
        document.querySelectorAll('[data-mover-banner]').forEach(botao => {
            botao.addEventListener('click', () => {
                const direcao = botao.dataset.moverBanner;
                const passo = 8 / proporcao;
                mover(direcao === 'direita' ? passo : direcao === 'esquerda' ? -passo : 0,
                    direcao === 'baixo' ? passo : direcao === 'cima' ? -passo : 0);
            });
        });
        preview.addEventListener('pointerdown', evento => {
            if (!imagem || !imagem.naturalWidth || evento.button !== 0) return;
            arraste = { id: evento.pointerId, px: evento.clientX, py: evento.clientY, x: Number(x.value), y: Number(y.value) };
            preview.setPointerCapture(evento.pointerId);
            preview.classList.add('arrastando');
            evento.preventDefault();
        });
        preview.addEventListener('pointermove', evento => {
            if (!arraste || evento.pointerId !== arraste.id) return;
            mover((evento.clientX - arraste.px) / proporcao, (evento.clientY - arraste.py) / proporcao, arraste.x, arraste.y);
        });
        const finalizar = () => { arraste = null; preview.classList.remove('arrastando'); };
        ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(tipo => preview.addEventListener(tipo, finalizar));

        upload.addEventListener('change', () => {
            const versao = ++versaoUpload;
            const arquivo = upload.files[0];
            if (!arquivo) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(arquivo.type) || arquivo.size > 8 * 1024 * 1024) {
                alert('Selecione JPG, PNG ou WEBP com no máximo 8 MB.');
                upload.value = '';
                return;
            }
            const url = URL.createObjectURL(arquivo);
            const nova = new Image();
            nova.onload = () => {
                URL.revokeObjectURL(url);
                if (versao !== versaoUpload) return;
                nova.id = 'banner-preview';
                nova.className = 'categoria-banner-imagem';
                nova.alt = 'Pré-visualização da imagem de fundo';
                nova.draggable = false;
                superficie.replaceChildren(nova);
                imagem = nova;
                x.value = y.value = '50';
                escala.value = '1';
                atualizar();
            };
            nova.onerror = () => {
                URL.revokeObjectURL(url);
                if (versao !== versaoUpload) return;
                upload.value = '';
                alert('Não foi possível ler esta imagem.');
            };
            nova.src = url;
        });
        atualizar();
        dimensionar();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();
})();
