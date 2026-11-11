<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$pdo = require __DIR__ . '/conexao.php';
$pdo->exec(file_get_contents(__DIR__ . '/05_clientes_seguranca.sql'));
echo "Migração de segurança dos clientes aplicada.\n";
