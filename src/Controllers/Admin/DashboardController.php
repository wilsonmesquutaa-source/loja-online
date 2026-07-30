<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        require APP_ROOT . '/conexao/conexao.php';


        $indicadores = [

            'produtos' => (int) $pdo->query(
                "SELECT COUNT(*) FROM produtos"
            )->fetchColumn(),


            'clientes' => (int) $pdo->query(
                "SELECT COUNT(*) FROM clientes"
            )->fetchColumn(),


            'pedidos' => (int) $pdo->query(
                "SELECT COUNT(*) FROM pedidos"
            )->fetchColumn(),


            'categorias' => (int) $pdo->query(
                "SELECT COUNT(*) FROM categorias"
            )->fetchColumn(),

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