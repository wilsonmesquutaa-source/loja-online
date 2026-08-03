<?php

declare(strict_types=1);

use App\Controllers\Site\ContatoController;
use App\Controllers\Site\HomeController;
use App\Controllers\Site\ProdutoController;
use App\Controllers\Site\QuemSomosController;
use App\Controllers\Site\TermosController;

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
        'path' => '/produtos',
        'action' => [
            ProdutoController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/produtos/categoria/{id}',
        'action' => [
            ProdutoController::class,
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
        'method' => 'POST',
        'path' => '/contato',
        'action' => [
            ContatoController::class,
            'enviar',
        ],
    ],
];
