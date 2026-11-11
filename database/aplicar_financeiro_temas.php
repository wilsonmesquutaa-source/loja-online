<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$pdo = require __DIR__ . '/conexao.php';
if (!extension_loaded('bcmath')) throw new RuntimeException('Ative a extensão BCMath.');
foreach (explode(';', file_get_contents(__DIR__ . '/07_financeiro_temas.sql')) as $sql) {
    if (trim($sql) !== '') $pdo->exec($sql);
}
if (!$pdo->query("SHOW COLUMNS FROM fichas_tecnicas LIKE 'custo_indireto_manual'")->fetch()) {
    $pdo->exec('ALTER TABLE fichas_tecnicas ADD custo_indireto_manual DECIMAL(12,4) NOT NULL DEFAULT 0');
}
echo "Migração financeira e de temas aplicada.\n";
