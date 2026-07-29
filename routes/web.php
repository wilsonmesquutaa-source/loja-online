<?php

declare(strict_types=1);

use App\Controllers\Site\ContatoController;
use App\Controllers\Site\HomeController;
use App\Controllers\Site\ProdutoController;

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
];
