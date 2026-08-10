<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Repositories\ProdutoRepository;

final class ProdutoController extends Controller
{
    public function index(): void
    {
        $produtoRepository = new ProdutoRepository(
            $this->pdo
        );

        $categorias = $produtoRepository
            ->buscarCategoriasDestaque();

        $this->view(
            'site/produtos',
            [
                'tituloPagina' =>
                'Produtos',

                'rotaAtual' =>
                'produtos',

                'categorias' =>
                $categorias,

                'etiquetaProdutos' =>
                'Cardápio',
            ]
        );
    }

    public function categoria(int $id): void
    {
      

        $produtoRepository = new ProdutoRepository(
            $this->pdo
        );

        $categoria = $produtoRepository
            ->buscarCategoriaPorId($id);

        if ($categoria === null) {
            http_response_code(404);

            require APP_ROOT . '/views/erros/404.php';

            return;
        }

        $produtos = $produtoRepository
            ->buscarProdutosPorCategoria($id);

        /*
     * Define quantos sabores podem ser escolhidos.
     *
     * Tradicionais: até 4
     * Folhados: até 2
     * Demais categorias: 1
     */

        $nomeCategoria = mb_strtolower(
            trim($categoria['nome']),
            'UTF-8'
        );

        if (str_contains($nomeCategoria, 'tradicionais')) {

            $limiteOpcoes = 4;
        } elseif (str_contains($nomeCategoria, 'folhados')) {

            $limiteOpcoes = 2;
        } else {

            $limiteOpcoes = 1;
        }


        $this->view(
            'site/categoria',
            [
                'tituloPagina' =>
                $categoria['nome'],

                'rotaAtual' =>
                'produtos',

                'categoria' =>
                $categoria,

                'nomeCategoria' =>
                $categoria['nome'],

                'produtos' =>
                $produtos,

                'limiteOpcoes' =>
                $limiteOpcoes,
            ]
        );
    }
}
