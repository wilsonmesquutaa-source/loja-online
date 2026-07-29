<?php

declare(strict_types=1);

use App\Controllers\Cliente\PerfilController;

return [
    [
        'method' => 'GET',
        'path' => '/cliente/perfil',
        'action' => [
            PerfilController::class,
            'index',
        ],
    ],
];
