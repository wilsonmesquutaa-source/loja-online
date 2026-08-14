<?php

declare(strict_types=1);

use App\Controllers\Cliente\ClienteController;
use App\Controllers\Cliente\PerfilController;

return [

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
    CADASTRO COM GOOGLE
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