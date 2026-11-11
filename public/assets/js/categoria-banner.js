'use strict';

// O site e o editor usam exatamente a mesma geometria de rolagem.
window.CategoriaBanner = Object.freeze({
    atualizar(contexto, deslocamento) {
        const progresso = Math.min(1, Math.max(0, deslocamento / 260));
        const margemInicial = contexto.clientWidth <= 575.98 ? 70 : 110;
        contexto.style.setProperty('--banner-movimento', (progresso * 70) + 'px');
        contexto.style.setProperty('--banner-sobreposicao', (margemInicial + progresso * 55) + 'px');
    }
});

(() => {
    const iniciar = () => {
        const pagina = document.querySelector('.categoria-pagina.categoria-banner-contexto');
        if (!pagina) return;
        let pendente = false;
        const atualizar = () => {
            window.CategoriaBanner.atualizar(pagina, Math.max(0, -pagina.getBoundingClientRect().top));
            pendente = false;
        };
        const solicitar = () => {
            if (pendente) return;
            pendente = true;
            requestAnimationFrame(atualizar);
        };
        window.addEventListener('scroll', solicitar, { passive: true });
        window.addEventListener('resize', solicitar);
        new ResizeObserver(solicitar).observe(pagina);
        atualizar();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();
})();
