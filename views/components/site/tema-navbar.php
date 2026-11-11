<?php
$temFaixaDecorativa = isset($temaLoja['assets']['header_overlay'])
    || isset($temaLoja['assets']['decoracao_superior']);
if (!$temFaixaDecorativa) return;
$escFaixa = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$temaAssetStyleLocal = static function (array $a): string {
    return sprintf(
        'object-position:%.2f%% %.2f%%;transform:scale(%.2f);transform-origin:%.2f%% %.2f%%;opacity:%.2f;',
        max(0, min(100, (float) ($a['posicao_x'] ?? 50))),
        max(0, min(100, (float) ($a['posicao_y'] ?? 50))),
        max(.1, min(2, (float) ($a['escala'] ?? 1))),
        max(0, min(100, (float) ($a['posicao_x'] ?? 50))),
        max(0, min(100, (float) ($a['posicao_y'] ?? 50))),
        max(0, min(1, (float) ($a['opacidade'] ?? 1)))
    );
};
?>
<div class="tema-faixa-decorativa" aria-hidden="true">
    <?php foreach (['header_overlay', 'decoracao_superior'] as $tipo):
        if (!isset($temaLoja['assets'][$tipo])) continue;
        $a = $temaLoja['assets'][$tipo];
    ?>
        <div class="tema-decor tema-faixa-decorativa-asset tema-<?= $escFaixa($tipo) ?>">
            <img src="<?= BASE_URL . $escFaixa($a['url_arquivo']) ?>" style="<?= $escFaixa($temaAssetStyleLocal($a)) ?>" alt="" data-tema-decor>
        </div>
    <?php endforeach; ?>
</div>
