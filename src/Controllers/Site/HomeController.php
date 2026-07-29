<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view(
            'site/home',
            [
                'tituloPagina' => 'Página inicial',
                'mensagem' => 'Bem-vindo à Loja Online',
            ]
        );
    }
}
