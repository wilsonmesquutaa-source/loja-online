<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', 'http://localhost/loja-online');
require APP_ROOT . '/vendor/autoload.php';

use App\Repositories\ClienteAdminRepository;
use App\Controllers\Admin\ClienteController;

$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE clientes (id INTEGER PRIMARY KEY, nome TEXT, email TEXT, email_verificado INTEGER, criado_em TEXT, senha_hash TEXT)');
$pdo->exec('CREATE TABLE pedidos (id INTEGER PRIMARY KEY, cliente_id INTEGER, codigo TEXT, status TEXT, total REAL, criado_em TEXT)');
$inserir = $pdo->prepare('INSERT INTO clientes (id, nome, email, email_verificado, criado_em) VALUES (?, ?, ?, ?, ?)');
for ($i = 1; $i <= 25; $i++) {
    $inserir->execute([$i, 'Cliente Exemplo ' . $i, "cliente$i@example.test", $i % 2, '2026-09-17 10:00:00']);
}
$inserir->execute([26, '<script>alert(1)</script> 100%_', 'teste@example.test', 1, '2026-09-17 11:00:00']);
$inserirPedido = $pdo->prepare('INSERT INTO pedidos VALUES (?, ?, ?, ?, ?, ?)');
for ($i = 1; $i <= 21; $i++) {
    $inserirPedido->execute([$i, 1, 'EXEMPLO-' . $i, 'entregue', 25.5, '2026-09-17 12:00:00']);
}
$checks = 0;
function verificar(bool $resultado, string $mensagem): void {
    global $checks;
    if (!$resultado) throw new RuntimeException($mensagem);
    $checks++;
}
$repo = new ClienteAdminRepository($pdo);
$lista = $repo->listar('', '', 1);
verificar($lista['total'] === 26 && count($lista['clientes']) === 20, 'Paginação inicial');
verificar($repo->listar('', '', 999)['pagina'] === 2, 'Limite de página');
verificar($repo->listar('', '', -1)['pagina'] === 1, 'Página negativa');
verificar($repo->listar('100%_', '', 1)['total'] === 1, 'Busca literal de curingas');
verificar($repo->listar("' OR 1=1 --", '', 1)['total'] === 0, 'Busca não pode virar SQL');
verificar($repo->listar('cliente1@example.test', '', 1)['total'] === 1, 'Busca por e-mail');
verificar($repo->listar('', '0', 1)['total'] === 12, 'Filtro de pendentes');
verificar($repo->buscar(999) === null, 'Cliente inexistente');
verificar(!isset($lista['clientes'][0]['email']), 'Listagem não entrega e-mail completo');
verificar($repo->pedidos(1, 2)['total'] === 21 && count($repo->pedidos(1, 2)['pedidos']) === 1, 'Paginação de pedidos');
verificar($repo->pedidos(2, 1)['total'] === 0, 'Separação entre clientes');
$_SESSION = ['usuario_admin' => ['id' => 1, 'nome' => 'Administrador de teste']];
$_GET = ['busca' => ['entrada-invalida'], 'verificacao' => ['entrada-invalida'], 'pagina' => '1'];
ob_start();
(new ClienteController($pdo))->index();
$html = ob_get_clean();
verificar(!str_contains($html, '<script>alert(1)</script>'), 'Escape de HTML');
verificar(str_contains($html, '&lt;script&gt;'), 'Nome escapado aparece');
verificar(!str_contains($html, 'teste@example.test'), 'E-mail completo ausente do HTML');
verificar(!str_contains($html, 'bootstrap.min.css'), 'Página sem framework Bootstrap');
$_GET = [];
ob_start();
(new ClienteController($pdo))->detalhes(1);
$detalhes = ob_get_clean();
verificar(str_contains($detalhes, 'cliente1@example.test'), 'Contato disponível nos detalhes');
verificar(str_contains($detalhes, '/admin/pedidos/21'), 'Link do pedido correto');
ob_start();
(new ClienteController($pdo))->detalhes(999);
$ausente = ob_get_clean();
verificar(http_response_code() === 404 && str_contains($ausente, 'Cliente não encontrado'), 'Resposta 404');
http_response_code(200);

if (isset($argv[1]) && is_dir($argv[1])) {
    $_GET = [];
    ob_start();
    (new ClienteController($pdo))->index();
    file_put_contents($argv[1] . '/clientes.html', ob_get_clean());
    file_put_contents($argv[1] . '/cliente-detalhes.html', $detalhes);
    $pdo->exec("UPDATE clientes SET senha_hash = 'senha-ficticia', email_verificado = 1 WHERE id = 2");
    ob_start();
    (new ClienteController($pdo))->detalhes(2);
    file_put_contents($argv[1] . '/cliente-acoes.html', ob_get_clean());
    $renderRecuperacao = static function (): string {
        $valido = true;
        ob_start();
        require APP_ROOT . '/views/cliente/redefinir-senha.php';
        return ob_get_clean();
    };
    file_put_contents($argv[1] . '/recuperacao.html', $renderRecuperacao());
    $renderProduto = static function (): string {
        $produto = ['id' => 1, 'nome' => 'Produto de exemplo', 'preco' => 15.5, 'estoque' => 10, 'status' => 'ativo', 'categoria_id' => 1];
        $imagemProduto = ['url_imagem' => '/assets/images/logo.webp', 'posicao_x' => 50, 'posicao_y' => 50, 'escala' => 1.4];
        $categorias = [['id' => 1, 'nome' => 'Categoria de exemplo']];
        ob_start();
        require APP_ROOT . '/views/admin/produto-form.php';
        return ob_get_clean();
    };
    file_put_contents($argv[1] . '/produto.html', $renderProduto());
}
if (in_array('--mysql', $argv, true)) {
    // Integração opcional: somente SELECT no banco configurado, sem imprimir cadastros.
    $conexao = require APP_ROOT . '/database/conexao.php';
    $integracao = new ClienteAdminRepository($conexao);
    $resultado = $integracao->listar('', '', 1);
    verificar(isset($resultado['total']), 'Consulta MySQL');
    verificar($integracao->listar('consulta-inexistente-100%_', '0', 1)['total'] >= 0, 'Busca preparada MySQL');
    if ($resultado['clientes'] !== []) {
        $idTeste = (int) $resultado['clientes'][0]['id'];
        verificar($integracao->buscar($idTeste) !== null, 'Detalhes MySQL');
        verificar(isset($integracao->pedidos($idTeste, 1)['total']), 'Pedidos MySQL');
    }
}
echo "$checks verificações passaram.\n";
