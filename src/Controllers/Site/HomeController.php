<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Repositories\ProdutoRepository;
use App\Controllers\Controller;
use PDO;

final class HomeController extends Controller
{
    public function index(): void
    {

        $beneficios = [

            [
                'icone' => 'bi bi-hand-thumbs-up',
                'titulo' => 'Feito à mão',
                'texto' =>
                'Salgados artesanais preparados com carinho e qualidade.',
            ],

            [
                'icone' => 'bi bi-clock',
                'titulo' => 'Pedidos organizados',
                'texto' =>
                'Receba seus pedidos de forma simples e rápida.',
            ],

            [
                'icone' => 'bi bi-truck',
                'titulo' => 'Entrega rápida',
                'texto' =>
                'Levamos sabor até você com segurança.',
            ],

            [
                'icone' => 'bi bi-heart',
                'titulo' => 'Muito amor',
                'texto' =>
                'Receitas especiais para deixar seus momentos melhores.',
            ],

        ];


        $produtoRepository = new ProdutoRepository($this->pdo);

        $produtos = $produtoRepository->buscarCategoriasDestaque();



        $this->view(
            'site/home',
            [

                'tituloPagina' =>
                'Cantim do Lanche',


                'descricaoPagina' =>
                'Salgados artesanais feitos com amor.',


                'rotaAtual' =>
                'home',


                'tituloHero' =>
                'Salgados artesanais feitos com muito amor',


                'textoHero' =>
                'Encomende salgados deliciosos para festas,
                eventos ou aquele lanche especial.',


                'beneficios' =>
                $beneficios,


                'produtos' =>
                $produtos,


                'emailContato' =>
                'contato@cantimdolanche.com',

                'telefoneContato' =>
                '(85) 99236-7866',

                'whatsappContato' =>
                '5585992367866',

                'instagramContato' =>
                '@cantimdolanche',

                'facebookContato' =>
                'Cantim do Lanche',

            ]
        );
    }
}
