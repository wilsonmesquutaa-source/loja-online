<?php

declare(strict_types=1);

namespace App\Controllers\site;

class QuemSomosController
{
    public function index(): void
    {
        require APP_ROOT . '/views/site/quemsomos.php';
    }
}