<?php

declare(strict_types=1);

use App\Controllers\Admin\LoginAdminController;

return [
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
    [
        'method' => 'POST',
        'path' => '/logout-admin',
        'action' => [
            LoginAdminController::class,
            'sair',
        ],
    ],
];
