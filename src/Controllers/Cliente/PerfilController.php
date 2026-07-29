<?php

declare(strict_types=1);

namespace App\Controllers\Cliente;

use App\Controllers\Controller;

final class PerfilController extends Controller
{
    public function index(): void
    {
        $cliente = [
            'nome' => 'Cliente de demonstração',
            'email' => 'cliente@exemplo.com',
        ];

        $this->view(
            'cliente/perfil',
            [
                'tituloPagina' => 'Meu perfil',
                'cliente' => $cliente,
            ]
        );
    }
}
