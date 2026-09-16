<?php

declare(strict_types=1);

use App\Controllers\Admin\LoginAdminController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProdutoController;
use App\Controllers\Admin\CategoriaController;
use App\Controllers\Admin\DestaquesHomeController;
use App\Controllers\Admin\BannerHomeController;
use App\Controllers\Admin\PedidoController;


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
    DESTAQUES DA HOME
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/admin/destaques-home',
        'action' => [
            DestaquesHomeController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/destaques-home/novo',
        'action' => [
            DestaquesHomeController::class,
            'novo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques-home/salvar',
        'action' => [
            DestaquesHomeController::class,
            'salvar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/destaques-home/editar/{id}',
        'action' => [
            DestaquesHomeController::class,
            'editar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques-home/atualizar/{id}',
        'action' => [
            DestaquesHomeController::class,
            'atualizar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques-home/alternar/{id}',
        'action' => [
            DestaquesHomeController::class,
            'alternarAtivo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques-home/excluir/{id}',
        'action' => [
            DestaquesHomeController::class,
            'excluir',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/destaques-home/ordem',
        'action' => [
            DestaquesHomeController::class,
            'atualizarOrdem',
        ],
    ],


    /*
    =================================
    BANNERS DA HOME
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/admin/banners-home',
        'action' => [
            BannerHomeController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/banners-home/novo',
        'action' => [
            BannerHomeController::class,
            'novo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/banners-home/salvar',
        'action' => [
            BannerHomeController::class,
            'salvar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/banners-home/editar/{id}',
        'action' => [
            BannerHomeController::class,
            'editar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/banners-home/atualizar/{id}',
        'action' => [
            BannerHomeController::class,
            'atualizar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/banners-home/alternar/{id}',
        'action' => [
            BannerHomeController::class,
            'alternarAtivo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/banners-home/excluir/{id}',
        'action' => [
            BannerHomeController::class,
            'excluir',
        ],
    ],


    /*
    =================================
    PEDIDOS
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/admin/pedidos',
        'action' => [
            PedidoController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/admin/pedidos/{id}',
        'action' => [
            PedidoController::class,
            'detalhes',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/pedidos/status/{id}',
        'action' => [
            PedidoController::class,
            'atualizarStatus',
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
