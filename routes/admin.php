<?php

declare(strict_types=1);

use App\Controllers\Admin\LoginAdminController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProdutoController;
use App\Controllers\Admin\CategoriaController;
use App\Controllers\Admin\DestaqueController;


return [

    /*
    =================================
    DASHBOARD
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/admin',
        'action' => [
            DashboardController::class,
            'index',
        ],
    ],


    /*
    =================================
    CATEGORIAS
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/admin/categorias',
        'action' => [
            CategoriaController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/categorias/novo',
        'action' => [
            CategoriaController::class,
            'novo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/categorias/salvar',
        'action' => [
            CategoriaController::class,
            'salvar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/categorias/editar/{id}',
        'action' => [
            CategoriaController::class,
            'editar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/categorias/atualizar/{id}',
        'action' => [
            CategoriaController::class,
            'atualizar',
        ],
    ],


    /*
    =================================
    DESTAQUES
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/admin/destaques',
        'action' => [
            DestaqueController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/destaques/novo',
        'action' => [
            DestaqueController::class,
            'novo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques/salvar',
        'action' => [
            DestaqueController::class,
            'salvar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/destaques/editar/{id}',
        'action' => [
            DestaqueController::class,
            'editar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques/atualizar/{id}',
        'action' => [
            DestaqueController::class,
            'atualizar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques/alternar/{id}',
        'action' => [
            DestaqueController::class,
            'alternarAtivo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques/excluir/{id}',
        'action' => [
            DestaqueController::class,
            'excluir',
        ],
    ],


    /*
    =================================
    LOGIN ADMIN
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/login-admin',
        'action' => [
            LoginAdminController::class,
            'formulario',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/login-admin',
        'action' => [
            LoginAdminController::class,
            'autenticar',
        ],
    ],


    /*
    =================================
    PRODUTOS
    =================================
    */

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
        'method' => 'POST',
        'path' => '/admin/produtos/excluir/{id}',
        'action' => [
            ProdutoController::class,
            'excluir',
        ],
    ],

];