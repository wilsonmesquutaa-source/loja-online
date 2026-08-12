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
        =================================
        IDENTIFICA A CATEGORIA
        =================================
        */

        $nomeCategoria = mb_strtolower(
            trim($categoria['nome']),
            'UTF-8'
        );


        /*
        =================================
        VALORES PADRÃO
        =================================
        */

        $tipoCategoria = 'unica';

        $limiteOpcoes = 1;


        /*
        =================================
        SALGADOS TRADICIONAIS
        =================================

        Pode escolher até 4 sabores.

        Cada sabor pode ser repetido.

        Exemplo:

        Coxinha 2
        Risoles 1
        Bolinha de queijo 1

        Total = 4
        */

        if (
            str_contains(
                $nomeCategoria,
                'tradicionais'
            )
        ) {

            $tipoCategoria =
                'cento_tradicionais';

            $limiteOpcoes = 4;
        }


        /*
        =================================
        SALGADOS FOLHADOS
        =================================

        Pode escolher até 2 sabores.

        Cada sabor pode ser repetido.

        Exemplo:

        Frango 2

        Total = 2
        */ elseif (
            str_contains(
                $nomeCategoria,
                'folhados'
            )
        ) {

            $tipoCategoria =
                'cento_folhados';

            $limiteOpcoes = 2;
        }


        /*
        =================================
        SALGADOS GRANDES
        =================================

        Quantidade livre.

        Não existe limite geral
        de quantidade.

        O cliente pode escolher:

        Coxinha grande = 5
        Pastel grande = 10
        Risoles grande = 3

        etc.
        */ elseif (
            str_contains(
                $nomeCategoria,
                'grandes'
            )
        ) {

            $tipoCategoria =
                'salgados_grandes';

            $limiteOpcoes = null;
        }


        /*
        =================================
        EMPADÃO
        =================================
        Quantidade livre.
        
        Atualmente existe apenas
        um sabor, mas futuramente
        poderão existir vários.
        */ elseif (
            str_contains($nomeCategoria, 'empadão')
            ||
            str_contains($nomeCategoria, 'empadões')
            ||
            str_contains($nomeCategoria, 'empadao')
            ||
            str_contains($nomeCategoria, 'empadoes')
        ) {

            $tipoCategoria = 'empadao';

            $limiteOpcoes = null;
        }


        /*
        =================================
        CARREGA A VIEW
        =================================
        */

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

                'tipoCategoria' =>
                $tipoCategoria,
            ]
        );
    }
}
