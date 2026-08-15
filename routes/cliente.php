<?php

declare(strict_types=1);

use App\Controllers\Cliente\ClienteController;
use App\Controllers\Cliente\PerfilController;

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
            'callbackLoginGoogle',
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
];
