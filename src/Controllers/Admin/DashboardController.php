<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $indicadores = [
            'produtos' => 18,
            'clientes' => 12,
            'pedidos' => 7,
            'categorias' => 4,
        ];

        $this->view(
            'admin/dashboard',
            [
                'tituloPagina' => 'Dashboard administrativo',
                'indicadores' => $indicadores
            ]
        );
    }
}