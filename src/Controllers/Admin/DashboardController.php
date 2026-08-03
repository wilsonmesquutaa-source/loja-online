<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;

final class DashboardController
    extends Controller
{
    public function index(): void
    {
        if (
            empty(
                $_SESSION[
                    'usuario_admin'
                ]['id']
            )
        ) {
            $this->redirecionar(
                '/login-admin'
            );
        }

        $this->view(
            'admin/dashboard',
            [
                'tituloPagina' =>
                    'Dashboard administrativo',

                'usuarioAdmin' =>
                    $_SESSION[
                        'usuario_admin'
                    ],

                'csrfToken' =>
                    Csrf::gerar(),

                'indicadores' => [
                    'produtos' => 0,
                    'clientes' => 0,
                    'pedidos' => 0,
                    'categorias' => 0,
                ],
            ]
        );
    }
}
