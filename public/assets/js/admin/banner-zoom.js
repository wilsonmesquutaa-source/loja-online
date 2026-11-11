document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-zoom-preview]').forEach(controle => {
        const preview = document.querySelector(controle.dataset.zoomPreview);
        const campo = document.getElementById(controle.dataset.zoomCampo);
        const saida = document.getElementById(controle.dataset.zoomSaida);
        if (!preview || !campo) return;
        let imagemAnterior = preview.querySelector('img');
        const atualizar = () => {
            const imagem = preview.querySelector('img');
            if (imagem && imagem !== imagemAnterior) { campo.value = '1'; imagemAnterior = imagem; }
            const escala = Math.min(2, Math.max(.1, Number(campo.value) || 1));
            controle.value = Math.round(escala * 100);
            if (saida) saida.textContent = Math.round(escala * 100) + '%';
            if (!imagem) return;
            const origem = imagem.style.objectPosition || '50% 50%';
            if (imagem.style.scale !== String(escala)) imagem.style.scale = String(escala);
            if (imagem.style.transformOrigin !== origem) imagem.style.transformOrigin = origem;
        };
        controle.addEventListener('input', () => { campo.value = (Number(controle.value) / 100).toFixed(2); atualizar(); });
        new MutationObserver(atualizar).observe(preview, { childList: true, subtree: true, attributes: true, attributeFilter: ['style'] });
        atualizar();
    });
});
