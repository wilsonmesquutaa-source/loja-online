<?php

declare(strict_types=1);

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProdutoController;


return [

    [
        'method' => 'GET',
        'path' => '/admin',
        'action' => [
            DashboardController::class,
            'index',
        ],
    ],


    [
        'method' => 'GET',
        'path' => '/admin/produtos',
        'action' => [
            ProdutoController::class,
            'index',
        ],
    ],


    [
        'method' => 'GET',
        'path' => '/admin/produtos/novo',
        'action' => [
            ProdutoController::class,
            'novo',
        ],
    ],


    [
        'method' => 'POST',
        'path' => '/admin/produtos/salvar',
        'action' => [
            ProdutoController::class,
            'salvar',
        ],
    ],


    [
        'method' => 'GET',
        'path' => '/admin/produtos/editar/{id}',
        'action' => [
            ProdutoController::class,
            'editar',
        ],
    ],


    [
        'method' => 'POST',
        'path' => '/admin/produtos/atualizar/{id}',
        'action' => [
            ProdutoController::class,
            'atualizar',
        ],
    ],


    [
        'method' => 'GET',
        'path' => '/admin/produtos/excluir/{id}',
        'action' => [
            ProdutoController::class,
            'excluir',
        ],
    ],

];