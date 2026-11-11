<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';
use App\Services\ClienteAdminService;
use App\Helpers\SessaoCliente;

session_save_path(sys_get_temp_dir());
session_start();
$_ENV['APP_URL'] = 'https://loja.example.test';
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo->exec('CREATE TABLE clientes (id INTEGER PRIMARY KEY, nome TEXT, email TEXT, senha_hash TEXT, email_verificado INTEGER, foto_url TEXT)');
$pdo->exec('CREATE TABLE usuarios_admin (id INTEGER PRIMARY KEY)');
$pdo->exec('INSERT INTO usuarios_admin VALUES (1)');
$pdo->exec('CREATE TABLE pedidos (id INTEGER PRIMARY KEY, cliente_id INTEGER REFERENCES clientes(id))');
$pdo->exec('CREATE TABLE enderecos (id INTEGER PRIMARY KEY, cliente_id INTEGER REFERENCES clientes(id) ON DELETE CASCADE)');
$pdo->exec('CREATE TABLE cliente_recuperacao (cliente_id INTEGER PRIMARY KEY REFERENCES clientes(id) ON DELETE CASCADE, versao_sessao INTEGER DEFAULT 0, token_hash TEXT UNIQUE, credencial_hash TEXT, expira_em INTEGER, ultimo_envio INTEGER DEFAULT 0, dia_envio TEXT, envios_dia INTEGER DEFAULT 0)');
$pdo->exec('CREATE TABLE cliente_admin_auditoria (id INTEGER PRIMARY KEY, admin_id INTEGER, cliente_id INTEGER, acao TEXT, motivo TEXT, criado_em INTEGER)');
$inserir = $pdo->prepare('INSERT INTO clientes VALUES (?, ?, ?, ?, ?, NULL)');
for ($i = 1; $i <= 4; $i++) $inserir->execute([$i, 'Teste', "teste$i@example.test", $i === 3 ? null : password_hash('SenhaAntiga123!', PASSWORD_DEFAULT), $i === 4 ? 0 : 1]);
$pdo->exec('INSERT INTO pedidos VALUES (1, 1)');
$pdo->exec('INSERT INTO enderecos VALUES (1, 2)');
$service = new ClienteAdminService($pdo);
$checks = 0;
function check(bool $value, string $message): void {
    global $checks;
    if (!$value) throw new RuntimeException($message);
    $checks++;
}
function recusa(callable $acao, string $mensagem): void {
    try { $acao(); } catch (DomainException $erro) { check(true, $mensagem); return; }
    throw new RuntimeException($mensagem);
}
$links = [];
$mailer = static function ($email, $nome, $link) use (&$links): void { $links[] = $link; };
$hashLink = static function (string $link): string { parse_str(parse_url($link, PHP_URL_QUERY), $query); return hash('sha256', $query['token']); };
$service->enviarRecuperacao(1, 1, $mailer);
$primeiro = $hashLink($links[0]);
check($service->tokenValido($primeiro), 'Token inicial válido');
check($pdo->query('SELECT token_hash FROM cliente_recuperacao WHERE cliente_id=1')->fetchColumn() === $primeiro, 'Banco guarda somente hash');
recusa(fn() => $service->enviarRecuperacao(1, 1, $mailer), 'Intervalo de envio');
recusa(fn() => $service->enviarRecuperacao(3, 1, $mailer), 'Conta Google');
recusa(fn() => $service->enviarRecuperacao(4, 1, $mailer), 'E-mail não verificado');
$pdo->exec('UPDATE cliente_recuperacao SET ultimo_envio = 0');
$service->enviarRecuperacao(1, 1, $mailer);
$hash = $hashLink($links[1]);
check(!$service->tokenValido($primeiro), 'Reenvio invalida link anterior');
$pdo->exec('UPDATE cliente_recuperacao SET expira_em = 1');
check(!$service->tokenValido($hash), 'Expiração');
$pdo->exec('UPDATE cliente_recuperacao SET expira_em = ' . (time() + 1800));
$pdo->exec("UPDATE clientes SET email = 'alterado@example.test' WHERE id=1");
check(!$service->tokenValido($hash), 'Troca de email invalida token');
$pdo->exec("UPDATE clientes SET email = 'teste1@example.test' WHERE id=1");
recusa(fn() => $service->redefinir($hash, 'curta'), 'Política de senha');
$_SESSION = ['cliente_id' => 1, 'cliente_versao_sessao' => 0, 'usuario_admin' => ['id' => 1]];
$service->redefinir($hash, 'MinhaNovaSenha123!');
check(password_verify('MinhaNovaSenha123!', $pdo->query('SELECT senha_hash FROM clientes WHERE id=1')->fetchColumn()), 'Senha atualizada');
recusa(fn() => $service->redefinir($hash, 'OutraNovaSenha123!'), 'Uso único');
SessaoCliente::validar($pdo);
check(!isset($_SESSION['cliente_id']) && isset($_SESSION['usuario_admin']), 'Revoga apenas sessão cliente');
recusa(fn() => $service->excluir(1, 1, 'solicitacao_titular'), 'Bloqueia cliente com pedidos');
check((int) $pdo->query('SELECT COUNT(*) FROM clientes WHERE id=1')->fetchColumn() === 1, 'Cadastro preservado');
recusa(fn() => $service->excluir(2, 1, 'invalido'), 'Motivo obrigatório');
try { $service->enviarRecuperacao(2, 1, static function (): void { throw new RuntimeException('SMTP simulado'); }); }
catch (RuntimeException $erro) { check(true, 'Falha SMTP tratada'); }
check((int) $pdo->query('SELECT COUNT(*) FROM cliente_recuperacao WHERE cliente_id=2')->fetchColumn() === 0, 'Rollback de token em falha SMTP');
$service->enviarRecuperacao(2, 1, $mailer);
$hashExcluido = $hashLink(end($links));
$service->excluir(2, 1, 'cadastro_teste');
check((int) $pdo->query('SELECT COUNT(*) FROM clientes WHERE id=2')->fetchColumn() === 0, 'Exclusão');
check((int) $pdo->query('SELECT COUNT(*) FROM enderecos WHERE cliente_id=2')->fetchColumn() === 0, 'Cascata');
check(!$service->tokenValido($hashExcluido), 'Exclusão invalida token');
check((int) $pdo->query("SELECT COUNT(*) FROM cliente_admin_auditoria WHERE cliente_id=2 AND acao='cliente_excluido'")->fetchColumn() === 1, 'Auditoria preservada');
$_SESSION['cliente_id'] = 2;
SessaoCliente::validar($pdo);
check(!isset($_SESSION['cliente_id']), 'Exclusão revoga sessão');
$pdo->exec('UPDATE cliente_recuperacao SET ultimo_envio=0, envios_dia=3 WHERE cliente_id=1');
recusa(fn() => $service->enviarRecuperacao(1, 1, $mailer), 'Limite diário');
$_ENV['APP_URL'] = 'http://site-inseguro.example.test';
recusa(fn() => $service->enviarRecuperacao(1, 1, $mailer), 'Link exige HTTPS');
session_destroy();
echo "$checks verificações de segurança passaram. Nenhum e-mail enviado ou cadastro real alterado.\n";
