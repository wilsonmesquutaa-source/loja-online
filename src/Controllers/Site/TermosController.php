<?php

declare(strict_types=1);

namespace App\Controllers\site;

class TermosController
{
    public function index(): void
    {
        require APP_ROOT . '/views/site/termos.php';
    }
}