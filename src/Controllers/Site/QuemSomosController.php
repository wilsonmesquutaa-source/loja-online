<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;

final class QuemSomosController extends Controller
{
    public function index(): void
    {
        $this->view(
            'site/quemsomos',
            [
                'tituloPagina' => 'Quem Somos',
                'rotaAtual' => 'quemsomos',
            ]
        );
    }
}