<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;

final class ProdutoController extends Controller
{
    public function index(): void
    {
        $produtos = [
            [
                'id' => 1,
                'nome' => 'Mouse sem fio',
                'preco' => 89.90,
            ],
            [
                'id' => 2,
                'nome' => 'Teclado mecânico',
                'preco' => 279.90,
            ],
            [
                'id' => 3,
                'nome' => 'Monitor LED 24',
                'preco' => 899.90,
            ],
        ];

        $this->view(
            'site/produtos',
            [
                'tituloPagina' => 'Produtos',
                'produtos' => $produtos,
            ]
        );
    }
}
