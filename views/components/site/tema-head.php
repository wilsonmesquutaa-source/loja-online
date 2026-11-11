<?php

declare(strict_types=1);
$temaLoja = (new \App\Services\TemaResolverService($GLOBALS['pdo'] ?? (require APP_ROOT . '/database/conexao.php')))->resolver(isset($GLOBALS['temaPreviewId']) ? (int)$GLOBALS['temaPreviewId'] : null);
$GLOBALS['temaLoja'] = $temaLoja;
$temaEsc = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$temaAssetStyle = static function (array $a): string {
    return sprintf('object-position:%.2f%% %.2f%%;transform:scale(%.2f);transform-origin:%.2f%% %.2f%%;opacity:%.2f;', max(0, min(100, (float)$a['posicao_x'])), max(0, min(100, (float)$a['posicao_y'])), max(.1, min(2, (float)$a['escala'])), (float)$a['posicao_x'], (float)$a['posicao_y'], max(0, min(1, (float)$a['opacidade'])));
};
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/site/enfeites-home.css?v=<?= filemtime(APP_ROOT . '/public/assets/css/site/enfeites-home.css') ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/site/temas.css?v=<?= filemtime(APP_ROOT . '/public/assets/css/site/temas.css') ?>">
<?php if (isset($temaLoja['assets']['favicon'])): ?>
    <link rel="icon" href="<?= BASE_URL . $temaEsc($temaLoja['assets']['favicon']['url_arquivo']) ?>"><?php endif; ?>
<style>
    :root {
        <?php $mapa = ['cor_primaria' => '--cor-primaria', 'cor_secundaria' => '--cor-secundaria', 'cor_destaque' => '--cor-destaque', 'cor_fundo' => '--tema-fundo', 'cor_texto' => '--tema-texto', 'cor_header_inicio' => '--tema-header-inicio', 'cor_header_fim' => '--tema-header-fim', 'cor_footer' => '--tema-footer'];
        foreach ($temaLoja['paleta'] as $k => $v) if (isset($mapa[$k])) echo $mapa[$k] . ':' . $v . ";\n"; ?>
    }

    <?php if (isset($temaLoja['assets']['background_site'])): ?>body {
        background-image: url("<?= BASE_URL . $temaEsc($temaLoja['assets']['background_site']['url_arquivo']) ?>");
        background-size: cover;
        background-attachment: fixed;
    }

    <?php endif; ?><?php if (isset($temaLoja['assets']['header_background'])): ?>.tema-header-wrapper {
        background-image: url("<?= BASE_URL . $temaEsc($temaLoja['assets']['header_background']['url_arquivo']) ?>") !important;
        background-size: cover;
    }

    <?php endif; ?><?php if (($temaLoja['config']['borda'] ?? '') === 'dourada'): ?>.navbar-site {
        border-bottom: 3px solid #d8ae48;
    }

    <?php endif; ?><?php if (($temaLoja['config']['sombra'] ?? '') === 'suave'): ?>.navbar-site {
        box-shadow: 0 5px 20px #51342522 !important;
    }

    <?php endif; ?>
</style>
<style>
    <?php if (isset($temaLoja['paleta']['cor_fundo'])): ?>body {
        background-color: var(--tema-fundo);
    }

    <?php endif; ?><?php if (isset($temaLoja['paleta']['cor_texto'])): ?>body {
        color: var(--tema-texto);
    }

    <?php endif; ?><?php if (!isset($temaLoja['assets']['header_background']) && (isset($temaLoja['paleta']['cor_header_inicio']) || isset($temaLoja['paleta']['cor_header_fim']))): ?>.tema-header-wrapper {
        background: linear-gradient(110deg, var(--tema-header-inicio, #4a2a21), var(--tema-header-fim, #84502d)) !important;
    }

    <?php endif; ?><?php if (isset($temaLoja['paleta']['cor_footer'])): ?>body footer {
        background-color: var(--tema-footer);
    }

    <?php endif; ?><?php if (isset($temaLoja['assets']['logo_desktop'])): ?>@media(min-width:992px) {
        .navbar-brand-logo img {
            <?= $temaAssetStyle($temaLoja['assets']['logo_desktop']) ?>
        }
    }

    <?php endif; ?><?php if (isset($temaLoja['assets']['logo_mobile'])): ?>@media(max-width:991.98px) {
        .navbar-brand-logo img {
            <?= $temaAssetStyle($temaLoja['assets']['logo_mobile']) ?>
        }
    }

    <?php endif; ?>
</style>
<script defer src="<?= BASE_URL ?>/assets/js/site/temas.js?v=<?= filemtime(APP_ROOT . '/public/assets/js/site/temas.js') ?>"></script>