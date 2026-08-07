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



        $produtos = $produtoRepository
            ->buscarProdutosPorCategoria($id);





        $this->view(
            'site/categoria',
            [

                'tituloPagina' =>
                'Produtos',


                'rotaAtual' =>
                'produtos',


                'produtos' =>
                $produtos,

            ]
        );


    }


}