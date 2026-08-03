<?php

declare(strict_types=1);

use App\Controllers\Admin\DashboardController;

return [
    [
        'method' => 'GET',
        'path' => '/admin',
        'action' => [
            DashboardController::class,
            'index',
        ],
    ],
];
