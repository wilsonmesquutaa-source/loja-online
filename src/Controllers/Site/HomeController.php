<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Repositories\CardapioRepository;
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
                'Cada salgado é preparado à mão, com cuidado e de forma artesanal.',
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


        /*
        |--------------------------------------------------------------------------
        | CARDÁPIO
        |--------------------------------------------------------------------------
        */

        $cardapioRepository =
            new CardapioRepository(
                $this->pdo
            );


        $destaquesHome =
            $cardapioRepository
                ->buscarDestaquesHome();


        /*
        |--------------------------------------------------------------------------
        | BANNERS DA HOME
        |--------------------------------------------------------------------------
        */

        $stmtBanners =
            $this->pdo->query("
                SELECT
                    id,
                    titulo,
                    texto_alternativo,
                    url_imagem,
                    posicao_x,
                    posicao_y,
                    ordem,
                    ativo

                FROM banners_home

                WHERE ativo = 1

                AND url_imagem <> ''

                ORDER BY
                    ordem ASC,
                    id ASC
            ");


        $bannersHome =
            $stmtBanners->fetchAll();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

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


                'destaquesHome' =>
                $destaquesHome,


                'bannersHome' =>
                $bannersHome,


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