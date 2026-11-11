<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', 'http://localhost/loja-online');
require APP_ROOT . '/vendor/autoload.php';
use App\Helpers\ImagemEscala;

$checks = 0;
function conferirImagem(bool $valor): void { global $checks; if (!$valor) throw new RuntimeException('Verificação de imagem falhou.'); $checks++; }
foreach ([['0.1', .1], ['0.5', .5], ['1', 1.0], ['2', 2.0], ['0', .1], ['9', 2.0], ['invalido', 1.0]] as [$entrada, $saida]) {
    conferirImagem(ImagemEscala::normalizar($entrada) === $saida);
    foreach ([\App\Controllers\Admin\ProdutoController::class, \App\Controllers\Admin\CategoriaController::class] as $classe) {
        $ref = new ReflectionClass($classe);
        conferirImagem($ref->getMethod('normalizarEscala')->invoke($ref->newInstanceWithoutConstructor(), $entrada) === $saida);
    }
}
$pdo = require APP_ROOT . '/database/conexao.php';
$repo = new \App\Repositories\CardapioRepository($pdo);
$categorias = $repo->buscarCategorias();
$repo->buscarCategoriasDestaque();
$repo->buscarDestaquesHome();
foreach (array_slice($categorias, 0, 1) as $categoria) {
    $repo->buscarCategoriaPorId((int) $categoria['id']);
    $repo->buscarProdutosPorCategoria((int) $categoria['id']);
    $repo->buscarBannerCategoriaPorId((int) $categoria['id']);
}
foreach (['categoria_banners', 'banners_home'] as $tabela) conferirImagem((bool) $pdo->query("SHOW COLUMNS FROM $tabela LIKE 'escala'")->fetch());
if (isset($argv[1]) && is_dir($argv[1])) {
    $_SESSION = ['usuario_admin' => ['id' => 1, 'nome' => 'Prévia de teste']];
    $render = static function (string $nome): string {
        $csrfToken = 'teste';
        $categoria = ['id' => 1, 'nome' => 'Categoria de exemplo'];
        $imagemCategoria = ['url_imagem' => '/assets/images/logo.webp', 'escala' => .5];
        $bannerCategoria = ['url_imagem' => '/assets/images/logo.webp', 'escala' => .75];
        $produto = ['id' => 1, 'nome' => 'Produto de exemplo'];
        $imagemProduto = ['url_imagem' => '/assets/images/logo.webp', 'escala' => .5];
        $categorias = [];
        $banner = ['id' => 1, 'url_imagem' => '/assets/images/logo.webp', 'escala' => .75];
        ob_start(); require APP_ROOT . '/views/admin/' . $nome . '.php'; return ob_get_clean();
    };
    foreach (['categoria-form', 'produto-form', 'banner-home-form'] as $nome) file_put_contents($argv[1] . '/' . $nome . '.html', $render($nome));
}
echo "$checks verificações de escala passaram; consultas públicas executadas sem erro.\n";
