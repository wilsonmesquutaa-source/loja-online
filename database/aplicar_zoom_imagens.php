<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$pdo = require __DIR__ . '/conexao.php';
foreach (['categoria_banners', 'banners_home'] as $tabela) {
    $consulta = $pdo->query("SHOW COLUMNS FROM `$tabela` LIKE 'escala'");
    if (!$consulta->fetch()) $pdo->exec("ALTER TABLE `$tabela` ADD escala DECIMAL(4,2) NOT NULL DEFAULT 1.00");
}
echo "Zoom de banners disponível; enquadramentos existentes preservados.\n";
