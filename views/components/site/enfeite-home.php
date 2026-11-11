<?php
// Elemento exclusivamente visual; não recebe cliques nem ocupa espaço no layout.
$temaEnfeite = $GLOBALS['temaLoja'] ?? [];
$assetEnfeite = $temaEnfeite['assets'][$tipoEnfeite ?? ''] ?? null;
if (!$assetEnfeite || (int)$assetEnfeite['tema_id'] !== (int)($temaEnfeite['tema']['id'] ?? 0)) return;
$escEnfeite = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$escalaEnfeite = max(.1, min(2, (float)($assetEnfeite['escala'] ?? 1)));
$xEnfeite = max(0, min(100, (float)($assetEnfeite['posicao_x'] ?? 50)));
$yEnfeite = max(0, min(100, (float)($assetEnfeite['posicao_y'] ?? 50)));
$estiloEnfeite = sprintf('width:%.2f%%;height:%.2f%%;left:%.2f%%;top:%.2f%%;opacity:%.2f;', $escalaEnfeite * 100, $escalaEnfeite * 100, - ($escalaEnfeite - 1) * $xEnfeite, - ($escalaEnfeite - 1) * $yEnfeite, max(0, min(1, (float)($assetEnfeite['opacidade'] ?? 1))));
?>
<div class="tema-home-enfeite tema-<?= $escEnfeite($tipoEnfeite) ?>" aria-hidden="true">
    <img src="<?= $escEnfeite(BASE_URL . $assetEnfeite['url_arquivo']) ?>" alt="" style="<?= $escEnfeite($estiloEnfeite) ?>" decoding="async">
</div>