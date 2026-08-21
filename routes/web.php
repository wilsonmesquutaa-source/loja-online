<?php

declare(strict_types=1);

use App\Controllers\Site\ContatoController;
use App\Controllers\Site\HomeController;
use App\Controllers\Site\CardapioController;
use App\Controllers\Site\QuemSomosController;
use App\Controllers\Site\TermosController;
use App\Controllers\Site\CheckoutController;

return [

    [
        'method' => 'GET',
        'path' => '/',
        'action' => [
            HomeController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/cardapio',
        'action' => [
            CardapioController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/cardapio/categoria/{id}',
        'action' => [
            CardapioController::class,
            'categoria',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/contato',
        'action' => [
            ContatoController::class,
            'index',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/contato',
        'action' => [
            ContatoController::class,
            'enviar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/termos',
        'action' => [
            TermosController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/quemsomos',
        'action' => [
            QuemSomosController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/carrinho',
        'action' => [
            \App\Controllers\Site\CarrinhoController::class,
            'index',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/carrinho/adicionar',
        'action' => [
            \App\Controllers\Site\CarrinhoController::class,
            'adicionar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/carrinho/editar/{id}',
        'action' => [
            \App\Controllers\Site\CarrinhoController::class,
            'editar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/carrinho/remover',
        'action' => [
            \App\Controllers\Site\CarrinhoController::class,
            'remover',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/checkout',
        'action' => [
            CheckoutController::class,
            'index',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/checkout/finalizar',
        'action' => [
            CheckoutController::class,
            'finalizar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/checkout/sucesso/{id}',
        'action' => [
            CheckoutController::class,
            'sucesso',
        ],
    ],

];
