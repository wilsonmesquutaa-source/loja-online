<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;

final class HomeController
    extends Controller
{
    public function index(): void
    {
        $beneficios = [
            [
                'icone' =>
                    'bi bi-truck',

                'titulo' =>
                    'Entrega organizada',

                'texto' =>
                    'Acompanhe o andamento dos seus pedidos.',
            ],
            [
                'icone' =>
                    'bi bi-shield-check',

                'titulo' =>
                    'Compra segura',

                'texto' =>
                    'Seus dados são processados com segurança.',
            ],
            [
                'icone' =>
                    'bi bi-credit-card',

                'titulo' =>
                    'Pagamento facilitado',

                'texto' =>
                    'Escolha entre diferentes formas de pagamento.',
            ],
            [
                'icone' =>
                    'bi bi-headset',

                'titulo' =>
                    'Suporte ao cliente',

                'texto' =>
                    'Nossa equipe está pronta para ajudar.',
            ],
        ];

        $produtos = [
            [
                'nome' =>
                    'Mouse sem fio',

                'descricao' =>
                    'Mouse confortável para trabalho e estudo.',

                'preco' =>
                    89.90,
            ],
            [
                'nome' =>
                    'Teclado mecânico',

                'descricao' =>
                    'Teclado resistente com ótima resposta.',

                'preco' =>
                    279.90,
            ],
            [
                'nome' =>
                    'Monitor LED 24 polegadas',

                'descricao' =>
                    'Imagem nítida para produtividade e lazer.',

                'preco' =>
                    899.90,
            ],
        ];

        $this->view(
            'site/home',
            [
                'tituloPagina' =>
                    'Página inicial',

                'descricaoPagina' =>
                    'Conheça os produtos da nossa Loja Online.',

                'rotaAtual' =>
                    'home',

                'tituloHero' =>
                    'Tecnologia para o seu dia a dia',

                'textoHero' =>
                    'Encontre produtos selecionados '
                    . 'para trabalho, estudo e lazer.',

                'beneficios' =>
                    $beneficios,

                'produtos' =>
                    $produtos,

                'emailContato' =>
                    'contato@lojaonline.com',

                'telefoneContato' =>
                    '(85) 99999-9999',
            ]
        );
    }
}
