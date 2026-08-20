<?php

declare(strict_types=1);

use App\Controllers\Cliente\ClienteController;
use App\Controllers\Cliente\PerfilController;
use App\Controllers\Cliente\PedidoController;
use App\Controllers\Cliente\EnderecoController;

return [

    /*
    =================================
    LOGIN DO CLIENTE
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/login',
        'action' => [
            ClienteController::class,
            'login',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/login',
        'action' => [
            ClienteController::class,
            'autenticar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/logout',
        'action' => [
            ClienteController::class,
            'logout',
        ],
    ],


    /*
    =================================
    CADASTRO TRADICIONAL
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/cadastro',
        'action' => [
            ClienteController::class,
            'cadastro',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/cadastro',
        'action' => [
            ClienteController::class,
            'registrar',
        ],
    ],


    /*
    =================================
    GOOGLE
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/cadastro/google',
        'action' => [
            ClienteController::class,
            'iniciarCadastroGoogle',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/cadastro/google/callback',
        'action' => [
            ClienteController::class,
            'callbackCadastroGoogle',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/login/google',
        'action' => [
            ClienteController::class,
            'iniciarLoginGoogle',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/login/google/callback',
        'action' => [
            ClienteController::class,
            'callbackCadastroGoogle',
        ],
    ],


    /*
    =================================
    PERFIL
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/cliente/perfil',
        'action' => [
            PerfilController::class,
            'index',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/cliente/perfil/atualizar',
        'action' => [
            PerfilController::class,
            'atualizar',
        ],
    ],


    /*
    =================================
    PEDIDOS
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/cliente/pedidos',
        'action' => [
            PedidoController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/cliente/pedidos/{id}',
        'action' => [
            PedidoController::class,
            'visualizar',
        ],
    ],


    /*
    =================================
    ENDEREÇOS
    =================================
    */

    [
        'method' => 'GET',
        'path' => '/cliente/enderecos',
        'action' => [
            EnderecoController::class,
            'index',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/cliente/enderecos/novo',
        'action' => [
            EnderecoController::class,
            'novo',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/cliente/enderecos/salvar',
        'action' => [
            EnderecoController::class,
            'salvar',
        ],
    ],

    [
        'method' => 'GET',
        'path' => '/cliente/enderecos/editar/{id}',
        'action' => [
            EnderecoController::class,
            'editar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/cliente/enderecos/atualizar/{id}',
        'action' => [
            EnderecoController::class,
            'atualizar',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/cliente/enderecos/principal/{id}',
        'action' => [
            EnderecoController::class,
            'principal',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/cliente/enderecos/excluir/{id}',
        'action' => [
            EnderecoController::class,
            'excluir',
        ],
    ],

];