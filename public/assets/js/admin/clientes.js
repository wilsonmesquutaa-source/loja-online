(() => {
    'use strict';
    document.querySelectorAll('[data-confirmar-exclusao]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm('Excluir definitivamente este cadastro e seus dados associados? Esta ação não pode ser desfeita.')) event.preventDefault();
        });
    });
    const form = document.getElementById('clientes-filtros');
    if (!form) return;
    form.addEventListener('submit', () => {
        const busca = form.elements.namedItem('busca');
        if (busca instanceof HTMLInputElement) busca.value = busca.value.trim();
        form.setAttribute('aria-busy', 'true');
    });
    window.addEventListener('pageshow', () => form.removeAttribute('aria-busy'));
})();
